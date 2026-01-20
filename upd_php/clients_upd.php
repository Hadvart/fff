<?php
// подключение к базе данных
include('subd.php');

// Проверка наличия всех необходимых данных
if (isset($_POST['id'], $_POST['familiya'], $_POST['imya'], $_POST['otchestv'], $_POST['telegramid'], $_POST['organizs'], $_POST['emailsc'], $_POST['phone'])) {
  
  // Сохранение переданных данных в переменные
  $id = $_POST['id'];
  $familiya = $_POST['familiya'];
  $imya = $_POST['imya'];
  $otchestv = $_POST['otchestv'];
  $telegramid = $_POST['telegramid'];
  $organizs = $_POST['organizs'];
  $emailsc = $_POST['emailsc'];
  $phone = $_POST['phone'];

  // Подготовка SQL запроса для обновления данных клиента
  $sql = "UPDATE clientsc SET Familiya = ?, Imya = ?, Otchestv = ?, telegramid = ?, organizs = ?, emailsc = ?, phone = ? WHERE id_cli = ?";

  if ($stmt = $conn->prepare($sql)) {
    // Привязка параметров
    $stmt->bind_param("sssssssi", $familiya, $imya, $otchestv, $telegramid, $organizs, $emailsc, $phone, $id);

    // Выполнение запроса
    if ($stmt->execute()) {
      echo "success"; // Возвращаем успех, если данные обновлены
    } else {
      echo "Ошибка обновления данных: " . $stmt->error; // Возвращаем ошибку, если не удалось обновить
    }

    $stmt->close();
  } else {
    echo "Ошибка подготовки запроса: " . $conn->error; // Возвращаем ошибку, если запрос не удалось подготовить
  }

} else {
  echo "Не все данные были переданы."; // Возвращаем ошибку, если данные не переданы
}
?>
