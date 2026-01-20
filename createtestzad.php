<?php
include "php/subd.php"; // mysqli подключение

// Получение данных для списков
$sotrudniki = mysqli_query($conn, "SELECT id_use, Familiya, Imya, Otchestvo FROM userscr");
$clients    = mysqli_query($conn, "SELECT id_cli, Familiya, Imya, Otchestv FROM clientsc");
$statusy    = mysqli_query($conn, "SELECT id_stat, name_stat FROM status");
$zayavki    = mysqli_query($conn, "SELECT id_zay, soderzh FROM zayavki");

// Обработка формы
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Берём поля как раньше
    $nazv        = $_POST['nazv'] ?? '';
    $status_id   = $_POST['status_id'] ?? '';
    $opisanie    = $_POST['opisanie'] ?? '';
    $iniciat_id  = $_POST['iniciat_id'] ?? '';
    $ispolnit_id = $_POST['ispolnit_id'] ?? '';
    $data_dedl   = $_POST['data_dedl'] ?? '';
    $zayav_id    = !empty($_POST['zayav_id']) ? $_POST['zayav_id'] : "NULL";
    $client_id   = $_POST['client_id'] ?? '';

    // файл как раньше
    $fileData = null;
    if (!empty($_FILES['file']['tmp_name'])) {
        $fileData = addslashes(file_get_contents($_FILES['file']['tmp_name']));
    }

    $sql = "INSERT INTO zadachi (nazv, status_id, opisanie, iniciat_id, ispolnit_id, file, data_naz, data_dedl, zayav_id, client_id)
            VALUES ('$nazv', '$status_id', '$opisanie', '$iniciat_id', '$ispolnit_id', " . ($fileData ? "'$fileData'" : "NULL") . ", NOW(), '$data_dedl', $zayav_id, '$client_id')";

    if (mysqli_query($conn, $sql)) {
        $msg = "✅ Задача успешно добавлена!";
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
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet"/>
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet"/>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

    <!-- твои стили/скрипты как в zadachacreate -->
    <link rel="stylesheet" href="Styles/applicationstyleCREATE.css" />
    <script src="Scripts/script.js"></script>
    <title>NG-CRM - Новая задача</title>
  </head>

  <body>
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
            <h1>Новая задача</h1>
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

        <!-- ФОРМА со всеми полями и теми же классами, что в zadachacreate -->
        <form method="post" enctype="multipart/form-data">
          <div class="info-container">
            <!-- Левый столбец -->
            <div class="info-column">
              <div class="info-item">
                <span class="info-label">Название:</span>
                <input type="text" class="info-edit" name="nazv" id="nazv" required>
              </div>

              <div class="info-item">
                <span class="info-label">Инициатор:</span>
                <select class="info-edit" name="iniciat_id" id="iniciat_id" required>
                  <?php mysqli_data_seek($sotrudniki, 0); while($u = mysqli_fetch_assoc($sotrudniki)): ?>
                    <option value="<?= $u['id_use'] ?>">
                      <?= htmlspecialchars($u['Familiya']." ".$u['Imya']." ".$u['Otchestvo']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="info-item">
                <span class="info-label">Исполнитель:</span>
                <select class="info-edit" name="ispolnit_id" id="ispolnit_id" required>
                  <?php mysqli_data_seek($sotrudniki, 0); while($u = mysqli_fetch_assoc($sotrudniki)): ?>
                    <option value="<?= $u['id_use'] ?>">
                      <?= htmlspecialchars($u['Familiya']." ".$u['Imya']." ".$u['Otchestvo']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="info-item">
                <span class="info-label">Дата назначения:</span>
                <input type="text" class="info-edit" value="<?= date('d.m.Y H:i') ?>" disabled>
              </div>

              <div class="info-item">
                <span class="info-label">Дата дедлайна:</span>
                <input type="date" class="info-edit" name="data_dedl" id="data_dedl" required>
              </div>
            </div>

            <!-- Правый столбец -->
            <div class="info-column">
              <div class="info-item">
                <span class="info-label">Статус:</span>
                <select class="info-edit" name="status_id" id="status_id" required>
                  <?php while($s = mysqli_fetch_assoc($statusy)): ?>
                    <option value="<?= $s['id_stat'] ?>"><?= htmlspecialchars($s['name_stat']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="info-item">
                <span class="info-label">Клиент:</span>
                <select class="info-edit" name="client_id" id="client_id" required>
                  <?php while($c = mysqli_fetch_assoc($clients)): ?>
                    <option value="<?= $c['id_cli'] ?>">
                      <?= htmlspecialchars($c['Familiya']." ".$c['Imya']." ".$c['Otchestv']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="info-item">
                <span class="info-label">Заявка (если связана):</span>
                <select class="info-edit" name="zayav_id" id="zayav_id">
                  <option value="">---</option>
                  <?php while($z = mysqli_fetch_assoc($zayavki)): ?>
                    <option value="<?= $z['id_zay'] ?>">
                      <?= "№".$z['id_zay']." - ".htmlspecialchars($z['soderzh']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="info-item">
                <span class="info-label">Описание:</span>
                <textarea class="info-edit" name="opisanie" id="opisanie" rows="4" placeholder=""></textarea>
              </div>
            </div>
          </div>

          <div class="comment-container">
            <div class="info-item">
              <span class="info-label">Комментарий:</span>
            </div>
            <!-- При желании можно писать тот же "описание", чтобы не плодить поля -->
            <textarea class="info-edit" id="commentMirror" oninput="document.getElementById('opisanie').value=this.value;"></textarea>
          </div>

          <div class="file-upload-container">
            <h3>Загрузить файлы</h3>
            <!-- ВАЖНО: name="file" как в исходнике -->
            <input type="file" class="filepond" name="file" />
          </div>

          <div class="save-button">
            <button type="submit" style="
              background-color:#02af75;color:#fff;border:none;border-radius:32px;
              cursor:pointer;padding:15px;font-size:18px;font-weight:bold;
              position:static;height:70px;box-shadow:0 0 15px rgba(2,175,117,.7);
              transition:box-shadow .3s ease, background-color .3s ease;opacity:1;">
              Создать задачу
            </button>
          </div>
        </form>

        <!-- FilePond -->
        <script src="https://cdn.jsdelivr.net/npm/filepond@4.24.0/dist/filepond.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.24.0/dist/filepond-plugin-image-preview.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.0/dist/filepond-plugin-file-validate-type.min.js"></script>
        <script>
          FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);
          const inputElement = document.querySelector('input[type="file"].filepond');
          FilePond.create(inputElement, {
            allowMultiple: false,
            acceptedFileTypes: ["image/*","application/pdf","text/*"],
            labelIdle: 'Нажмите или перетащите для загрузки файлов',
            storeAsFile: true // ключ: отправляем как обычный <input type="file" name="file">
          });
        </script>
      </div>
    </div>
  </body>
</html>
