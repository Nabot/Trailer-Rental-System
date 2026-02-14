<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trailer Rental Agreement {{ $contract->contract_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.35; }
        .contract-title { text-align: center; margin: 5px 0 15px; font-size: 16px; font-weight: bold; }
        .intro { margin-bottom: 15px; }
        .section-title { font-weight: bold; margin-top: 14px; margin-bottom: 6px; font-size: 11px; }
        .party-block { margin: 12px 0; }
        .party-label { font-weight: bold; margin-bottom: 4px; }
        .field-line { margin: 3px 0; }
        .field-label { display: inline-block; min-width: 140px; }
        .field-value { text-decoration: underline; }
        .underline { border-bottom: 1px solid #333; display: inline-block; min-width: 200px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 10px; }
        th, td { padding: 5px 8px; text-align: left; border: 1px solid #333; }
        th { background-color: #eee; font-weight: bold; }
        .terms-list { margin: 4px 0 4px 16px; padding: 0; }
        .terms-list li { margin: 2px 0; }
        .signature-block { margin-top: 30px; }
        .signature-line { border-bottom: 1px solid #333; margin-top: 25px; padding-top: 2px; font-size: 10px; }
        .checkbox { font-size: 12px; }
        .divider { border-top: 1px solid #999; margin: 12px 0; }
    </style>
</head>
<body>

    <div class="contract-title">TRAILER RENTAL AGREEMENT</div>

    <p class="intro">This Trailer Rental Agreement ("Agreement") is entered into on:</p>
    <p class="field-line"><strong>Date:</strong> <span class="field-value">{{ $contract->contract_date->format('d F Y') }}</span></p>

    <p class="intro">Between:</p>

    <div class="party-block">
        <div class="party-label">Lessor (Owner):</div>
        <div class="field-line"><span class="field-label">Business Name:</span> <span class="field-value">{{ $companyName }}</span></div>
        <div class="field-line"><span class="field-label">Registration No:</span> <span class="field-value">{{ $companyRegistrationNo ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Address:</span> <span class="field-value">{{ $companyAddress ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Phone:</span> <span class="field-value">{{ $companyPhone ?: '______________________________' }}</span></div>
    </div>

    <p class="intro">AND</p>

    <div class="party-block">
        <div class="party-label">Lessee (Customer):</div>
        <div class="field-line"><span class="field-label">Full Name:</span> <span class="field-value">{{ $contract->customer->name }}</span></div>
        <div class="field-line"><span class="field-label">ID / Passport No:</span> <span class="field-value">{{ $contract->customer->id_number ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Residential Address:</span> <span class="field-value">{{ $contract->customer->address ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Phone:</span> <span class="field-value">{{ $contract->customer->phone ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Email:</span> <span class="field-value">{{ $contract->customer->email ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Driver's Licence No:</span> <span class="field-value">______________________________</span></div>
    </div>

    <div class="divider"></div>

    <div class="section-title">1. TRAILER DETAILS</div>
    <div class="field-line"><span class="field-label">Trailer Type:</span> <span class="field-value">{{ $booking->trailer->type ?? $booking->trailer->name }}</span></div>
    <div class="field-line"><span class="field-label">Make/Model:</span> <span class="field-value">{{ $booking->trailer->name }}</span></div>
    <div class="field-line"><span class="field-label">Registration No:</span> <span class="field-value">{{ $booking->trailer->registration_number ?: '______________________________' }}</span></div>
    <div class="field-line"><span class="field-label">Colour:</span> <span class="field-value">______________________________</span></div>
    <div class="field-line"><span class="field-label">Condition at Handover:</span> <span class="field-value">Good / Fair / Noted damages (see inspection)</span></div>

    <div class="section-title">2. RENTAL PERIOD</div>
    <div class="field-line"><span class="field-label">Pickup Date &amp; Time:</span> <span class="field-value">{{ $booking->start_date->format('d M Y') }}{{ $booking->pickup_time ? ' ' . $booking->pickup_time->format('H:i') : '' }}</span></div>
    <div class="field-line"><span class="field-label">Return Date &amp; Time:</span> <span class="field-value">{{ $booking->end_date->format('d M Y') }}</span></div>
    <p style="margin: 8px 0 4px;"></p>
    <div class="field-line">Daily Rental Fee: N$ <span class="field-value">{{ number_format($booking->rate_per_day, 2) }}</span> per day</div>
    <div class="field-line">Deposit (Refundable): N$ <span class="field-value">{{ number_format($booking->required_deposit ?? 0, 2) }}</span></div>
    <div class="field-line">Late Return Fee: <span class="field-value">{{ $lateReturnFee ?: 'N$ __________ per hour / day' }}</span></div>

    <div class="section-title">3. PAYMENT TERMS</div>
    <p>The Lessee agrees to pay the full rental amount before collection of the trailer.</p>
    <p>The deposit will be refunded within <strong>48 hours</strong> after inspection, provided the trailer is returned:</p>
    <ul class="terms-list">
        <li>Clean</li>
        <li>Undamaged</li>
        <li>With all accessories</li>
    </ul>

    <div class="section-title">4. USE OF TRAILER</div>
    <p>The Lessee agrees that the trailer will:</p>
    <ul class="terms-list">
        <li>Be used only on public roads suitable for trailers</li>
        <li>Not be overloaded beyond legal weight</li>
        <li>Not be used for illegal activities</li>
        <li>Not be sub-leased or lent to another person</li>
        <li>Not leave Namibia without written permission</li>
    </ul>
    <p>Maximum Load Capacity: <span class="field-value">{{ $maxLoadCapacity ?: '__________' }}</span> kg</p>

    <div class="section-title">5. RESPONSIBILITY &amp; LIABILITY</div>
    <p>The Lessee is fully responsible for the trailer during the rental period.</p>
    <p>The Lessee will be liable for:</p>
    <ul class="terms-list">
        <li>Accidents</li>
        <li>Tyre damage</li>
        <li>Rim damage</li>
        <li>Bent axle</li>
        <li>Electrical plug damage</li>
        <li>Lights damage</li>
        <li>Broken boards or panels</li>
        <li>Theft or loss</li>
    </ul>
    <p>In case of theft, the Lessee is liable for <strong>replacement value of trailer: {{ $trailerReplacementValue ? 'N$ ' . $trailerReplacementValue : 'N$ ________' }}</strong></p>

    <div class="section-title">6. BREAKDOWN &amp; REPAIRS</div>
    <p>The Lessee must immediately inform the Lessor in case of breakdown.</p>
    <p>No repairs may be done without approval.</p>
    <p>Unauthorised repairs will not be reimbursed.</p>

    <div class="section-title">7. TRAFFIC FINES</div>
    <p>All traffic fines, toll fees, permits, or penalties during the rental period are the responsibility of the Lessee.</p>

    <div class="section-title">8. CLEANING</div>
    <p>Trailer must be returned clean.</p>
    <p>Cleaning fee: <span class="field-value">{{ $cleaningFee ? 'N$ ' . $cleaningFee : 'N$ ________' }}</span> (if excessively dirty)</p>

    <div class="section-title">9. TERMINATION</div>
    <p>The Lessor reserves the right to repossess the trailer if:</p>
    <ul class="terms-list">
        <li>The agreement is violated</li>
        <li>The trailer is misused</li>
        <li>Payment terms are breached</li>
    </ul>
    <p>No refund will be provided in such cases.</p>

    <div class="section-title">10. INSPECTION CHECKLIST</div>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Condition at Pickup</th>
                <th>Condition at Return</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tires</td>
                <td><span class="checkbox">☐</span> Good <span class="checkbox">☐</span> Worn</td>
                <td><span class="checkbox">☐</span> Good <span class="checkbox">☐</span> Damaged</td>
            </tr>
            <tr>
                <td>Lights</td>
                <td><span class="checkbox">☐</span> Working <span class="checkbox">☐</span> Faulty</td>
                <td><span class="checkbox">☐</span> Working <span class="checkbox">☐</span> Faulty</td>
            </tr>
            <tr>
                <td>Plug</td>
                <td><span class="checkbox">☐</span> Good <span class="checkbox">☐</span> Loose</td>
                <td><span class="checkbox">☐</span> Good <span class="checkbox">☐</span> Damaged</td>
            </tr>
            <tr>
                <td>Body</td>
                <td><span class="checkbox">☐</span> Good <span class="checkbox">☐</span> Scratches</td>
                <td><span class="checkbox">☐</span> Good <span class="checkbox">☐</span> Damaged</td>
            </tr>
            <tr>
                <td>Spare Wheel</td>
                <td><span class="checkbox">☐</span> Yes <span class="checkbox">☐</span> No</td>
                <td><span class="checkbox">☐</span> Yes <span class="checkbox">☐</span> No</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">11. AGREEMENT</div>
    <p>I confirm that I inspected the trailer and accept its condition.</p>
    <div class="signature-block">
        <div class="signature-line"><strong>Lessor Signature:</strong> __________________________ Date: _______</div>
        <div class="signature-line"><strong>Lessee Signature:</strong> __________________________ Date: _______</div>
    </div>

    <div class="section-title">12. ATTACHMENTS</div>
    <p><span class="checkbox">☐</span> Copy of Driver's Licence</p>
    <p><span class="checkbox">☐</span> Copy of ID</p>
    <p><span class="checkbox">☐</span> Vehicle Registration Disc Photo</p>
    <p><span class="checkbox">☐</span> Trailer Photos (Before &amp; After)</p>

    <div class="divider"></div>
    <p style="font-size: 9px; color: #666;">Contract No: {{ $contract->contract_number }} | Booking: {{ $booking->booking_number }} | {{ $companyName }} | Generated {{ now()->format('d M Y H:i') }}</p>

</body>
</html>
