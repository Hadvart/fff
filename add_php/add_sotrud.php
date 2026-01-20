<?php
// Подключаем файл для работы с базой данных
require_once('subd.php');

// Функция для генерации пароля
function generatePassword($length = 16) {
    return bin2hex(random_bytes($length / 2));
}

// Проверка, что форма была отправлена методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы с фильтрацией для предотвращения XSS атак
    $formData = filter_input_array(INPUT_POST, [
        'lastName' => FILTER_SANITIZE_STRING,
        'firstName' => FILTER_SANITIZE_STRING,
        'middleName' => FILTER_SANITIZE_STRING,
        'email' => FILTER_SANITIZE_EMAIL,
        'telephone' => FILTER_SANITIZE_STRING,
        'telegramid' => FILTER_SANITIZE_STRING,
        'otdel' => FILTER_VALIDATE_INT,
        'dolznost' => FILTER_VALIDATE_INT,
        'role' => FILTER_SANITIZE_STRING,
    ]);

    // Проверка на пустые поля
    if (in_array(false, $formData, true) || in_array(null, $formData, true)) {
        echo "<script>alert('Пожалуйста, заполните все поля.'); window.history.back();</script>";
        exit();
    }

    // Генерация случайного пароля
    $password = generatePassword();

    // Подготовка SQL-запроса для вставки данных
    $query = "
        INSERT INTO userscr (Familiya, Imya, Otchestvo, phone, otdel_id, dolzh_id, telegramID, mailc, admin, passwd) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    // Подготовленный запрос с привязкой параметров
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param(
            'ssssiiisss',
            $formData['lastName'],
            $formData['firstName'],
            $formData['middleName'],
            $formData['telephone'],
            $formData['otdel'],
            $formData['dolznost'],
            $formData['telegramid'],
            $formData['email'],
            $formData['role'],
            $password
        );

        // Выполнение запроса и проверка результата
        if ($stmt->execute()) {
			echo "<script>alert('Сотрудник добавлен успешно!'); window.history.back(); location.reload();</script>";
        } else {
            echo "<script>alert('Ошибка при добавлении сотрудника: " . $stmt->error . "'); window.history.back();</script>";
        }

        // Закрытие подготовленного запроса
        $stmt->close();
    } else {
        echo "<script>alert('Ошибка подготовки запроса'); window.history.back();</script>";
    }

    // Закрытие соединения с базой данных
    $conn->close();
}
?>
