<?php
$servername = "db"; // Укажите ваш сервер базы данных
$username = "infong7e_crm";    // Укажите ваше имя пользователя
$password = "HVnj4WNmSy5*";    // Укажите ваш пароль
$dbname = "infong7e_crm"; // Укажите вашу базу данных

// Создание соединения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
