<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait streamPdf
{
    /**
     * Algemene (PDF) streamer vanaf een storage disk.
     *
     * @param  string                        $relativePath  Relatief pad binnen de disk (geen ..)
     * @param  string                        $downloadName  Naam voor de client (gebruik .pdf indien PDF)
     * @param  callable(mixed):bool|null     $authCallback  Callback krijgt het relativePath (of wrapper) en moet true geven
     * @param  string                        $diskName      Storage disk naam
     * @param  array                         $options       Extra opties: ['inline'=>bool,true/false,'chunk'=>int]
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function streamPdf(string $relativePath, string $downloadName, ?callable $authCallback = null, string $diskName = 'public', array $options = [])
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

        $fileSize   = @filesize($fullPath) ?: null;
        $mime       = $disk->mimeType($relativePath) ?: 'application/pdf';
        $inline     = (bool)($options['inline'] ?? false);
        $chunkSize  = (int)($options['chunk'] ?? 8192);
        if ($chunkSize < 1024) { $chunkSize = 1024; }

        $disposition = ($inline ? 'inline' : 'attachment').'; filename="'.$downloadName.'"';

        $headers = [
            'Content-Type'        => $mime,
            'Cache-Control'       => 'private, no-store, max-age=0, must-revalidate',
            'Pragma'              => 'public',
            'Content-Disposition' => $disposition,
        ];
        if ($fileSize) {
            $headers['Content-Length'] = (string) $fileSize;
        }

        return response()->streamDownload(function () use ($fullPath, $chunkSize) {
            $h = fopen($fullPath, 'rb');
            if ($h === false) { return; }
            try {
                while (!feof($h)) {
                    echo fread($h, $chunkSize);
                    flush();
                }
            } finally { fclose($h); }
        }, $downloadName, $headers);
    }

    /**
     * Backwards-compatible helper specifiek voor Order invoices.
     * Gebruikt streamPdf onder water.
     */
    protected function streamInvoice(Order $order, ?callable $authCallback = null, array $options = [])
    {
        // Wrap callback zodat bestaande closures met (Order $o) blijven werken.
        $wrapped = null;
        if ($authCallback) {
            $wrapped = function () use ($authCallback, $order) {
                return $authCallback($order);
            };
        }
        return $this->streamPdf(
            (string) $order->invoice_pdf_path,
            'factuur_'.$order->id.'.pdf',
            $wrapped,
            'public',
            $options
        );
    }
}
