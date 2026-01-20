<?php
include('subd.php'); // Подключаем БД

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]); // Безопасное преобразование ID в число

    $sql = "DELETE FROM userscr WHERE id_use = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Ошибка: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Некорректный запрос.";
}
?>
