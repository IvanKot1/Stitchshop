<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проверка конфигурации - СтичШоп</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .check {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 4px solid #ccc;
        }
        .check.success {
            border-left-color: #28a745;
        }
        .check.error {
            border-left-color: #dc3545;
        }
        .check.warning {
            border-left-color: #ffc107;
        }
        h1 {
            color: #333;
        }
        .status {
            font-weight: bold;
        }
        .success .status {
            color: #28a745;
        }
        .error .status {
            color: #dc3545;
        }
        .warning .status {
            color: #ffc107;
        }
        pre {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔧 Проверка конфигурации СтичШоп</h1>
    
    <?php
    // Проверка PHP версии
    echo '<div class="check ' . (version_compare(PHP_VERSION, '7.4', '>=') ? 'success' : 'error') . '">';
    echo '<strong>Версия PHP:</strong> <span class="status">' . PHP_VERSION . '</span><br>';
    echo version_compare(PHP_VERSION, '7.4', '>=') ? '✅ Требуемая версия (7.4+)' : '❌ Требуется PHP 7.4+';
    echo '</div>';
    
    // Проверка расширений
    $extensions = ['pdo', 'pdo_mysql', 'json', 'gd'];
    foreach ($extensions as $ext) {
        $loaded = extension_loaded($ext);
        echo '<div class="check ' . ($loaded ? 'success' : 'error') . '">';
        echo '<strong>Расширение ' . $ext . ':</strong> <span class="status">' . ($loaded ? 'Загружено' : 'Не загружено') . '</span>';
        echo '</div>';
    }
    
    // Проверка прав на запись
    $uploadDir = __DIR__ . '/uploads/products/';
    $writable = is_writable($uploadDir);
    echo '<div class="check ' . ($writable ? 'success' : 'error') . '">';
    echo '<strong>Папка uploads/products/:</strong> <span class="status">' . ($writable ? 'Доступна для записи' : 'Нет прав на запись') . '</span>';
    echo '</div>';
    
    // Проверка подключения к БД
    require_once __DIR__ . '/includes/config.php';
    
    try {
        $testConn = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS);
        echo '<div class="check success">';
        echo '<strong>Подключение к MySQL:</strong> <span class="status">✅ Успешно</span>';
        echo '</div>';
        
        // Проверка существования БД
        $testConn->exec("USE " . DB_NAME);
        echo '<div class="check success">';
        echo '<strong>База данных ' . DB_NAME . ':</strong> <span class="status">✅ Доступна</span>';
        echo '</div>';
        
        // Проверка таблиц
        $tables = ['users', 'categories', 'products', 'orders', 'order_items', 'reviews'];
        foreach ($tables as $table) {
            $stmt = $testConn->query("SHOW TABLES LIKE '$table'");
            $exists = $stmt->rowCount() > 0;
            echo '<div class="check ' . ($exists ? 'success' : 'warning') . '">';
            echo '<strong>Таблица ' . $table . ':</strong> <span class="status">' . ($exists ? '✅ Существует' : '⚠️ Не создана') . '</span>';
            echo '</div>';
        }
        
    } catch(PDOException $e) {
        echo '<div class="check error">';
        echo '<strong>Подключение к MySQL:</strong> <span class="status">❌ Ошибка: ' . $e->getMessage() . '</span>';
        echo '</div>';
    }
    
    // Информация о сервере
    echo '<h2>ℹ️ Информация о сервере</h2>';
    echo '<div class="check">';
    echo '<pre>';
    echo 'Server Software: ' . $_SERVER['SERVER_SOFTWARE'] . "\n";
    echo 'Document Root: ' . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo 'Script Path: ' . __FILE__ . "\n";
    echo 'Upload Max Filesize: ' . ini_get('upload_max_filesize') . "\n";
    echo 'Post Max Size: ' . ini_get('post_max_size') . "\n";
    echo '</pre>';
    echo '</div>';
    
    echo '<div style="text-align: center; margin-top: 30px;">';
    echo '<a href="index.php" style="background: #667eea; color: white; padding: 15px 30px; border-radius: 5px; text-decoration: none;">🏠 Перейти на сайт</a>';
    echo '</div>';
    ?>
</body>
</html>
