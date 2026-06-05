<?php
require_once __DIR__ . '/functions.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$cssVersion = file_exists(__DIR__ . '/../css/style.css') ? filemtime(__DIR__ . '/../css/style.css') : time();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css?v=<?= $cssVersion ?>">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?= SITE_URL ?>/index.php" class="logo" aria-label="На главную">
                    <img src="<?= SITE_URL ?>/img/logo.png" alt="<?= SITE_NAME ?>" class="logo-image">
                </a>

                <form action="<?= SITE_URL ?>/catalog.php" method="GET" class="search-box search-box-mobile">
                    <button type="submit" class="search-submit" aria-label="Найти">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <line x1="16.65" y1="16.65" x2="21" y2="21"></line>
                        </svg>
                    </button>
                    <input type="text" name="search" placeholder="Поиск" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </form>

                <button class="mobile-menu-toggle" aria-label="Меню">
                    <img src="<?= SITE_URL ?>/img/burger.png" alt="Меню" class="burger-icon">
                </button>

                <nav class="nav" aria-label="Основная навигация">
                    <a href="<?= SITE_URL ?>/index.php" class="nav-link <?= $currentPage == 'index' ? 'active' : '' ?>">Главная</a>
                    <a href="<?= SITE_URL ?>/catalog.php" class="nav-link <?= $currentPage == 'catalog' ? 'active' : '' ?>">Каталог</a>
                    <?php if (isAdmin()): ?>
                        <a href="<?= SITE_URL ?>/admin/index.php" class="nav-link nav-link-admin <?= $currentPage == 'admin' ? 'active' : '' ?>">Админ</a>
                    <?php endif; ?>
                    
                    <!-- Мобильные кнопки (видны только на мобильных) -->
                    <div class="nav-mobile-items">
                        <?php if (isLoggedIn()): ?>
                            <a href="<?= SITE_URL ?>/profile.php" class="nav-link">
                                <img src="<?= SITE_URL ?>/img/prof.png" alt="Профиль" class="nav-mobile-icon"> Личный кабинет
                            </a>
                            <a href="<?= SITE_URL ?>/cart.php" class="nav-link">
                                <img src="<?= SITE_URL ?>/img/trash.png" alt="Корзина" class="nav-mobile-icon"> Корзина
                                <?php if (getCartTotalItems() > 0): ?>
                                    <span class="mobile-cart-badge"><?= getCartTotalItems() ?></span>
                                <?php endif; ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= SITE_URL ?>/login.php" class="nav-link">🔐 Вход</a>
                        <?php endif; ?>
                    </div>
                </nav>

                <div class="header-actions">
                    <form action="<?= SITE_URL ?>/catalog.php" method="GET" class="search-box search-box-desktop">
                        <button type="submit" class="search-submit" aria-label="Найти">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"></circle>
                                <line x1="16.65" y1="16.65" x2="21" y2="21"></line>
                            </svg>
                        </button>
                        <input type="text" name="search" placeholder="Поиск" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </form>

                    <a href="<?= SITE_URL ?>/cart.php" class="circle-action" aria-label="Корзина">
                        <img src="<?= SITE_URL ?>/img/trash.png" alt="" class="act">
                        <?php if (isLoggedIn() && getCartTotalItems() > 0): ?>
                            <span class="cart-badge"><?= getCartTotalItems() ?></span>
                        <?php endif; ?>
                    </a>

                    <?php if (isLoggedIn()): ?>
                        <a href="<?= SITE_URL ?>/profile.php" class="circle-action <?= $currentPage == 'profile' ? 'active' : '' ?>" aria-label="Профиль">
                            <img src="<?= SITE_URL ?>/img/prof.png" alt="" class="act">
                        </a>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/login.php" class="circle-action <?= $currentPage == 'login' ? 'active' : '' ?>" aria-label="Войти">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M4 20c2.1-3.6 5-5 8-5s5.9 1.4 8 5"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main>
