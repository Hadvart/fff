<?php
session_start();
include('subd.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_zay']) && isset($_POST['id_cli'])) {
        $id_zay = intval($_POST['id_zay']);
        $id_cli = intval($_POST['id_cli']);

        // Удаляем запись из таблицы заявок
        $sql = "DELETE FROM zayavki WHERE id_zay = ? AND cli_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_zay, $id_cli);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["success" => false, "error" => "Отсутствуют параметры"]);
    }
}
?>
