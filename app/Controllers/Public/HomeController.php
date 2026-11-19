<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\BibliographicItemModel;
use Throwable;

final class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Session::getUser();
        $greetingName = 'Sarah';
        if (!empty($user['full_name'])) {
            $nameParts = explode(' ', trim((string)$user['full_name']));
            $greetingName = $nameParts[0] !== '' ? $nameParts[0] : 'Sarah';
        }

        $totalTitles = 1200;
        $totalCopies = 4820;

        try {
            $biblioModel = new BibliographicItemModel();
            $dbTitles = $biblioModel->countTotal();
            if ($dbTitles > 0) {
                $totalTitles = $dbTitles;
            }
            $dbCopies = $biblioModel->countAvailableCopies();
            if ($dbCopies > 0) {
                $totalCopies = $dbCopies;
            }
        } catch (Throwable $e) {
            $totalTitles = 1200;
            $totalCopies = 4820;
        }

        $currentlyReading = [
            'title' => 'Filosofi Teras',
            'author' => 'Henry Manampiring',
            'slug' => 'filosofi-teras',
            'status_label' => 'Sedang Dibaca',
            'last_read' => 'Dibaca 2 jam yang lalu',
            'progress_percent' => 64,
            'progress_text' => '64% (Hal 178 dari 278)',
            'reader_url' => '/reader/filosofi-teras',
            'catalog_url' => '/catalog/filosofi-teras',
        ];

        $curatedWeeklyBooks = [
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'slug' => 'atomic-habits',
                'category' => 'Pengembangan Diri',
                'device_type' => 'tablet',
                'status' => 'Tersedia Digital',
            ],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'slug' => 'bumi-manusia',
                'category' => 'Sastra Klasik',
                'device_type' => 'book',
                'status' => 'Tersedia Digital',
            ],
            [
                'title' => 'Laut Bercerita',
                'author' => 'Leila S. Chudori',
                'slug' => 'laut-bercerita',
                'category' => 'Fiksi Indonesia',
                'device_type' => 'laptop',
                'status' => 'Tersedia Digital',
            ],
            [
                'title' => 'Kecerdasan Emosional',
                'author' => 'Daniel Goleman',
                'slug' => 'kecerdasan-emosional',
                'category' => 'Psikologi',
                'device_type' => 'phone',
                'status' => 'Tersedia Digital',
            ],
        ];

        $popularBooks = [
            [
                'title' => 'Sapiens: Riwayat Singkat Umat Manusia',
                'author' => 'Yuval Noah Harari',
                'slug' => 'sapiens',
                'rating' => '4.9',
                'readers_count' => '1.4k pembaca',
            ],
            [
                'title' => 'Gadis Kretek',
                'author' => 'Ratih Kumala',
                'slug' => 'gadis-kretek',
                'rating' => '4.8',
                'readers_count' => '980 pembaca',
            ],
            [
                'title' => 'The Psychology of Money',
                'author' => 'Morgan Housel',
                'slug' => 'the-psychology-of-money',
                'rating' => '4.9',
                'readers_count' => '2.1k pembaca',
            ],
            [
                'title' => 'Cantik Itu Luka',
                'author' => 'Eka Kurniawan',
                'slug' => 'cantik-itu-luka',
                'rating' => '4.7',
                'readers_count' => '1.1k pembaca',
            ],
        ];

        return $this->render('pages/public/home', [
            'pageTitle' => 'RuangBaca - Perpustakaan Digital yang Ramah & Tenang',
            'user' => $user,
            'greetingName' => $greetingName,
            'currentlyReading' => $currentlyReading,
            'curatedWeeklyBooks' => $curatedWeeklyBooks,
            'popularBooks' => $popularBooks,
            'totalTitles' => $totalTitles,
            'totalCopies' => $totalCopies,
        ]);
    }
}
