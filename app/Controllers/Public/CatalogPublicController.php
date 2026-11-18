<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\BibliographicItemModel;
use App\Models\BookCopyModel;
use App\Models\DigitalAssetModel;
use Throwable;

final class CatalogPublicController extends Controller
{
    private BibliographicItemModel $biblioModel;
    private BookCopyModel $copyModel;
    private DigitalAssetModel $digitalModel;

    public function __construct()
    {
        $this->biblioModel = new BibliographicItemModel();
        $this->copyModel = new BookCopyModel();
        $this->digitalModel = new DigitalAssetModel();
    }

    public function index(Request $request): Response
    {
        $q = trim((string)$request->input('q', ''));
        $type = trim((string)$request->input('type', ''));
        $ddc = trim((string)$request->input('ddc', ''));
        $avail = (int)$request->input('avail', 0);
        $sort = trim((string)$request->input('sort', 'latest'));
        $page = max(1, (int)$request->input('page', 1));
        $limit = 12;

        $filters = [];
        if ($type !== '') {
            $filters['media_type'] = $type;
        }
        if ($ddc !== '') {
            $filters['ddc_code'] = $ddc;
        }
        if ($avail === 1) {
            $filters['only_available'] = true;
        }

        $items = [];
        $totalItems = 0;
        $totalPages = 1;
        $ddcCategories = [];

        try {
            $totalItems = $this->biblioModel->countSearch($q, $filters);
            $totalPages = max(1, (int)ceil($totalItems / $limit));
            if ($page > $totalPages) {
                $page = $totalPages;
            }
            $offset = ($page - 1) * $limit;
            $items = $this->biblioModel->search($q, $filters, $sort, $limit, $offset);
            $ddcCategories = $this->biblioModel->getDdcCategories();
        } catch (Throwable $e) {
            $items = [];
            $totalItems = 0;
            $totalPages = 1;
            $ddcCategories = [];
        }

        $queryParams = [];
        if ($q !== '') {
            $queryParams['q'] = $q;
        }
        if ($type !== '') {
            $queryParams['type'] = $type;
        }
        if ($ddc !== '') {
            $queryParams['ddc'] = $ddc;
        }
        if ($avail === 1) {
            $queryParams['avail'] = 1;
        }
        if ($sort !== '' && $sort !== 'latest') {
            $queryParams['sort'] = $sort;
        }

        if ($request->isJson()) {
            return Response::json([
                'items' => $items,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_items' => $totalItems,
                    'per_page' => $limit,
                ],
                'filters' => $filters,
                'sort' => $sort,
            ]);
        }

        return $this->render('pages/public/catalog/index', [
            'pageTitle' => 'Katalog OPAC & Penelusuran Pustaka',
            'items' => $items,
            'totalItems' => $totalItems,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'keyword' => $q,
            'mediaType' => $type,
            'ddcCode' => $ddc,
            'onlyAvailable' => $avail === 1,
            'sort' => $sort,
            'ddcCategories' => $ddcCategories,
            'queryParams' => $queryParams,
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $item = null;
        $copies = [];
        $digitalAsset = null;
        $canAccessDigital = false;
        $relatedItems = [];

        try {
            $item = $this->biblioModel->findBySlugOrId($slug);
            if (!$item) {
                return Response::html('<h1>404 Koleksi Tidak Ditemukan</h1><p>Bahan pustaka yang Anda cari tidak terdaftar dalam pangkalan data katalog.</p>', 404);
            }

            $copies = $this->copyModel->findByBiblioId((int)$item['id']);
            $digitalAsset = $this->digitalModel->findByBiblioId((int)$item['id']);
            $user = Session::getUser();
            if ($digitalAsset !== null) {
                $canAccessDigital = $this->digitalModel->canUserAccess($digitalAsset, $user);
            }
            $relatedItems = $this->biblioModel->getRelatedItems((int)$item['id'], (string)($item['subject_tags'] ?? ''), 4);
        } catch (Throwable $e) {
            return Response::html('<h1>Terjadi Gangguan Sistem</h1><p>Katalog sedang dalam pemeliharaan. Silakan coba sesaat lagi.</p>', 500);
        }

        return $this->render('pages/public/catalog/detail', [
            'pageTitle' => $item['title'] . ' - Katalog Perpustakaan',
            'item' => $item,
            'copies' => $copies,
            'digitalAsset' => $digitalAsset,
            'canAccessDigital' => $canAccessDigital,
            'relatedItems' => $relatedItems,
            'user' => Session::getUser(),
        ]);
    }

    public function searchSuggest(Request $request): Response
    {
        $q = trim((string)$request->input('q', ''));
        if ($q === '') {
            return Response::json(['results' => []]);
        }

        $results = [];
        try {
            $items = $this->biblioModel->search($q, [], 'popular', 6, 0);
            foreach ($items as $item) {
                $results[] = [
                    'id' => (int)$item['id'],
                    'slug' => $item['slug'],
                    'title' => $item['title'],
                    'authors' => $item['authors_text'],
                    'media_type' => $item['media_type'],
                    'available_copies' => (int)$item['available_physical_copies'],
                    'year' => (int)$item['publication_year'],
                    'cover_path' => $item['cover_image_path'],
                    'url' => '/catalog/' . urlencode($item['slug']),
                ];
            }
        } catch (Throwable $e) {
            $results = [];
        }

        return Response::json(['results' => $results]);
    }
}
