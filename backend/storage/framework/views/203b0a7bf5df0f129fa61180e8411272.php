<style>
    .invoice-print-root {
        font-family: DejaVu Sans, ui-sans-serif, system-ui, sans-serif;
        font-size: 10.5px;
        color: #1e293b;
        line-height: 1.45;
    }
    .invoice-print-root * { box-sizing: border-box; }
    .invoice-print-root .sheet { padding: 22px 26px 32px; }
    .invoice-print-root .top-rule {
        height: 4px;
        background: linear-gradient(90deg, #0f172a 0%, #0d9488 55%, #0ea5e9 100%);
        margin: -22px -26px 18px;
    }
    .invoice-print-root .head-row { width: 100%; margin-bottom: 14px; }
    .invoice-print-root .head-row td { vertical-align: top; }
    .invoice-print-root .logo {
        max-height: 56px;
        max-width: 180px;
        object-fit: contain;
    }
    .invoice-print-root .facility-name {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .invoice-print-root .tagline {
        font-size: 9px;
        color: #64748b;
        margin-top: 2px;
    }
    .invoice-print-root .letterhead {
        font-size: 9.5px;
        color: #475569;
        margin-top: 8px;
        line-height: 1.5;
    }
    .invoice-print-root .meta-box {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px 12px;
        background: #f8fafc;
    }
    .invoice-print-root .doc-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        text-align: right;
        letter-spacing: 0.06em;
    }
    .invoice-print-root .doc-sub {
        font-size: 8.5px;
        color: #64748b;
        text-align: right;
        margin-top: 3px;
        max-width: 220px;
        margin-left: auto;
    }
    .invoice-print-root .inv-num {
        font-size: 11px;
        font-weight: 700;
        margin-top: 8px;
        text-align: right;
        color: #334155;
    }
    .invoice-print-root .gst-strip {
        margin-top: 10px;
        padding: 8px 10px;
        background: #ecfeff;
        border: 1px solid #a5f3fc;
        font-size: 9px;
        border-radius: 4px;
    }
    .invoice-print-root .section-h {
        font-size: 8px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 14px 0 6px;
    }
    .invoice-print-root .party {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px 12px;
        min-height: 72px;
    }
    .invoice-print-root .party-name { font-weight: 700; font-size: 11px; color: #0f172a; }
    .invoice-print-root .party-sm { font-size: 9px; color: #64748b; margin-top: 4px; }
    .invoice-print-root table.items {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }
    .invoice-print-root table.items th {
        background: #f1f5f9;
        color: #475569;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 8px 6px;
        border: 1px solid #e2e8f0;
        text-align: left;
    }
    .invoice-print-root table.items th.r, .invoice-print-root table.items td.r { text-align: right; }
    .invoice-print-root table.items th.c { text-align: center; }
    .invoice-print-root table.items td {
        padding: 8px 6px;
        border: 1px solid #e2e8f0;
        font-size: 9.5px;
        vertical-align: top;
    }
    .invoice-print-root table.items tr:nth-child(even) td { background: #fafafa; }
    .invoice-print-root .sum-wrap { width: 100%; margin-top: 14px; }
    .invoice-print-root .sum-wrap td { vertical-align: top; }
    .invoice-print-root .words {
        font-size: 9px;
        color: #334155;
        border: 1px dashed #cbd5e1;
        padding: 10px;
        border-radius: 6px;
        line-height: 1.5;
    }
    .invoice-print-root .sum-table { width: 280px; margin-left: auto; border-collapse: collapse; }
    .invoice-print-root .sum-table td { padding: 5px 10px; font-size: 10px; border: none; }
    .invoice-print-root .sum-table .lbl { color: #64748b; }
    .invoice-print-root .sum-table .val { text-align: right; font-weight: 600; color: #0f172a; }
    .invoice-print-root .sum-table .grand td {
        border-top: 2px solid #0f172a;
        padding-top: 10px;
        font-size: 13px;
        font-weight: 800;
        color: #0d9488;
    }
    .invoice-print-root .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .invoice-print-root .badge-paid { background: #d1fae5; color: #065f46; }
    .invoice-print-root .badge-pend { background: #fee2e2; color: #991b1b; }
    .invoice-print-root .badge-part { background: #fef3c7; color: #92400e; }
    .invoice-print-root .foot {
        margin-top: 22px;
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
        font-size: 8.5px;
        color: #94a3b8;
        text-align: center;
    }
    .invoice-print-root .foot strong { color: #64748b; }
</style>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/billing/partials/invoice-print-styles.blade.php ENDPATH**/ ?>