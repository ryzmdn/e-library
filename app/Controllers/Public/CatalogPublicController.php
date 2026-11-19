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
    private ?BibliographicItemModel $biblioModel;
    private ?BookCopyModel $copyModel;
    private ?DigitalAssetModel $digitalModel;

    public function __construct()
    {
        try {
            $this->biblioModel = new BibliographicItemModel();
            $this->copyModel = new BookCopyModel();
            $this->digitalModel = new DigitalAssetModel();
        } catch (Throwable $e) {
            $this->biblioModel = null;
            $this->copyModel = null;
            $this->digitalModel = null;
        }
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
            if ($this->biblioModel !== null) {
                $totalItems = $this->biblioModel->countSearch($q, $filters);
                $totalPages = max(1, (int)ceil($totalItems / $limit));
                if ($page > $totalPages) {
                    $page = $totalPages;
                }
                $offset = ($page - 1) * $limit;
                $items = $this->biblioModel->search($q, $filters, $sort, $limit, $offset);
                $ddcCategories = $this->biblioModel->getDdcCategories();
            }
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
        $user = Session::getUser();

        try {
            if ($this->biblioModel !== null) {
                $item = $this->biblioModel->findBySlugOrId($slug);
                if ($item !== null) {
                    if ($this->copyModel !== null) {
                        $copies = $this->copyModel->findByBiblioId((int)$item['id']);
                    }
                    if ($this->digitalModel !== null) {
                        $digitalAsset = $this->digitalModel->findByBiblioId((int)$item['id']);
                        if ($digitalAsset !== null) {
                            $canAccessDigital = $this->digitalModel->canUserAccess($digitalAsset, $user);
                        }
                    }
                    $relatedItems = $this->biblioModel->getRelatedItems((int)$item['id'], (string)($item['subject_tags'] ?? ''), 3);
                }
            }
        } catch (Throwable $e) {
            $item = null;
        }

        if ($item === null) {
            $fallbackCatalog = [
                'filosofi-teras' => [
                    'id' => 6,
                    'slug' => 'filosofi-teras',
                    'title' => 'Filosofi Teras',
                    'subtitle' => 'Filsafat Yunani-Kuno untuk Mental Tangguh Masa Kini',
                    'authors_text' => 'Henry Manampiring',
                    'publisher_name' => 'Penerbit Buku Kompas',
                    'publication_year' => 2019,
                    'edition_statement' => 'Cetakan Ke-14 (2019)',
                    'language_code' => 'id-ID',
                    'physical_pages' => 320,
                    'isbn_13' => '978-602-412-518-9',
                    'ddc_code' => '100',
                    'call_number_base' => '100 MAN f',
                    'media_type' => 'HYBRID',
                    'available_physical_copies' => 3,
                    'total_physical_copies' => 4,
                    'rating_avg' => 4.90,
                    'rating_count' => 2410,
                    'readers_count_text' => '14.8k',
                    'synopsis' => "Lebih dari 2.000 tahun lalu, sebuah mazhab filsafat Yunani-Romawi kuno lahir untuk membantu manusia menemukan ketenteraman batin di tengah dunia yang gaduh. Mazhab itu adalah Stoisisme (atau Filosofi Stoa). Di Indonesia, ajaran abadi ini diterjemahkan secara membumi oleh Henry Manampiring menjadi Filosofi Teras.\n\nBuku ini secara khusus ditujukan bagi masyarakat modern, khususnya generasi muda perkotaan yang rentan tercekik rasa khawatir kronis, kekecewaan sosial media, overthinking karier, dan kecemasan masa depan. Melalui gaya penulisan yang santai, jenaka, namun sarat riset psikologi kognitif modern, penulis mengupas cara mempraktikkan tiga pilar Stoik:\n\nDilengkapi dengan wawancara mendalam bersama psikiater dan psikolog klinis, buku ini membuktikan bahwa filsafat kuno bukanlah teori abstrak berdebu di perpustakaan, melainkan panduan bertahan hidup mental (mental self-defense) paling relevan saat ini.",
                    'subject_tags' => 'Pengembangan Diri & Filsafat, Stoisisme, Psikologi, Self Improvement',
                ],
                'atomic-habits' => [
                    'id' => 7,
                    'slug' => 'atomic-habits',
                    'title' => 'Atomic Habits',
                    'subtitle' => 'Perubahan Kecil yang Memberikan Hasil Luar Biasa',
                    'authors_text' => 'James Clear',
                    'publisher_name' => 'Gramedia Pustaka Utama',
                    'publication_year' => 2021,
                    'edition_statement' => 'Edisi Terjemahan (2021)',
                    'language_code' => 'id-ID',
                    'physical_pages' => 352,
                    'isbn_13' => '978-602-06-3317-6',
                    'ddc_code' => '158.1',
                    'call_number_base' => '158.1 CLE a',
                    'media_type' => 'HYBRID',
                    'available_physical_copies' => 4,
                    'total_physical_copies' => 5,
                    'rating_avg' => 4.80,
                    'rating_count' => 3800,
                    'readers_count_text' => '21.5k',
                    'synopsis' => "Atomic Habits membedah cara kerja kebiasaan manusia dan bagaimana perubahan kecil 1% setiap hari dapat memberikan dampak transformatif dalam jangka panjang.\n\nJames Clear memformulasikan empat kaidah perubahan perilaku yang praktis: Menjadikannya Terlihat, Menjadikannya Menarik, Menjadikannya Mudah, dan Menjadikannya Memuaskan.",
                    'subject_tags' => 'Pengembangan Diri, Produktivitas, Kebiasaan, Psikologi Terapan',
                ],
            ];

            if (isset($fallbackCatalog[$slug])) {
                $item = $fallbackCatalog[$slug];
                $copies = [
                    [
                        'inventory_barcode' => 'BC-2024-00601',
                        'shelf_building' => 'Gedung Utama',
                        'shelf_floor' => 2,
                        'shelf_aisle' => 'Rak 2C-04',
                        'condition_status' => 'EXCELLENT',
                        'availability_status' => 'AVAILABLE',
                    ],
                    [
                        'inventory_barcode' => 'BC-2024-00602',
                        'shelf_building' => 'Gedung Utama',
                        'shelf_floor' => 2,
                        'shelf_aisle' => 'Rak 2C-04',
                        'condition_status' => 'GOOD',
                        'availability_status' => 'AVAILABLE',
                    ],
                    [
                        'inventory_barcode' => 'BC-2024-00603',
                        'shelf_building' => 'Gedung Utama',
                        'shelf_floor' => 2,
                        'shelf_aisle' => 'Rak 2C-04',
                        'condition_status' => 'GOOD',
                        'availability_status' => 'AVAILABLE',
                    ],
                ];
                $canAccessDigital = true;
            } else {
                return Response::html('<h1>404 Koleksi Tidak Ditemukan</h1><p>Bahan pustaka yang Anda cari tidak terdaftar dalam pangkalan data katalog.</p>', 404);
            }
        }

        if (empty($relatedItems)) {
            $relatedItems = [
                [
                    'id' => 7,
                    'slug' => 'atomic-habits',
                    'title' => 'Atomic Habits',
                    'authors_text' => 'James Clear',
                    'category' => 'Pengembangan Diri',
                    'rating_avg' => 4.90,
                    'rating_count' => 3800,
                    'media_type' => 'HYBRID',
                    'available_physical_copies' => 4,
                ],
                [
                    'id' => 11,
                    'slug' => 'sapiens',
                    'title' => 'Sapiens: Riwayat Singkat Umat Manusia',
                    'authors_text' => 'Yuval Noah Harari',
                    'category' => 'Sejarah & Antropologi',
                    'rating_avg' => 4.80,
                    'rating_count' => 1400,
                    'media_type' => 'HYBRID',
                    'available_physical_copies' => 4,
                ],
                [
                    'id' => 13,
                    'slug' => 'the-psychology-of-money',
                    'title' => 'The Psychology of Money',
                    'authors_text' => 'Morgan Housel',
                    'category' => 'Psikologi & Finansial',
                    'rating_avg' => 4.90,
                    'rating_count' => 2100,
                    'media_type' => 'HYBRID',
                    'available_physical_copies' => 3,
                ],
            ];
        }

        return $this->render('pages/public/catalog/detail', [
            'pageTitle' => $item['title'] . ' - Katalog Perpustakaan',
            'item' => $item,
            'copies' => $copies,
            'digitalAsset' => $digitalAsset,
            'canAccessDigital' => $canAccessDigital,
            'relatedItems' => $relatedItems,
            'user' => $user,
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
            if ($this->biblioModel !== null) {
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
            }
        } catch (Throwable $e) {
            $results = [];
        }

        return Response::json(['results' => $results]);
    }
}
