<?php
declare(strict_types=1);

namespace App\Services;

final class BarcodeService
{
    private static array $code39 = [
        '0' => '000110100', '1' => '100100001', '2' => '001100001', '3' => '101100000',
        '4' => '000110001', '5' => '100110000', '6' => '001110000', '7' => '000100101',
        '8' => '100100100', '9' => '001100100', 'A' => '100001001', 'B' => '001001001',
        'C' => '101001000', 'D' => '000011001', 'E' => '100011000', 'F' => '001011000',
        'G' => '000001101', 'H' => '100001100', 'I' => '001001100', 'J' => '000011100',
        'K' => '100000011', 'L' => '001000011', 'M' => '101000010', 'N' => '000010011',
        'O' => '100010010', 'P' => '001010010', 'Q' => '000000111', 'R' => '100000110',
        'S' => '001000110', 'T' => '000010110', 'U' => '110000001', 'V' => '011000001',
        'W' => '111000000', 'X' => '010010001', 'Y' => '110010000', 'Z' => '011010000',
        '-' => '010000101', '.' => '110000100', ' ' => '011000100', '*' => '010010100',
        '$' => '010101000', '/' => '010100010', '+' => '010001010', '%' => '000101010',
    ];

    public function generateSvg(string $code, int $barHeight = 50, int $narrowWidth = 2, int $wideWidth = 5): string
    {
        $clean = strtoupper(trim($code));
        $clean = preg_replace('/[^0-9A-Z\-\.\ \$\/\+\%]/', '', $clean);
        if ($clean === '') {
            $clean = 'ITEM';
        }

        $formatted = "*{$clean}*";
        $rectangles = [];
        $x = 10;
        $gap = $narrowWidth;

        $chars = str_split($formatted);
        foreach ($chars as $char) {
            $pattern = self::$code39[$char] ?? self::$code39['-'];
            $bits = str_split($pattern);

            for ($i = 0; $i < 9; $i++) {
                $isBar = ($i % 2 === 0);
                $isWide = ($bits[$i] === '1');
                $width = $isWide ? $wideWidth : $narrowWidth;

                if ($isBar) {
                    $rectangles[] = sprintf(
                        '<rect x="%d" y="10" width="%d" height="%d" fill="#000000" />',
                        $x,
                        $width,
                        $barHeight
                    );
                }

                $x += $width;
            }

            $x += $gap;
        }

        $totalWidth = $x + 10;
        $totalHeight = $barHeight + 35;
        $textX = (int)($totalWidth / 2);
        $textY = $barHeight + 26;

        $svg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d" style="background-color:#ffffff; font-family:monospace; display:inline-block;">',
            $totalWidth,
            $totalHeight,
            $totalWidth,
            $totalHeight
        );

        $svg .= implode('', $rectangles);
        $svg .= sprintf(
            '<text x="%d" y="%d" text-anchor="middle" font-size="12" font-weight="600" fill="#000000" letter-spacing="2">%s</text>',
            $textX,
            $textY,
            htmlspecialchars($clean, ENT_QUOTES, 'UTF-8')
        );
        $svg .= '</svg>';

        return $svg;
    }

    public function generateCallNumber(string $ddc, string $author, string $title): string
    {
        $ddcClean = trim($ddc);
        $authorAuthorCutter = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $author), 0, 3));
        $titleInitial = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $title), 0, 1));

        return "{$ddcClean} {$authorAuthorCutter} {$titleInitial}";
    }
}
