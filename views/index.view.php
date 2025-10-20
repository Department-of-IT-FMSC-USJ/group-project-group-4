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

    <section class="home-hero" aria-labelledby="hero-heading">
        <div class="home-hero__content">
            <h1 id="hero-heading">Access trusted government services in minutes</h1>
            <p class="home-hero__subtitle">Apply for your NIC, request birth certificate copies, and settle fines securely – all in one place.</p>
            <div class="home-hero__actions">
                
            </div>
        </div>
    </section>
    <br><br>

    <section id="services" class="services" aria-label="Available services">
        <?php require __DIR__ . '/components/service-cards.php'; ?>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
