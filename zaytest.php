<?php
// zaytest.php — просмотр заявки в макете, как у zadtest.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['dolzhid'])) {
  header("Location: login");
  exit();
}


include('php/subd.php'); // mysqli $conn
$conn->set_charset("utf8mb4");


$user_id = intval($_SESSION['user_id']);
$access_granted = false;

$zayavka_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$zayavka_id) {
  die("Некорректный ID заявки");
}


// Проверка по zayavki
$sql = "SELECT $zayavka_id FROM zayavki WHERE sot_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $access_granted = true;
}
$stmt->close();
$access_granted = false;
// Если не нашли — проверяем админку
if (!$access_granted) {
    $sql = "SELECT admin FROM userscr WHERE id_use = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($is_admins);
    if ($stmt->fetch() && $is_admins == 1) {
        $access_granted = true;
    }
    $stmt->close();
}

if (!$access_granted) {
   echo "<script>alert('Ошибка: доступ запрещен'); history.go(-1);</script>";
exit;

}



$user_id = (int) $_SESSION['user_id'];
$is_admin = in_array($user_id, [1, 2, 3], true); // список админов

// === Обработка POST ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  switch ($_POST['action']) {
    case 'upload_file': {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/files_zayavki/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["file"]["name"]);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("UPDATE zayavki SET file = ? WHERE id_zay = ?");
            $stmt->bind_param("si", $fileName, $zayavka_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    break;
}
    case 'update_status': {
      $new_status = (int) ($_POST['status_id'] ?? 0);
      $stmt = $conn->prepare("UPDATE zayavki SET status_id = ? WHERE id_zay = ?");
      $stmt->bind_param("ii", $new_status, $zayavka_id);
      $stmt->execute();
      $stmt->close();
      break;
    }

    case 'delegate': {
      $new_user = (int) ($_POST['new_user'] ?? 0);
      $stmt = $conn->prepare("UPDATE zayavki SET sot_id = ? WHERE id_zay = ?");
      $stmt->bind_param("ii", $new_user, $zayavka_id);
      $stmt->execute();
      $stmt->close();
      break;
    }

    case 'add_comment': {
      $comment = trim((string) ($_POST['comment'] ?? ''));
      if ($comment !== '') {
        $stmt = $conn->prepare("
          INSERT INTO comments (name_com, sot_id, zayav_id, created_at)
          VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("sii", $comment, $user_id, $zayavka_id);
        $stmt->execute();
        $stmt->close();
      }
      break;
    }

    case 'edit_comment': {
      $comment_id = (int) ($_POST['comment_id'] ?? 0);
      $comment_text = trim((string) ($_POST['comment'] ?? ''));
      if ($comment_text !== '' && $comment_id > 0) {
        if ($is_admin) {
          // Админ может редактировать существующий комментарий
          $stmt = $conn->prepare("
            UPDATE comments
            SET name_com = ?, created_at = NOW()
            WHERE id_com = ?
          ");
          $stmt->bind_param("si", $comment_text, $comment_id);
        } else {
          // Пользователь без прав редактирования создает новый комментарий
          $stmt = $conn->prepare("
            INSERT INTO comments (name_com, sot_id, zayav_id, created_at)
            VALUES (?, ?, ?, NOW())
          ");
          $stmt->bind_param("sii", $comment_text, $user_id, $zayavka_id);
        }
        $stmt->execute();
        $stmt->close();
      }
      break;
    }

    case 'delete_comment': {
      $comment_id = (int) ($_POST['comment_id'] ?? 0);
      if ($comment_id > 0) {
        if ($is_admin) {
          $stmt = $conn->prepare("DELETE FROM comments WHERE id_com = ?");
          $stmt->bind_param("i", $comment_id);
        } else {
          $stmt = $conn->prepare("DELETE FROM comments WHERE id_com = ? AND sot_id = ?");
          $stmt->bind_param("ii", $comment_id, $user_id);
        }
        $stmt->execute();
        $stmt->close();
      }
      break;
    }
    case 'delete_file': {
    // Берём имя файла из БД
    $stmt = $conn->prepare("SELECT file FROM zayavki WHERE id_zay = ?");
    $stmt->bind_param("i", $zayavka_id);
    $stmt->execute();
    $stmt->bind_result($currentFile);
    $stmt->fetch();
    $stmt->close();

    if ($currentFile) {
        $filePath = __DIR__ . "/files_zayavki/" . $currentFile;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Очищаем DB
        $stmt = $conn->prepare("UPDATE zayavki SET file = NULL WHERE id_zay = ?");
        $stmt->bind_param("i", $zayavka_id);
        $stmt->execute();
        $stmt->close();
    }
    break;
}
  }

  header("Location: zaytest?id=" . $zayavka_id);
  exit();
}

// === Получение данных заявки ===
$stmt = $conn->prepare("SELECT * FROM full_zayavki_view WHERE id_zay = ?");
$stmt->bind_param("i", $zayavka_id);
$stmt->execute();
$zayavka = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$zayavka) {
  die("Заявка не найдена");
}

// === Справочники ===
$statusy = $conn->query("SELECT id_stat, name_stat FROM status ORDER BY id_stat")->fetch_all(MYSQLI_ASSOC);
$users   = $conn->query("SELECT id_use, Familiya, Imya, Otchestvo FROM userscr ORDER BY Familiya")->fetch_all(MYSQLI_ASSOC);

// === Комментарии заявки ===
$stmt = $conn->prepare("
  SELECT c.id_com, c.name_com, c.sot_id, c.created_at,
         u.Familiya, u.Imya, u.Otchestvo
  FROM comments c
  LEFT JOIN userscr u ON c.sot_id = u.id_use
  WHERE c.zayav_id = ?
  ORDER BY c.id_com ASC
");
$stmt->bind_param("i", $zayavka_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// === Хелперы для экранирования/ФИО (как в zadtest) ===
function esc($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function fio($f, $i, $o) {
  return trim(esc($f) . ' ' . esc($i) . ' ' . esc($o));
}

// === Подготовка отображаемых полей под макет zadtest ===
$z_title_id   = (int)($zayavka['id_zay'] ?? 0);
$status_name  = $zayavka['status'] ?? '';

$client_fio   = fio($zayavka['client_familiya'] ?? '', $zayavka['client_imya'] ?? '', $zayavka['client_otchestvo'] ?? '');
$client_phone = $zayavka['client_phone'] ?? '';
$client_mail  = $zayavka['client_email'] ?? '';
$client_org   = $zayavka['client_organization'] ?? '';

$soderzh      = $zayavka['soderzh'] ?? '';
$date_reg     = $zayavka['date_reg'] ?? '';
$dedlain      = $zayavka['dedlain'] ?? '';
$has_file     = !empty($zayavka['file'] ?? '');

$otdel_name   = $zayavka['otdel_zayav_name'] ?? '';
$sotr_fio     = trim(($zayavka['sotrudnik_familiya'] ?? '') . ' ' . ($zayavka['sotrudnik_imya'] ?? '') . ' ' . ($zayavka['sotrudnik_otchestvo'] ?? ''));

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
  <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
  <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
  <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
  <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

  <!-- Единые стили приложения как в zadtest -->
  <link rel="stylesheet" href="Styles\applicationstyle.css" />
  <script src="Scripts\script.js"></script>

  <title>NG-CRM — Заявка №<?= $z_title_id ?></title>
</head>

<body>
  <div class="container">
    <!-- Боковая панель (как в zadtest) -->
    <aside class="sidebar">
      <div class="logo-container">
        <img id="logo" src="images/nglogo_light.png" alt="Logo" />
      </div>
      <nav>
        <ul>
          <li><a href="employees"><i class="fa-solid fa-user-tie"></i></a></li>
          <li><a href="clients"><i class="fa-solid fa-users"></i></a></li>
          <li class="active"><i class="fa-solid fa-file"></i></li> <!-- Заявки — активный пункт -->
          <li><a href="zadachi"><i class="fa-solid fa-id-card"></i></a></li>
          <li><a href="kalend"><i class="fa-solid fa-calendar"></i></a></li>
          <li><a href="analyt"><i class="fa-solid fa-line-chart"></i></a></li>
        </ul>
      </nav>
      <div class="exit" onclick="logout()">
        <i class="fa-solid fa-right-from-bracket"></i>
      </div>
      <script>
        function logout() { window.location.href = "php/logout.php"; }
      </script>
    </aside>

    <div class="overlay"></div>
    <div class="sidebar-toggle" id="sidebarToggle">
      <i class="fa-regular fa-square-caret-right"></i>
    </div>

    <!-- Основной контент -->
    <div class="main-content">
      <header class="header">
        <div class="header-top" style="display:flex; align-items:center; gap:10px; width:100%; justify-content:space-between;">
          <h1 style="margin:0;">
            Заявка №<?= $z_title_id ?><?= $date_reg ? (' от ' . esc($date_reg)) : '' ?>
          </h1>
          <div style="display:flex; align-items:center; gap:10px;">
            <button id="themeToggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
          </div>
        </div>
      </header>

      <!-- Две информационные колонки, одинаковая сетка и классы как в zadtest -->
      <div class="info-container">
        <div class="info-column">
          <div class="info-item">
            <span class="info-label">Статус:</span>
            <span class="info-value"><?= esc($status_name) ?></span>
          </div>

          <div class="info-item">
            <span class="info-label">Клиент:</span>
            <span class="info-value">
              <?= $client_fio ?><br />
              <?= $client_phone ? ('Телефон: ' . esc($client_phone) . '<br/>') : '' ?>
              <?= $client_mail  ? ('Email: '   . esc($client_mail)  . '<br/>') : '' ?>
              <?= $client_org   ? ('Организация: ' . esc($client_org)) : '' ?>
            </span>
          </div>

          <div class="info-item">
            <span class="info-label">Отдел:</span>
            <span class="info-value"><?= esc($otdel_name) ?></span>
          </div>

          <div class="info-item">
            <span class="info-label">Сотрудник:</span>
            <span class="info-value"><?= esc($sotr_fio) ?></span>
          </div>
        </div>

<div class="divider"></div>

        <div class="info-column">
          <div class="info-item">
            <span class="info-label">Дата регистрации:</span>
            <span class="info-value"><?= esc($date_reg) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Дедлайн:</span>
            <span class="info-value"><?= esc($dedlain) ?></span>
          </div>

<div class="info-item">
  <span class="info-label">Файл:</span>
  <span class="info-value">

<?php if ($has_file): ?>
  
  <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">

      <!-- Скачать -->
      <a href="download.php?type=zay&id=<?= (int)$zayavka_id ?>" 
         class="edit-btn"
         style="text-decoration:none; display:inline-flex; align-items:center; height:38px;">
         Скачать файл
      </a>

      <!-- Удалить -->
      <form method="post" 
            onsubmit="return confirm('Удалить прикреплённый файл?');" 
            style="margin:0;">
          <input type="hidden" name="action" value="delete_file">
          <button type="submit" 
                  class="edit-btn" 
                  style="background:#c43d3d; height:38px;">
              Удалить файл
          </button>
      </form>

  </div>

<?php else: ?>
  <span style="opacity:0.7;">Нет прикрепленного файла</span>
<?php endif; ?>

    <!-- Форма загрузки -->
    <form id="fileUploadForm" method="post" enctype="multipart/form-data" style="margin-top:10px;">
      <input type="hidden" name="action" value="upload_file">
      <input type="file" name="file" required style="margin-bottom:6px;">
      <button type="submit" class="edit-btn">Прикрепить файл</button>
    </form>

  </span>
</div>

        </div>
      </div>

      <!-- Содержание заявки -->
      <?php if (trim((string)$soderzh) !== ''): ?>
        <div class="comment-container" style="margin-top:20px;">
          <div class="info-item">
            <span class="info-label">Содержание:</span>
          </div>
          <div class="info-value"><?= nl2br(esc($soderzh)) ?></div>
        </div>
      <?php endif; ?>

      <!-- Комментарии -->
      <div class="comment-container" style="margin-top:20px;">
        <h3 style="margin-top:0;">Комментарии</h3>

        <?php if (!empty($comments)): ?>
          <?php foreach ($comments as $c): ?>
            <?php
              $cid = (int)$c['id_com'];
              $can_edit_delete = $is_admin || ((int)$c['sot_id'] === $user_id);
              $author = fio($c['Familiya'] ?? '', $c['Imya'] ?? '', $c['Otchestvo'] ?? '');
            ?>
            <div style="padding:12px 0; border-bottom:1px solid rgba(0,0,0,0.08);">
              <div style="display:flex; gap:8px; align-items:baseline; justify-content:space-between;">
                <div>
                  <strong><?= $author ?></strong>
                  <small style="opacity:.7;">(<?= esc($c['created_at']) ?>)</small>
                </div>
                <?php if ($can_edit_delete): ?>
                  <div style="display:flex; gap:12px;">
                    <button type="button" class="edit-btn" onclick="toggleCommentEdit(<?= $cid ?>)">Редактировать</button>
                    <button type="button" class="edit-btn" style="background:#c43d3d;" onclick="confirmDeleteComment(<?= $cid ?>)">Удалить</button>
                  </div>
                  
                <?php endif; ?>
              </div>

              <div id="comment_text_<?= $cid ?>" style="margin-top:6px;"><?= nl2br(esc($c['name_com'])) ?></div>

              <?php if ($can_edit_delete): ?>
                <form method="post" id="edit_form_<?= $cid ?>" style="display:none; margin-top:8px;">
                  <input type="hidden" name="action" value="edit_comment" />
                  <input type="hidden" name="comment_id" value="<?= $cid ?>" />
                  <textarea name="comment" rows="3" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;"><?= esc($c['name_com']) ?></textarea>
                  <div style="margin-top:8px;">
                    <button type="submit" class="edit-btn">Сохранить</button>
                  </div>
                </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="margin:0;">Комментариев пока нет</p>
        <?php endif; ?>

        <!-- Удаление комментария -->
        <form method="post" id="delete_comment_form" style="display:none;">
          <input type="hidden" name="action" value="delete_comment" />
          <input type="hidden" name="comment_id" id="delete_comment_id" />
        </form>

        <!-- Новый комментарий -->
        <div style="margin-top:16px;">
          <h4 style="margin:0 0 8px 0;">Новый комментарий</h4>
          <form method="post">
            <input type="hidden" name="action" value="add_comment" />
            <textarea name="comment" rows="4" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;"></textarea>
            <div style="margin-top:8px;">
              <button type="submit" class="edit-btn">Добавить комментарий</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Действия (как в zadtest) -->
      <div class="comment-container-2" style="margin-top:20px;">
        <h3 style="margin-top:0;">Действия</h3>

        <form method="post" style="margin-bottom:16px;">
          <input type="hidden" name="action" value="update_status" />
          <div class="info-item">
            <label class="info-label" for="status_id">Изменить статус</label>
            <span class="info-value" style="width:100%;">
              <select id="status_id" name="status_id" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                <?php foreach ($statusy as $st): ?>
                  <option value="<?= (int)$st['id_stat'] ?>" <?= ((int)$st['id_stat'] === (int)($zayavka['id_stat'] ?? 0)) ? 'selected' : '' ?>>
                    <?= esc($st['name_stat']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </span>
          </div>
          <div style="margin-top:8px;">
            <button type="submit" class="edit-btn">Сохранить</button>
          </div>
        </form>

        <form method="post">
          <input type="hidden" name="action" value="delegate" />
          <div class="info-item">
            <label class="info-label" for="new_user">Делегировать исполнителю</label>
            <span class="info-value" style="width:100%;">
              <select id="new_user" name="new_user" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                <?php foreach ($users as $u): ?>
                  <option value="<?= (int)$u['id_use'] ?>">
                    <?= fio($u['Familiya'] ?? '', $u['Imya'] ?? '', $u['Otchestvo'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </span>
          </div>
          <div style="margin-top:8px;">
            <button type="submit" class="edit-btn" style="background:#f0ad4e;">Делегировать</button>
          </div>
        </form>
      </div>

      <!-- Загрузка файлов (опционально; сохранено в едином стиле с zadtest) -->

      <!-- Скрипты FilePond — те же, что и в zadtest -->
      <script src="https://cdn.jsdelivr.net/npm/filepond@4.24.0/dist/filepond.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.24.0/dist/filepond-plugin-image-preview.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.0/dist/filepond-plugin-file-validate-type.min.js"></script>
      <script>
        FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);
        const inputElement = document.querySelector('input[type="file"].filepond');
        if (inputElement) {
          FilePond.create(inputElement, {
            allowMultiple: true,
            acceptedFileTypes: ["image/*", "application/pdf", "text/*"],
            labelIdle: 'Нажмите или перетащите для загрузки файлов',
            server: {
              url: '/upload.php',
              process: {
                url: '/',
                method: 'POST',
                withCredentials: false,
                headers: { 'X-Custom-Header': 'FilePond' },
                onload: (response) => { console.log('Файл загружен:', response); },
                onerror: (error) => { console.error('Ошибка загрузки файла:', error); }
              },
              revert: '/revert.php'
            }
          });
        }

        function confirmDeleteComment(comment_id) {
          if (confirm('Удалить этот комментарий?')) {
            document.getElementById('delete_comment_id').value = comment_id;
            document.getElementById('delete_comment_form').submit();
          }
        }
        function toggleCommentEdit(comment_id) {
          const textDiv = document.getElementById('comment_text_' + comment_id);
          const editForm = document.getElementById('edit_form_' + comment_id);
          if (!textDiv || !editForm) return;
          const hidden = editForm.style.display === 'none' || editForm.style.display === '';
          editForm.style.display = hidden ? 'block' : 'none';
          textDiv.style.display = hidden ? 'none' : 'block';
        }
      </script>
    </div>
  </div>
</body>
</html>
