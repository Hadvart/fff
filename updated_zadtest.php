<?php
// ====== ОТЛАДКА ======
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['dolzhid'])) {
    header("Location: login");
    exit();
}

include('php/subd.php');
$conn->set_charset("utf8mb4");

$zadacha_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$zadacha_id) die("Некорректный ID задачи");

$user_id = intval($_SESSION['user_id']);
$is_admin = in_array($user_id, [1,2,3]);

// --- Обработка POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_status':
            $new_status = intval($_POST['status_id']);
            $stmt = $conn->prepare("UPDATE zadachi SET status_id = ? WHERE id_zadch = ?");
            $stmt->bind_param("ii", $new_status, $zadacha_id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'delegate':
            $new_user = intval($_POST['new_user']);
            $stmt = $conn->prepare("UPDATE zadachi SET ispolnit_id = ? WHERE id_zadch = ?");
            $stmt->bind_param("ii", $new_user, $zadacha_id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'add_comment':
            $comment = trim($_POST['comment']);
            $stmt = $conn->prepare("INSERT INTO comments_zadach (id_zad, name_com, sot_id, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("isi", $zadacha_id, $comment, $user_id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'edit_comment':
            $comment_id = intval($_POST['comment_id']);
            $comment_text = trim($_POST['comment']);
            if ($is_admin) {
                $stmt = $conn->prepare("UPDATE comments_zadach SET name_com = ? WHERE id_com = ?");
                $stmt->bind_param("si", $comment_text, $comment_id);
            } else {
                $stmt = $conn->prepare("UPDATE comments_zadach SET name_com = ? WHERE id_com = ? AND sot_id = ?");
                $stmt->bind_param("sii", $comment_text, $comment_id, $user_id);
            }
            $stmt->execute();
            $stmt->close();
            break;

        case 'delete_comment':
            $comment_id = intval($_POST['comment_id']);
            if ($is_admin) {
                $stmt = $conn->prepare("DELETE FROM comments_zadach WHERE id_com = ?");
                $stmt->bind_param("i", $comment_id);
            } else {
                $stmt = $conn->prepare("DELETE FROM comments_zadach WHERE id_com = ? AND sot_id = ?");
                $stmt->bind_param("ii", $comment_id, $user_id);
            }
            $stmt->execute();
            $stmt->close();
            break;
    }

    header("Location: zadtest?id=" . $zadacha_id);
    exit();
}

// --- Получение данных задачи ---
$stmt = $conn->prepare("SELECT * FROM zadachi_detailed_view WHERE id_zadch = ?");
$stmt->bind_param("i", $zadacha_id);
$stmt->execute();
$zadacha = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$zadacha) die("Задача не найдена");

// --- Справочники ---
$statusy = $conn->query("SELECT id_stat, name_stat FROM status")->fetch_all(MYSQLI_ASSOC);
$users = $conn->query("SELECT id_use, Familiya, Imya, Otchestvo FROM userscr")->fetch_all(MYSQLI_ASSOC);

// --- Комментарии ---
$stmt = $conn->prepare("
    SELECT cm.id_com, cm.name_com, cm.created_at, u.Familiya, u.Imya, u.Otchestvo, cm.sot_id
    FROM comments_zadach cm
    JOIN userscr u ON cm.sot_id = u.id_use
    WHERE cm.id_zad = ?
    ORDER BY cm.id_com ASC
");
$stmt->bind_param("i", $zadacha_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="applicationstyle.css" />
  <title>NG-CRM — Задача: <?= htmlspecialchars($zadacha['nazv']) ?></title>
  <script>
    // Тоггл тёмной темы
    document.addEventListener('DOMContentLoaded', function() {
      const btn = document.getElementById('themeToggle');
      const saved = localStorage.getItem('ngcrm_theme');
      if (saved === 'dark') document.body.classList.add('dark-theme');
      btn?.addEventListener('click', () => {
        document.body.classList.toggle('dark-theme');
        localStorage.setItem('ngcrm_theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
      });
    });
    function confirmDelete(comment_id) {
      if(confirm('Удалить этот комментарий?')) {
          document.getElementById('delete_comment_id').value = comment_id;
          document.getElementById('delete_comment_form').submit();
      }
    }
    function toggleEdit(id) {
      const t = document.getElementById('comment_text_' + id);
      const f = document.getElementById('edit_form_' + id);
      if(!t || !f) return;
      const hidden = t.style.display === 'none';
      t.style.display = hidden ? 'block' : 'none';
      f.style.display = hidden ? 'none' : 'block';
    }
  </script>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logo-container">
        <img src="images/nglogo_light.png" alt="Logo" />
      </div>
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
      <div class="exit"><a href="logout"><i class="fa-solid fa-right-from-bracket"></i></a></div>
    </aside>

    <div class="main-content">
      <header class="header">
        <div class="header-top">
          <h1><?= htmlspecialchars($zadacha['nazv']) ?></h1>
          <button id="themeToggle" class="theme-toggle">
            <i class="fas fa-moon"></i>
          </button>
        </div>
      </header>

      <!-- Блок с ключевой информацией -->
      <div class="info-container">
        <div class="info-column">
          <div class="info-item">
            <span class="info-label">Статус:</span>
            <span class="info-value"><?= htmlspecialchars($zadacha['status_name']) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Срок:</span>
            <span class="info-value"><?= htmlspecialchars($zadacha['data_dedl']) ?></span>
          </div>
          <div class="info-item">
            <span class="info-label">Дата создания:</span>
            <span class="info-value"><?= htmlspecialchars($zadacha['data_naz']) ?></span>
          </div>
        </div>
        <div class="divider"></div>
        <div class="info-column">
          <div class="info-item">
            <span class="info-label">Инициатор:</span>
            <span class="info-value">
              <?= htmlspecialchars(($zadacha['init_familiya'] ?? '') . ' ' . ($zadacha['init_imya'] ?? '') . ' ' . ($zadacha['init_otchestvo'] ?? '')) ?>
            </span>
          </div>
          <div class="info-item">
            <span class="info-label">Исполнитель:</span>
            <span class="info-value">
              <?= htmlspecialchars(($zadacha['isp_familiya'] ?? '') . ' ' . ($zadacha['isp_imya'] ?? '') . ' ' . ($zadacha['isp_otchestvo'] ?? '')) ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Описание / содержание заявки -->
      <div class="comment-container" style="margin-top:10px;">
        <div class="info-item"><span class="info-label">Описание:</span></div>
        <div class="info-value"><?= nl2br(htmlspecialchars($zadacha['opisanie'] ?? $zadacha['zayavka_soderzh'] ?? '')) ?></div>
      </div>

      <!-- Управление статусом -->
      <div class="form-container" style="height:auto; width:60%;">
        <form method="post">
          <input type="hidden" name="action" value="update_status">
          <div class="form-row">
            <select name="status_id">
              <?php foreach ($statusy as $s): ?>
                <option value="<?= $s['id_stat'] ?>" <?= $s['id_stat']==$zadacha['id_stat']?'selected':'' ?>>
                  <?= htmlspecialchars($s['name_stat']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <input type="submit" value="Обновить статус">
          </div>
        </form>
      </div>

      <!-- Делегирование -->
      <div class="form-container" style="height:auto; width:60%;">
        <form method="post">
          <input type="hidden" name="action" value="delegate">
          <div class="form-row">
            <select name="new_user">
              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id_use'] ?>">
                  <?= htmlspecialchars(($u['Familiya'] ?? '') . ' ' . ($u['Imya'] ?? '') . ' ' . ($u['Otchestvo'] ?? '')) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <input type="submit" value="Делегировать">
          </div>
        </form>
      </div>

      <!-- Комментарии -->
      <div class="comment-container">
        <div class="info-label" style="margin-bottom:10px;">Комментарии</div>
        <?php if(!empty($comments)): ?>
          <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
            <?php foreach($comments as $c): 
              $can_edit_delete = $is_admin || ($c['sot_id'] == $user_id);
            ?>
              <li style="background:#f9f9f9; border-radius:12px; padding:12px;" class="body">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                  <strong><?= htmlspecialchars($c['Familiya'] . ' ' . $c['Imya'] . ' ' . $c['Otchestvo']) ?></strong>
                  <small class="info-value"><?= htmlspecialchars($c['created_at']) ?></small>
                </div>
                <div id="comment_text_<?= $c['id_com'] ?>" style="margin-top:8px;">
                  <?= nl2br(htmlspecialchars($c['name_com'])) ?>
                </div>
                <?php if($can_edit_delete): ?>
                  <div style="margin-top:8px;">
                    <button class="edit-btn" type="button" onclick="toggleEdit(<?= $c['id_com'] ?>)">Редактировать</button>
                    <button class="delete-btn" type="button" onclick="confirmDelete(<?= $c['id_com'] ?>)">Удалить</button>
                  </div>
                  <form method="post" id="edit_form_<?= $c['id_com'] ?>" style="display:none; margin-top:10px;">
                    <input type="hidden" name="action" value="edit_comment">
                    <input type="hidden" name="comment_id" value="<?= $c['id_com'] ?>">
                    <textarea name="comment" rows="3" style="width:100%;"><?= htmlspecialchars($c['name_com']) ?></textarea>
                    <div style="text-align:right; margin-top:8px;">
                      <button class="save-btn" type="submit">Сохранить</button>
                      <button class="cancel-btn" type="button" onclick="toggleEdit(<?= $c['id_com'] ?>)">Отмена</button>
                    </div>
                  </form>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <div class="info-value">Комментариев пока нет</div>
        <?php endif; ?>
        <form method="post" style="margin-top:15px;">
          <input type="hidden" name="action" value="add_comment">
          <textarea name="comment" rows="3" placeholder="Новый комментарий..."></textarea>
          <div style="text-align:right; margin-top:8px;">
            <button class="save-btn" type="submit">Добавить</button>
          </div>
        </form>
        <form method="post" id="delete_comment_form" style="display:none;">
          <input type="hidden" name="action" value="delete_comment">
          <input type="hidden" id="delete_comment_id" name="comment_id" value="">
        </form>
      </div>

    </div>
  </div>
</body>
</html>
