<?php

namespace App\Traits;

use App\Services\MidtransService;
use Illuminate\Support\Facades\App;

trait HasMidtransPayment
{
    public function createMidtransPayment()
    {
        // If payment is pending and has snap token, don't create new one
        if ($this->canRetryPayment()) {
            return [
                'success' => true,
                'token' => $this->snap_token,
                'order_id' => $this->order_id
            ];
        }

        try {
            $midtransService = App::make(MidtransService::class);
            $result = $midtransService->createTransaction($this);

            if ($result['success']) {
                $this->update([
                    'snap_token' => $result['token'],
                    'order_id' => $result['order_id'],
                    'payment_status' => 'pending'
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function handlePaymentNotification(array $notification): void
    {
        $transactionStatus = $notification['transaction_status'];
        $paymentType = $notification['payment_type'];
        $orderId = $notification['order_id'];

        // Create or update payment record
        $payment = $this->payments()->updateOrCreate(
            ['payment_id' => $notification['transaction_id']],
            [
                'amount' => $notification['gross_amount'],
                'payment_type' => $paymentType,
                'status' => $transactionStatus,
                'payment_details' => $notification,
                'paid_at' => in_array($transactionStatus, ['capture', 'settlement']) ? now() : null
            ]
        );

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
