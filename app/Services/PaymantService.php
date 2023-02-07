<?php


namespace App\Services;

use YooKassa\Client;
use Illuminate\Support\Facades\Auth;

class PaymantService
{
    public function getClient()
    {
        $client = new Client();
        $client->setAuth('980330', 'test_IsxOicQ9padJOR9omr-Bdqt9nlKTimAkTcYoIamYDGo');

        return $client;
    }

    public function createPayment(float $amount, string $description, array $options = [])
    {
        $client = $this->getClient();

        $payment = $client->createPayment([
            'amount' => [
                'value' => $amount,
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => 'https://82d5-89-22-175-236.eu.ngrok.io',
                // 'return_url' => route('payments.callback'),
            ],
            'metadata' => [
                'transaction_id' => $options['transaction_id'],
                'booking_id' => $options['booking_id'],
                'user_id' => $options['user_id'],
            ],
            'capture' => false,
            'description' => $description,
        ], uniqid('', true));

        return $payment->getConfirmation()->getConfirmationUrl();
    }
}
