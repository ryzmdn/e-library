<?php
declare(strict_types=1);

namespace App\Controllers\Member;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\ReservationQueueModel;
use App\Services\CirculationService;
use Throwable;

final class ReservationMemberController extends Controller
{
    private ReservationQueueModel $reservationModel;
    private CirculationService $circulationService;

    public function __construct()
    {
        $this->reservationModel = new ReservationQueueModel();
        $this->circulationService = new CirculationService();
    }

    public function index(Request $request): Response
    {
        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        $reservations = [];
        try {
            $reservations = $this->reservationModel->getActiveReservationsByUserId($userId);
        } catch (Throwable $e) {
            $reservations = [];
        }

        return $this->render('pages/member/reservations/index', [
            'pageTitle' => 'Antrean Reservasi Buku',
            'reservations' => $reservations,
        ]);
    }

    public function create(Request $request): Response
    {
        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);
        $biblioId = (int)$request->input('biblio_id', 0);

        if ($biblioId <= 0) {
            Session::setFlash('error', 'Koleksi bibliografi tidak valid.');
            return $this->redirect('/catalog');
        }

        try {
            $res = $this->circulationService->reserve($biblioId, $userId);
            Session::setFlash('success', "Reservasi berhasil dibuat untuk buku \"{$res['book_title']}\". Anda berada di nomor antrean ke-{$res['queue_position']}.");
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect('/member/reservations');
    }

    public function cancel(Request $request, string $id): Response
    {
        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        try {
            $this->circulationService->cancelReservation((int)$id, $userId);
            Session::setFlash('success', 'Antrean reservasi buku berhasil dibatalkan.');
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect('/member/reservations');
    }
}
