<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
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
        .invoice-info {
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
            <div class="invoice-info">
                <h2 style="margin: 0; font-size: 20px;">INVOICE</h2>
                <p style="margin: 5px 0;"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p style="margin: 5px 0;"><strong>Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}</p>
                <p style="margin: 5px 0;"><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <h3>Bill To:</h3>
        <p style="margin: 5px 0;"><strong>{{ $invoice->customer->name }}</strong></p>
        @if($invoice->customer->email)
        <p style="margin: 5px 0;">{{ $invoice->customer->email }}</p>
        @endif
        @if($invoice->customer->phone)
        <p style="margin: 5px 0;">{{ $invoice->customer->phone }}</p>
        @endif
    </div>

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
            @foreach($invoice->items as $item)
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
                <td class="text-right"><strong>N${{ number_format($invoice->subtotal, 2) }}</strong></td>
            </tr>
            @if($invoice->tax > 0)
            <tr>
                <td colspan="3" class="text-right">Tax:</td>
                <td class="text-right">N${{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>N${{ number_format($invoice->total_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right">Paid:</td>
                <td class="text-right">N${{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Balance:</strong></td>
                <td class="text-right"><strong>N${{ number_format($invoice->balance, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($invoice->notes)
    <div style="margin-top: 20px; padding: 10px; background-color: #f5f5f5;">
        <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
    </div>
    @endif

    <div style="margin-top: 30px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd;">
        <h3 style="margin-top: 0; margin-bottom: 10px; font-size: 14px;">Banking Details:</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 5px 10px 5px 0; width: 150px;"><strong>Account Name:</strong></td>
                <td style="padding: 5px 0;">{{ $bankAccountName }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 10px 5px 0;"><strong>Bank Name:</strong></td>
                <td style="padding: 5px 0;">{{ $bankName }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 10px 5px 0;"><strong>Account Number:</strong></td>
                <td style="padding: 5px 0;">{{ $bankAccountNumber }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 10px 5px 0;"><strong>Branch Name:</strong></td>
                <td style="padding: 5px 0;">{{ $bankBranchName }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 10px 5px 0;"><strong>Branch Code:</strong></td>
                <td style="padding: 5px 0;">{{ $bankBranchCode }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Please make payment by the due date to avoid late fees.</p>
    </div>
</body>
</html>
