<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Environment;
use App\Core\Request;
use App\Core\Response;

final class DigitalStreamService
{
    private string $storageDir;
    private string $secretKey;

    public function __construct(?string $storageDir = null, ?string $secretKey = null)
    {
        $this->storageDir = $storageDir ?? (dirname(__DIR__, 2) . '/storage/digital_books');
        $this->secretKey = $secretKey ?? Environment::get('APP_KEY', 'e-library-secure-hmac-salt-key-2026');
    }

    public function generateStreamToken(int $userId, int $digitalAssetId, int $ttlSeconds = 14400): string
    {
        $expiresAt = time() + $ttlSeconds;
        $payload = "{$userId}:{$digitalAssetId}:{$expiresAt}";
        $signature = hash_hmac('sha256', $payload, $this->secretKey);
        $raw = "{$payload}:{$signature}";

        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    public function verifyStreamToken(string $token, int $userId, int $digitalAssetId): bool
    {
        $decoded = base64_decode(strtr($token, '-_', '+/'), true);
        if ($decoded === false) {
            return false;
        }

        $parts = explode(':', $decoded);
        if (count($parts) !== 4) {
            return false;
        }

        [$tokenUserId, $tokenAssetId, $expiresAt, $signature] = $parts;
        if ((int)$tokenUserId !== $userId || (int)$tokenAssetId !== $digitalAssetId) {
            return false;
        }

        if (time() > (int)$expiresAt) {
            return false;
        }

        $expectedPayload = "{$tokenUserId}:{$tokenAssetId}:{$expiresAt}";
        $expectedSignature = hash_hmac('sha256', $expectedPayload, $this->secretKey);

        return hash_equals($expectedSignature, $signature);
    }

    public function resolveFilePath(string $hash): ?string
    {
        $candidates = [
            $this->storageDir . '/' . $hash,
            $this->storageDir . '/' . $hash . '.pdf',
            $this->storageDir . '/' . $hash . '.epub',
        ];

        foreach ($candidates as $file) {
            if (file_exists($file) && is_readable($file)) {
                return $file;
            }
        }

        return null;
    }

    public function streamFile(array $asset, Request $request, string $displayTitle = 'Dokumen Digital'): Response
    {
        $hash = (string)($asset['storage_filename_hash'] ?? '');
        $format = strtoupper((string)($asset['file_format'] ?? 'PDF'));
        $filePath = $this->resolveFilePath($hash);

        $contentType = ($format === 'EPUB') ? 'application/epub+zip' : 'application/pdf';

        if ($filePath !== null) {
            $fileSize = (int)filesize($filePath);
            $rangeHeader = $request->getHeader('range');

            if ($rangeHeader !== null && preg_match('/bytes=(\d+)-(\d*)/i', $rangeHeader, $matches)) {
                $start = (int)$matches[1];
                $end = ($matches[2] !== '') ? (int)$matches[2] : $fileSize - 1;

                if ($start >= $fileSize || $end >= $fileSize || $start > $end) {
                    return new Response('', 416, [
                        'Content-Range' => 'bytes *' . '/' . $fileSize,
                    ]);
                }

                $length = $end - $start + 1;
                $fp = fopen($filePath, 'rb');
                fseek($fp, $start);
                $content = fread($fp, $length);
                fclose($fp);

                return new Response($content !== false ? $content : '', 206, [
                    'Content-Type' => $contentType,
                    'Content-Length' => (string)$length,
                    'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
                    'Accept-Ranges' => 'bytes',
                    'Content-Disposition' => 'inline; filename="reader-document.pdf"',
                    'X-Content-Type-Options' => 'nosniff',
                    'Cache-Control' => 'private, no-transform, max-age=3600',
                ]);
            }

            $content = file_get_contents($filePath);
            return new Response($content !== false ? $content : '', 200, [
                'Content-Type' => $contentType,
                'Content-Length' => (string)$fileSize,
                'Accept-Ranges' => 'bytes',
                'Content-Disposition' => 'inline; filename="reader-document.pdf"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-transform, max-age=3600',
            ]);
        }

        $fallbackPdf = $this->generateFallbackPdf($displayTitle);
        $size = strlen($fallbackPdf);

        return new Response($fallbackPdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => (string)$size,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="reader-document.pdf"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-transform, max-age=3600',
        ]);
    }

    public function generateFallbackPdf(string $title): string
    {
        $sanitizedTitle = preg_replace('/[^\x20-\x7E]/', '', $title);
        $escapedTitle = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $sanitizedTitle);

        $obj1 = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $obj2 = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $obj3 = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";

        $streamContent = "BT\n/F1 20 Tf\n50 720 Td\n({$escapedTitle}) Tj\nET\n";
        $streamContent .= "BT\n/F1 12 Tf\n50 680 Td\n(Buku Digital Terproteksi - E-Library System) Tj\nET\n";
        $streamContent .= "BT\n/F1 10 Tf\n50 650 Td\n(Salinan digital ini disediakan secara resmi untuk kepentingan studi dan riset.) Tj\nET\n";
        $streamLength = strlen($streamContent);

        $obj4 = "4 0 obj\n<< /Length {$streamLength} >>\nstream\n{$streamContent}endstream\nendobj\n";
        $obj5 = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $header = "%PDF-1.4\n";
        $offset1 = strlen($header);
        $offset2 = $offset1 + strlen($obj1);
        $offset3 = $offset2 + strlen($obj2);
        $offset4 = $offset3 + strlen($obj3);
        $offset5 = $offset4 + strlen($obj4);
        $xrefOffset = $offset5 + strlen($obj5);

        $body = $header . $obj1 . $obj2 . $obj3 . $obj4 . $obj5;

        $xref = "xref\n0 6\n";
        $xref .= "0000000000 65535 f \n";
        $xref .= sprintf("%010d 00000 n \n", $offset1);
        $xref .= sprintf("%010d 00000 n \n", $offset2);
        $xref .= sprintf("%010d 00000 n \n", $offset3);
        $xref .= sprintf("%010d 00000 n \n", $offset4);
        $xref .= sprintf("%010d 00000 n \n", $offset5);

        $trailer = "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $body . $xref . $trailer;
    }
}
