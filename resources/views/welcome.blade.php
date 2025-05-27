<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Options</title>
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>
<body>
    <div class="payment-container">
        <h1>Pay Now Securely with Your Favorite Method!</h1>
        <div class="payment-buttons">
            <!-- SSLCommerz Button -->
            <!-- SSLCommerz Button -->
            <form action="{{ route('payment.checkout') }}" method="POST">
    @csrf
    <button type="submit" class="payment-btn sslcommerz" style="margin-bottom: 4px;">
        Pay with SSLCommerz
    </button>

    <button type="submit" class="payment-btn bkash">
        Pay with Bcash
    </button>
</form>


            <!-- bKash Button -->
            <!-- <a href="#" class="payment-btn bkash">
                Pay with bKash
            </a> -->

            <!-- Nagad Button -->
            <a href="#" class="payment-btn nagad">
                Pay with Nagad
            </a>

        </div>
        <div class="info">
            <p>✅ 100% Secure Payments</p>
            <p>🕐 Fast Processing</p>
            <p>📱 Mobile & Online Friendly</p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
