<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Доступ запрещён');
}

require_once __DIR__ . '/php/subd.php';

$type = $_GET['type'] ?? '';
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    exit('Некорректный ID');
}

switch ($type) {
    // ======== СКАЧИВАНИЕ ФАЙЛА ЗАДАЧИ ========
    case 'task':
        $stmt = $conn->prepare("SELECT file FROM zadachi WHERE id_zadch = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($file_name);
        $stmt->fetch();
        $stmt->close();

        if (!$file_name) {
            exit('Файл не найден в базе данных');
        }

        $file_path = __DIR__ . '/files_zadachi/' . basename($file_name);
        if (!file_exists($file_path)) {
            exit('Файл отсутствует на сервере');
        }

        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($file_name) . "\"");
        header("Content-Length: " . filesize($file_path));
        readfile($file_path);
        exit;


    // ======== СКАЧИВАНИЕ ФАЙЛА ЗАЯВКИ ========
    case 'zay':
        $stmt = $conn->prepare("SELECT file FROM zayavki WHERE id_zay = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($file_name);
        $stmt->fetch();
        $stmt->close();

        if (!$file_name) {
            exit('Файл не найден в базе данных');
        }

        $file_path = __DIR__ . '/files_zayavki/' . basename($file_name);
        if (!file_exists($file_path)) {
            exit('Файл отсутствует на сервере');
        }

        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($file_name) . "\"");
        header("Content-Length: " . filesize($file_path));
        readfile($file_path);
        exit;


    default:
        http_response_code(400);
        exit('Неверный тип загрузки');
}
