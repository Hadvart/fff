<?php
include "php/subd.php"; // mysqli подключение

// Получение данных для выпадающих списков
$sotrudniki = mysqli_query($conn, "SELECT id_use, Familiya, Imya, Otchestvo FROM userscr");
$clients    = mysqli_query($conn, "SELECT id_cli, Familiya, Imya, Otchestv FROM clientsc");
$statusy    = mysqli_query($conn, "SELECT id_stat, name_stat FROM status");

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_id = $_POST['status_id'] ?? '';
    $cli_id    = $_POST['cli_id'] ?? '';
    $sot_id    = $_POST['sot_id'] ?? '';
    $istoch    = $_POST['istoch'] ?? '';
    $soderzh   = $_POST['soderzh'] ?? '';
    $dedlain   = $_POST['dedlain'] ?? '';

    // файл
    $fileData = null;
    if (!empty($_FILES['file']['tmp_name'])) {
        $fileData = addslashes(file_get_contents($_FILES['file']['tmp_name']));
    }

    $sql = "INSERT INTO zayavki (date_reg, status_id, cli_id, sot_id, istoch, soderzh, dedlain, file)
            VALUES (NOW(), '$status_id', '$cli_id', '$sot_id', '$istoch', '$soderzh', '$dedlain', " . ($fileData ? "'$fileData'" : "NULL") . ")";
    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Заявка успешно добавлена!";
    } else {
        $msg = "❌ Ошибка: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- иконки / filepond -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

    <!-- твой общий стиль/скрипт -->
    <link rel="stylesheet" href="Styles/applicationstyleCREATE.css" />
    <script src="Scripts/script.js"></script>

    <!-- Локальные правки ширины и вида input[type=date] только для этой страницы -->
    <style>
      .page-create-request .main-content{max-width:none;width:100%}
      .page-create-request .main-content form{width:100%;align-items:stretch}
      .page-create-request .info-container,
      .page-create-request .comment-container,
      .page-create-request .file-upload-container{width:100%;box-sizing:border-box}
      .page-create-request .info-column{flex:1 1 0;min-width:0}
      .page-create-request .info-item{grid-template-columns:220px 1fr}
      .page-create-request .info-edit,
      .page-create-request select.info-edit,
      .page-create-request textarea.info-edit{width:100%!important;max-width:none!important;box-sizing:border-box}
      .page-create-request select{width:100%!important;max-width:none!important}

      /* Дата как остальные поля */
      .page-create-request input[type="date"].info-edit{
        background:#f9f9f9;padding:12px 30px;border-radius:30px;margin-bottom:15px;
        border:1px solid #ddd;width:100%;box-sizing:border-box;color:#000;font-size:14px;
        font-family:inherit;appearance:none;-webkit-appearance:none;-moz-appearance:none;cursor:pointer;
      }
      body.dark-theme .page-create-request input[type="date"].info-edit{
        background:#333;color:#fff;border:1px solid #444;
      }
      .page-create-request input[type="date"].info-edit::-webkit-calendar-picker-indicator{
        cursor:pointer;filter:invert(50%);margin-right:5px;
      }
    </style>

    <title>NG-CRM — Новая заявка</title>
  </head>

  <body class="page-create-request">
    <div class="container">
      <!-- Сайдбар -->
      <aside class="sidebar">
        <div class="logo-container"><img src="images/nglogo_light.png" alt="Logo" /></div>
        <nav>
          <ul>
            <li><a href="employees"><i class="fa-solid fa-user-tie"></i></a></li>
            <li><a href="clients"><i class="fa-solid fa-users"></i></a></li>
            <li class="active"><i class="fa-solid fa-file"></i></li>
            <li><a href="zadachi"><i class="fa-solid fa-id-card"></i></a></li>
            <li><a href="kalend"><i class="fa-solid fa-calendar"></i></a></li>
            <li><a href="analyt"><i class="fa-solid fa-line-chart"></i></a></li>
          </ul>
        </nav>
        <div class="exit" onclick="logout()"><i class="fa-solid fa-right-from-bracket"></i></div>
        <script>function logout(){window.location.href="php/logout.php";}</script>
      </aside>

      <div class="overlay"></div>
      <div class="sidebar-toggle" id="sidebarToggle">
        <i class="fa-regular fa-square-caret-right"></i>
      </div>

      <!-- Контент -->
      <div class="main-content">
        <header class="header">
          <div class="header-top">
            <h1>Новая заявка</h1>
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
          </div>
        </header>

        <?php if(!empty($msg)): ?>
          <div class="info-container" style="margin-top:0">
            <div class="info-item" style="width:100%">
              <span class="info-label"></span>
              <div class="info-edit" style="background:transparent;border:none;color:#ccc;padding:0">
                <?= htmlspecialchars($msg) ?>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- ФОРМА -->
        <form method="post" enctype="multipart/form-data">
          <div class="info-container">
            <!-- Левая колонка -->
            <div class="info-column">
              <div class="info-item">
                <span class="info-label">Статус:</span>
                <select class="info-edit" name="status_id" required>
                  <?php while($s = mysqli_fetch_assoc($statusy)): ?>
                    <option value="<?= $s['id_stat'] ?>"><?= htmlspecialchars($s['name_stat']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="info-item">
                <span class="info-label">Клиент:</span>
                <select class="info-edit" name="cli_id" required>
                  <?php while($c = mysqli_fetch_assoc($clients)): ?>
                    <option value="<?= $c['id_cli'] ?>">
                      <?= htmlspecialchars($c['Familiya']." ".$c['Imya']." ".$c['Otchestv']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="info-item">
                <span class="info-label">Сотрудник:</span>
                <select class="info-edit" name="sot_id" required>
                  <?php while($u = mysqli_fetch_assoc($sotrudniki)): ?>
                    <option value="<?= $u['id_use'] ?>">
                      <?= htmlspecialchars($u['Familiya']." ".$u['Imya']." ".$u['Otchestvo']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="info-item">
                <span class="info-label">Дата регистрации:</span>
                <input type="text" class="info-edit" value="<?= date('d.m.Y H:i') ?>" disabled>
              </div>

              <div class="info-item">
                <span class="info-label">Дедлайн:</span>
                <input type="date" class="info-edit" name="dedlain" required>
              </div>
            </div>

            <!-- Правая колонка -->
            <div class="info-column">
              <div class="info-item">
                <span class="info-label">Источник:</span>
                <input type="text" class="info-edit" name="istoch" placeholder="">
              </div>

              <div class="info-item" style="align-items:start">
                <span class="info-label">Содержание:</span>
                <textarea class="info-edit" name="soderzh" rows="6" placeholder=""></textarea>
              </div>
            </div>
          </div>

          <div class="comment-container">
            <div class="info-item">
              <span class="info-label">Комментарий:</span>
            </div>
            <!-- Если нужно хранить отдельно — добавь новое поле в БД. Сейчас это просто визуальный блок -->
            <textarea class="info-edit" rows="4" placeholder="Доп. информация…" oninput="// при желании можно синхронизировать с содержанием"></textarea>
          </div>

          <div class="file-upload-container">
            <h3>Загрузить файлы</h3>
            <!-- name="file" — важно для PHP -->
            <input type="file" class="filepond" name="file" />
          </div>

          <div class="save-button">
            <button type="submit" style="
              background-color:#02af75;color:white;border:none;border-radius:32px;cursor:pointer;
              padding:15px;font-size:18px;font-weight:bold;position:static;height:70px;
              box-shadow:0 0 15px rgba(2,175,117,.7);transition:box-shadow .3s ease, background-color .3s ease;opacity:1;">
              Добавить заявку
            </button>
          </div>
        </form>

        <!-- FilePond (храним как обычный <input type="file">) -->
        <script src="https://cdn.jsdelivr.net/npm/filepond@4.24.0/dist/filepond.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.24.0/dist/filepond-plugin-image-preview.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.0/dist/filepond-plugin-file-validate-type.min.js"></script>
        <script>
          FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);
          const pond = FilePond.create(document.querySelector('input[type="file"].filepond'), {
            allowMultiple: false,
            acceptedFileTypes: ["image/*","application/pdf","text/*"],
            labelIdle: 'Нажмите или перетащите для загрузки файлов',
            storeAsFile: true // файл попадёт в $_FILES['file'], как и в исходной логике
          });
        </script>
      </div>
    </div>
  </body>
</html>
