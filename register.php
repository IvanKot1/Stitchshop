<?php
$pageTitle = 'Регистрация';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/profile.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($passwordConfirm)) {
        $errors[] = 'Заполните все поля';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Неверный формат email';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен быть не менее 6 символов';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Пароли не совпадают';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Email уже зарегистрирован';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $hashedPassword]);

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: ' . SITE_URL . '/profile.php');
            exit;
        }
    }
}
?>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="section auth-page">
    <div class="container">
        <div class="auth-form">
            <h2>Регистрация</h2>

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
                    <label for="name">Имя</label>
                    <input type="text" name="name" id="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Почта</label>
                    <input type="email" name="email" id="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Повторить пароль</label>
                    <input type="password" name="password_confirm" id="password_confirm" required>
                </div>

                <div class="form-group terms-group">
                    <label class="terms-label">
                        <input type="checkbox" name="terms" required>
                        <span class="terms-checkbox"></span>
                        <span class="terms-text">Я прочитал <a href="#">Политику обработки персональных данных</a> и согласен с условиями.</span>
                    </label>
                </div>

                <button type="submit" class="auth-btn">Зарегистрироваться</button>
            </form>

            <p class="auth-switch">Уже есть аккаунт? <a href="<?= SITE_URL ?>/login.php" class="link">Войти</a></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>