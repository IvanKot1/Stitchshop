<?php
$pageTitle = 'Вход';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/profile.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = 'Введите email и пароль';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['user_name'] = $user['name'];

            if ($user['is_admin']) {
                header('Location: ' . SITE_URL . '/admin/index.php');
            } else {
                header('Location: ' . SITE_URL . '/profile.php');
            }
            exit;
        } else {
            $errors[] = 'Неверный email или пароль';
        }
    }
}
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="section auth-page">
    <div class="container">
        <div class="auth-form">
            <h2>Вход в систему</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-fields">
                <div class="form-group">
                    <label for="email">Почта</label>
                    <input type="email" name="email" id="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <button type="submit" class="auth-btn">Войти</button>
            </form>

            <p class="auth-switch">Нет аккаунт? <a href="<?= SITE_URL ?>/register.php" class="link">Зарегистрироваться</a></p>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>