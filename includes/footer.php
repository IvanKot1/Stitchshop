    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content footer-layout">
                <div class="footer-section footer-contact">
                    <img src="<?= SITE_URL ?>/img/logo.png" alt="<?= SITE_NAME ?>" class="logo-image footer-logo">
                    <h3 class="footer-contacts-title">Контакты</h3>
                    <p>Адрес: г. Москва, ул. Игрушечная, д 32</p>
                    <p>Телефон: +7 (927) 070-0887</p>
                    <p>Почта: stichShop@gmail.com</p>
                </div>

                <div class="footer-section footer-menu">
                    <h3 class="footer-menu-title">Меню</h3>
                    <p><a href="<?= SITE_URL ?>/index.php">Главная</a></p>
                    <p><a href="<?= SITE_URL ?>/catalog.php">Каталог</a></p>
                    <?php if (isLoggedIn()): ?>
                        <p><a href="<?= SITE_URL ?>/profile.php">Личный кабинет</a></p>
                    <?php else: ?>
                        <p><a href="<?= SITE_URL ?>/login.php">Вход</a></p>
                    <?php endif; ?>
                </div>

                <div class="footer-section footer-right">
                    <div class="footer-policy">
                        <h3 class="footer-policy-title">Политика конфиденциальности</h3>
                    </div>

                    <div class="footer-social">
                        <h3 class="footer-social-title">Соцсети</h3>
                        <div class="social-links">
                            <a href="#" aria-label="Telegram" class="social-link-tg">
                                <img src="<?= SITE_URL ?>/img/tgs.png" alt="Telegram">
                            </a>
                            <a href="#" aria-label="VK" class="social-link-vk">
                                <img src="<?= SITE_URL ?>/img/vks.png" alt="VK">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const SITE_URL = '<?= SITE_URL ?>';
        const USER_ID = <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>;
        const IS_ADMIN = <?= isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'true' : 'false' ?>;
    </script>
    <script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>