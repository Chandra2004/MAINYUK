<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Generate booking invoice PDF and return the PDF instance.
     */
    public function generateInvoice(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        $booking->load('user', 'court');

        $data = [
            'booking'      => $booking,
            'user'         => $booking->user,
            'court'        => $booking->court,
            'generated_at' => now()->format('d M Y H:i'),
            'app_name'     => config('app.name', 'MainYuk'),
            'app_url'      => config('app.url'),
        ];

        return Pdf::loadView('pdf.invoice', $data)
                  ->setPaper('a4', 'portrait');
    }

    /**
     * Stream PDF to browser.
     */
    public function streamInvoice(Booking $booking)
    {
        $pdf = $this->generateInvoice($booking);
        $filename = "invoice-{$booking->booking_code}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Download PDF.
     */
    public function downloadInvoice(Booking $booking)
    {
        $pdf = $this->generateInvoice($booking);
        $filename = "invoice-{$booking->booking_code}.pdf";

        return $pdf->download($filename);
    }
}
