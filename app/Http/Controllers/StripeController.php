<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function reservationPayment()
    {
        if (!session()->has('reservation_details')) {
            return redirect()->route('client.dashboard')->with('error', 'No reservation details found');
        }

        $details = session('reservation_details');
        Stripe::setApiKey(config('stripe.test.sk'));
        $session = Session::create([
            'line_items'  => [
                [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => 'Room: ' . $details['roomNumber'],
                            'description' => 'Room reservation for ' . $details['days'] . ' day(s)',
                        ],
                        'unit_amount'  => $details['price'],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode'        => 'payment',
            'success_url' => route('stripe.reservation.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('client.dashboard'),
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function reservationSuccess(Request $request): RedirectResponse
    {
        if (!session()->has('reservation_details')) {
            return redirect()->route('client.dashboard')->with('error', 'No reservation details found');
        }

        try {
            Stripe::setApiKey(config('stripe.test.sk'));
            $sessionId = $request->get('session_id');
            if (!$sessionId) {
                return redirect()->route('client.dashboard')->with('error', 'Invalid session ID');
            }

            $session = Session::retrieve($sessionId);
            $paymentIntentId = $session->payment_intent;
            return redirect()->route('client.store.reservation', ['payment_id' => $paymentIntentId]);
        } catch (\Exception $e) {
            return redirect()->route('client.dashboard')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }
}
