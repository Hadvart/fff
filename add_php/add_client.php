<?php
require 'subd.php'; // Подключаем файл с БД

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Если данные не были переданы, ставим прочерк "-"
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '-';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '-';
    $middleName = isset($_POST['middleName']) ? trim($_POST['middleName']) : '-';
    $telegram = isset($_POST['telegram']) ? trim($_POST['telegram']) : '-';
    $organization = isset($_POST['organiz']) ? trim($_POST['organiz']) : '-';
    $mail = isset($_POST['mail']) ? trim($_POST['mail']) : '-';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '-';

    // Подготавливаем SQL-запрос
    $sql = "INSERT INTO clientsc (Familiya, Imya, Otchestv, telegramid, organizs, emailsc, phone) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("<script>alert('Ошибка запроса: " . $conn->error . "'); window.history.back();</script>");
    }

    $stmt->bind_param("sssssss", $firstName, $lastName, $middleName, $telegram, $organization, $mail, $phone);

    if ($stmt->execute()) {
        echo "<script>alert('Сотрудник успешно добавлен!'); window.history.back();</script>";
    } else {
        echo "<script>alert('Ошибка при добавлении: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
