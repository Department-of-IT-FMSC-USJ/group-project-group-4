<?php
$variant = $variant ?? 'nic';
$messages = [
    'nic' => [
        'title' => 'Application Submitted Successfully',
        'body' => 'Your NIC application and payment have been successfully received. Your request is now being processed. The new NIC will be issued and delivered to your registered address. Please keep this for your records.',
        'confirmation' => 'NIC-986050',
    ],
    'birth' => [
        'title' => 'Payment Successful',
        'body' => 'Your payment has been successfully processed. Your request for birth certificate copies has been received. The copies will be delivered to your home address within the specified time period. Please save this number for your records.',
        'confirmation' => 'BC-986050',
    ],
    'fine' => [
        'title' => 'Fine Payment Successful',
        'body' => 'Your fine has been successfully paid. The payment has been recorded in the system, and your driving license status will be updated accordingly. Please retain this receipt for future reference.',
        'confirmation' => 'FINE-986050',
    ],
    'payment' => [
        'title' => 'Payment Successful',
        'body' => 'Your payment has been successfully processed. Thank you for using OneID services.',
        'confirmation' => 'PAY-986050',
    ],

];

$message = $messages[$variant] ?? $messages['nic'];
// Allow overriding confirmation number via $confirmation if provided
$confirmationValue = isset($confirmation) && $confirmation !== ''
    ? $confirmation
    : ($message['confirmation'] ?? 'CONF-000000');
?>

<section class="success-message" aria-labelledby="success-message-heading">
    <div class="success-message__card">
        <h2 id="success-message-heading" class="success-message__title"><?= htmlspecialchars($message['title']) ?></h2>

        <div class="success-message__badge" role="status">
            <span class="success-message__badge-label">Confirmation Number:</span>
            <span class="success-message__badge-value"><?= htmlspecialchars($confirmationValue) ?></span>
        </div>

        <p class="success-message__body">
            <?= htmlspecialchars($message['body']) ?>
        </p>

        <div class="success-message__help" role="note">
            <p>Need help? Contact our support team at support@oneid.gov.lk or call ‪+94 112 223 333‬ with your confirmation number.</p>
        </div>

        <a href="/index.php" class="success-message__home-btn btn">Return To Home</a>
    </div>
</section>
