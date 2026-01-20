<?php
session_start();
include('subd.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_zadch']) && isset($_POST['id_cli'])) {
        $id_zadch = intval($_POST['id_zadch']);
        $id_cli   = intval($_POST['id_cli']);

        // --- Сначала удаляем комментарии задачи ---
        $sql_comments = "DELETE FROM comments_zadach WHERE id_zad = ?";
        $stmt_comments = $conn->prepare($sql_comments);
        $stmt_comments->bind_param("i", $id_zadch);

        if (!$stmt_comments->execute()) {
            echo json_encode(["success" => false, "error" => "Ошибка удаления комментариев: " . $stmt_comments->error]);
            $stmt_comments->close();
            $conn->close();
            exit;
        }
        $stmt_comments->close();

        // --- Теперь удаляем задачу ---
        $sql_task = "DELETE FROM zadachi WHERE id_zadch = ?";
        $stmt_task = $conn->prepare($sql_task);
        $stmt_task->bind_param("i", $id_zadch);

        if ($stmt_task->execute()) {
            echo json_encode(["success" => true, "deleted_id" => $id_zadch]);
        } else {
            echo json_encode(["success" => false, "error" => "Ошибка удаления задачи: " . $stmt_task->error]);
        }

        $stmt_task->close();
        $conn->close();
    } else {
        echo json_encode(["success" => false, "error" => "Отсутствуют параметры"]);
    }
}
?>
