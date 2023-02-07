<?php

namespace App\Http\Controllers\API\YooKassa;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Services\PaymantService;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\User;
use App\Models\Transaction;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Validator;

use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;

class PaymantController extends MainController
{
    public function store(Request $request, PaymantService $service)
    {
        $validators = Validator::make($request->all(), [
            'amount' => 'required|string',
            'booking_id' => 'required|string',
            'user_id' => 'required|string',
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->errors(), 422);
        }

        $booking = Booking::find($request->booking_id);
        $amount = $request->amount;
        $desc = "Оплата бронирования $booking->code";

        try {

            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'booking_id' => $booking->id,
                'price' => $request->amount,
                'description' => $desc
            ]);

        } catch (Exception $e) {
            return $this->sendError('Ошибка, сервер не смог обработать запрос, попробуйте позже','500');
        }

        $link = $service->createPayment($amount, $desc, [
            'transaction_id' => $transaction->id,
            'booking_id' => $booking->id,
            'user_id' => $request->user_id,
        ]);

        return $this->sendResponse($link);

    }

    public function callback(Request $request, PaymantService $service)
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);


        \Log::info($requestBody);

        try {
            $notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
              ? new NotificationSucceeded($requestBody)
              : new NotificationWaitingForCapture($requestBody);
          } catch (Exception $e) {
            return $this->sendError('Ошибка, сервер или платежная система не смогли обработать запрос','500');
        }

        $payment = $notification->getObject();

        try {
            $response = $service->getClient()->capturePayment($payment, $payment->id, uniqid('', true));
        } catch (\Exception $e) {
            $response = $e;
        }

        // if (isset($payment) && $payment->status === 'waiting_for_capture') {
        //     $service->getClient()->capturePayment([
        //         'amount' => $payment->amount,
        //     ], $payment->id, uniqid('', true));
        // }

        if (isset($payment) && $payment->status === 'succeeded') {
            if ((bool) $payment->paid === true) {
                $metadata = (object) $payment->metadata;
                if (isset($metadata)) {
                    $transactionId = (int) $metadata->transaction_id;
                    $transaction = Transaction::find($transactionId);
                    $transaction->status = PaymentStatusEnum::CONFIRMED;
                    $transaction->paymentId = (string)$payment->id;
                    $transaction->save();
                }
            }
        } elseif (sset($payment) && $payment->status === 'canceled') {
            if ((bool) $payment->paid === false) {
                $metadata = (object) $payment->metadata;
                if (isset($metadata)) {
                    $transactionId = (int) $metadata->transaction_id;
                    $transaction = Transaction::find($transactionId);
                    $transaction->status = PaymentStatusEnum::FAILED;
                    $transaction->save();
                }
            }
        }
        return $this->sendResponse([]);
    }

    public function cancellation(Request $request, PaymantService $service, $payment_id)
    {
        $payment = $service->getClient()->getPaymentInfo($payment_id);

        dd($response);
        if (!$payment) {
            return $this->sendError("Банковская транзакция не найдена",'404');
        } else {
            $response = $service->getClient()->cancelPayment($payment_id, uniqid('', true));
        }
    }
}
