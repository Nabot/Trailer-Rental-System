# Lead Management & Inquiry Module Proposal

## Overview
A comprehensive lead generation and tracking system that captures inquiries, manages follow-ups, generates quotes, and tracks conversions to bookings.

## Core Features

### 1. **Inquiry/Lead Capture**
- **Quick Lead Entry**: Fast form to capture basic contact info
- **Inquiry Details**: 
  - Contact information (name, phone, email, WhatsApp)
  - Trailer interest (which trailers they're interested in)
  - Rental dates (preferred start/end dates)
  - Rental purpose/use case
  - Budget range
  - Source tracking (website, phone, referral, walk-in, social media, etc.)
  - Notes and special requirements

### 2. **Lead Status Management**
- **Status Workflow**:
  - `new` - Just received
  - `contacted` - Initial contact made
  - `quoted` - Quote sent
  - `follow_up` - Needs follow-up
  - `converted` - Converted to booking
  - `lost` - Not interested/closed
  - `on_hold` - Temporarily paused

### 3. **Quote Generation**
- Generate quotes directly from inquiries
- Quote includes:
  - Trailer selection
  - Rental period
  - Pricing breakdown (rental, delivery, extras)
  - Validity period
  - Terms and conditions
- **Quote Status**: Draft, Sent, Accepted, Expired, Converted
- **One-click conversion**: Convert quote directly to booking

### 4. **Follow-up & Communication Tracking**
- **Activity Log**: Track all interactions
  - Phone calls
  - Emails sent/received
  - WhatsApp messages
  - In-person meetings
  - Notes and comments
- **Follow-up Reminders**: Set reminders for follow-ups
- **Automated Follow-ups**: Schedule automatic follow-up emails/SMS

### 5. **Source Tracking & Analytics**
- Track lead sources:
  - Website form
  - Phone inquiry
  - Walk-in
  - Referral
  - Social media (Facebook, Instagram, etc.)
  - Google Ads
  - Other marketing channels
- **Conversion Rates**: Track which sources convert best
- **ROI Tracking**: Measure marketing effectiveness

### 6. **Lead Scoring & Prioritization**
- **Scoring Factors**:
  - Urgency (rental date proximity)
  - Budget alignment
  - Engagement level
  - Source quality
- **Priority Levels**: High, Medium, Low
- **Hot Leads**: Flag urgent/high-value leads

### 7. **Integration with Existing System**
- **Convert to Customer**: One-click conversion from lead to customer
- **Convert to Booking**: Direct conversion from quote to booking
- **Link Existing**: Link lead to existing customer if found
- **Duplicate Detection**: Check for existing customers/leads

## Database Schema

### `inquiries` Table
```php
- id
- inquiry_number (INQ-YYYY-NNNN)
- source (website, phone, referral, etc.)
- status (new, contacted, quoted, follow_up, converted, lost, on_hold)
- priority (high, medium, low)
- customer_id (nullable - if linked to existing customer)
- name
- email
- phone
- whatsapp_number
- preferred_start_date
- preferred_end_date
- trailer_interests (JSON - array of trailer IDs)
- rental_purpose
- budget_range
- notes
- assigned_to (user_id)
- created_by (user_id)
- converted_at (nullable)
- converted_to_booking_id (nullable)
- created_at
- updated_at
```

### `quotes` Table
```php
- id
- quote_number (QTE-YYYY-NNNN)
- inquiry_id
- customer_id (nullable)
- trailer_id
- start_date
- end_date
- total_days
- rate_per_day
- rental_cost
- delivery_fee
- straps_fee
- damage_waiver_fee
- subtotal
- tax
- total_amount
- validity_days (default 14)
- expires_at
- status (draft, sent, accepted, expired, converted)
- notes
- terms_conditions
- created_by
- sent_at
- accepted_at
- converted_to_booking_id (nullable)
- created_at
- updated_at
```

### `quote_items` Table
```php
- id
- quote_id
- description
- quantity
- unit_price
- total
```

### `inquiry_activities` Table
```php
- id
- inquiry_id
- type (call, email, whatsapp, meeting, note, follow_up)
- subject
- description
- scheduled_at (for follow-ups)
- completed_at
- created_by
- created_at
- updated_at
```

## User Interface Features

### 1. **Lead Dashboard**
- Overview cards: New leads, Follow-ups due, Quotes sent, Conversion rate
- Recent leads list
- Upcoming follow-ups
- Quick actions

### 2. **Lead List View**
- Filterable by: Status, Source, Priority, Assigned To, Date Range
- Sortable columns
- Bulk actions (assign, change status, export)
- Search functionality

### 3. **Lead Detail Page**
- Lead information
- Activity timeline
- Quote history
- Related bookings (if converted)
- Quick actions (call, email, WhatsApp, create quote, convert)

### 4. **Quick Lead Entry**
- Simple form for fast entry
- Auto-save
- Duplicate detection

### 5. **Quote Builder**
- Similar to booking creation
- Preview before sending
- PDF generation
- Email/WhatsApp sending

## Workflow Examples

### Scenario 1: Website Inquiry
1. Lead comes in via website form → Status: `new`
2. Staff reviews → Status: `contacted`
3. Create quote → Status: `quoted`
4. Send quote via email/WhatsApp
5. Customer accepts → Convert to booking → Status: `converted`

### Scenario 2: Phone Inquiry
1. Staff takes call → Quick lead entry
2. Create quote on the spot
3. Send quote immediately
4. Follow up in 2 days
5. Convert to booking when ready

### Scenario 3: Walk-in
1. Customer visits → Create lead
2. Show trailers
3. Generate quote immediately
4. Convert to booking if ready

## Benefits

1. **No Lost Leads**: Capture every inquiry
2. **Better Follow-up**: Never miss a follow-up
3. **Conversion Tracking**: Know what works
4. **Marketing ROI**: Track source effectiveness
5. **Professional Quotes**: Standardized quote generation
6. **Time Savings**: Quick entry and conversion
7. **Customer Insights**: Understand customer needs before booking

## Integration Points

- **Customers**: Link leads to existing customers or create new ones
- **Bookings**: One-click conversion from quote to booking
- **Trailers**: Show availability when creating quotes
- **Invoices**: Link converted bookings to original inquiry
- **Reports**: Lead conversion reports, source analytics

## Reports & Analytics

1. **Lead Conversion Report**: Conversion rates by source, status, period
2. **Source Performance**: Which sources generate most bookings
3. **Follow-up Effectiveness**: Response rates to follow-ups
4. **Quote Acceptance Rate**: How many quotes convert
5. **Sales Pipeline**: Visual pipeline view
6. **Lost Lead Analysis**: Why leads don't convert

## Implementation Priority

### Phase 1 (MVP - Core Functionality)
- ✅ Inquiry/Lead capture
- ✅ Lead status management
- ✅ Basic quote generation
- ✅ Convert to booking
- ✅ Activity logging

### Phase 2 (Enhanced Features)
- ✅ Follow-up reminders
- ✅ Email/SMS integration
- ✅ Quote PDF generation
- ✅ Source tracking
- ✅ Basic analytics

### Phase 3 (Advanced Features)
- ✅ Lead scoring
- ✅ Automated follow-ups
- ✅ Advanced analytics
- ✅ Marketing integration
- ✅ CRM-like features

## Technical Considerations

- Use existing patterns (similar to Booking/Invoice structure)
- Reuse components (forms, tables, modals)
- Integrate with WhatsApp service
- PDF generation for quotes
- Email notifications
- Permissions (leads.view, leads.create, leads.update, quotes.create, etc.)

## Questions to Consider

1. Do you want a public inquiry form on your website?
2. Should quotes have expiration dates?
3. Do you need automated follow-up emails?
4. Should leads be assigned to specific staff members?
5. Do you want lead scoring/prioritization?
6. Should quotes be editable after sending?

---

**Recommendation**: Start with Phase 1 (MVP) to capture leads and generate quotes. This provides immediate value and can be expanded based on usage patterns.
