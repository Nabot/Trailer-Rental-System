<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .quote-info {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .expiry-notice {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="display: flex; justify-content: space-between;">
            <div class="company-info">
                <h1 style="margin: 0; font-size: 24px;">{{ $companyName }}</h1>
                @if($companyAddress)
                <p style="margin: 5px 0;">{{ $companyAddress }}</p>
                @endif
                @if($companyPhone)
                <p style="margin: 5px 0;">Phone: {{ $companyPhone }}</p>
                @endif
                @if($companyEmail)
                <p style="margin: 5px 0;">Email: {{ $companyEmail }}</p>
                @endif
            </div>
            <div class="quote-info">
                <h2 style="margin: 0; font-size: 20px;">QUOTE</h2>
                <p style="margin: 5px 0;"><strong>Quote #:</strong> {{ $quote->quote_number }}</p>
                <p style="margin: 5px 0;"><strong>Date:</strong> {{ $quote->created_at->format('M d, Y') }}</p>
                @if($quote->expires_at)
                <p style="margin: 5px 0;"><strong>Valid Until:</strong> {{ $quote->expires_at->format('M d, Y') }}</p>
                @endif
            </div>
        </div>
    </div>

    @if($quote->expires_at && $quote->expires_at->isPast())
    <div class="expiry-notice">
        <strong>⚠️ This quote has expired.</strong> Please contact us for a new quote.
    </div>
    @endif

    <div style="margin-bottom: 20px;">
        <h3>Quote For:</h3>
        <p style="margin: 5px 0;"><strong>{{ $quote->customer->name ?? ($quote->inquiry->name ?? 'N/A') }}</strong></p>
        @if($quote->customer)
            @if($quote->customer->email)
            <p style="margin: 5px 0;">{{ $quote->customer->email }}</p>
            @endif
            @if($quote->customer->phone)
            <p style="margin: 5px 0;">{{ $quote->customer->phone }}</p>
            @endif
        @elseif($quote->inquiry)
            @if($quote->inquiry->email)
            <p style="margin: 5px 0;">{{ $quote->inquiry->email }}</p>
            @endif
            @if($quote->inquiry->phone)
            <p style="margin: 5px 0;">{{ $quote->inquiry->phone }}</p>
            @endif
        @endif
    </div>

    @if($quote->trailer)
    <div style="margin-bottom: 20px;">
        <h3>Trailer Details:</h3>
        <p style="margin: 5px 0;"><strong>{{ $quote->trailer->name }}</strong> - {{ $quote->trailer->type }}</p>
        @if($quote->start_date && $quote->end_date)
        <p style="margin: 5px 0;"><strong>Rental Period:</strong> {{ $quote->start_date->format('M d, Y') }} to {{ $quote->end_date->format('M d, Y') }}</p>
        <p style="margin: 5px 0;"><strong>Duration:</strong> {{ $quote->total_days }} days</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($quote->rental_cost > 0)
            <tr>
                <td>Trailer Rental ({{ $quote->total_days }} days × N${{ number_format($quote->rate_per_day, 2) }}/day)</td>
                <td class="text-right">{{ $quote->total_days }}</td>
                <td class="text-right">N${{ number_format($quote->rate_per_day, 2) }}</td>
                <td class="text-right">N${{ number_format($quote->rental_cost, 2) }}</td>
            </tr>
            @endif
            @if($quote->delivery_fee > 0)
            <tr>
                <td>Delivery Fee</td>
                <td class="text-right">1</td>
                <td class="text-right">N${{ number_format($quote->delivery_fee, 2) }}</td>
                <td class="text-right">N${{ number_format($quote->delivery_fee, 2) }}</td>
            </tr>
            @endif
            @if($quote->straps_fee > 0)
            <tr>
                <td>Straps Fee</td>
                <td class="text-right">1</td>
                <td class="text-right">N${{ number_format($quote->straps_fee, 2) }}</td>
                <td class="text-right">N${{ number_format($quote->straps_fee, 2) }}</td>
            </tr>
            @endif
            @if($quote->damage_waiver_fee > 0)
            <tr>
                <td>Damage Waiver Fee</td>
                <td class="text-right">1</td>
                <td class="text-right">N${{ number_format($quote->damage_waiver_fee, 2) }}</td>
                <td class="text-right">N${{ number_format($quote->damage_waiver_fee, 2) }}</td>
            </tr>
            @endif
            @foreach($quote->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-right">N${{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">N${{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right"><strong>N${{ number_format($quote->subtotal, 2) }}</strong></td>
            </tr>
            @if($quote->tax > 0)
            <tr>
                <td colspan="3" class="text-right">Tax:</td>
                <td class="text-right">N${{ number_format($quote->tax, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>N${{ number_format($quote->total_amount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($quote->notes)
    <div style="margin-top: 20px; padding: 10px; background-color: #f5f5f5;">
        <p><strong>Notes:</strong> {{ $quote->notes }}</p>
    </div>
    @endif

    @if($quote->terms_conditions)
    <div style="margin-top: 20px; padding: 10px; background-color: #f9f9f9;">
        <p><strong>Terms & Conditions:</strong></p>
        <p style="white-space: pre-wrap; font-size: 10px;">{{ $quote->terms_conditions }}</p>
    </div>
    @endif

    <div class="footer">
        <p>This is a quotation and does not constitute a binding agreement until accepted.</p>
        @if($quote->expires_at)
        <p>This quote is valid until {{ $quote->expires_at->format('F d, Y') }}.</p>
        @endif
        <p>Thank you for considering IronAxle Rentals!</p>
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>
