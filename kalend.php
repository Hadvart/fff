<?php
session_start();

// Проверка, что сессионные переменные установлены
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['dolzhid'])) {
  header("Location: login");
  exit();
}

// Подключаем файл с соединением к базе
include('php/subd.php');

$user_id = $_SESSION['user_id']; // ID текущего пользователя

// Строим SQL-запрос в зависимости от значения user_id
if ($user_id == 1 || $user_id == 4) {
    // Запрос для всех заявок
    $sql = "SELECT *
            FROM full_zayavki_view WHERE id_stat NOT IN (4, 5)";
} else {
    // Запрос только для текущего пользователя
    $sql = "SELECT * FROM full_zayavki_view WHERE id_use = ? AND id_stat NOT IN (4, 5)";
}

// Подготовка и выполнение запроса
$stmt = $conn->prepare($sql);

// Привязка параметра, если пользователь не имеет прав администратора
if ($user_id != 1 && $user_id != 4) {
  $stmt->bind_param('i', $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Обрабатываем результат запроса
$zayavki = [];

while ($row = $result->fetch_assoc()) {
  // Получаем данные из результата запроса
  $zayavki[] = [
    'id_zay' => $row['id_zay'],
    'sotrudnik_familiya' => $row['sotrudnik_familiya'],
    'sotrudnik_imya' => $row['sotrudnik_imya'],
    'sotrudnik_otchestvo' => $row['sotrudnik_otchestvo'],
    'client_familiya' => $row['client_familiya'],
    'client_imya' => $row['client_imya'],
    'client_otchestvo' => $row['client_otchestvo'],
    'client_organization' => $row['client_organization'],
    'dedlain' => $row['dedlain'],
	'status' => $row['status']
  ];
}


// Строим SQL-запрос в зависимости от значения user_id
if ($user_id == 1 || $user_id == 4) {
    // Запрос для всех задач
    $sql = "SELECT *
            FROM zadachi_full_view WHERE id_stat NOT IN (4, 5)";
} else {
    // Запрос только для задач, где пользователь является инициатором или исполнителем
    $sql = "SELECT *
            FROM zadachi_full_view
            WHERE init_id_use = ? OR isp_id_use = ? AND id_stat NOT IN (4, 5)";
}

// Подготовка и выполнение запроса
$stmt = $conn->prepare($sql);

// Привязка параметра, если пользователь не имеет прав администратора
if ($user_id != 1 && $user_id != 4) {
  $stmt->bind_param('ii', $user_id, $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Обрабатываем результат запроса
$zadachi = [];

while ($row = $result->fetch_assoc()) {
  // Сохраняем данные в массив
  $zadachi[] = [
    'id_zadch' => $row['id_zadch'],
    'status_name' => $row['status_name'],
    'init_Familiya' => $row['init_Familiya'],
    'init_Imya' => $row['init_Imya'],
    'init_Otchestvo' => $row['init_Otchestvo'],
    'isp_Familiya' => $row['isp_Familiya'],
    'isp_Imya' => $row['isp_Imya'],
    'isp_Otchestvo' => $row['isp_Otchestvo'],
	'data_dedl' => $row['data_dedl'] 
  ];
}


$stmt->close();
?>



<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="Styles/mainstyle.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="Scripts/script.js"></script>
  <title>NG-CRM - Календарь</title>
<style>
  /* Основные стили */
  body,
  html {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: Arial, sans-serif;
  }

  .main-content {
    padding: 20px;
  }

  .overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
  }

  .overlay-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    max-width: 500px;
    width: 100%;
    text-align: center;
  }

  .overlay-content h2 {
    margin-top: 0;
  }

  .overlay-content button {
    background: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
  }

  .overlay-content button:hover {
    background: #0056b3;
  }

  @media (prefers-color-scheme: dark) {
    :root {
      --calendar-day-bg: #333;
      --calendar-day-text: #fff;
      --calendar-day-hover-bg: #444;
      --calendar-today-bg: #007bff;
      --calendar-today-text: #fff;
      --calendar-zayavka-bg: #ffcc00;
      --calendar-zayavka-border: #ff9900;
      --calendar-zayavka-icon: #ff6600;
      --zayavka-bg: rgba(30, 30, 30, 0.9);
      --zayavka-border: #555;
      --zayavka-text: #ddd;
      --zayavka-title: #4da6ff;
      --zayavka-link: #4da6ff;
    }
  }
  
  #overlayDate {
  color: gray; /* Серый цвет текста */
}

.calendar-day.has-zayavka {
  background-color: #f9d342; /* Цвет для заявки */
}

.calendar-day.has-zadacha {
  background-color: #aad3ff; /* Цвет для задачи (синий) */
}

.calendar-day.has-zayavka.has-zadacha {
  background-color: #d5aaff; /* Цвет для заявки и задачи */
}

.calendar-day.has-both {
  background-color: #d5aaff; /* Цвет для заявки и задачи */
}

/**/

body.dark-theme .calendar-day.has-zayavka {
  background-color: #cca81f; /* Цвет для заявки */
}

body.dark-theme .calendar-day.has-zadacha {
  background-color: #5e88b5; /* Цвет для задачи (синий) */
}

body.dark-theme .calendar-day.has-zayavka.has-zadacha {
  background-color: #d5aaff; /* Цвет для заявки и задачи */
}

body.dark-theme .calendar-day.has-both {
  background-color: #d5aaff; /* Цвет для заявки и задачи */
}




</style>





</head>

<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logo-container">
        <img src="images/nglogo_light.png" alt="Logo" />
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
          <li class="active">
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
    <div class="main-content">
      <header class="header">
        <div class="header-top">
          <h1>Календарь</h1>
          <button id="themeToggle" class="theme-toggle">
            <i class="fas fa-moon"></i>
          </button>
        </div>
      </header>
      <!-- Контейнер для календаря -->
      <div class="calendar">
        <div class="calendar-header">
          <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
          <h2 id="currentMonth"></h2>
          <button id="nextMonth"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
      </div>
    </div>
  </div>

  <!-- Оверлей для выбранной даты -->
  <div class="overlay" id="overlay">
    <div class="overlay-content">
      <h2 id="overlayDate"></h2>
      <p id="overlayInfo">Информация по выбранной дате.</p>
      <button id="closeOverlay">Закрыть</button>
    </div>
  </div>

  <!-- Подключаем Day.js для работы с датами -->
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/dayjs.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1.10.7/locale/ru.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
  const calendarGrid = document.getElementById('calendarGrid');
  const currentMonthElement = document.getElementById('currentMonth');
  const overlay = document.getElementById('overlay');
  const overlayDate = document.getElementById('overlayDate');
  const overlayInfo = document.getElementById('overlayInfo');
  const closeOverlayButton = document.getElementById('closeOverlay');
  const zayavki = <?php echo json_encode($zayavki); ?>;
  const zadachi = <?php echo json_encode($zadachi); ?>;
  let currentDate = dayjs();

  // Устанавливаем локаль на русский
  dayjs.locale('ru');

  // Функция для отрисовки календаря
  function renderCalendar(date) {
    const startOfMonth = date.startOf('month');
    const endOfMonth = date.endOf('month');
    const daysInMonth = date.daysInMonth();

    // Получаем день недели для первого дня месяца (0 - воскресенье, 1 - понедельник, ..., 6 - суббота)
    const startDay = startOfMonth.day();

    // Корректируем startDay для России (понедельник - первый день недели)
    const startDayCorrected = startDay === 0 ? 6 : startDay - 1; // Если воскресенье (0), то это 6-й день (суббота)

    // Очищаем сетку календаря
    calendarGrid.innerHTML = '';

    // Отображаем текущий месяц и год
    currentMonthElement.textContent = date
      .locale('ru')
      .format('MMMM YYYY')
      .replace(/^\p{L}/u, match => match.toUpperCase());

    // Добавляем пустые ячейки для дней предыдущего месяца
    for (let i = 0; i < startDayCorrected; i++) {
      const emptyDay = document.createElement('div');
      emptyDay.classList.add('calendar-day', 'empty');
      calendarGrid.appendChild(emptyDay);
    }

    // Добавляем дни текущего месяца
    for (let i = 1; i <= daysInMonth; i++) {
      const day = document.createElement('div');
      day.classList.add('calendar-day');
      day.textContent = i;

      // Проверяем, есть ли заявка или задача на эту дату
      const selectedDate = date.date(i).format('DD.MM.YYYY');
      const zayavkaForDay = zayavki.filter(zayavka => dayjs(zayavka.dedlain).format('DD.MM.YYYY') === selectedDate);
      const zadachaForDay = zadachi.filter(zadacha => dayjs(zadacha.data_dedl).format('DD.MM.YYYY') === selectedDate);

      // Добавляем информацию о количестве заявок и задач
      const infoContainer = document.createElement('div');
      infoContainer.classList.add('day-info');
      infoContainer.innerHTML = `
        <div>Заявка - ${zayavkaForDay.length}</div>
        <div>Задача - ${zadachaForDay.length}</div>
      `;
      day.appendChild(infoContainer);

      // Определяем цвет в зависимости от наличия заявок и задач
      if (zayavkaForDay.length > 0 && zadachaForDay.length > 0) {
        day.classList.add('has-both');
      } else if (zayavkaForDay.length > 0) {
        day.classList.add('has-zayavka');
      } else if (zadachaForDay.length > 0) {
        day.classList.add('has-zadacha');
      }

      // Обработчик клика по дню
      day.addEventListener('click', () => {
        if (zayavkaForDay.length > 0 || zadachaForDay.length > 0) {
          overlayDate.textContent = `События на ${selectedDate}`;

          // Строим информацию по заявкам и задачам
          let eventDetails = '';

          if (zayavkaForDay.length > 0) {
            eventDetails += zayavkaForDay.map((zayavka, index) => {
              return `
                <div class="zayavka-info">
                  <h3>Заявка ${index + 1} (ID: ${zayavka.id_zay})</h3>
                  <p><strong>Статус:</strong> ${zayavka.status}</p>
                  <p><strong>Сотрудник:</strong> ${zayavka.sotrudnik_familiya} ${zayavka.sotrudnik_imya} ${zayavka.sotrudnik_otchestvo}</p>
                  <p><strong>Клиент:</strong> ${zayavka.client_familiya} ${zayavka.client_imya} ${zayavka.client_otchestvo}</p>
                  <p><strong>Организация:</strong> ${zayavka.client_organization}</p>
                  <p><strong>Ссылка:</strong> <a href="https://ngsoftcrm.ru/zaytest?id=${zayavka.id_zay}" target="_blank">Перейти к заявке</a></p>
                </div>
                <hr />
              `;
            }).join('');
          }

          if (zadachaForDay.length > 0) {
            eventDetails += zadachaForDay.map((zadacha, index) => {
              return `
                <div class="zayavka-info">
                  <h3>Задача ${index + 1} (ID: ${zadacha.id_zadch})</h3>
                  <p><strong>Статус:</strong> ${zadacha.status_name}</p>
                  <p><strong>Инициатор:</strong> ${zadacha.init_Familiya} ${zadacha.init_Imya} ${zadacha.init_Otchestvo}</p>
                  <p><strong>Исполнитель:</strong> ${zadacha.isp_Familiya} ${zadacha.isp_Imya} ${zadacha.isp_Otchestvo}</p>
                  <p><strong>Ссылка:</strong> <a href="https://ngsoftcrm.ru/zadtest?id=${zadacha.id_zadch}" target="_blank">Перейти к задаче</a></p>
                </div>
                <hr />
              `;
            }).join('');
          }

          // Вставляем данные в оверлей
          overlayInfo.innerHTML = eventDetails;
          overlay.style.display = 'flex';
        }
      });

      calendarGrid.appendChild(day);
    }
  }

  // Инициализация календаря
  renderCalendar(currentDate);

  // Перелистывание месяцев
  document.getElementById('prevMonth').addEventListener('click', () => {
    currentDate = currentDate.subtract(1, 'month');
    renderCalendar(currentDate);
  });

  document.getElementById('nextMonth').addEventListener('click', () => {
    currentDate = currentDate.add(1, 'month');
    renderCalendar(currentDate);
  });

  // Закрытие оверлея
  closeOverlayButton.addEventListener('click', () => {
    overlay.style.display = 'none';
  });
});
  </script>
</body>

</html>
