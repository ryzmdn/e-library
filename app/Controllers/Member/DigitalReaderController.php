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
    private BibliographicItemModel $biblioModel;
    private DigitalAssetModel $digitalModel;
    private ReadingProgressModel $progressModel;
    private DigitalStreamService $streamService;

    public function __construct()
    {
        $this->biblioModel = new BibliographicItemModel();
        $this->digitalModel = new DigitalAssetModel();
        $this->progressModel = new ReadingProgressModel();
        $this->streamService = new DigitalStreamService();
    }

    public function open(Request $request, string $identifier): Response
    {
        $user = Session::getUser();
        if (!$user) {
            Session::setFlash('error', 'Silakan masuk untuk membaca koleksi e-book digital.');
            return $this->redirect('/login');
        }

        $userId = (int)$user['id'];
        $item = $this->biblioModel->findBySlugOrId($identifier);
        if (!$item) {
            return Response::html('<h1>404 Koleksi Tidak Ditemukan</h1><p>Buku digital yang Anda cari tidak tersedia dalam pangkalan data.</p>', 404);
        }

        $biblioId = (int)$item['id'];
        $asset = $this->digitalModel->findByBiblioId($biblioId);
        if (!$asset) {
            Session::setFlash('error', 'Buku ini tidak memiliki berkas digital yang terlampir.');
            return $this->redirect('/catalog/' . urlencode((string)$item['slug']));
        }

        if (!$this->digitalModel->canUserAccess($asset, $user)) {
            Session::setFlash('error', 'Akses dibatasi: Tingkatan keanggotaan Anda belum memiliki wewenang untuk membaca bahan pustaka digital ini.');
            return $this->redirect('/catalog/' . urlencode((string)$item['slug']));
        }

        $assetId = (int)$asset['id'];
        $progress = $this->progressModel->getProgress($userId, $assetId);
        $streamToken = $this->streamService->generateStreamToken($userId, $assetId);

        $watermarkText = sprintf(
            '%s (%s) - %s',
            $user['full_name'],
            $user['member_number'],
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
