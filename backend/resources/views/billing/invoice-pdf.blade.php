<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $pdfTitle }} {{ $invoice->invoice_number }}</title>
    @include('billing.partials.invoice-print-styles')
    <style>
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
<div class="invoice-print-root">
@include('billing.partials.invoice-print-body')
</div>
</body>
</html>
