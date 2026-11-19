<?php
declare(strict_types=1);

namespace App\Controllers\Member;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\UserFavoriteBookModel;
use Throwable;

final class FavoriteMemberController extends Controller
{
    private ?UserFavoriteBookModel $favoriteModel;

    public function __construct()
    {
        try {
            $this->favoriteModel = new UserFavoriteBookModel();
        } catch (Throwable $e) {
            $this->favoriteModel = null;
        }
    }

    public function index(Request $request): Response
    {
        $user = Session::getUser();
        $userId = (int)($user['id'] ?? 4);

        $statusFilter = strtolower(trim((string)$request->input('status', 'all')));
        $sort = trim((string)$request->input('sort', 'latest'));

        $items = [];
        $totalCount = 0;
        $unreadCount = 0;
        $readingCount = 0;
        $completedCount = 0;

        try {
            if ($this->favoriteModel !== null) {
                $totalCount = $this->favoriteModel->countByUser($userId, 'ALL');
                $unreadCount = $this->favoriteModel->countByUser($userId, 'UNREAD');
                $readingCount = $this->favoriteModel->countByUser($userId, 'READING');
                $completedCount = $this->favoriteModel->countByUser($userId, 'COMPLETED');

                $dbStatus = match ($statusFilter) {
                    'unread' => 'UNREAD',
                    'reading' => 'READING',
                    'completed' => 'COMPLETED',
                    default => 'ALL',
                };

                $items = $this->favoriteModel->getUserFavorites($userId, $dbStatus, $sort);
            }
        } catch (Throwable $e) {
            $items = [];
        }

        if (empty($items) && $totalCount === 0) {
            $mockFavorites = [
                [
                    'id' => 1,
                    'biblio_id' => 9,
                    'slug' => 'laut-bercerita',
                    'title' => 'Laut Bercerita',
                    'authors_text' => 'Leila S. Chudori',
                    'rating_avg' => 4.9,
                    'rating_count_text' => '1.4k ulasan',
                    'badge_status' => 'Tersedia',
                    'badge_color' => 'success',
                    'personal_note' => 'Rekomendasi dari teman klub buku',
                    'footer_info' => 'E-Book',
                    'footer_icon' => 'book',
                    'action_label' => 'Baca Sekarang',
                    'action_url' => '/reader/laut-bercerita',
                    'action_style' => 'primary',
                    'reading_status' => 'UNREAD',
                ],
                [
                    'id' => 2,
                    'biblio_id' => 7,
                    'slug' => 'atomic-habits',
                    'title' => 'Atomic Habits',
                    'authors_text' => 'James Clear',
                    'rating_avg' => 4.9,
                    'rating_count_text' => '3.8k ulasan',
                    'badge_status' => 'Antrean',
                    'badge_color' => 'warning',
                    'personal_note' => 'Perlu re-read bab habit stacking',
                    'footer_info' => 'Kembali dlm 2 hari',
                    'footer_icon' => 'clock',
                    'action_label' => 'Reservasi',
                    'action_url' => '/catalog/atomic-habits',
                    'action_style' => 'outline',
                    'reading_status' => 'UNREAD',
                ],
                [
                    'id' => 3,
                    'biblio_id' => 6,
                    'slug' => 'filosofi-teras',
                    'title' => 'Filosofi Teras',
                    'authors_text' => 'Henry Manampiring',
                    'rating_avg' => 4.9,
                    'rating_count_text' => 'Hal 192 dari 300',
                    'badge_status' => '64% Dibaca',
                    'badge_color' => 'primary',
                    'personal_note' => 'Buku pegangan ketenangan hidup',
                    'footer_info' => 'Bab V',
                    'footer_icon' => 'bookmark',
                    'action_label' => 'Lanjutkan',
                    'action_url' => '/reader/filosofi-teras',
                    'action_style' => 'primary',
                    'reading_status' => 'READING',
                ],
                [
                    'id' => 4,
                    'biblio_id' => 11,
                    'slug' => 'sapiens',
                    'title' => 'Sapiens',
                    'authors_text' => 'Yuval Noah Harari',
                    'rating_avg' => 4.7,
                    'rating_count_text' => '4.2k ulasan',
                    'badge_status' => 'Tersedia',
                    'badge_color' => 'success',
                    'personal_note' => 'Target baca bulan depan',
                    'footer_info' => 'E-Book & Audio',
                    'footer_icon' => 'headphones',
                    'action_label' => 'Pinjam Buku',
                    'action_url' => '/catalog/sapiens',
                    'action_style' => 'secondary',
                    'reading_status' => 'UNREAD',
                ],
                [
                    'id' => 5,
                    'biblio_id' => 12,
                    'slug' => 'gadis-kretek',
                    'title' => 'Gadis Kretek',
                    'authors_text' => 'Ratih Kumala',
                    'rating_avg' => 4.6,
                    'rating_count_text' => '980 ulasan',
                    'badge_status' => 'Tersedia',
                    'badge_color' => 'success',
                    'personal_note' => 'Fiksi sejarah aroma cengkeh',
                    'footer_info' => 'Selesai Dibaca',
                    'footer_icon' => 'check',
                    'action_label' => 'Baca Lagi',
                    'action_url' => '/reader/gadis-kretek',
                    'action_style' => 'outline',
                    'reading_status' => 'COMPLETED',
                ],
                [
                    'id' => 6,
                    'biblio_id' => 13,
                    'slug' => 'the-psychology-of-money',
                    'title' => 'The Psychology of Money',
                    'authors_text' => 'Morgan Housel',
                    'rating_avg' => 4.9,
                    'rating_count_text' => '2.9k ulasan',
                    'badge_status' => 'Tersedia',
                    'badge_color' => 'success',
                    'personal_note' => 'Bagus untuk belajar mindset finansial',
                    'footer_info' => 'E-Book',
                    'footer_icon' => 'book',
                    'action_label' => 'Pinjam Buku',
                    'action_url' => '/catalog/the-psychology-of-money',
                    'action_style' => 'secondary',
                    'reading_status' => 'UNREAD',
                ],
            ];

            $totalCount = 8;
            $unreadCount = 5;
            $readingCount = 1;
            $completedCount = 2;

            if ($statusFilter !== 'all') {
                $filtered = [];
                $targetStatus = strtoupper($statusFilter);
                foreach ($mockFavorites as $mf) {
                    if ($mf['reading_status'] === $targetStatus) {
                        $filtered[] = $mf;
                    }
                }
                $items = $filtered;
            } else {
                $items = $mockFavorites;
            }
        }

        $metrics = [
            'total' => $totalCount > 0 ? $totalCount : 8,
            'available' => 5,
            'waiting' => 3,
        ];

        $readingGoal = [
            'year' => 2024,
            'target' => 20,
            'current' => 12,
            'percent' => 60,
            'streak_text' => 'Konsisten membaca 4 hari minggu ini',
            'remaining_text' => 'Tinggal 8 buku lagi untuk memenuhi resolusi tahunanmu!',
        ];

        $topicStats = [
            ['name' => 'Pengembangan Diri', 'percent' => 45, 'color' => '#3E49D7'],
            ['name' => 'Fiksi Sejarah & Sastra', 'percent' => 35, 'color' => '#6366F1'],
            ['name' => 'Finansial & Bisnis', 'percent' => 20, 'color' => '#A5B4FC'],
        ];

        $librarianPick = [
            'title' => 'Kupikir-pikir Senja di Jakarta',
            'author' => 'Mochtar Lubis',
            'quote' => '"Kupikir-pikir Senja di Jakarta" memiliki kemiripan suasana dengan buku fiksi sejarah favoritmu.',
            'slug' => 'bumi-manusia',
        ];

        return $this->render('pages/member/favorites/index', [
            'pageTitle' => 'Koleksi Favorit Saya - RuangBaca',
            'items' => $items,
            'statusFilter' => $statusFilter,
            'sort' => $sort,
            'metrics' => $metrics,
            'counts' => [
                'all' => $totalCount > 0 ? $totalCount : 8,
                'unread' => $unreadCount > 0 ? $unreadCount : 5,
                'reading' => $readingCount > 0 ? $readingCount : 1,
                'completed' => $completedCount > 0 ? $completedCount : 2,
            ],
            'readingGoal' => $readingGoal,
            'topicStats' => $topicStats,
            'librarianPick' => $librarianPick,
        ]);
    }

    public function toggle(Request $request): Response
    {
        $user = Session::getUser();
        if ($user === null) {
            return Response::json(['success' => false, 'message' => 'Silakan masuk terlebih dahulu.'], 401);
        }

        $biblioId = (int)$request->input('biblio_id', 0);
        if ($biblioId <= 0) {
            return Response::json(['success' => false, 'message' => 'Parameter tidak valid.'], 400);
        }

        $userId = (int)$user['id'];
        $isFav = false;

        try {
            if ($this->favoriteModel !== null) {
                $isFav = $this->favoriteModel->toggleFavorite($userId, $biblioId);
                $total = $this->favoriteModel->countByUser($userId, 'ALL');
                return Response::json([
                    'success' => true,
                    'is_favorite' => $isFav,
                    'total_favorites' => $total,
                ]);
            }
        } catch (Throwable $e) {
            return Response::json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }

        return Response::json([
            'success' => true,
            'is_favorite' => true,
            'total_favorites' => 1,
        ]);
    }

    public function updateNote(Request $request): Response
    {
        $user = Session::getUser();
        if ($user === null) {
            return Response::json(['success' => false, 'message' => 'Akses ditolak.'], 401);
        }

        $biblioId = (int)$request->input('biblio_id', 0);
        $note = trim((string)$request->input('personal_note', ''));

        try {
            if ($this->favoriteModel !== null) {
                $this->favoriteModel->updateNote((int)$user['id'], $biblioId, $note);
                return Response::json(['success' => true]);
            }
        } catch (Throwable $e) {
            return Response::json(['success' => false, 'message' => 'Gagal memperbarui catatan.'], 500);
        }

        return Response::json(['success' => true]);
    }

    public function remove(Request $request, string $id): Response
    {
        $user = Session::getUser();
        if ($user === null) {
            return Response::redirect('/login');
        }

        $biblioId = (int)$id;
        try {
            if ($this->favoriteModel !== null) {
                $this->favoriteModel->removeFavorite((int)$user['id'], $biblioId);
            }
        } catch (Throwable $e) {
        }

        return Response::redirect('/member/favorites');
    }
}
