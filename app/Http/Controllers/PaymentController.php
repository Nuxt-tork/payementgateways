<?php

namespace App\Http\Controllers;

use Log;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {

        // dd(env('SSLCOMMERZ_URL'));
        // Prepare order data

        
        $storeId = config('services.sslcommerz.store_id');
        $storePassword = config('services.sslcommerz.store_password');
        $sslcommerzUrl = config('services.sslcommerz.url');

        if (empty($storeId) || empty($storePassword)) {
            dd('storeId or storePassword is missing');
        }

        $order = [
            'total_amount' => 100,  // Total amount (in BDT)
            'currency' => 'BDT',    // Currency code (BDT for Bangladesh Taka)
            'tran_id' => uniqid(),  // Unique transaction ID
            'cus_name' => 'Rishad',
            'cus_email' => 'rishad@gmail.com',
            'cus_phone' => '01717171717',
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_postcode' => '1200',
            'cus_country' => 'BD',
            'return_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel'),
            'notify_url' => route('payment.notify'),
        ];

            $client = new Client();
            $response = $client->post($sslcommerzUrl, [
                'form_params' => [
                    'store_id' => $storeId,
                    'store_passwd' => $storePassword,
                    'total_amount' => $order['total_amount'],
                    'currency' => $order['currency'],
                    'tran_id' => $order['tran_id'],
                    'cus_name' => $order['cus_name'],
                    'cus_email' => $order['cus_email'],
                    'cus_phone' => $order['cus_phone'],
                    'cus_add1' => $order['cus_add1'],
                    'cus_city' => $order['cus_city'],
                    'cus_postcode' => $order['cus_postcode'],
                    'cus_country' => $order['cus_country'] ?? 'BD',

                    // Shipping Info (Same as billing in your case)
                    'ship_name'      => $order['cus_name'],
                    'ship_add1'      => $order['cus_add1'],
                    'ship_city'      => $order['cus_city'],
                    'ship_postcode'  => $order['cus_postcode'],
                    'ship_country'   => $order['cus_country'],

                    // Product Info
                    'product_name'    => 'LMS Course Payment',
                    'product_category'=> 'LMS',
                    'product_profile' => 'general',
                    'shipping_method' => 'NO',


                    // Optional fields
                    'emi_option'      => 0,
                    'cus_ip'          => request()->ip(),
                    'cus_city'        => $order['cus_city'],
                    'cus_country'     => $order['cus_country'],
                    'cus_phone'       => $order['cus_phone'],   

                    // URLs
                    'success_url' => $order['return_url'],
                    'cancel_url' => $order['cancel_url'],
                    'notify_url' => $order['return_url'],
                ]
            ]);
// dd($response);
            $result = json_decode($response->getBody(), true);
            // dd($result);
            if ($result['status'] === 'SUCCESS') {
                return redirect($result['GatewayPageURL']);
            } else {
                return redirect()->route('payment.fail')->withErrors('Payment initiation failed');
            }

      
    }

    // Success Response (handled by the return URL)
    public function paymentSuccess(Request $request)
    {

        Log::info('SSLCommerz return payload:', $request->all());
        // SSLCommerz verifies the payment after user returns to your site
        $verify_payment = $this->verifyPayment($request->all());

        if ($verify_payment['status'] === 'SUCCESS') {
            // Handle successful payment (e.g., update order status, store transaction details, etc.)
            return view('payment.success', ['data' => $verify_payment]);
        } else {
            return redirect()->route('payment.fail')->withErrors('Payment verification failed');
        }
    }

    // Cancel Response (handled by the cancel URL)
    public function paymentCancel()
    {
        return view('payment.cancel');
    }

    // Notification URL (asynchronously handled by SSLCommerz)
    public function paymentNotify(Request $request)
    {
        // SSLCommerz notifies the status after a transaction
        $notify = $this->verifyPayment($request->all());

        if ($notify['status'] === 'SUCCESS') {
            // Handle the payment notification logic here (e.g., update the order status)
        }

        // Acknowledge receipt of the payment status
        return response()->json(['status' => 'received']);
    }

    // Fail Response (handled by the fail URL)
    public function paymentFail()
    {
        return view('payment.fail');
    }

    // Helper function to verify the payment
    private function verifyPayment($paymentData)
    {
        $client = new Client();
        $response = $client->post(env('SSLCOMMERZ_URL'), [
            'form_params' => [
                'store_id' => env('SSLCOMMERZ_STORE_ID'),
                'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
                'tran_id' => $paymentData['tran_id'],
                'verify_sign' => $paymentData['verify_sign'],  // The verify sign provided by SSLCommerz after the payment
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
