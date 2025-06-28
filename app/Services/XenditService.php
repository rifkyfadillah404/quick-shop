<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class XenditService
{
    public function __construct()
    {
        // Placeholder for now - will implement Xendit later
    }

    /**
     * Create invoice for payment (placeholder)
     */
    public function createInvoice(Order $order, array $options = [])
    {
        // Placeholder - return mock data for now
        Log::info('Mock Xendit invoice created', ['order_id' => $order->id]);

        return [
            'id' => 'mock_invoice_' . $order->id,
            'invoice_url' => route('checkout.payment', $order),
            'status' => 'PENDING'
        ];
    }

    /**
     * Create Virtual Account payment (placeholder)
     */
    public function createVirtualAccount(Order $order, $bankCode = 'BCA')
    {
        Log::info('Mock Xendit VA created', ['order_id' => $order->id, 'bank_code' => $bankCode]);

        return [
            'id' => 'mock_va_' . $order->id,
            'account_number' => '1234567890',
            'bank_code' => $bankCode
        ];
    }

    /**
     * Create E-Wallet payment (placeholder)
     */
    public function createEWalletPayment(Order $order, $ewalletType = 'OVO')
    {
        Log::info('Mock Xendit E-Wallet created', ['order_id' => $order->id, 'ewallet_type' => $ewalletType]);

        return [
            'id' => 'mock_ewallet_' . $order->id,
            'checkout_url' => route('checkout.payment', $order),
            'ewallet_type' => $ewalletType
        ];
    }

    /**
     * Get invoice by external ID (placeholder)
     */
    public function getInvoice($externalId)
    {
        Log::info('Mock get invoice', ['external_id' => $externalId]);
        return ['status' => 'PENDING'];
    }

    /**
     * Verify webhook signature (placeholder)
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        return true; // Always return true for testing
    }
}
