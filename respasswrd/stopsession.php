<?php
// Стартуем сессию
session_start();

// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Подключаемся к базе данных
include 'subd.php'; // Подключение к файлу с конфигурацией БД

// Проверяем наличие данных в сессии
if (isset($_SESSION['user_id']) && isset($_SESSION['session_code'])) {
    $user_id = $_SESSION['user_id'];  // Получаем user_id из сессии
    $session_code = $_SESSION['session_code'];  // Получаем session_code из сессии
} else {
    // Если данные сессии отсутствуют, выводим ошибку или перенаправляем на страницу входа
    die('Ошибка: сессия не найдена.');
}

// Подготавливаем запрос на проверку данных сессии в таблице vosstanovlenie
$query = "SELECT * FROM vosstanovlenie WHERE user_id = ? AND session = ?";

// Подготавливаем запрос
$stmt = $conn->prepare($query);

// Проверка на ошибку при подготовке
if ($stmt === false) {
    die('Ошибка подготовки запроса: ' . $conn->error); // Показываем подробную ошибку при проблемах с подготовкой
}

// Привязываем параметры
$stmt->bind_param('is', $user_id, $session_code); // i - целое число, s - строка

// Выполняем запрос
if (!$stmt->execute()) {
    die('Ошибка выполнения запроса: ' . $stmt->error); // Показываем ошибку при выполнении запроса
}

// Получаем результат
$result = $stmt->get_result();

// Проверяем, есть ли совпадение
if ($result->num_rows == 0) {
    // Если данных нет, выводим ошибку
    echo "<script>
            alert('Ошибка: данные не совпадают или сессия не найдена.');
            window.location.href = '/login.php';
          </script>";
    exit;
} else {
    // Если данные совпадают, выполняем DELETE запрос
    $deleteQuery = "DELETE FROM vosstanovlenie WHERE user_id = ? AND session = ?";

    // Подготавливаем запрос на удаление
    $deleteStmt = $conn->prepare($deleteQuery);

    // Проверка на ошибку при подготовке запроса
    if ($deleteStmt === false) {
        die('Ошибка подготовки запроса на удаление: ' . $conn->error);
    }

    // Привязываем параметры
    $deleteStmt->bind_param('is', $user_id, $session_code);

    // Выполняем запрос на удаление
    if (!$deleteStmt->execute()) {
        die('Ошибка выполнения запроса на удаление: ' . $deleteStmt->error);
    }

    // Уничтожаем все данные сессии
    session_unset();  // Удаляем все данные сессии
    session_destroy();  // Уничтожаем саму сессию

    // Перенаправляем пользователя на страницу входа
    echo "<script>
            window.location.href = '/login';
          </script>";
    exit;
}

// Закрываем соединение с базой данных
$stmt->close();
$deleteStmt->close();
$conn->close();
?>
