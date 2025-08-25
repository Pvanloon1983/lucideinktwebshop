<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

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
        // Generate PDF from the Blade view
        $pdf = Pdf::loadView('invoices.order', ['order' => $this->order])->output();

        // Store the PDF in storage/app/invoices
        $filename = 'factuur_' . $this->order->id . '.pdf';
        $dir = storage_path('app/invoices');
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }
        $path = $dir . '/' . $filename;
        file_put_contents($path, $pdf);

        return $this->subject('Uw bestelling bij Lucide Inkt')
            ->view('emails.orderpaid')
            ->with(['order' => $this->order])
            ->attach($path, [
                'as' => 'factuur.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
