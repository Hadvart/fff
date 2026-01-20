<?php
session_start();
include('subd.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['dolzhid'])) {
    http_response_code(403);
    echo "Доступ запрещен";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $familiya = trim($_POST['lastName']);
    $imya = trim($_POST['firstName']);
    $otchestvo = trim($_POST['middleName']);
    $otdel = intval($_POST['otdel']);
    $dolzh = intval($_POST['dolzh']);
    $telegramID = trim($_POST['telegramID']);
    $mailc = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = intval($_POST['role']);

    $stmt = $conn->prepare("UPDATE userscr SET Familiya=?, Imya=?, Otchestvo=?, otdel_id=?, dolzh_id=?, telegramID=?, mailc=?, phone=?, admin=? WHERE id_use=?");
    $stmt->bind_param("sssiiissii", $familiya, $imya, $otchestvo, $otdel, $dolzh, $telegramID, $mailc, $phone, $role, $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Ошибка: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo "Метод не разрешен";
}
