<?php
$title = 'OneID Connected - Secure Government Services';
?>
<?php require __DIR__ . '/partials/head.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="page home-page">
    <?php if ($successMessage): ?>
        <div class="notification notification--success" style="padding: 1rem; margin: 1rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724;">
            <span class="notification__message"><?= htmlspecialchars($successMessage) ?></span>
        </div>
    <?php endif; ?>

    <section class="services" aria-label="Available services">
        <?php require __DIR__ . '/components/service-cards.php'; ?>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
