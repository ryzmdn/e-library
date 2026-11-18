<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\BibliographicItemModel;
use Throwable;

final class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $books = [];
        $totalTitles = 0;
        $totalCopies = 0;

        try {
            $biblioModel = new BibliographicItemModel();
            $books = $biblioModel->getLatestItems(6);
            $totalTitles = $biblioModel->countTotal();
            $totalCopies = $biblioModel->countAvailableCopies();
        } catch (Throwable $e) {
            $books = [];
        }

        if (empty($books)) {
            $books = [
                [
                    'slug' => 'pengantar-rekayasa-perangkat-lunak',
                    'title' => 'Pengantar Rekayasa Perangkat Lunak',
                    'authors_text' => 'Roger S. Pressman, Bruce R. Maxim',
                    'call_number_base' => '005.1 PRE p',
                    'publication_year' => 2024,
                    'available_physical_copies' => 4,
                    'media_type' => 'HYBRID',
                ],
                [
                    'slug' => 'arsitektur-komputer-modern',
                    'title' => 'Arsitektur Komputer Modern & Desain Sistem',
                    'authors_text' => 'David A. Patterson, John L. Hennessy',
                    'call_number_base' => '004.22 PAT a',
                    'publication_year' => 2023,
                    'available_physical_copies' => 0,
                    'media_type' => 'PHYSICAL_ONLY',
                ],
                [
                    'slug' => 'dasar-dasar-sains-data-terapan',
                    'title' => 'Dasar-Dasar Sains Data Terapan',
                    'authors_text' => 'Hadley Wickham, Garrett Grolemund',
                    'call_number_base' => '005.7 WIC d',
                    'publication_year' => 2024,
                    'available_physical_copies' => 2,
                    'media_type' => 'HYBRID',
                ],
            ];
            $totalTitles = 3;
            $totalCopies = 6;
        }

        return $this->render('pages/public/home', [
            'pageTitle' => 'Beranda Utama',
            'sampleBooks' => $books,
            'totalTitles' => $totalTitles,
            'totalCopies' => $totalCopies,
        ]);
    }
}
