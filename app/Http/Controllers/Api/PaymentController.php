<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Stripe\{Stripe, Checkout\Session, Exception\CardException};
use App\Services\{FlowService, TransactionHistoryService};
use App\Models\{Payment};
use Auth;

class PaymentController extends Controller
{
    public function throughStripeGateway(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $success_url = $_SERVER['HTTP_ORIGIN'] . '/success-payment?session_id={CHECKOUT_SESSION_ID}';
            if (isset($request->buyAfterPaymentThisUrl)) $success_url = $request->buyAfterPaymentThisUrl . '?session_id={CHECKOUT_SESSION_ID}';

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Blucoins',
                            'images' => ['https://my.beatoken.com/assets/img/logo4x.png']
                        ],
                        'unit_amount' => ceil($request->amount) . '00',
                    ],
                    'quantity' => 1
                ]],
                'customer_email' => Auth::user()->email,
                'mode' => 'payment',
                'success_url' => $success_url,
                'cancel_url' => $_SERVER['HTTP_ORIGIN'] . '/cancel-payment?session_id={CHECKOUT_SESSION_ID}',
            ]);

            $payment = Payment::create([
                'session_id' => $session->id,
                'user_id' => Auth::id(),
                'amount' => ceil($request->amount),
                'status' => $session->payment_status
            ]);

            return response()->json(['session' => $session, 'payment' => $payment], 200);
        } catch (CardException $e) {
            return response()->json([$e->getError()], $e->getHttpStatus());
        }
    }

    public function updatePaymentRefillBalance(Request $request, FlowService $flowService, TransactionHistoryService $transactionHistoryService)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::retrieve($request->session_id);
            $payment = Payment::where(['session_id' => $request->session_id, 'user_id' => Auth::id()])->first();

            if ('paid' === $session->payment_status && $payment && 'paid_closed' != $payment->status) {

                $flowService->refillBalance(Auth::user(), $payment);
                $transactionHistoryService->createTransactionHistoryWhenUserRefillBalance(['session_id' => $request->session_id, 'amount' => $payment->amount]);

                $payment->status = 'paid_closed';
                $payment->save();

                return response()->json($payment, 200);
            } else {
                return response()->json(['message' => 'Not paid', 'payment_status' => $session->payment_status], 402);
            }
        } catch (CardException $e) {
            return response()->json([$e->getError()], $e->getHttpStatus());
        }
    }
}
