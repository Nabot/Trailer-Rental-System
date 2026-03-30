<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Deposit Refund Receipt #{{ $depositRefund->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 20px; }
        .amount { font-size: 22px; font-weight: bold; color: #b45309; text-align: right; }
        .box { border: 1px solid #ddd; padding: 12px; margin: 12px 0; }
        .row { margin: 5px 0; }
        .label { font-weight: bold; display: inline-block; min-width: 140px; }
        .footer { margin-top: 24px; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">Deposit Refund Receipt</h2>
        <p style="margin: 4px 0;">Receipt #: {{ str_pad($depositRefund->id, 6, '0', STR_PAD_LEFT) }}</p>
        <div class="amount">-N${{ number_format($depositRefund->amount, 2) }}</div>
    </div>

    <div class="box">
        <div class="row"><span class="label">Company:</span> {{ $companyName }}</div>
        @if($companyAddress)<div class="row"><span class="label">Address:</span> {{ $companyAddress }}</div>@endif
        @if($companyPhone)<div class="row"><span class="label">Phone:</span> {{ $companyPhone }}</div>@endif
        @if($companyEmail)<div class="row"><span class="label">Email:</span> {{ $companyEmail }}</div>@endif
    </div>

    <div class="box">
        <div class="row"><span class="label">Customer:</span> {{ $depositRefund->customer->name }}</div>
        <div class="row"><span class="label">Booking #:</span> {{ $depositRefund->booking->booking_number }}</div>
        @if($depositRefund->invoice)<div class="row"><span class="label">Invoice #:</span> {{ $depositRefund->invoice->invoice_number }}</div>@endif
        <div class="row"><span class="label">Refund Date:</span> {{ $depositRefund->refund_date->format('M d, Y') }}</div>
        <div class="row"><span class="label">Method:</span> {{ strtoupper($depositRefund->method) }}</div>
        <div class="row"><span class="label">Reference:</span> {{ $depositRefund->reference_number ?: '-' }}</div>
        <div class="row"><span class="label">Status:</span> {{ ucfirst($depositRefund->status) }}</div>
        <div class="row"><span class="label">Processed By:</span> {{ $depositRefund->processedBy?->name ?? '-' }}</div>
    </div>

    @if($depositRefund->notes)
    <div class="box">
        <strong>Notes:</strong> {{ $depositRefund->notes }}
    </div>
    @endif

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
