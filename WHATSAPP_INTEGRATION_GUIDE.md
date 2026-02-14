# WhatsApp Integration Guide

## Overview
This guide explains how to configure and use the WhatsApp Business integration for sending invoices and receipts as PDFs.

## Features Implemented

### 1. WhatsApp Number Field
- Added `whatsapp_number` field to bookings table
- Field is available in booking create/edit forms
- Format: Include country code (e.g., +264811234567 for Namibia)

### 2. WhatsApp Service Class
- Created `App\Services\WhatsAppService`
- Supports WhatsApp Cloud API (Meta)
- Supports Twilio WhatsApp API (alternative)
- Methods available:
  - `sendMessage()` - Send text messages
  - `sendDocument()` - Send PDF documents
  - `sendInvoice()` - Send invoice PDFs
  - `sendReceipt()` - Send payment receipt PDFs

### 3. Invoice WhatsApp Integration
- "Send via WhatsApp" button on invoice detail page
- Automatically uses booking WhatsApp number or customer phone
- Sends invoice PDF with formatted message

## Configuration

### Option 1: WhatsApp Cloud API (Meta) - Recommended

1. **Set up Meta Business Account**
   - Go to https://business.facebook.com
   - Create a Business Account
   - Add WhatsApp Business Account

2. **Get API Credentials**
   - Go to Meta for Developers: https://developers.facebook.com
   - Create a new app
   - Add WhatsApp product
   - Get your credentials:
     - API Token (Temporary token for testing, or Permanent token for production)
     - Phone Number ID
     - Business Account ID

3. **Add to .env file**
   ```env
   WHATSAPP_API_URL=https://graph.facebook.com/v18.0
   WHATSAPP_API_TOKEN=your_api_token_here
   WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
   WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id
   ```

### Option 2: Twilio WhatsApp API

1. **Set up Twilio Account**
   - Sign up at https://www.twilio.com
   - Get a WhatsApp-enabled phone number

2. **Add to .env file**
   ```env
   TWILIO_SID=your_account_sid
   TWILIO_AUTH_TOKEN=your_auth_token
   TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
   ```

3. **Update WhatsAppService**
   - Modify `sendInvoice()` and `sendReceipt()` methods to use `sendViaTwilio()` instead

## Usage

### Sending Invoice via WhatsApp

1. **From Invoice Detail Page:**
   - Navigate to Invoices → Select an invoice
   - Click "📱 Send via WhatsApp" button
   - System will:
     - Generate PDF
     - Upload to WhatsApp
     - Send to booking WhatsApp number or customer phone

2. **WhatsApp Number Priority:**
   - First: Booking WhatsApp number
   - Second: Customer phone number
   - If neither exists, shows error message

### Sending Receipt via WhatsApp

1. **From Payment Detail Page:**
   - Navigate to Payments → Select a payment
   - Click "📱 Send via WhatsApp" button
   - System will send receipt PDF

## API Methods

### Send Invoice
```php
$whatsappService = new \App\Services\WhatsAppService();
$whatsappService->sendInvoice($invoice, $whatsappNumber);
```

### Send Receipt
```php
$whatsappService = new \App\Services\WhatsAppService();
$whatsappService->sendReceipt($payment, $whatsappNumber);
```

### Send Custom Message
```php
$whatsappService = new \App\Services\WhatsAppService();
$whatsappService->sendMessage($phoneNumber, "Your message here");
```

## Testing

### Test WhatsApp Configuration
1. Ensure WhatsApp credentials are in `.env`
2. Create a test booking with WhatsApp number
3. Create an invoice for that booking
4. Click "Send via WhatsApp" button
5. Check WhatsApp for the message

### Troubleshooting

**Issue: "WhatsApp service not configured"**
- Check `.env` file has all required WhatsApp variables
- Verify values are correct (no extra spaces)

**Issue: "Failed to send invoice via WhatsApp"**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify API token is valid
- Ensure phone number format is correct (with country code)

**Issue: "No WhatsApp number found"**
- Add WhatsApp number to booking when creating/editing
- Or ensure customer has a phone number

## Security Notes

- Never commit `.env` file with real credentials
- Use environment-specific tokens
- Rotate API tokens regularly
- Monitor usage to prevent abuse

## Next Steps

To fully enable WhatsApp integration:

1. **Choose Provider:** WhatsApp Cloud API or Twilio
2. **Get Credentials:** Follow setup steps above
3. **Add to .env:** Add your credentials
4. **Test:** Send a test invoice
5. **Monitor:** Check logs for any issues

## Additional Features (Future)

- WhatsApp message templates
- Delivery status tracking
- Bulk sending
- Scheduled messages
- Two-way communication handling
