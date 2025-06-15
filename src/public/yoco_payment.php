<?php
include "../includes/navbar.php";
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\public\yoco_payment.php
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = currentUser();
$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die('Error: No reference supplied.');
}

// Fetch the transaction details
$stmt = $db->prepare("SELECT * FROM transactions WHERE paystack_reference = ?");
$stmt->execute([$reference]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    die('Error: Invalid transaction reference.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yoco Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.yoco.com/sdk/v1/yoco-sdk-web.js"></script>
    <link href="../assets/css/public.css?v=1.0"  rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Complete Your Payment</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-center fs-5">Total Amount: <strong>R<?= number_format($transaction['total_amount'], 2) ?></strong></p>
                        <p class="text-muted text-center">Transaction Reference: <strong><?= htmlspecialchars($reference) ?></strong></p>
                        <div class="d-grid">
                            <button id="pay-button" class="btn btn-primary btn-lg">Pay Now</button>
                        </div>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>Powered by Yoco</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const yoco = new YocoSDK({
            publicKey: 'pk_test_3af98acejVlg2oDb2a54'
        });

        const checkoutButton = document.getElementById('pay-button');
        checkoutButton.addEventListener('click', function () {
            yoco.showPopup({
                amountInCents: <?= intval($transaction['total_amount'] * 100) ?>,
                currency: 'ZAR',
                name: 'BuyItt Marketplace',
                description: 'Transaction Reference: <?= htmlspecialchars($reference) ?>',
                callback: function (result) {
                    if (result.error) {
                        alert('Payment failed: ' + result.error.message);
                    } else {
                        // Send the token to the server for processing
                        fetch('/public/yoco_callback.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                token: result.id,
                                reference: '<?= htmlspecialchars($reference) ?>',
                                amountInCents: <?= intval($transaction['total_amount'] * 100) ?>
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Redirect to the orders page
                                window.location.href = data.redirect;
                            } else {
                                alert('Payment verification failed: ' + data.message);
                            }
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>