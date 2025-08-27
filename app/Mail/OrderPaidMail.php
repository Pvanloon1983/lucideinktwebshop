<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        // Only read/attach – no DB writes here
        $pathOnDisk = Storage::disk('public')->path($this->order->invoice_pdf_path);

        return $this->subject('Uw bestelling bij Lucide Inkt')
            ->view('emails.orderpaid', ['order' => $this->order])
            ->attach($pathOnDisk, [
                'as' => 'factuur.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
