<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - Payment #{{ $payment->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 10px;
            color: #666;
        }
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .details {
            margin: 20px 0;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .label {
            font-weight: bold;
            width: 40%;
        }
        .value {
            width: 60%;
            text-align: right;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $companyName }}</div>
        @if($companyAddress)
        <div class="company-details">{{ $companyAddress }}</div>
        @endif
        @if($companyPhone)
        <div class="company-details">Phone: {{ $companyPhone }}</div>
        @endif
        @if($companyEmail)
        <div class="company-details">Email: {{ $companyEmail }}</div>
        @endif
    </div>

    <div class="receipt-title">PAYMENT RECEIPT</div>

    <div class="details">
        <div class="details-row">
            <div class="label">Receipt Number:</div>
            <div class="value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="details-row">
            <div class="label">Payment Date:</div>
            <div class="value">{{ $payment->payment_date->format('F d, Y') }}</div>
        </div>
        @if($payment->booking)
        <div class="details-row">
            <div class="label">Booking Number:</div>
            <div class="value">{{ $payment->booking->booking_number }}</div>
        </div>
        @endif
        @if($payment->invoice)
        <div class="details-row">
            <div class="label">Invoice Number:</div>
            <div class="value">{{ $payment->invoice->invoice_number }}</div>
        </div>
        @endif
        @if($customer)
        <div class="details-row">
            <div class="label">Customer:</div>
            <div class="value">{{ $customer->name }}</div>
        </div>
        @endif
        <div class="details-row">
            <div class="label">Payment Method:</div>
            <div class="value">{{ ucfirst($payment->method) }}</div>
        </div>
        @if($payment->reference_number)
        <div class="details-row">
            <div class="label">Reference Number:</div>
            <div class="value">{{ $payment->reference_number }}</div>
        </div>
        @endif
        <div class="details-row">
            <div class="label">Amount Paid:</div>
            <div class="value amount">N${{ number_format($payment->amount, 2) }}</div>
        </div>
        @if($payment->notes)
        <div class="details-row">
            <div class="label">Notes:</div>
            <div class="value">{{ $payment->notes }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Thank you for your payment!</p>
        <p>This is a computer-generated receipt. No signature required.</p>
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>
