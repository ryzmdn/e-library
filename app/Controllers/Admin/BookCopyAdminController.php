<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\AuditLogModel;
use App\Models\BibliographicItemModel;
use App\Models\BookCopyModel;
use App\Services\BarcodeService;
use Throwable;

final class BookCopyAdminController extends Controller
{
    private BookCopyModel $copyModel;
    private BibliographicItemModel $biblioModel;
    private BarcodeService $barcodeService;
    private AuditLogModel $auditModel;

    public function __construct()
    {
        $this->copyModel = new BookCopyModel();
        $this->biblioModel = new BibliographicItemModel();
        $this->barcodeService = new BarcodeService();
        $this->auditModel = new AuditLogModel();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)($request->get('page') ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'keyword' => $request->get('q') ?? '',
            'status' => $request->get('status') ?? '',
            'condition' => $request->get('condition') ?? '',
            'biblio_id' => $request->get('biblio_id') ?? '',
        ];

        $copies = $this->copyModel->getAllCopiesAdmin($filters, $limit, $offset);
        $totalCopies = $this->copyModel->countAllCopiesAdmin($filters);
        $totalPages = (int)ceil($totalCopies / $limit);

        return $this->render('pages/admin/copies/index', [
            'pageTitle' => 'Manajemen Eksemplar Fisik & Barcode',
            'copies' => $copies,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCopies' => $totalCopies,
        ], 'admin');
    }

    public function create(Request $request): Response
    {
        $biblioId = (int)($request->get('biblio_id') ?? 0);
        $selectedBiblio = $biblioId > 0 ? $this->biblioModel->findById($biblioId) : null;
        $allBiblios = $this->biblioModel->getAllItemsAdmin([], 100, 0);

        if ($request->isPost()) {
            $postBiblioId = (int)$request->post('biblio_id');
            if ($postBiblioId <= 0) {
                Session::flash('error', 'Silakan pilih judul buku katalog.');
                return $this->redirect('/admin/copies/create');
            }

            $barcode = trim((string)$request->post('inventory_barcode'));
            if ($barcode === '') {
                $barcode = $this->copyModel->generateBarcode();
            }

            $data = [
                'biblio_id' => $postBiblioId,
                'inventory_barcode' => $barcode,
                'rfid_epc_hex' => trim((string)$request->post('rfid_epc_hex')),
                'shelf_building' => trim((string)($request->post('shelf_building') ?: 'Gedung Utama')),
                'shelf_floor' => (int)($request->post('shelf_floor') ?: 1),
                'shelf_aisle' => trim((string)$request->post('shelf_aisle')),
                'condition_status' => trim((string)($request->post('condition_status') ?: 'GOOD')),
                'availability_status' => trim((string)($request->post('availability_status') ?: 'AVAILABLE')),
                'acquisition_date' => $request->post('acquisition_date') ?: date('Y-m-d'),
                'unit_replacement_cost' => (float)($request->post('unit_replacement_cost') ?: 0.00),
                'notes' => trim((string)$request->post('notes')),
            ];

            try {
                $copyId = $this->copyModel->createCopy($data);

                $staffUser = Session::get('user');
                $staffId = $staffUser ? (int)$staffUser['id'] : null;
                $this->auditModel->log($staffId, 'CREATE_BOOK_COPY', 'book_copies', $copyId, ['barcode' => $barcode]);

                Session::flash('success', 'Eksemplar fisik berhasil didaftarkan dengan barcode ' . $barcode . '.');
                return $this->redirect('/admin/copies');
            } catch (Throwable $e) {
                Session::flash('error', 'Gagal mendaftarkan eksemplar: ' . $e->getMessage());
                return $this->redirect('/admin/copies/create' . ($postBiblioId > 0 ? '?biblio_id=' . $postBiblioId : ''));
            }
        }

        $suggestedBarcode = $this->copyModel->generateBarcode();

        return $this->render('pages/admin/copies/create', [
            'pageTitle' => 'Registrasi Eksemplar Fisik Baru',
            'selectedBiblio' => $selectedBiblio,
            'allBiblios' => $allBiblios,
            'suggestedBarcode' => $suggestedBarcode,
        ], 'admin');
    }

    public function edit(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $copy = $this->copyModel->getCopyWithBiblio($id);
        if ($copy === null) {
            Session::flash('error', 'Data eksemplar tidak ditemukan.');
            return $this->redirect('/admin/copies');
        }

        if ($request->isPost()) {
            $data = [
                'inventory_barcode' => trim((string)$request->post('inventory_barcode')),
                'rfid_epc_hex' => trim((string)$request->post('rfid_epc_hex')),
                'shelf_building' => trim((string)($request->post('shelf_building') ?: 'Gedung Utama')),
                'shelf_floor' => (int)($request->post('shelf_floor') ?: 1),
                'shelf_aisle' => trim((string)$request->post('shelf_aisle')),
                'condition_status' => trim((string)($request->post('condition_status') ?: 'GOOD')),
                'availability_status' => trim((string)($request->post('availability_status') ?: 'AVAILABLE')),
                'acquisition_date' => $request->post('acquisition_date') ?: date('Y-m-d'),
                'unit_replacement_cost' => (float)($request->post('unit_replacement_cost') ?: 0.00),
                'notes' => trim((string)$request->post('notes')),
            ];

            try {
                $this->copyModel->updateCopy($id, $data);

                $staffUser = Session::get('user');
                $staffId = $staffUser ? (int)$staffUser['id'] : null;
                $this->auditModel->log($staffId, 'UPDATE_BOOK_COPY', 'book_copies', $id, $data);

                Session::flash('success', 'Data eksemplar berhasil diperbarui.');
                return $this->redirect('/admin/copies');
            } catch (Throwable $e) {
                Session::flash('error', 'Gagal memperbarui eksemplar: ' . $e->getMessage());
                return $this->redirect('/admin/copies/' . $id . '/edit');
            }
        }

        return $this->render('pages/admin/copies/edit', [
            'pageTitle' => 'Edit Eksemplar: ' . $copy['inventory_barcode'],
            'copy' => $copy,
        ], 'admin');
    }

    public function delete(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $copy = $this->copyModel->findById($id);
        if ($copy === null) {
            Session::flash('error', 'Eksemplar tidak ditemukan.');
            return $this->redirect('/admin/copies');
        }

        if ($copy['availability_status'] === 'BORROWED') {
            Session::flash('error', 'Eksemplar tidak dapat dihapus karena sedang berstatus DIPINJAM.');
            return $this->redirect('/admin/copies');
        }

        $success = $this->copyModel->deleteCopy($id);
        if (!$success) {
            Session::flash('error', 'Gagal menghapus eksemplar.');
            return $this->redirect('/admin/copies');
        }

        $staffUser = Session::get('user');
        $staffId = $staffUser ? (int)$staffUser['id'] : null;
        $this->auditModel->log($staffId, 'DELETE_BOOK_COPY', 'book_copies', $id, ['barcode' => $copy['inventory_barcode']]);

        Session::flash('success', 'Eksemplar fisik berhasil dihapus.');
        return $this->redirect('/admin/copies');
    }

    public function label(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $copy = $this->copyModel->getCopyWithBiblio($id);
        if ($copy === null) {
            Session::flash('error', 'Eksemplar tidak ditemukan.');
            return $this->redirect('/admin/copies');
        }

        $barcodeSvg = $this->barcodeService->generateSvg($copy['inventory_barcode'], 42, 2, 4);
        $callNumber = $this->barcodeService->generateCallNumber(
            $copy['ddc_code'] ?? ($copy['call_number_base'] ?? '000'),
            $copy['authors_text'] ?? 'Anonim',
            $copy['title'] ?? 'Buku'
        );

        return $this->render('pages/admin/copies/print_label', [
            'pageTitle' => 'Cetak Label Barcode & Punggung Buku: ' . $copy['inventory_barcode'],
            'copy' => $copy,
            'barcodeSvg' => $barcodeSvg,
            'callNumber' => $callNumber,
        ], 'blank');
    }
}
