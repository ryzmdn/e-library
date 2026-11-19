<?php
declare(strict_types=1);

namespace App\Controllers\Member;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\LoanTransactionModel;
use App\Models\ReadingProgressModel;
use Throwable;

final class ReadingHistoryMemberController extends Controller
{
    private ?ReadingProgressModel $progressModel;
    private ?LoanTransactionModel $loanModel;

    public function __construct()
    {
        try {
            $this->progressModel = new ReadingProgressModel();
            $this->loanModel = new LoanTransactionModel();
        } catch (Throwable $e) {
            $this->progressModel = null;
            $this->loanModel = null;
        }
    }

    public function index(Request $request): Response
    {
        $user = Session::getUser();
        $greetingName = 'Sarah';
        if (!empty($user['full_name'])) {
            $parts = explode(' ', trim((string)$user['full_name']));
            $greetingName = $parts[0] !== '' ? $parts[0] : 'Sarah';
        }

        $genreFilter = strtolower(trim((string)$request->input('genre', 'all')));
        $yearFilter = (int)$request->input('year', 2024);

        $metrics = [
            'active_books' => 2,
            'completed_books' => 14,
            'reading_hours' => 38,
            'active_sub' => 'Target mingguan terpenuhi',
            'completed_sub' => '+3 buku lebih banyak dari 2023',
            'hours_sub' => 'Rata-rata 42 menit per hari',
        ];

        $readingBooks = [
            [
                'slug' => 'filosofi-teras',
                'title' => 'Filosofi Teras',
                'author' => 'Henry Manampiring',
                'category' => 'Filsafat Populer & Psikologi',
                'status_dot' => 'Aktif',
                'status_color' => '#10B981',
                'last_read' => 'Hari ini, 09:30 WIB',
                'progress_percent' => 64,
                'progress_text' => '64% (Hal. 178 / 278)',
                'remaining_est' => 'Sisa est. ~45 mnt',
                'action_url' => '/reader/filosofi-teras',
            ],
            [
                'slug' => 'laut-bercerita',
                'title' => 'Laut Bercerita',
                'author' => 'Leila S. Chudori',
                'category' => 'Fiksi Sejarah & Sastra',
                'status_dot' => 'Tersimpan',
                'status_color' => '#64748B',
                'last_read' => 'Kemarin, 21:15 WIB',
                'progress_percent' => 32,
                'progress_text' => '32% (Hal. 122 / 380)',
                'remaining_est' => 'Sisa est. ~2 jam',
                'action_url' => '/reader/laut-bercerita',
            ],
        ];

        $allArchiveBooks = [
            [
                'slug' => 'atomic-habits',
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'details' => '320 Halaman • Non-Fiksi',
                'finished_date' => '12 Oktober 2024',
                'duration' => 'Durasi: 9 Hari',
                'rating' => 5,
                'note' => 'Luar biasa inspiratif',
                'genre' => 'non-fiksi',
            ],
            [
                'slug' => 'the-psychology-of-money',
                'title' => 'The Psychology of Money',
                'author' => 'Morgan Housel',
                'details' => '262 Halaman • Keuangan',
                'finished_date' => '28 September 2024',
                'duration' => 'Durasi: 6 Hari',
                'rating' => 5,
                'note' => 'Wajib baca berulang',
                'genre' => 'non-fiksi',
            ],
            [
                'slug' => 'bumi-manusia',
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'details' => '535 Halaman • Sastra Klasik',
                'finished_date' => '14 September 2024',
                'duration' => 'Durasi: 14 Hari',
                'rating' => 5,
                'note' => 'Karya mahakarya abadi',
                'genre' => 'fiksi',
            ],
            [
                'slug' => 'cantik-itu-luka',
                'title' => 'Cantik Itu Luka',
                'author' => 'Eka Kurniawan',
                'details' => '492 Halaman • Realisme Magis',
                'finished_date' => '22 Agustus 2024',
                'duration' => 'Durasi: 11 Hari',
                'rating' => 5,
                'note' => 'Sangat memikat & kelam',
                'genre' => 'fiksi',
            ],
        ];

        $filteredArchive = [];
        foreach ($allArchiveBooks as $book) {
            if ($genreFilter !== 'all' && $book['genre'] !== $genreFilter) {
                continue;
            }
            $filteredArchive[] = $book;
        }

        $streak = [
            'days' => 6,
            'label' => '6 Hari Beruntun',
            'sub' => 'Rekor terbaik bulan ini',
        ];

        return $this->render('pages/member/history/index', [
            'pageTitle' => 'Jurnal & Riwayat Membaca - RuangBaca',
            'greetingName' => $greetingName,
            'metrics' => $metrics,
            'readingBooks' => $readingBooks,
            'archiveBooks' => $filteredArchive,
            'genreFilter' => $genreFilter,
            'yearFilter' => $yearFilter,
            'streak' => $streak,
            'totalArchiveCount' => 14,
        ]);
    }
}
