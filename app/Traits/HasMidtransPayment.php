<?php

namespace App\Traits;

use App\Services\MidtransService;
use Illuminate\Support\Facades\App;

trait HasMidtransPayment
{
    public function createMidtransPayment()
    {
        // If payment is pending and has snap token, reuse it
        if ($this->canRetryPayment()) {
            return true;
        }

        try {
            // Get MidtransService instance from container
            $midtransService = App::make(MidtransService::class);
            $result = $midtransService->createTransaction($this);

            if ($result['success']) {
                $this->update([
                    'snap_token' => $result['token'],
                    'order_id' => $result['order_id'],
                    'payment_status' => 'pending'
                ]);
                return true;
            }

            throw new \Exception($result['message']);
        } catch (\Exception $e) {
            throw new \Exception('Payment creation failed: ' . $e->getMessage());
        }
    }

    public function handlePaymentNotification(array $notification): void
    {
        $transactionStatus = $notification['transaction_status'];
        $paymentType = $notification['payment_type'];
        $orderId = $notification['order_id'];

        // Update booking status based on payment status
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $this->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed'
                ]);
                break;

            case 'pending':
                $this->update([
                    'payment_status' => 'pending',
                    'status' => 'pending'
                ]);
                break;

            case 'deny':
            case 'expire':
            case 'cancel':
                $this->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);
                break;
        }
    }
}
