<?php
session_start();


if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['dolzhid'])) {
  header("Location: login");
  exit();
}

include('php/subd.php');


$sql = "SELECT * FROM `user_info` ORDER BY `id_use` ASC";
$result = $conn->query($sql);


$sql_dolzh = "SELECT id_dolzh, namedolzh FROM dolzh";
$result_dolzh = $conn->query($sql_dolzh);

$sql_otdeli = "SELECT id_otd, name_otd FROM otdeli";
$result_otdeli = $conn->query($sql_otdeli);

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

$rolea = $_SESSION['rolea'];

// Сохраняем результаты в массивы
$otdeli_data = [];
while ($row = $result_otdeli->fetch_assoc()) {
    $otdeli_data[] = $row;
}

$dolzh_data = [];
while ($row = $result_dolzh->fetch_assoc()) {
    $dolzh_data[] = $row;
}


?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="Styles\mainstyleTables.css" />
  <script src="Scripts\script.js"></script>
  <title>NG-CRM - Сотрудники</title>
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
          <li class="active"><i class="fa-solid fa-user-tie"></i></li>
          <li>
            <a href="clients">
              <i class="fa-solid fa-users"></i>
            </a>
          </li>
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

<div class="header-top">
  <h1>Сотрудники</h1>
  <button id="viewToggleBtn" class="view-toggle" title="Переключить вид">
    <i class="fas fa-th-large"></i> Карточки
  </button>
  <button id="themeToggle" class="theme-toggle">
    <i class="fas fa-moon"></i>
  </button>
</div>

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
              <th>Отдел</th>
              <th>Должность</th>
              <th>Телеграм(ID)</th>
              <th>Почта</th>
              <th>Телефон</th>
              <th>Роль</th>
			  <?php if ($rolea == 1) { 
			  echo 
              "<th></th>";
			  }
			  ?>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $options_otd = "";
                    foreach ($otdeli_data as $otd) {
                      $selected = ($otd['id_otd'] == $row['id_otd']) ? "selected" : "";
                      $options_otd .= "<option value='{$otd['id_otd']}' $selected>{$otd['name_otd']}</option>";
                    }

                $options_dolzh = "";
                foreach ($dolzh_data as $dolzh) {
                  $selected = ($dolzh['id_dolzh'] == $row['id_dolzh']) ? "selected" : "";
                  $options_dolzh .= "<option value='{$dolzh['id_dolzh']}' $selected>{$dolzh['namedolzh']}</option>";
                }
                echo "<tr>";
                echo "<td>{$row['id_use']}</td>";
                echo "<td>{$row['Familiya']}</td>";
                echo "<td>{$row['Imya']}</td>";
                echo "<td>{$row['Otchestvo']}</td>";

                echo "<td data-field='otdel' data-value='{$row['id_otd']}' data-options=\"" . htmlspecialchars($options_otd, ENT_QUOTES) . "\">{$row['name_otd']}</td>";

                echo "<td data-field='dolzh' data-value='{$row['id_dolzh']}' data-options=\"" . htmlspecialchars($options_dolzh, ENT_QUOTES) . "\">{$row['namedolzh']}</td>";

                echo "<td>{$row['telegramID']}</td>";
                echo "<td>{$row['mailc']}</td>";
                echo "<td>{$row['phone']}</td>";

                $roleOptions = "
                <option value='1' " . ($row['admin'] == 1 ? "selected" : "") . ">Администратор</option>
                <option value='0' " . ($row['admin'] == 0 ? "selected" : "") . ">Пользователь</option>
            ";
                echo "<td data-field='role' data-value='{$row['admin']}' data-options=\"" . htmlspecialchars($roleOptions, ENT_QUOTES) . "\">" . ($row['admin'] ? "Администратор" : "Пользователь") . "</td>";

                if ($rolea == 1) {
					echo "<td>
						<button class='delete-btn'>X</button> 
						<button class='edit-btn'>✎</button> 
						<button class='cancel-btn' style='display: none;'>✗</button> 
						<button class='save-btn' style='display: none;'>✔</button>
					</td>";
}
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='11'>Нет данных</td></tr>";
            }
            ?>
          </tbody>
        </table>

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
                  if (field === 'otdel' || field === 'dolzh' || field === 'role') {
                    let select = document.createElement('select');
                    select.innerHTML = cell.dataset.options; // Вставляем HTML со всеми option
                    select.value = cell.dataset.value; // Выбираем текущий ID

                    // Устанавливаем выбранный option для соответствующего значения
                    let selectedOption = select.querySelector(`option[value="${select.value}"]`);
                    if (selectedOption) {
                      selectedOption.selected = true;
                    }

                    select.dataset.originalValue = cell.dataset.value;
                    select.addEventListener('change', () => cell.dataset.value = select.value);
                    cell.textContent = '';
                    cell.appendChild(select);
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
                if (cell.querySelector('select')) {
                  let select = cell.querySelector('select');
                  let selectedOption = select.querySelector(`option[value="${select.dataset.originalValue}"]`);
                  cell.textContent = selectedOption ? selectedOption.textContent : select.dataset.originalValue;
                } else {
                  cell.textContent = originalValues[index];
                }
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
              let lastName = row.cells[1].textContent.trim();
			  let firstName = row.cells[2].textContent.trim();
			  let middleName = row.cells[3].textContent.trim();
			  let telegramID = row.cells[6].textContent.trim();
			  let email = row.cells[7].textContent.trim();
			  let phone = row.cells[8].textContent.trim();
		
              let otdel = row.querySelector('[data-field="otdel"] select').value;
              let dolzh = row.querySelector('[data-field="dolzh"] select').value;
              let role = row.querySelector('[data-field="role"] select').value;
              let deleteBtn = row.querySelector('.delete-btn');

              // Отправка данных на сервер через AJAX
              fetch('upd_php/sotrud_upd.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: `id=${id}&lastName=${encodeURIComponent(lastName)}&firstName=${encodeURIComponent(firstName)}&middleName=${encodeURIComponent(middleName)}&otdel=${otdel}&dolzh=${dolzh}&role=${role}&telegramID=${encodeURIComponent(telegramID)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}`
			})
			.then(response => response.text())
			.then(response => {
				if (response.trim() === "success") {
					alert("Данные успешно обновлены!");
					location.reload(); // Перезагрузка страницы после обновления
				} else {
					alert("Ошибка при обновлении: " + response);
				}
			})
			.catch(error => {
				alert("Ошибка при отправке запроса: " + error);
			});
				

			
              // Отключаем редактирование и устанавливаем текст
              cells.forEach(cell => {
                if (cell.querySelector('select')) {
                  let select = cell.querySelector('select');
                  let selectedOption = select.querySelector(`option[value="${select.value}"]`);
                  cell.textContent = selectedOption ? selectedOption.textContent : select.value;
                } else {
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



      </div>
	  <?php if ($rolea == 1): ?>
      <div class="form-container collapsed" id="formContainer">
		
       <div class="toggle-btn" id="toggleBtn"></div>
		<?php endif; ?>
        <form id="employeeForm" action="add_php/add_sotrud.php" method="post" style="display: none">
          <div class="form-row">
            <input type="text" id="lastName" name="lastName" placeholder="Фамилия" required />
            <input type="text" id="firstName" name="firstName" placeholder="Имя" required />
            <input type="text" id="middleName" name="middleName" placeholder="Отчество" required />
          </div>
          <div class="form-row">
            <input type="email" id="email" name="email" placeholder="Почта" required />
            <input type="tel" id="telephone" name="telephone" placeholder="Телефон" required />
            <input type="text" name="telegramid" id="telegramid" placeholder="Telegram-ID" required />
          </div>
          <div class="form-row">
            <select id="otdel" name="otdel" required>
                  <option value="" disabled selected>Отдел</option>
                  <?php
                  foreach ($otdeli_data as $row) {
                    echo '<option value="' . $row['id_otd'] . '">' . $row['name_otd'] . '</option>';
                  }
                  ?>
                </select>

            <select id="dolznost" name="dolznost" required>
              <option value="" disabled selected>Должность</option>
              <?php
              foreach ($dolzh_data as $row) {
                echo '<option value="' . $row['id_dolzh'] . '">' . $row['namedolzh'] . '</option>';
              }
              ?>
            </select>

            <select id="role" name="role" required>
              <option value="" disabled selected>Роль</option>
              <option value="1" <?php if ($user_role == 1)
                echo 'selected'; ?>>Администратор</option>
              <option value="0" <?php if ($user_role == 0)
                echo 'selected'; ?>>Пользователь</option>
            </select>
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
        let row = $(this).closest("tr");
        let id = row.find("td:first").text().trim();

        if (confirm(`Вы уверены, что хотите удалить запись с ID ${id}?`)) {
          $.ajax({
            url: "del_php/delete_sotrud.php",
            type: "POST",
            data: { id: id },
            success: function (response) {
              if (response.trim() === "success") {
                row.remove();
              } else {
                alert("Ошибка удаления: " + response);
              }
            },
            error: function () {
              alert("Произошла ошибка при отправке запроса.");
            }
          });
        }
      });
    });
  </script>
  <script>
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
      localStorage.setItem('employeeViewMode', 'cards');
      generateCards();
    } else {
      // Переключаемся на таблицу
      tableView.style.display = 'table';
      cardsView.style.display = 'none';
      toggleBtn.innerHTML = '<i class="fas fa-th-large"></i> Карточки';
      localStorage.setItem('employeeViewMode', 'table');
    }
    
    toggleBtn.classList.toggle('active');
  }
  
  // Проверяем сохранённый вид при загрузке
  document.addEventListener('DOMContentLoaded', function() {
    const savedViewMode = localStorage.getItem('employeeViewMode');
    const toggleBtn = document.getElementById('viewToggleBtn');
    
    if (savedViewMode === 'cards') {
      // Если был сохранён вид карточек, переключаем сразу
      setTimeout(() => {
        toggleView();
      }, 100);
    }
    
    // Назначаем обработчик клика
    toggleBtn.addEventListener('click', toggleView);
  });

  // Генерация карточек на основе табличных данных
  function generateCards() {
  const cardsContainer = document.getElementById('cardsContainer');
  cardsContainer.innerHTML = '';
  
  const rows = document.querySelectorAll('#dataTable tbody tr');
  rows.forEach(row => {
    if (row.cells.length < 10) return;
    
    const id = row.cells[0].textContent;
    const lastName = row.cells[1].textContent;
    const firstName = row.cells[2].textContent;
    const middleName = row.cells[3].textContent;
    const otdel = row.cells[4].textContent;
    const dolzh = row.cells[5].textContent;
    const telegram = row.cells[6].textContent;
    const email = row.cells[7].textContent;
    const phone = row.cells[8].textContent;
    const role = row.cells[9].textContent;
    
    const card = document.createElement('div');
    card.className = 'employee-card';
    
    card.innerHTML = `
      <div class="card-id">ID: ${id}</div>
      <div class="card-content">
        <div class="employee-name">${lastName} ${firstName} ${middleName}</div>
        <div class="card-row">
          <span class="card-label">Должность:</span>
          <span class="card-value">${dolzh}</span>
        </div>
        <div class="card-row">
          <span class="card-label">Отдел:</span>
          <span class="card-value">${otdel}</span>
        </div>
        <div class="card-row">
          <span class="card-label">Роль:</span>
          <span class="card-value">${role}</span>
        </div>
        <div class="card-row">
          <span class="card-label">Телефон:</span>
          <span class="card-value">${phone}</span>
        </div>
        <div class="card-row">
          <span class="card-label">Email:</span>
          <span class="card-value">${email}</span>
        </div>
        <div class="card-row">
          <span class="card-label">Telegram:</span>
          <span class="card-value">${telegram || 'не указан'}</span>
        </div>
      </div>
    `;
    
    if (row.cells[10]) {
      const actionsDiv = document.createElement('div');
      actionsDiv.className = 'card-actions';
      actionsDiv.innerHTML = `
        <button class="edit-btn"><i class="fas fa-edit"></i> Редактировать</button>
        <button class="delete-btn"><i class="fas fa-trash"></i> Удалить</button>
      `;
      card.appendChild(actionsDiv);
      
      // Обработчики событий
      const editBtn = actionsDiv.querySelector('.edit-btn');
      const deleteBtn = actionsDiv.querySelector('.delete-btn');
      
      if (editBtn) editBtn.addEventListener('click', function() {
        document.getElementById('tableViewBtn').click();
        const tableRow = document.querySelector(`#dataTable tbody tr:has(td:first-child:contains("${id}"))`);
        if (tableRow) tableRow.querySelector('.edit-btn').click();
      });
      
      if (deleteBtn) deleteBtn.addEventListener('click', function() {
        if (confirm(`Вы уверены, что хотите удалить сотрудника ${lastName} ${firstName}?`)) {
          $.ajax({
            url: "del_php/delete_sotrud.php",
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
    }
    
    cardsContainer.appendChild(card);
  });
}
</script>
</body>

</html>