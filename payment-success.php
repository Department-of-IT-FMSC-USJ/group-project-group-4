<?php


$variant = $_GET['success'] ?? 'payment';
$confirmation = $_GET['id'] ?? '';
$title = 'Payment Success - OneID';

require __DIR__ . '/views/partials/head.php';
require __DIR__ . '/views/partials/nav.php';
?>
<main class="page success-page">
    <section aria-labelledby="success-heading">
        <h1 id="success-heading" class="visually-hidden">Success</h1>
        <?php

        $variantForCard = in_array($variant, ['nic', 'birth', 'fine']) ? $variant : 'payment';

        $variant = $variantForCard;
        require __DIR__ . '/views/components/success-message.php';


        if ($variant === 'fine' && $confirmation) {

            echo '<form id="linkFinePayment" action="/fines.php" method="POST" style="display:none;">';
            echo '<input type="hidden" name="action" value="payment_done">';
            echo '<input type="hidden" name="payment_id" value="' . htmlspecialchars($confirmation) . '">';
            echo '</form>';
            echo '<script>document.getElementById("linkFinePayment").submit();</script>';
        }

        // For NIC: link the payment to the latest application via session
        $alreadyLinked = isset($_GET['linked']) && $_GET['linked'] == '1';
        if ($variant === 'nic' && $confirmation && !$alreadyLinked) {
            echo '<form id="linkNicPayment" action="/nic.php" method="POST" style="display:none;">';
            echo '<input type="hidden" name="action" value="payment_done">';
            echo '<input type="hidden" name="payment_id" value="' . htmlspecialchars($confirmation) . '">';
            echo '</form>';
            echo '<script>document.getElementById("linkNicPayment").submit();</script>';
        }
        ?>
    </section>
</main>