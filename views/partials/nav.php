<?php
// Helper function to determine active nav item
function isActive($page)
{
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $page ? 'active' : '';
}

function ariaCurrent($page)
{
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $page ? ' aria-current="page"' : '';
}
?>

<header class="main-header">
    <div class="top-bar"></div>
    <nav class="main-nav" data-nav>
        <a href="/index.php" class="logo-header">
            <img src="assets/logo.png" alt="OneID Logo" width="50" height="50">
            <p class="logo-text">OneID</p>
        </a>

        <button class="nav-toggle" type="button" aria-controls="primary-navigation" aria-expanded="false" data-nav-toggle>
            <span class="nav-toggle__bar" aria-hidden="true"></span>
            <span class="nav-toggle__bar" aria-hidden="true"></span>
            <span class="nav-toggle__bar" aria-hidden="true"></span>
            <span class="nav-toggle__label">Menu</span>
        </button>

        <div class="main-nav__menu" id="primary-navigation" data-nav-menu data-visible="false">
            <ul>
                <li>
                    <a href="/index.php" class="nav-link <?= isActive('index.php') ?>" <?= ariaCurrent('index.php') ?>>Home</a>
                </li>
                <li>
                    <a href="/nic.php" class="nav-link <?= isActive('nic.php') ?>" <?= ariaCurrent('nic.php') ?>>NIC</a>
                </li>
                <li>
                    <a href="/birthcertificate.php" class="nav-link <?= isActive('birthcertificate.php') ?>" <?= ariaCurrent('birthcertificate.php') ?>>Birth Certificate</a>
                </li>
                <li>
                    <a href="/fines.php" class="nav-link <?= isActive('fines.php') ?>" <?= ariaCurrent('fines.php') ?>>Fines</a>
                </li>
                <li>
                    <a href="/about.php" class="nav-link <?= isActive('about.php') ?>" <?= ariaCurrent('about.php') ?>>About</a>
                </li>
                <li>
                    <a href="/contact.php" class="nav-link <?= isActive('contact.php') ?>" <?= ariaCurrent('contact.php') ?>>Contact</a>
                </li>
            </ul>
        </div>
    </nav>
</header>