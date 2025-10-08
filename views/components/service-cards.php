<?php
$services = [
    [
        'id' => 'birth',
        'title' => 'Birth Certificate',
        'description' => 'Request official copies of your birth certificate quickly and securely.',
        'cta' => 'Get Birth Certificate',
        'href' => '/birthcertificate.php',
    ],
    [
        'id' => 'nic',
        'title' => 'National ID',
        'description' => 'Apply for a replacement National Identity Card and track your request.',
        'cta' => 'Apply for NIC',
        'href' => '/nic.php',
    ],
    [
        'id' => 'fines',
        'title' => 'Pay Fines',
        'description' => 'Review outstanding fines and complete your payment online.',
        'cta' => 'Pay Fines',
        'href' => '/fines.php',
    ],
];
?>

<div class="services-grid" role="list">
    <?php foreach ($services as $service): ?>
        <article class="service-card" data-service-id="<?= htmlspecialchars($service['id']) ?>" role="listitem">
            <div class="service-card__media" aria-hidden="true">Image</div>
            <div class="service-card__content">
                <h3><?= htmlspecialchars($service['title']) ?></h3>
                <p><?= htmlspecialchars($service['description']) ?></p>
                <a class="btn" href="<?= htmlspecialchars($service['href']) ?>"><?= htmlspecialchars($service['cta']) ?></a>
            </div>
        </article>
    <?php endforeach; ?>
</div>