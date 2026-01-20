<?php
session_start();

// Проверка, что все необходимые сессионные переменные установлены
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['dolzhid']) || !isset($_SESSION['rolea'])) {
  // Если хотя бы одна из переменных отсутствует, перенаправляем на страницу логина
  header("Location: login");
  exit();
}


include('php/subd.php');

// Запрос к представлению full_zayavki_view
$sql = "SELECT * FROM full_zayavki_view";
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="Styles\mainstyleTables.css" />
  <script src="Scripts\script.js"></script>
  <title>NG-CRM - Заявки</title>
</head>

<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logo-container">
        <img src="images\nglogo_light.png" alt="Logo" />
      </div>
      <nav>
        <ul>
          <li>
            <a href="employees">
              <i class="fa-solid fa-user-tie"></i>
            </a>
          </li>
          <li>
            <a href="clients">
              <i class="fa-solid fa-users"></i>
            </a>
          </li>
          <li class="active"><i class="fa-solid fa-file"></i></li>
          <li>
            <a href="zadachi">
              <i class="fa-solid fa-id-card"></i>
            </a>
          </li>
          <li>
            <a href="kalend">
              <i class="fa-solid fa-calendar"></i>
            </a>
          </li>
          <li>
            <a href="analyt">
              <i class="fa-solid fa-line-chart"></i>
            </a>
          </li>

        </ul>
      </nav>
      <div class="exit" onclick="logout()">
        <i class="fa-solid fa-right-from-bracket"></i>
      </div>
      <script>
        function logout() {
          // Перенаправляем на logout.php в папке php
          window.location.href = 'php/logout.php';
        }
      </script>

    </aside>

    <script>
document.getElementById("myButton").addEventListener("click", function () {
  window.location.href = "ApplicationCreate";
});
</script>

    <div class="sidebar-toggle" id="sidebarToggle">
      <i class="fa-regular fa-square-caret-right"></i>
    </div>
    <div class="main-content">
      <header class="header">
        <!-- Первая строка -->
        <div class="header-top">
            <h1>Заявки</h1>
            <button id="viewToggleBtn" class="view-toggle" title="Переключить вид">
                <i class="fas fa-th-large"></i> Карточки
            </button>
            <button id="themeToggle" class="theme-toggle">
                <i class="fas fa-moon"></i>
            </button>
        </div>

        
        <!-- Вторая строка -->
        <div class="header-bottom">
          <div class="user-info">
            <input type="text" id="searchInput" placeholder="Поиск по таблице" />
            <span><i class="fa-solid fa-magnifying-glass"></i></span>
          </div>
        </div>
      </header>
      <div class="table-container">
      <div class="cards-container" id="cardsContainer"></div>
        <table id="dataTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Дата регистрации</th>
              <th>Срок выполнения</th>
              <th>Статус</th>
              <th>Ответственный</th>
              <th>Отдел</th>
              <th>Организация клиента</th>
              <th>ФИО клиента</th>
              <th>Содержание</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              $id = 1; // Начальный ID для строк
              while ($row = $result->fetch_assoc()) {
                // Формируем строки таблицы с данными из БД
                echo "<tr data-id_zay='" . htmlspecialchars($row['id_zay']) . "'>";
                echo "<td>" . htmlspecialchars($row['id_zay']) . "</td>";
                echo "<td style='display: none;'>" . htmlspecialchars($row['id_cli']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date_reg']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dedlain']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sotrudnik_familiya']) . " " . htmlspecialchars($row['sotrudnik_imya']) . " " . htmlspecialchars($row['sotrudnik_otchestvo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['otdel_zayav_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['client_organization']) . "<br>" . htmlspecialchars($row['client_email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['client_familiya']) . " " . htmlspecialchars($row['client_imya']) . " " . htmlspecialchars($row['client_otchestvo']) . "</td>";
                echo "<td>" . (mb_strlen($row['soderzh']) > 40 ? mb_substr(htmlspecialchars($row['soderzh']), 0, 30) . "..." : htmlspecialchars($row['soderzh'])) . "</td>";
                echo "<td> <button class='delete-btn' data-id_zay='" . htmlspecialchars($row['id_zay']) . "' data-id_cli='" . htmlspecialchars($row['id_cli']) . "'>X</button> <button class='edit-btn'>✎</button> </td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='10'>Нет данных</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="form-container collapsed" id="formContainer">
        <div class="toggle-btn" id="toggleBtn"></div>
        <form id="employeeForm" style="display: none">
          <div class="form-row">
            <select id="otvet" required>
              <option value="" disabled selected>Ответственный</option>
              <option value="Вариант1">Вариант1</option>
              <option value="Вариант2">Вариант2</option>
              <option value="Вариант3">Вариант3</option>
            </select>
            <select id="otvet" required>
              <option value="" disabled selected>Организация клиента</option>
              <option value="Вариант1">Вариант1</option>
              <option value="Вариант2">Вариант2</option>
              <option value="Вариант3">Вариант3</option>
            </select>
            <select id="otvet" required>
              <option value="" disabled selected>ФИО Клиента</option>
              <option value="Вариант1">Вариант1</option>
              <option value="Вариант2">Вариант2</option>
              <option value="Вариант3">Вариант3</option>
            </select>
          </div>
          <div class="form-row">
            <input type="text" id="istoch" placeholder="Источник" required />
            <input type="text" id="soderj" placeholder="Содержание" required />
            <input type="text" id="comm" placeholder="Комментарий" required />
          </div>
          <div class="overlay"></div>
          <div class="form-row2">
            <span id="close-btn" class="close-btn">×</span>
          </div>
          <input type="submit" value="Добавить" />
        </form>
      </div>
    </div>
  </div>
  <script>
    // Функция для генерации карточек заявок
    function generateApplicationCards() {
      const cardsContainer = document.getElementById('cardsContainer');
      cardsContainer.innerHTML = '';

      const rows = document.querySelectorAll('#dataTable tbody tr[data-id_zay]');
      rows.forEach(row => {
        const id_zay = row.getAttribute('data-id_zay');
        const id_cli = row.querySelector('td:nth-child(2)').textContent;
        const date_reg = row.querySelector('td:nth-child(3)').textContent;
        const dedlain = row.querySelector('td:nth-child(4)').textContent;
        const status = row.querySelector('td:nth-child(5)').textContent;
        const sotrudnik = row.querySelector('td:nth-child(6)').textContent;
        const otdel = row.querySelector('td:nth-child(7)').textContent;
        const client_org = row.querySelector('td:nth-child(8)').textContent.split('\n')[0];
        const client_email = row.querySelector('td:nth-child(8)').textContent.split('\n')[1] || '';
        const client_fio = row.querySelector('td:nth-child(9)').textContent;
        const soderzh = row.querySelector('td:nth-child(10)').textContent;

        const card = document.createElement('div');
        card.className = 'employee-card';
        card.setAttribute('data-id_zay', id_zay);
        card.innerHTML = `
                    <div class="card-header">
                        <span class="card-id">ID: ${id_zay}</span>
                        <span class="card-date">${date_reg}</span>
                    </div>
                    <div class="employee-name">${soderzh}</div>
                    <div class="card-row">
                        <div class="card-label">Статус</div>
                        <div class="card-value">${status}</div>
                    </div>
                    <div class="card-row">
                        <div class="card-label">Срок выполнения</div>
                        <div class="card-value">${dedlain}</div>
                    </div>
                    <div class="card-row">
                        <div class="card-label">Ответственный</div>
                        <div class="card-value">${sotrudnik}</div>
                    </div>
                    <div class="card-row">
                        <div class="card-label">Отдел</div>
                        <div class="card-value">${otdel}</div>
                    </div>
                    <div class="card-row">
                        <div class="card-label">Клиент</div>
                        <div class="card-value">${client_fio}</div>
                    </div>
                    <div class="card-row">
                        <div class="card-label">Организация</div>
                        <div class="card-value">${client_org}</div>
                    </div>
                    ${client_email ? `
                    <div class="card-row">
                        <div class="card-label">Email</div>
                        <div class="card-value">${client_email}</div>
                    </div>` : ''}
                    <div class="card-actions">
                        <button class="delete-btn" data-id_zay="${id_zay}" data-id_cli="${id_cli}"><i class="fas fa-trash"></i></button>
                    </div>
                `;

        // Обработчик клика на карточку
        card.addEventListener('click', function (e) {
          if (!e.target.classList.contains('delete-btn')) {
            window.location.href = `https://ngsoftcrm.ru/zaytest?id=${id_zay}`;
          }
        });

        // Обработчик для кнопки удаления
        card.querySelector('.delete-btn').addEventListener('click', function (e) {
          e.stopPropagation();

          if (confirm("Вы уверены, что хотите удалить эту заявку?")) {
            fetch("zayav_php/del.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded"
              },
              body: `id_zay=${id_zay}&id_cli=${id_cli}`
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  card.remove();
                  const tableRow = document.querySelector(`#dataTable tbody tr[data-id_zay="${id_zay}"]`);
                  if (tableRow) tableRow.remove();
                } else {
                  alert("Ошибка удаления: " + data.error);
                }
              })
              .catch(error => console.error("Ошибка:", error));
          }
        });

        cardsContainer.appendChild(card);
      });
    }

    // Функция для переключения вида
    function toggleView() {
      const tableView = document.getElementById('dataTable');
      const cardsView = document.getElementById('cardsContainer');
      const toggleBtn = document.getElementById('viewToggleBtn');

      if (tableView.style.display !== 'none') {
        // Переключаемся на карточки
        tableView.style.display = 'none';
        cardsView.style.display = 'flex';
        toggleBtn.innerHTML = '<i class="fas fa-table"></i> Таблица';
        localStorage.setItem('applicationViewMode', 'cards');
        generateApplicationCards();
      } else {
        // Переключаемся на таблицу
        tableView.style.display = 'table';
        cardsView.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-th-large"></i> Карточки';
        localStorage.setItem('applicationViewMode', 'table');
      }

      toggleBtn.classList.toggle('active');
    }

    // Функция для добавления обработчиков событий к строкам таблицы
    function addTableRowHandlers() {
      // Обработчики клика для строк таблицы
      document.querySelectorAll('#dataTable tbody tr[data-id_zay]').forEach(row => {
        row.addEventListener('click', function(e) {
          // Проверяем, что клик был не по кнопке удаления или редактирования
          if (!e.target.classList.contains('delete-btn') && !e.target.classList.contains('edit-btn') && 
              !e.target.closest('.delete-btn') && !e.target.closest('.edit-btn')) {
            const id_zay = this.getAttribute('data-id_zay');
            window.location.href = `https://ngsoftcrm.ru/zaytest?id=${id_zay}`;
          }
        });
      });

      // Обработчики для кнопок удаления в таблице
      document.querySelectorAll('#dataTable .delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.stopPropagation();
          const id_zay = this.getAttribute('data-id_zay');
          const id_cli = this.getAttribute('data-id_cli');

          if (confirm("Вы уверены, что хотите удалить эту заявку?")) {
            fetch("zayav_php/del.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded"
              },
              body: `id_zay=${id_zay}&id_cli=${id_cli}`
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  const row = document.querySelector(`tr[data-id_zay="${id_zay}"]`);
                  if (row) row.remove();
                  const card = document.querySelector(`.employee-card[data-id_zay="${id_zay}"]`);
                  if (card) card.remove();
                } else {
                  alert("Ошибка удаления: " + data.error);
                }
              })
              .catch(error => console.error("Ошибка:", error));
          }
        });
      });
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function () {
      // Добавляем обработчики для таблицы сразу после загрузки
      addTableRowHandlers();

      // Проверяем сохранённый вид при загрузке
      const savedViewMode = localStorage.getItem('applicationViewMode');
      const toggleBtn = document.getElementById('viewToggleBtn');

      if (savedViewMode === 'cards') {
        setTimeout(() => {
          toggleView();
        }, 100);
      }

      toggleBtn.addEventListener('click', toggleView);
    });

    document.addEventListener("DOMContentLoaded", function () {
    const button = document.getElementById("toggleBtn");

    button.addEventListener("click", function () {
      window.location.href = "ApplicationCreate";
    });
  });

  </script>

  <style>
    #dataTable tbody tr {
      cursor: pointer;
    }
  </style>
</body>

</html>