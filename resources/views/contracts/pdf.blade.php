<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trailer Rental Agreement {{ $contract->contract_number }}</title>
    <style>
        @page { size: A4; margin: 20mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.45; margin: 0; padding: 0 0 18px; max-width: 100%; word-wrap: break-word; }
        .contract-title { text-align: center; margin: 0 0 8px; font-size: 18px; font-weight: bold; letter-spacing: 0.5px; padding-bottom: 10px; border-bottom: 2px solid #333; }
        .intro { margin-bottom: 10px; }
        .section { page-break-inside: avoid; }
        .section-title { font-weight: bold; margin-top: 18px; margin-bottom: 8px; font-size: 12px; color: #222; }
        .party-block { margin: 12px 0; padding: 12px 14px; border: 1px solid #ccc; background: #f8f9fa; border-radius: 2px; page-break-inside: avoid; }
        .party-label { font-weight: bold; margin-bottom: 8px; font-size: 11px; }
        .field-line { margin: 4px 0; min-height: 1.4em; }
        .field-label { display: inline-block; min-width: 150px; vertical-align: top; }
        .field-value { border-bottom: 1px solid #333; padding-bottom: 1px; display: inline; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 10px; page-break-inside: avoid; }
        th, td { padding: 8px 10px; text-align: left; border: 1px solid #333; }
        th { background-color: #e8e8e8; font-weight: bold; }
        .terms-list { margin: 6px 0 6px 20px; padding: 0; }
        .terms-list li { margin: 4px 0; }
        .signature-block { margin-top: 40px; page-break-inside: avoid; }
        .signature-line { margin-top: 36px; padding-top: 4px; font-size: 10px; }
        .signature-line .sig-label { font-weight: bold; display: inline-block; min-width: 140px; }
        .signature-line .sig-space { border-bottom: 1px solid #333; display: inline-block; width: 220px; margin: 0 12px; vertical-align: bottom; min-height: 14px; }
        .signature-line .sig-date { margin-left: 8px; }
        .checkbox { font-size: 12px; }
        .divider { border-top: 1px solid #999; margin: 16px 0; }
        .and-sep { text-align: center; margin: 14px 0; font-weight: bold; font-size: 12px; letter-spacing: 1px; }
        .footer-text { font-size: 9px; color: #666; margin-top: 20px; padding-top: 8px; }
        #page-footer { position: fixed; bottom: 0; left: 20mm; right: 20mm; font-size: 8px; color: #888; padding-top: 4px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .footer-initials { font-size: 9px; color: #333; }
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

    <p class="and-sep">AND</p>

    <div class="party-block">
        <div class="party-label">Lessee (Customer):</div>
        <div class="field-line"><span class="field-label">Full Name:</span> <span class="field-value">{{ $contract->customer->name }}</span></div>
        <div class="field-line"><span class="field-label">ID / Passport No:</span> <span class="field-value">{{ $contract->customer->id_number ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Residential Address:</span> <span class="field-value">{{ $contract->customer->address ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Phone:</span> <span class="field-value">{{ $contract->customer->phone ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Email:</span> <span class="field-value">{{ $contract->customer->email ?: '______________________________' }}</span></div>
        <div class="field-line"><span class="field-label">Driver's Licence No:</span> <span class="field-value">{{ $contract->customer->driver_licence ?: '______________________________' }}</span></div>
        <p class="field-line" style="margin-top: 6px;"><strong>Driver qualification:</strong> The driver must be at least 23 years old and hold a valid Namibian Code B or higher licence for the duration of the rental.</p>
    </div>

    <div class="divider"></div>

    <div class="section">
    <div class="section-title">1. TRAILER DETAILS</div>
    <div class="field-line"><span class="field-label">Trailer Type:</span> <span class="field-value">{{ $booking->trailer->type ?? $booking->trailer->name }}</span></div>
    <div class="field-line"><span class="field-label">Make/Model:</span> <span class="field-value">{{ $booking->trailer->name }}</span></div>
    <div class="field-line"><span class="field-label">Registration No:</span> <span class="field-value">{{ $booking->trailer->registration_number ?: '______________________________' }}</span></div>
    <div class="field-line"><span class="field-label">Colour:</span> <span class="field-value">{{ $booking->trailer->colour ?: '______________________________' }}</span></div>
    <div class="field-line"><span class="field-label">Condition at Handover:</span> <span class="field-value">Good / Fair / Noted damages (see inspection)</span></div>
    </div>

    <div class="section">
    <div class="section-title">2. RENTAL PERIOD</div>
    <div class="field-line"><span class="field-label">Pickup Date &amp; Time:</span> <span class="field-value">{{ $booking->start_date->format('d M Y') }}{{ $booking->pickup_time ? ' ' . $booking->pickup_time->format('H:i') : '' }}</span></div>
    <div class="field-line"><span class="field-label">Return Date &amp; Time:</span> <span class="field-value">{{ $booking->end_date->format('d M Y') }}</span></div>
    <p style="margin: 8px 0 4px;"></p>
    <div class="field-line">Daily Rental Fee: N$ <span class="field-value">{{ number_format($booking->rate_per_day, 2) }}</span> per day</div>
    <div class="field-line">Deposit (Refundable): N$ <span class="field-value">{{ number_format($booking->required_deposit ?? 0, 2) }}</span></div>
    <div class="field-line">Late Return Fee: <span class="field-value">{{ $lateReturnFee ?: 'N$ __________ per hour / day' }}</span></div>
    <p style="margin-top: 8px;">Any extension of the rental must be requested before the expiry of the current period and is subject to availability and payment of the applicable daily rate.</p>
    </div>

    <div class="section">
    <div class="section-title">3. PAYMENT TERMS</div>
    <p>The Lessee agrees to pay the full rental amount before collection of the trailer.</p>
    <p>The deposit will be refunded <strong>within 24–72 hours after inspection</strong> following return of the trailer, provided the trailer is returned:</p>
    <ul class="terms-list">
        <li>Clean</li>
        <li>Undamaged</li>
        <li>With all accessories</li>
    </ul>
    </div>

    <div class="section">
    <div class="section-title">4. USE OF TRAILER</div>
    <p>The Lessee agrees that the trailer will:</p>
    <ul class="terms-list">
        <li>Be used only on public roads suitable for trailers</li>
        <li>Not be overloaded beyond legal weight</li>
        <li>Not be used for illegal activities</li>
        <li>Not be sub-leased or lent to another person</li>
        <li>Not leave Namibia without written permission</li>
    </ul>
    <p>Maximum Load Capacity: <span class="field-value">{{ $booking->trailer->load_capacity_kg ? number_format($booking->trailer->load_capacity_kg) : ($maxLoadCapacity ?: '__________') }}</span> kg</p>
    </div>

    <div class="section">
    <div class="section-title">5. INDEMNITY</div>
    <p>The Lessee indemnifies and holds harmless {{ $companyName }} against any claims, damages, third-party injuries, or losses arising from the use of the trailer during the rental period. This includes but is not limited to injury claims, cargo damage claims, and any other liability arising from the Lessee's use or possession of the trailer.</p>
    </div>

    <div class="section">
    <div class="section-title">6. THEFT &amp; LOSS PROCEDURE</div>
    <p>In case of theft:</p>
    <ul class="terms-list">
        <li>The Lessee must immediately report the theft to the police.</li>
        <li>A police case number must be provided to the Lessor within 24 hours.</li>
        <li>Full replacement value is payable if negligence is involved.</li>
    </ul>
    <p>For the purposes of this Agreement, replacement value of trailer is <strong>{{ ($booking->trailer->trailer_value ? 'N$ ' . number_format($booking->trailer->trailer_value, 2) : ($trailerReplacementValue ? 'N$ ' . $trailerReplacementValue : 'N$ ________')) }}</strong>.</p>
    </div>

    <div class="section">
    <div class="section-title">7. BREAKDOWN &amp; REPAIRS</div>
    <ul class="terms-list">
        <li>The Lessor must be notified immediately in the event of a breakdown.</li>
        <li>No repairs are permitted without prior approval from the Lessor.</li>
        <li>Unauthorized repairs will not be reimbursed.</li>
    </ul>
    </div>

    <div class="section">
    <div class="section-title">8. TRAFFIC FINES</div>
    <p>All traffic fines, toll fees, permits, or penalties during the rental period are the responsibility of the Lessee.</p>
    </div>

    <div class="section">
    <div class="section-title">9. CLEANING</div>
    <p>Trailer must be returned clean.</p>
    <p>Cleaning fee: <span class="field-value">{{ $cleaningFee ? 'N$ ' . $cleaningFee : 'N$ 40' }}</span> (if excessively dirty)</p>
    </div>

    <div class="section">
    <div class="section-title">10. CANCELLATION &amp; NO-SHOW</div>
    <p>Cancellation must be communicated to the Lessor as soon as possible. Same-day cancellations may incur a cancellation penalty. No-show (failure to collect the trailer on the agreed date without prior notice) may result in a no-show fee and/or forfeiture of deposit or prepaid amounts as per the Lessor's policy.</p>
    </div>

    <div class="section">
    <div class="section-title">11. TERMINATION &amp; REPOSSESSION</div>
    <p>The Lessor may repossess the trailer if:</p>
    <ul class="terms-list">
        <li>Terms are violated</li>
        <li>Payment is not made</li>
        <li>Trailer is misused</li>
    </ul>
    <p>No refund will be issued in such cases.</p>
    <p>The Lessee authorizes the Lessor to recover the trailer where legally permissible.</p>
    </div>

    <div class="section">
    <div class="section-title">12. INSPECTION &amp; DAMAGE ASSESSMENT</div>
    <ul class="terms-list">
        <li>A signed checklist and photos at pickup and return are binding.</li>
        <li>Photo evidence will be used in case of disputes.</li>
        <li>The Lessee accepts trailer condition at handover.</li>
    </ul>
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
    </div>

    <div class="section">
    <div class="section-title">13. LIMITATION OF LIABILITY</div>
    <p>The Lessor is not liable for:</p>
    <ul class="terms-list">
        <li>Loss of business or income</li>
        <li>Indirect or consequential damages</li>
        <li>Loss or damage to goods transported</li>
    </ul>
    </div>

    <div class="section">
    <div class="section-title">14. FORCE MAJEURE</div>
    <p>The Lessor shall not be liable for failure to perform due to:</p>
    <ul class="terms-list">
        <li>Natural disasters</li>
        <li>Accidents</li>
        <li>Events beyond reasonable control</li>
    </ul>
    </div>

    <div class="section">
    <div class="section-title">15. GOVERNING LAW &amp; JURISDICTION</div>
    <p>This Agreement shall be governed by the laws of the Republic of Namibia. Any disputes arising from or in connection with this Agreement shall be subject to the exclusive jurisdiction of the Namibian courts.</p>
    </div>

    <div class="section">
    <div class="section-title">16. AGREEMENT</div>
    <p>I confirm that I inspected the trailer and accept its condition.</p>
    <p><strong>Corporate / Business Lessee:</strong> Where the Lessee is a company, the signatory binds himself/herself as surety and co-principal debtor.</p>
    <div class="signature-block">
        <div class="signature-line"><span class="sig-label">Lessor Signature:</span><span class="sig-space"></span><span class="sig-date">Date: _______________</span></div>
        <div class="signature-line"><span class="sig-label">Lessee Signature:</span><span class="sig-space"></span><span class="sig-date">Date: _______________</span></div>
    </div>
    </div>

    <div class="section">
    <div class="section-title">17. ATTACHMENTS</div>
    <p><span class="checkbox">☐</span> Copy of Driver's Licence</p>
    <p><span class="checkbox">☐</span> Copy of ID</p>
    <p><span class="checkbox">☐</span> Vehicle Registration Disc Photo</p>
    <p><span class="checkbox">☐</span> Trailer Photos (Before &amp; After)</p>
    </div>

    <div class="divider"></div>
    <p class="footer-text">Contract No: {{ $contract->contract_number }} | Booking: {{ $booking->booking_number }} | {{ $companyName }} | Generated {{ now()->format('d M Y H:i') }}</p>

    <div id="page-footer"><span class="footer-initials">Lessee Initials: _____________</span><span>{{ $companyName }} — Contract {{ $contract->contract_number }} — Confidential</span><span class="footer-initials">Lessee Initials: _____________</span></div>

</body>
</html>
