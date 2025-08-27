<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait streamPdf
{
    /**
     * Algemene PDF streamer vanaf (standaard) de public disk.
     *
     * @param  string                        $relativePath  Relatief pad binnen de gekozen disk (geen .. toegestaan)
     * @param  string                        $downloadName  Bestandsnaam voor de browser
     * @param  callable(mixed):bool|null     $authCallback  Optionele autorisatie (true = doorgaan)
     * @param  string                        $diskName      Storage disk naam (default 'public')
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function streamPdf(string $relativePath, string $downloadName, ?callable $authCallback = null, string $diskName = 'public')
    {
        if ($authCallback && ! $authCallback($relativePath)) {
            abort(403, 'Geen toegang.');
        }

        $relativePath = trim($relativePath);
        if ($relativePath === '') {
            abort(404, 'Bestand niet gevonden.');
        }
        if (str_contains($relativePath, '..')) {
            abort(400, 'Ongeldig pad.');
        }

        $disk = Storage::disk($diskName);
        if (! $disk->exists($relativePath)) {
            Log::warning('PDF stream: file missing', [
                'path' => $relativePath,
                'disk' => $diskName,
            ]);
            abort(404, 'Bestand ontbreekt.');
        }

        $fullPath = method_exists($disk, 'path') ? $disk->path($relativePath) : storage_path('app/'.$diskName.'/'.$relativePath);
        if (! is_file($fullPath) || ! is_readable($fullPath)) {
            Log::error('PDF stream: unreadable', [
                'full_path' => $fullPath,
                'disk'      => $diskName,
            ]);
            abort(500, 'Bestand kan niet worden gelezen.');
        }

        while (ob_get_level() > 0) { @ob_end_clean(); }

        $fileSize = @filesize($fullPath) ?: null;
        $headers = [
            'Content-Type'  => 'application/pdf',
            'Cache-Control' => 'private, no-store, max-age=0, must-revalidate',
            'Pragma'        => 'public',
        ];
        if ($fileSize) {
            $headers['Content-Length'] = (string) $fileSize;
        }

        return response()->streamDownload(function () use ($fullPath) {
            $h = fopen($fullPath, 'rb');
            if ($h === false) { return; }
            try {
                while (!feof($h)) {
                    echo fread($h, 8192);
                    flush();
                }
            } finally { fclose($h); }
        }, $downloadName, $headers);
    }

    /**
     * Backwards-compatible helper specifiek voor Order invoices.
     * Gebruikt streamPdf onder water.
     */
    protected function streamInvoice(Order $order, ?callable $authCallback = null)
    {
        return $this->streamPdf(
            (string) $order->invoice_pdf_path,
            'factuur_'.$order->id.'.pdf',
            $authCallback,
            'public'
        );
    }
}
