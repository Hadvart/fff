<?php
// Стартуем сессию
session_start();

// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверяем, передан ли пароль через POST
if (isset($_POST['password']) && !empty($_POST['password'])) {
    $newPassword = $_POST['password']; // Получаем новый пароль

    // Хешируем новый пароль
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Подключаемся к базе данных
    include 'subd.php'; // Подключаем конфигурацию БД

    // Проверяем, есть ли данные в сессии
    if (isset($_SESSION['user_id']) && isset($_SESSION['session_code'])) {
        $user_id = $_SESSION['user_id'];
        $session_code = $_SESSION['session_code'];

        // Подготавливаем запрос на проверку данных сессии в таблице vosstanovlenie
        $query = "SELECT * FROM vosstanovlenie WHERE user_id = ? AND session = ?";

        // Подготовка запроса
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die('Ошибка подготовки запроса: ' . $conn->error);
        }

        // Привязываем параметры
        $stmt->bind_param('is', $user_id, $session_code);

        // Выполняем запрос
        if (!$stmt->execute()) {
            die('Ошибка выполнения запроса: ' . $stmt->error);
        }

        // Получаем результат
        $result = $stmt->get_result();

        // Проверяем, есть ли совпадение
        if ($result->num_rows > 0) {
            // Если данные совпадают, обновляем пароль в таблице userscr
            $updateQuery = "UPDATE userscr SET passwd = ? WHERE id_use = ?";
            $updateStmt = $conn->prepare($updateQuery);

            if ($updateStmt === false) {
                die('Ошибка подготовки запроса на обновление: ' . $conn->error);
            }

            // Привязываем параметры для обновления
            $updateStmt->bind_param('si', $hashedPassword, $user_id);

            // Выполняем запрос на обновление пароля
            if ($updateStmt->execute()) {
                // Удаляем запись из таблицы vosstanovlenie
                $deleteQuery = "DELETE FROM vosstanovlenie WHERE user_id = ? AND session = ?";
                $deleteStmt = $conn->prepare($deleteQuery);

                if ($deleteStmt === false) {
                    die('Ошибка подготовки запроса на удаление: ' . $conn->error);
                }

                // Привязываем параметры для удаления
                $deleteStmt->bind_param('is', $user_id, $session_code);

                // Выполняем запрос на удаление
                if ($deleteStmt->execute()) {
                    // Очистка сессии после успешного удаления данных восстановления
                    session_unset();
                    session_destroy();

                    // Перенаправляем на страницу входа
                    echo "<script>
                            alert('Пароль успешно обновлен. Войдите в аккаунт.');
                            window.location.href = '/login.php';
                          </script>";
                    exit;
                } else {
                    die('Ошибка выполнения запроса на удаление записи.');
                }
            } else {
                die('Ошибка выполнения запроса на обновление пароля.');
            }
        } else {
            echo "<script>
                    alert('Ошибка: данные сессии не совпадают.');
                    window.location.href = '/login.php';
                  </script>";
            exit;
        }

        // Закрываем соединение с БД
        $stmt->close();
        $conn->close();
    } else {
        echo "<script>
                alert('Ошибка: сессия не найдена.');
                window.location.href = '/login.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('Ошибка: пароль не указан.');
            window.location.href = '/resetpas.php';
          </script>";
    exit;
}
?>
