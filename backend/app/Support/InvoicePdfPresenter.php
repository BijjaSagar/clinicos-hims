<?php

namespace App\Support;

use App\Models\Clinic;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Helpers for printable invoice PDFs (DomPDF): logo embedding, copy, amount in words.
 */
class InvoicePdfPresenter
{
    /**
     * @return array{0: string, 1: string} [title, subtitle]
     */
    public static function titles(string $format): array
    {
        return $format === 'gst'
            ? ['TAX INVOICE', 'GST invoice as per GST rules (intra-state supply)']
            : ['INVOICE', 'Bill / receipt — tax break-up not shown (non-GST or consolidated display)'];
    }

    /**
     * Multi-line letterhead: settings override, else clinic address fields.
     */
    public static function letterheadLines(Clinic $clinic): array
    {
        $settings = $clinic->settings ?? [];
        $raw = trim((string) ($settings['invoice_letterhead'] ?? ''));
        if ($raw !== '') {
            return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw))));
        }

        $lines = [];
        $lines[] = $clinic->name ?? 'Clinic';
        if ($clinic->address_line1) {
            $lines[] = $clinic->address_line1;
        }
        if ($clinic->address_line2) {
            $lines[] = $clinic->address_line2;
        }
        $cityLine = trim(implode(', ', array_filter([
            $clinic->city,
            $clinic->state,
            $clinic->pincode,
        ])));
        if ($cityLine !== '') {
            $lines[] = $cityLine;
        }
        if ($clinic->phone) {
            $lines[] = 'Phone: '.$clinic->phone;
        }
        if ($clinic->email) {
            $lines[] = 'Email: '.$clinic->email;
        }

        return $lines;
    }

    /**
     * Data-URI for DomPDF <img src="..."> (avoids remote file issues).
     */
    public static function logoDataUri(?Clinic $clinic): ?string
    {
        if (!$clinic) {
            return null;
        }
        $path = data_get($clinic->settings, 'invoice_logo_path');
        if (!$path || !is_string($path)) {
            if (!empty($clinic->logo_url) && str_starts_with($clinic->logo_url, 'http')) {
                Log::info('InvoicePdfPresenter: logo_url is remote; PDF may skip image', ['url' => $clinic->logo_url]);

                return null;
            }

            return null;
        }

        if (!Storage::disk('public')->exists($path)) {
            Log::warning('InvoicePdfPresenter: invoice logo file missing', ['path' => $path]);

            return null;
        }

        $full = Storage::disk('public')->path($path);
        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/png',
        };

        $bin = @file_get_contents($full);
        if ($bin === false || $bin === '') {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode($bin);
    }

    /**
     * Indian rupees in words (integer paise rounded; sufficient for invoices).
     */
    public static function amountInWords(float $amount): string
    {
        $paise = (int) round($amount * 100);
        $rupees = intdiv($paise, 100);
        $p = $paise % 100;

        $rWords = $rupees === 0 ? 'zero' : self::numToWords($rupees);
        $out = ucfirst($rWords).' Rupees';
        if ($p > 0) {
            $out .= ' and '.self::numToWords($p).' Paise';
        }
        $out .= ' Only';

        return $out;
    }

    private static function numToWords(int $n): string
    {
        if ($n === 0) {
            return 'zero';
        }
        $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        if ($n < 20) {
            return $ones[$n];
        }
        if ($n < 100) {
            return trim($tens[intdiv($n, 10)].($n % 10 ? ' '.$ones[$n % 10] : ''));
        }
        if ($n < 1000) {
            return trim($ones[intdiv($n, 100)].' hundred'.($n % 100 ? ' and '.self::numToWords($n % 100) : ''));
        }
        if ($n < 100000) {
            $th = intdiv($n, 1000);
            $rem = $n % 1000;

            return trim(self::numToWords($th).' thousand'.($rem ? ' '.self::numToWords($rem) : ''));
        }
        if ($n < 10000000) {
            $l = intdiv($n, 100000);
            $rem = $n % 100000;

            return trim(self::numToWords($l).' lakh'.($rem ? ' '.self::numToWords($rem) : ''));
        }
        $c = intdiv($n, 10000000);
        $rem = $n % 10000000;

        return trim(self::numToWords($c).' crore'.($rem ? ' '.self::numToWords($rem) : ''));
    }
}
