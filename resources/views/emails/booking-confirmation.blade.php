<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #ea580c;">Booking Confirmed</h2>
    <p>Hi {{ $booking->customer->name }},</p>
    <p>Your trailer rental booking has been confirmed.</p>
    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Booking number</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $booking->booking_number }}</td></tr>
        <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Trailer</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $booking->trailer->name }}</td></tr>
        <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Dates</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $booking->start_date->format('M d, Y') }} – {{ $booking->end_date->format('M d, Y') }}</td></tr>
        <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Total</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ format_money((float) $booking->total_amount) }}</td></tr>
    </table>
    <p>If you have any questions, please contact us.</p>
    <p style="color: #666; font-size: 14px;">{{ config('app.name') }}</p>
</body>
</html>
