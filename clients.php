<?php
session_start();

// Проверка, что все необходимые сессионные переменные установлены
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['dolzhid'])) {
  // Если хотя бы одна из переменных отсутствует, перенаправляем на страницу логина
  header("Location: login");
  exit();
}

// Подключаем файл с соединением к базе
include('php/subd.php');

// Запрос к представлению view_clientsc
$sql = "SELECT * FROM `clients_info`";
$result = $conn->query($sql);

$rolea = $_SESSION['rolea'];
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <script src="Scripts\script.js"></script>
  <title>NG-CRM - Клиенты</title>
  <link rel="stylesheet" href="Styles\mainstyleTables.css" />
  <script>
  document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("toggleBtn").addEventListener("click", function () {
      let formContainer = document.getElementById("formContainer");
      let form = document.getElementById("employeeForm");

      if (form.style.display === "none") {
          form.style.display = "flex";
          
          if (window.innerWidth <= 768) {
              formContainer.style.width = "100%";
              formContainer.style.height = "900px"; // Устанавливаем 600px на мобильных
          } else {
              formContainer.style.width = "45%";
              formContainer.style.height = "350px"; // Оставляем 350px для десктопа
          }

          form.classList.remove("hidden");
      } else {
          form.style.display = "none";
          formContainer.classList.add("collapsed");
          formContainer.classList.remove("expanded");
          formContainer.style.height = "50px";
          formContainer.style.width = "150px";
          form.classList.add("hidden");
      }
  });
});
  </script>
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
          <li class="active"><i class="fa-solid fa-users"></i></li>
          <li>
            <a href="applications">
              <i class="fa-solid fa-file"></i>
            </a>
          </li>
		  
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
    <div class="overlay"></div>
    <div class="sidebar-toggle" id="sidebarToggle">
      <i class="fa-regular fa-square-caret-right"></i>
    </div>
    <div class="main-content">
      <header class="header">
        <!-- Первая строка -->
        <div class="header-top">
          <h1>Клиенты</h1>
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
              <th>Фамилия</th>
              <th>Имя</th>
              <th>Отчество</th>
              <th>Телеграм(ID)</th>
              <th>Данные организации</th>
              <th>Почта</th>
              <th>Телефон</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id_cli'] . "</td>";
                echo "<td>" . $row['Familiya'] . "</td>";
                echo "<td>" . $row['Imya'] . "</td>";
                echo "<td>" . $row['Otchestv'] . "</td>";
                echo "<td>" . $row['telegramid'] . "</td>";

                // Данные организации как текст
                echo "<td data-field='organizs' data-value='" . $row['organizs'] . "'>" . $row['organizs'] . "</td>";

                echo "<td>" . $row['emailsc'] . "</td>";
                echo "<td>" . $row['phone'] . "</td>";
                echo "<td><button class='delete-btn'>X</button> <button class='edit-btn'>✎</button> <button class='cancel-btn' style='display: none;'>✗</button> <button class='save-btn' style='display: none;'>✔</button></td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='9'>Нет данных</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>


      <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
          button.addEventListener('click', function () {
            let row = this.closest('tr');
            let cells = row.querySelectorAll('td:not(:first-child):not(:last-child)');
            let isEditable = cells[0].getAttribute('contenteditable') === 'true';

            let cancelBtn = row.querySelector('.cancel-btn');
            let saveBtn = row.querySelector('.save-btn');
            let deleteBtn = row.querySelector('.delete-btn');

            if (!isEditable) {
              // Сохраняем оригинальные значения
              row.dataset.originalValues = JSON.stringify(Array.from(cells).map(cell => cell.textContent));

              // Включаем редактирование
              cells.forEach(cell => {
                let field = cell.dataset.field;
                if (field === 'organizs') {
                  // Для "Данных организации" оставляем обычное текстовое редактирование
                  cell.setAttribute('contenteditable', 'true');
                } else {
                  cell.setAttribute('contenteditable', 'true');
                }
              });

              // Скрываем "Редактировать" и "Удалить", показываем "Отмена" и "Сохранить"
              this.style.display = 'none';
              deleteBtn.style.display = 'none';
              cancelBtn.style.display = 'inline-block';
              saveBtn.style.display = 'inline-block';
            }
          });
        });

        document.querySelectorAll('.cancel-btn').forEach(button => {
          button.addEventListener('click', function () {
            let row = this.closest('tr');
            let cells = row.querySelectorAll('td:not(:first-child):not(:last-child)');
            let originalValues = JSON.parse(row.dataset.originalValues);
            let deleteBtn = row.querySelector('.delete-btn');

            cells.forEach((cell, index) => {
              cell.textContent = originalValues[index];
            });

            // Отключаем редактирование
            cells.forEach(cell => cell.setAttribute('contenteditable', 'false'));

            // Скрываем "Отмена" и "Сохранить", показываем "Редактировать" и "Удалить"
            row.querySelector('.edit-btn').style.display = 'inline-block';
            deleteBtn.style.display = 'inline-block';
            row.querySelector('.cancel-btn').style.display = 'none';
            row.querySelector('.save-btn').style.display = 'none';
          });
        });

        document.querySelectorAll('.save-btn').forEach(button => {
		  button.addEventListener('click', function () {
			let row = this.closest('tr');
			let cells = row.querySelectorAll('td:not(:first-child):not(:last-child)');

			let id = row.cells[0].textContent;
			let familiya = row.cells[1].textContent;
			let imya = row.cells[2].textContent;
			let otchestv = row.cells[3].textContent;
			let telegramid = row.cells[4].textContent;
			let organizs = row.querySelector('[data-field="organizs"]').textContent;
			let emailsc = row.cells[6].textContent;
			let phone = row.cells[7].textContent;

			let deleteBtn = row.querySelector('.delete-btn');

			// Отправка данных на сервер через AJAX
			fetch('upd_php/clients_upd.php', {
			  method: 'POST',
			  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			  body: `id=${id}&familiya=${encodeURIComponent(familiya)}&imya=${encodeURIComponent(imya)}&otchestv=${encodeURIComponent(otchestv)}&telegramid=${encodeURIComponent(telegramid)}&organizs=${encodeURIComponent(organizs)}&emailsc=${encodeURIComponent(emailsc)}&phone=${encodeURIComponent(phone)}`
			})
			.then(response => response.text())
			.then(response => {
			  // Assuming the response contains success or failure message
			  if (response.trim() === "success") {
				alert("Данные успешно обновлены.");
				location.reload(); // Перезагружаем страницу после успешного обновления
			  } else {
				alert("Ошибка обновления данных: " + response);
			  }
			})
			.catch(error => {
			  alert("Произошла ошибка при отправке запроса.");
			});

			// Отключаем редактирование и устанавливаем текст
			cells.forEach(cell => {
			  if (cell.getAttribute('contenteditable') === 'true') {
				cell.setAttribute('contenteditable', 'false');
			  }
			});

			// Скрываем "Отмена" и "Сохранить", показываем "Редактировать" и "Удалить"
			row.querySelector('.edit-btn').style.display = 'inline-block';
			deleteBtn.style.display = 'inline-block';
			row.querySelector('.cancel-btn').style.display = 'none';
			row.querySelector('.save-btn').style.display = 'none';
		  });
		});


      </script>

      <div class="form-container collapsed" id="formContainer">
        <div class="toggle-btn" id="toggleBtn"></div>
        <form id="employeeForm" action="add_php/add_client.php" method="post" style="display: none">
          <div class="form-row">
            <input type="text" id="firstName" name="firstName" placeholder="Фамилия" />
            <input type="text" id="lastName" name="lastName" placeholder="Имя" />
            <input type="text" id="middleName" name="middleName" placeholder="Отвечство" />
          </div>
          <div class="form-row">
            <input type="text" id="telegram" name="telegram" placeholder="Телеграм-ID" />
            <input type="text" id="organiz" name="organiz" placeholder="Организация" />

            <input type="text" id="mail" name="mail" placeholder="Почта" />
          </div>
          <div class="form-row">
            <input type="tel" id="phone" name="phone" placeholder="Номер телефона" />

          </div>
          <input type="submit" value="Добавить" />
        </form>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      $(".delete-btn").click(function () {
        let row = $(this).closest("tr"); // Получаем строку
        let id = row.find("td:first").text().trim(); // Берем ID из первой ячейки

        if (confirm(`Вы уверены, что хотите удалить запись с ID ${id}?`)) {
          $.ajax({
            url: "del_php/delete_client.php",
            type: "POST",
            data: { id: id },
            success: function (response) {
              if (response.trim() === "success") {
                location.reload(); // Перезагружаем страницу после успешного удаления
              } else {
                alert("Ошибка удаления: " + response);
              }
            },
            error: function () {
              alert("Произошла ошибка при отправке запроса. Вероятно у клиента имеются активные заявки!");
            }
          });
        }
      });
    });
  </script>

<script>
    // Функция для генерации карточек клиентов
    function generateClientCards() {
      const cardsContainer = document.getElementById('cardsContainer');
      cardsContainer.innerHTML = '';
      
      const rows = document.querySelectorAll('#dataTable tbody tr');
      rows.forEach(row => {
        if (row.cells.length < 9) return;
        
        const id = row.cells[0].textContent;
        const lastName = row.cells[1].textContent;
        const firstName = row.cells[2].textContent;
        const middleName = row.cells[3].textContent;
        const telegram = row.cells[4].textContent;
        const organization = row.cells[5].textContent;
        const email = row.cells[6].textContent;
        const phone = row.cells[7].textContent;
        
        const card = document.createElement('div');
        card.className = 'employee-card';
        card.innerHTML = `
          <div class="card-id">ID: ${id}</div>
          <div class="card-content">
            <div class="employee-name">${lastName} ${firstName} ${middleName}</div>
            <div class="card-row">
              <span class="card-label">Организация:</span>
              <span class="card-value">${organization || '—'}</span>
            </div>
            <div class="card-row">
              <span class="card-label">Телеграм:</span>
              <span class="card-value">${telegram || '—'}</span>
            </div>
            <div class="card-row">
              <span class="card-label">Email:</span>
              <span class="card-value">${email || '—'}</span>
            </div>
            <div class="card-row">
              <span class="card-label">Телефон:</span>
              <span class="card-value">${phone || '—'}</span>
            </div>
            <div class="card-actions">
              <button class="edit-btn"><i class="fas fa-edit"></i></button>
              <button class="delete-btn"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        `;
        
        // Назначаем обработчики событий для кнопок
        card.querySelector('.edit-btn').addEventListener('click', function() {
          document.getElementById('viewToggleBtn').click();
          const tableRow = document.querySelector(`#dataTable tbody tr:has(td:first-child:contains("${id}"))`);
          if (tableRow) tableRow.querySelector('.edit-btn').click();
        });
        
        card.querySelector('.delete-btn').addEventListener('click', function() {
          if (confirm(`Удалить клиента ${lastName} ${firstName}?`)) {
            $.ajax({
              url: "del_php/delete_client.php",
              type: "POST",
              data: { id: id },
              success: function(response) {
                if (response.trim() === "success") {
                  card.remove();
                  const tableRow = document.querySelector(`#dataTable tbody tr:has(td:first-child:contains("${id}"))`);
                  if (tableRow) tableRow.remove();
                } else {
                  alert("Ошибка удаления: " + response);
                }
              },
              error: function() {
                alert("Произошла ошибка при отправке запроса.");
              }
            });
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
        localStorage.setItem('clientViewMode', 'cards');
        generateClientCards();
      } else {
        // Переключаемся на таблицу
        tableView.style.display = 'table';
        cardsView.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-th-large"></i> Карточки';
        localStorage.setItem('clientViewMode', 'table');
      }
      
      toggleBtn.classList.toggle('active');
    }
    
    // Проверяем сохранённый вид при загрузке
    document.addEventListener('DOMContentLoaded', function() {
      const savedViewMode = localStorage.getItem('clientViewMode');
      const toggleBtn = document.getElementById('viewToggleBtn');
      
      if (savedViewMode === 'cards') {
        setTimeout(() => {
          toggleView();
        }, 100);
      }
      
      toggleBtn.addEventListener('click', toggleView);
    });
  </script>


</body>

</html>