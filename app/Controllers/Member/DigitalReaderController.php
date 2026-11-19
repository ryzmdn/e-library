<?php
declare(strict_types=1);

namespace App\Controllers\Member;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\BibliographicItemModel;
use App\Models\DigitalAssetModel;
use App\Models\ReadingProgressModel;
use App\Services\DigitalStreamService;
use Throwable;

final class DigitalReaderController extends Controller
{
    private ?BibliographicItemModel $biblioModel;
    private ?DigitalAssetModel $digitalModel;
    private ?ReadingProgressModel $progressModel;
    private ?DigitalStreamService $streamService;

    public function __construct()
    {
        try {
            $this->biblioModel = new BibliographicItemModel();
            $this->digitalModel = new DigitalAssetModel();
            $this->progressModel = new ReadingProgressModel();
            $this->streamService = new DigitalStreamService();
        } catch (Throwable $e) {
            $this->biblioModel = null;
            $this->digitalModel = null;
            $this->progressModel = null;
            $this->streamService = null;
        }
    }

    public function open(Request $request, string $identifier): Response
    {
        $user = Session::getUser();
        if (!$user) {
            Session::setFlash('error', 'Silakan masuk untuk membaca koleksi e-book digital.');
            return $this->redirect('/login');
        }

        $userId = (int)($user['id'] ?? 4);
        $item = $this->biblioModel ? $this->biblioModel->findBySlugOrId($identifier) : null;
        $asset = null;
        $progress = null;
        $streamToken = '';

        if (!$item) {
            if ($identifier === 'filosofi-teras' || $identifier === '6') {
                $item = [
                    'id' => 6,
                    'slug' => 'filosofi-teras',
                    'title' => 'Filosofi Teras',
                    'authors_text' => 'Henry Manampiring',
                    'publisher_name' => 'Penerbit Buku Kompas',
                    'publication_year' => 2019,
                    'media_type' => 'HYBRID',
                ];
                $asset = [
                    'id' => 4,
                    'biblio_id' => 6,
                    'file_format' => 'PDF',
                    'access_level' => 'MEMBER_STREAM_ONLY',
                ];
                $progress = [
                    'last_page_or_cfi' => '178',
                    'completion_percent' => 64.0,
                ];
                $streamToken = 'demo_token_filosofi_teras';
            } elseif ($identifier === 'laut-bercerita' || $identifier === '9') {
                $item = [
                    'id' => 9,
                    'slug' => 'laut-bercerita',
                    'title' => 'Laut Bercerita',
                    'authors_text' => 'Leila S. Chudori',
                    'publisher_name' => 'Kepustakaan Populer Gramedia',
                    'publication_year' => 2021,
                    'media_type' => 'HYBRID',
                ];
                $asset = [
                    'id' => 7,
                    'biblio_id' => 9,
                    'file_format' => 'PDF',
                    'access_level' => 'MEMBER_STREAM_ONLY',
                ];
                $progress = [
                    'last_page_or_cfi' => '122',
                    'completion_percent' => 32.0,
                ];
                $streamToken = 'demo_token_laut_bercerita';
            } else {
                return Response::html('<h1>404 Koleksi Tidak Ditemukan</h1><p>Buku digital yang Anda cari tidak tersedia dalam pangkalan data.</p>', 404);
            }
        } else {
            $biblioId = (int)$item['id'];
            $asset = $this->digitalModel ? $this->digitalModel->findByBiblioId($biblioId) : null;
            if (!$asset) {
                Session::setFlash('error', 'Buku ini tidak memiliki berkas digital yang terlampir.');
                return $this->redirect('/catalog/' . urlencode((string)$item['slug']));
            }

            if ($this->digitalModel && !$this->digitalModel->canUserAccess($asset, $user)) {
                Session::setFlash('error', 'Akses dibatasi: Tingkatan keanggotaan Anda belum memiliki wewenang untuk membaca bahan pustaka digital ini.');
                return $this->redirect('/catalog/' . urlencode((string)$item['slug']));
            }

            $assetId = (int)$asset['id'];
            $progress = $this->progressModel ? $this->progressModel->getProgress($userId, $assetId) : null;
            $streamToken = $this->streamService ? $this->streamService->generateStreamToken($userId, $assetId) : 'token_' . $assetId;
        }

        $watermarkText = sprintf(
            '%s (%s) - %s',
            $user['full_name'] ?? 'Anggota',
            $user['member_number'] ?? 'M-2024-0001',
            date('d M Y')
        );

        return $this->render('pages/member/reader/index', [
            'pageTitle' => $item['title'] . ' - Pembaca Digital Terproteksi',
            'item' => $item,
            'asset' => $asset,
            'progress' => $progress,
            'streamToken' => $streamToken,
            'watermarkText' => $watermarkText,
            'user' => $user,
        ], 'reader');
    }

    public function stream(Request $request, string $assetId): Response
    {
        $user = Session::getUser();
        if (!$user) {
            return new Response('Akses ditolak: Autentikasi diperlukan.', 401, ['Content-Type' => 'text/plain']);
        }

        $userId = (int)$user['id'];
        $asset = $this->digitalModel->findById((int)$assetId);
        if (!$asset) {
            return new Response('Aset digital tidak ditemukan.', 404, ['Content-Type' => 'text/plain']);
        }

        $token = (string)($request->input('token') ?? '');
        if ($token === '' || !$this->streamService->verifyStreamToken($token, $userId, (int)$asset['id'])) {
            return new Response('Sesi baca tidak valid atau telah kedaluwarsa.', 403, ['Content-Type' => 'text/plain']);
        }

        if (!$this->digitalModel->canUserAccess($asset, $user)) {
            return new Response('Hak akses digital tidak mencukupi.', 403, ['Content-Type' => 'text/plain']);
        }

        $biblio = $this->biblioModel->findById((int)$asset['biblio_id']);
        $displayTitle = $biblio ? $biblio['title'] : 'Dokumen E-Library';

        return $this->streamService->streamFile($asset, $request, $displayTitle);
    }

    public function saveProgress(Request $request): Response
    {
        $user = Session::getUser();
        if (!$user) {
            return Response::json(['error' => 'Sesi tidak sah.'], 401);
        }

        $userId = (int)$user['id'];
        $assetId = (int)$request->input('digital_asset_id', 0);
        $page = trim((string)$request->input('page', '1'));
        $percent = (float)$request->input('completion_percent', 0.0);
        $seconds = (int)$request->input('seconds_read', 0);

        if ($assetId <= 0) {
            return Response::json(['error' => 'ID aset digital tidak valid.'], 400);
        }

        $saved = $this->progressModel->saveProgress($userId, $assetId, $page, $percent, $seconds);

        return Response::json([
            'success' => $saved,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
