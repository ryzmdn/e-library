<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\AuditLogModel;
use App\Models\BibliographicItemModel;
use App\Models\DigitalAssetModel;
use Throwable;

final class CatalogAdminController extends Controller
{
    private BibliographicItemModel $biblioModel;
    private DigitalAssetModel $digitalModel;
    private AuditLogModel $auditModel;

    public function __construct()
    {
        $this->biblioModel = new BibliographicItemModel();
        $this->digitalModel = new DigitalAssetModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)($request->get('page') ?? 1));
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'keyword' => $request->get('q') ?? '',
            'media_type' => $request->get('media') ?? '',
            'ddc' => $request->get('ddc') ?? '',
        ];

        $items = $this->biblioModel->getAllItemsAdmin($filters, $limit, $offset);
        $totalItems = $this->biblioModel->countAllItemsAdmin($filters);
        $totalPages = (int)ceil($totalItems / $limit);

        return $this->render('pages/admin/catalog/index', [
            'pageTitle' => 'Manajemen Katalog Bibliografi',
            'items' => $items,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
        ], 'admin');
    }

    public function create(Request $request): Response
    {
        if ($request->isPost()) {
            $title = trim((string)$request->post('title'));
            if ($title === '') {
                Session::flash('error', 'Judul buku wajib diisi.');
                return $this->redirect('/admin/catalog/create');
            }

            $coverPath = null;
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['cover_image']['tmp_name'];
                $mimeType = mime_content_type($fileTmp);
                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

                if (in_array($mimeType, $allowedMimes, true)) {
                    $ext = $mimeType === 'image/png' ? 'png' : ($mimeType === 'image/webp' ? 'webp' : 'jpg');
                    $fileName = 'cover_' . bin2hex(random_bytes(10)) . '.' . $ext;
                    $targetDir = dirname(__DIR__, 3) . '/public/uploads/covers';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    $targetPath = $targetDir . '/' . $fileName;
                    if (move_uploaded_file($fileTmp, $targetPath)) {
                        $coverPath = '/uploads/covers/' . $fileName;
                    }
                }
            }

            $data = [
                'title' => $title,
                'isbn_13' => trim((string)$request->post('isbn_13')),
                'isbn_10' => trim((string)$request->post('isbn_10')),
                'issn' => trim((string)$request->post('issn')),
                'ddc_code' => trim((string)$request->post('ddc_code')),
                'call_number_base' => trim((string)$request->post('call_number_base')),
                'authors_text' => trim((string)$request->post('authors_text')),
                'publisher_name' => trim((string)$request->post('publisher_name')),
                'publication_year' => $request->post('publication_year') !== '' ? (int)$request->post('publication_year') : null,
                'edition_statement' => trim((string)$request->post('edition_statement')),
                'language_code' => trim((string)($request->post('language_code') ?: 'id')),
                'physical_pages' => $request->post('physical_pages') !== '' ? (int)$request->post('physical_pages') : null,
                'synopsis' => trim((string)$request->post('synopsis')),
                'subject_tags' => trim((string)$request->post('subject_tags')),
                'media_type' => trim((string)($request->post('media_type') ?: 'PRINTED_BOOK')),
                'cover_image_path' => $coverPath,
            ];

            try {
                $newId = $this->biblioModel->createItem($data);

                if (isset($_FILES['digital_pdf']) && $_FILES['digital_pdf']['error'] === UPLOAD_ERR_OK) {
                    $pdfTmp = $_FILES['digital_pdf']['tmp_name'];
                    $pdfMime = mime_content_type($pdfTmp);
                    if ($pdfMime === 'application/pdf') {
                        $pdfSize = (int)$_FILES['digital_pdf']['size'];
                        $sha256 = hash_file('sha256', $pdfTmp);
                        $hashName = hash('sha256', $pdfTmp . microtime());
                        $digitalDir = dirname(__DIR__, 3) . '/storage/digital_assets/books';
                        if (!is_dir($digitalDir)) {
                            mkdir($digitalDir, 0755, true);
                        }
                        $pdfTarget = $digitalDir . '/' . $hashName . '.pdf';
                        if (move_uploaded_file($pdfTmp, $pdfTarget)) {
                            $staffUser = Session::get('user');
                            $staffId = (int)($staffUser['id'] ?? 1);
                            $accessLevel = trim((string)($request->post('access_level') ?: 'MEMBER_STREAM_ONLY'));
                            $this->digitalModel->createAsset([
                                'biblio_id' => $newId,
                                'storage_filename_hash' => $hashName,
                                'file_format' => 'PDF',
                                'file_size_bytes' => $pdfSize,
                                'sha256_checksum' => $sha256,
                                'access_level' => $accessLevel,
                                'max_concurrent_readers' => 0,
                                'uploaded_by' => $staffId,
                            ]);
                        }
                    }
                }

                $staffUser = Session::get('user');
                $staffId = $staffUser ? (int)$staffUser['id'] : null;
                $this->auditModel->log($staffId, 'CREATE_BIBLIO', 'bibliographic_items', $newId, ['title' => $title]);

                Session::flash('success', 'Buku baru berhasil ditambahkan ke dalam katalog.');
                return $this->redirect('/admin/catalog');
            } catch (Throwable $e) {
                Session::flash('error', 'Gagal menyimpan buku: ' . $e->getMessage());
                return $this->redirect('/admin/catalog/create');
            }
        }

        return $this->render('pages/admin/catalog/create', [
            'pageTitle' => 'Tambah Buku Baru ke Katalog',
        ], 'admin');
    }

    public function edit(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $item = $this->biblioModel->findById($id);
        if ($item === null) {
            Session::flash('error', 'Data bibliografi tidak ditemukan.');
            return $this->redirect('/admin/catalog');
        }

        $digitalAsset = $this->digitalModel->findByBiblioId($id);

        if ($request->isPost()) {
            $title = trim((string)$request->post('title'));
            if ($title === '') {
                Session::flash('error', 'Judul buku wajib diisi.');
                return $this->redirect('/admin/catalog/' . $id . '/edit');
            }

            $data = [
                'title' => $title,
                'isbn_13' => trim((string)$request->post('isbn_13')),
                'isbn_10' => trim((string)$request->post('isbn_10')),
                'issn' => trim((string)$request->post('issn')),
                'ddc_code' => trim((string)$request->post('ddc_code')),
                'call_number_base' => trim((string)$request->post('call_number_base')),
                'authors_text' => trim((string)$request->post('authors_text')),
                'publisher_name' => trim((string)$request->post('publisher_name')),
                'publication_year' => $request->post('publication_year') !== '' ? (int)$request->post('publication_year') : null,
                'edition_statement' => trim((string)$request->post('edition_statement')),
                'language_code' => trim((string)($request->post('language_code') ?: 'id')),
                'physical_pages' => $request->post('physical_pages') !== '' ? (int)$request->post('physical_pages') : null,
                'synopsis' => trim((string)$request->post('synopsis')),
                'subject_tags' => trim((string)$request->post('subject_tags')),
                'media_type' => trim((string)($request->post('media_type') ?: 'PRINTED_BOOK')),
            ];

            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['cover_image']['tmp_name'];
                $mimeType = mime_content_type($fileTmp);
                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

                if (in_array($mimeType, $allowedMimes, true)) {
                    $ext = $mimeType === 'image/png' ? 'png' : ($mimeType === 'image/webp' ? 'webp' : 'jpg');
                    $fileName = 'cover_' . bin2hex(random_bytes(10)) . '.' . $ext;
                    $targetDir = dirname(__DIR__, 3) . '/public/uploads/covers';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    $targetPath = $targetDir . '/' . $fileName;
                    if (move_uploaded_file($fileTmp, $targetPath)) {
                        $data['cover_image_path'] = '/uploads/covers/' . $fileName;
                    }
                }
            }

            try {
                $this->biblioModel->updateItem($id, $data);

                if (isset($_FILES['digital_pdf']) && $_FILES['digital_pdf']['error'] === UPLOAD_ERR_OK) {
                    $pdfTmp = $_FILES['digital_pdf']['tmp_name'];
                    $pdfMime = mime_content_type($pdfTmp);
                    if ($pdfMime === 'application/pdf') {
                        $pdfSize = (int)$_FILES['digital_pdf']['size'];
                        $sha256 = hash_file('sha256', $pdfTmp);
                        $hashName = hash('sha256', $pdfTmp . microtime());
                        $digitalDir = dirname(__DIR__, 3) . '/storage/digital_assets/books';
                        if (!is_dir($digitalDir)) {
                            mkdir($digitalDir, 0755, true);
                        }
                        $pdfTarget = $digitalDir . '/' . $hashName . '.pdf';
                        if (move_uploaded_file($pdfTmp, $pdfTarget)) {
                            $staffUser = Session::get('user');
                            $staffId = (int)($staffUser['id'] ?? 1);
                            $accessLevel = trim((string)($request->post('access_level') ?: 'MEMBER_STREAM_ONLY'));

                            $this->digitalModel->deleteByBiblioId($id);
                            $this->digitalModel->createAsset([
                                'biblio_id' => $id,
                                'storage_filename_hash' => $hashName,
                                'file_format' => 'PDF',
                                'file_size_bytes' => $pdfSize,
                                'sha256_checksum' => $sha256,
                                'access_level' => $accessLevel,
                                'max_concurrent_readers' => 0,
                                'uploaded_by' => $staffId,
                            ]);
                        }
                    }
                }

                $staffUser = Session::get('user');
                $staffId = $staffUser ? (int)$staffUser['id'] : null;
                $this->auditModel->log($staffId, 'UPDATE_BIBLIO', 'bibliographic_items', $id, ['title' => $title]);

                Session::flash('success', 'Perubahan katalog berhasil diperbarui.');
                return $this->redirect('/admin/catalog');
            } catch (Throwable $e) {
                Session::flash('error', 'Gagal memperbarui buku: ' . $e->getMessage());
                return $this->redirect('/admin/catalog/' . $id . '/edit');
            }
        }

        return $this->render('pages/admin/catalog/edit', [
            'pageTitle' => 'Edit Katalog: ' . $item['title'],
            'item' => $item,
            'digitalAsset' => $digitalAsset,
        ], 'admin');
    }

    public function delete(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $item = $this->biblioModel->findById($id);
        if ($item === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            return $this->redirect('/admin/catalog');
        }

        $success = $this->biblioModel->deleteItem($id);
        if (!$success) {
            Session::flash('error', 'Buku tidak dapat dihapus karena masih ada eksemplar fisik yang sedang berstatus DIPINJAM.');
            return $this->redirect('/admin/catalog');
        }

        $staffUser = Session::get('user');
        $staffId = $staffUser ? (int)$staffUser['id'] : null;
        $this->auditModel->log($staffId, 'DELETE_BIBLIO', 'bibliographic_items', $id, ['title' => $item['title']]);

        Session::flash('success', 'Judul buku dan seluruh aset terkait berhasil dihapus dari sistem.');
        return $this->redirect('/admin/catalog');
    }
}
