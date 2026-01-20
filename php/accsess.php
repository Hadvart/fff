<?php
require_once 'subd.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Запрос к базе
    $stmt = $conn->prepare("SELECT id_use, mailc, passwd, dolzh_id, admin FROM userscr WHERE mailc = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Проверка пароля
        if (password_verify($password, $user['passwd'])) {
            // Успешный вход - пишем сессию
            $_SESSION['user_id']  = $user['id_use'];
            $_SESSION['username'] = $user['mailc'];
            $_SESSION['dolzhid']  = $user['dolzh_id'];
            $_SESSION['rolea']    = $user['admin'];

            // РЕДИРЕКТ (Обратите внимание на две точки в начале пути)
            header("Location: ../applications.php"); 
            exit();
        } else {
            $_SESSION['error'] = "Неверный пароль!";
        }
    } else {
        $_SESSION['error'] = "Пользователь не найден!";
    }

    $stmt->close();
    $conn->close();

    // Если ошибка - назад на логин
    header("Location: ../login.php");
    exit();
}
?>