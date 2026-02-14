<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiToken;
    protected $phoneNumberId;
    protected $businessAccountId;

    public function __construct()
    {
        // WhatsApp Cloud API Configuration
        // You can use Meta's WhatsApp Cloud API or third-party providers like Twilio, MessageBird, etc.
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->apiToken = config('services.whatsapp.api_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->businessAccountId = config('services.whatsapp.business_account_id');
    }

    /**
     * Send a text message via WhatsApp.
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('WhatsApp service not configured. Message not sent.');
            return false;
        }

        try {
            $response = Http::withToken($this->apiToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'text',
                    'text' => [
                        'body' => $message
                    ]
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', ['to' => $to]);
                return true;
            } else {
                Log::error('WhatsApp API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send a PDF document via WhatsApp.
     */
    public function sendDocument(string $to, string $pdfPath, string $filename, ?string $caption = null): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('WhatsApp service not configured. Document not sent.');
            return false;
        }

        try {
            // Upload PDF to a publicly accessible URL
            // For WhatsApp Cloud API, you need to upload media first
            $mediaId = $this->uploadMedia($pdfPath, 'document', $filename);
            
            if (!$mediaId) {
                return false;
            }

            $response = Http::withToken($this->apiToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'document',
                    'document' => [
                        'id' => $mediaId,
                        'caption' => $caption ?? '',
                        'filename' => $filename
                    ]
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp document sent successfully', ['to' => $to, 'filename' => $filename]);
                return true;
            } else {
                Log::error('WhatsApp API error sending document', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service exception sending document', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Upload media file to WhatsApp servers.
     */
    protected function uploadMedia(string $filePath, string $type, string $filename): ?string
    {
        try {
            $fileContent = Storage::disk('public')->get($filePath);
            
            $response = Http::withToken($this->apiToken)
                ->attach('file', $fileContent, $filename)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/media", [
                    'messaging_product' => 'whatsapp',
                    'type' => $type,
                ]);

            if ($response->successful()) {
                return $response->json('id');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('WhatsApp media upload failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Send invoice PDF via WhatsApp.
     */
    public function sendInvoice(\App\Models\Invoice $invoice, ?string $whatsappNumber = null): bool
    {
        $whatsappNumber = $whatsappNumber ?? $invoice->booking?->whatsapp_number ?? $invoice->customer?->phone;
        
        if (!$whatsappNumber) {
            Log::warning('No WhatsApp number found for invoice', ['invoice_id' => $invoice->id]);
            return false;
        }

        try {
            // Generate PDF
            $pdf = Pdf::loadView('invoices.pdf', [
                'invoice' => $invoice,
                'companyName' => \App\Models\Setting::get('company_name', 'Trailer Rental Company'),
                'companyAddress' => \App\Models\Setting::get('company_address', ''),
                'companyPhone' => \App\Models\Setting::get('company_phone', ''),
                'companyEmail' => \App\Models\Setting::get('company_email', ''),
            ]);

            // Save PDF temporarily
            $filename = "invoice-{$invoice->invoice_number}.pdf";
            $pdfPath = "temp/{$filename}";
            Storage::disk('public')->put($pdfPath, $pdf->output());

            // Send via WhatsApp
            $message = "📄 Invoice {$invoice->invoice_number}\n\n";
            $message .= "Customer: {$invoice->customer->name}\n";
            $message .= "Amount: N$" . number_format($invoice->total_amount, 2) . "\n";
            $message .= "Due Date: " . $invoice->due_date->format('M d, Y') . "\n\n";
            $message .= "Please find your invoice attached.";

            $result = $this->sendDocument($whatsappNumber, $pdfPath, $filename, $message);

            // Clean up temporary file
            Storage::disk('public')->delete($pdfPath);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send invoice via WhatsApp', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send receipt PDF via WhatsApp.
     */
    public function sendReceipt(\App\Models\Payment $payment, ?string $whatsappNumber = null): bool
    {
        $payment->load(['booking.customer', 'invoice.customer']);
        $customer = $payment->booking?->customer ?? $payment->invoice?->customer;
        $whatsappNumber = $whatsappNumber ?? $payment->booking?->whatsapp_number ?? $customer?->phone;
        
        if (!$whatsappNumber) {
            Log::warning('No WhatsApp number found for payment', ['payment_id' => $payment->id]);
            return false;
        }

        try {
            // Generate receipt PDF (you'll need to create this view)
            $pdf = Pdf::loadView('payments.receipt-pdf', [
                'payment' => $payment,
                'companyName' => \App\Models\Setting::get('company_name', 'Trailer Rental Company'),
                'companyAddress' => \App\Models\Setting::get('company_address', ''),
                'companyPhone' => \App\Models\Setting::get('company_phone', ''),
                'companyEmail' => \App\Models\Setting::get('company_email', ''),
            ]);

            // Save PDF temporarily
            $filename = "receipt-{$payment->id}.pdf";
            $pdfPath = "temp/{$filename}";
            Storage::disk('public')->put($pdfPath, $pdf->output());

            // Send via WhatsApp
            $message = "✅ Payment Receipt\n\n";
            $message .= "Payment ID: {$payment->id}\n";
            $message .= "Amount: N$" . number_format($payment->amount, 2) . "\n";
            $message .= "Date: " . $payment->payment_date->format('M d, Y') . "\n";
            $message .= "Method: " . ucfirst($payment->payment_method) . "\n\n";
            $message .= "Thank you for your payment!";

            $result = $this->sendDocument($whatsappNumber, $pdfPath, $filename, $message);

            // Clean up temporary file
            Storage::disk('public')->delete($pdfPath);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send receipt via WhatsApp', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format phone number for WhatsApp (remove spaces, ensure country code).
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // If doesn't start with +, assume it needs country code
        if (!str_starts_with($phone, '+')) {
            // Default to Namibia (+264) if no country code
            $phone = '+264' . ltrim($phone, '0');
        }
        
        return $phone;
    }

    /**
     * Check if WhatsApp service is configured.
     */
    protected function isConfigured(): bool
    {
        return !empty($this->apiToken) && !empty($this->phoneNumberId);
    }

    /**
     * Alternative: Send via Twilio WhatsApp API.
     */
    public function sendViaTwilio(string $to, string $message, ?string $mediaUrl = null): bool
    {
        $twilioSid = config('services.twilio.sid');
        $twilioToken = config('services.twilio.token');
        $twilioFrom = config('services.twilio.whatsapp_from');

        if (!$twilioSid || !$twilioToken || !$twilioFrom) {
            Log::warning('Twilio WhatsApp not configured');
            return false;
        }

        try {
            $client = new \Twilio\Rest\Client($twilioSid, $twilioToken);
            
            $messageParams = [
                'from' => "whatsapp:{$twilioFrom}",
                'body' => $message
            ];

            if ($mediaUrl) {
                $messageParams['mediaUrl'] = [$mediaUrl];
            }

            $client->messages->create(
                "whatsapp:{$this->formatPhoneNumber($to)}",
                $messageParams
            );

            Log::info('Twilio WhatsApp message sent', ['to' => $to]);
            return true;
        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
