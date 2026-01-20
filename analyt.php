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

$rolea = $_SESSION['rolea'];

// Получение данных из базы данных
$employeeCount = $conn->query("SELECT COUNT(*) as count FROM userscr")->fetch_assoc()['count'];
$clientCount = $conn->query("SELECT COUNT(*) as count FROM clientsc")->fetch_assoc()['count'];
$employeesByPosition = $conn->query("SELECT d.namedolzh, COUNT(u.id_use) as count FROM userscr u JOIN dolzh d ON u.dolzh_id = d.id_dolzh GROUP BY d.namedolzh")->fetch_all(MYSQLI_ASSOC);
$taskCount = $conn->query("SELECT COUNT(*) as count FROM zadachi")->fetch_assoc()['count'];
$requestCount = $conn->query("SELECT COUNT(*) as count FROM zayavki")->fetch_assoc()['count'];

// Исправленный запрос для сотрудников с активными заявками
$activeRequestsByEmployee = $conn->query("
    SELECT u.Familiya, COUNT(z.id_zay) as count 
    FROM zayavki z 
    JOIN userscr u ON z.sot_id = u.id_use 
    WHERE z.status_id IN (1, 2,3,4) -- Статусы 'Новая' и 'В работе'
    GROUP BY u.Familiya
")->fetch_all(MYSQLI_ASSOC);

$requestsByStatus = $conn->query("SELECT s.name_stat, COUNT(z.id_zay) as count FROM zayavki z JOIN status s ON z.status_id = s.id_stat GROUP BY s.name_stat")->fetch_all(MYSQLI_ASSOC);
$activeTasksByEmployee = $conn->query("SELECT u.Familiya, COUNT(z.id_zadch) as count FROM zadachi z JOIN userscr u ON z.ispolnit_id = u.id_use WHERE z.status_id IN (1, 2) GROUP BY u.Familiya")->fetch_all(MYSQLI_ASSOC);
$tasksByStatus = $conn->query("SELECT s.name_stat, COUNT(z.id_zadch) as count FROM zadachi z JOIN status s ON z.status_id = s.id_stat GROUP BY s.name_stat")->fetch_all(MYSQLI_ASSOC);
$otdels = $conn->query("SELECT COUNT(*) AS total_count FROM otdeli")->fetch_assoc()['total_count'];
$dolzh = $conn->query("SELECT COUNT(*) AS total_count FROM dolzh")->fetch_assoc()['total_count'];

// Новые запросы
$employeesByDepartment = $conn->query("SELECT o.name_otd, COUNT(u.id_use) as count FROM userscr u JOIN otdeli o ON u.otdel_id = o.id_otd GROUP BY o.name_otd")->fetch_all(MYSQLI_ASSOC);

// Преобразование данных в JSON
$data = json_encode([
    'employeeCount' => $employeeCount,
    'clientCount' => $clientCount,
    'employeesByPosition' => $employeesByPosition,
    'taskCount' => $taskCount,
    'requestCount' => $requestCount,
    'activeRequestsByEmployee' => $activeRequestsByEmployee,
    'requestsByStatus' => $requestsByStatus,
    'activeTasksByEmployee' => $activeTasksByEmployee,
    'tasksByStatus' => $tasksByStatus,
    'employeesByDepartment' => $employeesByDepartment,
	'otdels' => $otdels,
	'dolzh' => $dolzh,
	
]);

// Закрываем подключение
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="Styles\mainstyle.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="Scripts\script.js"></script>
  <title>NG-CRM - Аналитика</title>
  <style>
    /* Общие стили для контейнеров с графиками */
    .chart-container {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
      margin-bottom: 20px;
    }

    .chart-container h2 {
      font-size: 18px;
      margin-bottom: 15px;
      color: #333;
    }

    /* Стили для текстовых блоков с аналитикой */
    .text-analytics {
      display: grid;
      grid-template-columns: repeat(3, 1fr); /* Три колонки */
      gap: 20px; /* Расстояние между блоками */
      margin-bottom: 20px;
    }

    /* Стили для canvas (графиков) */
    canvas {
      max-width: 100%;
      height: auto;
    }

    /* Стили для сетки графиков */
    .charts-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr); /* Три колонки */
      gap: 20px; /* Расстояние между блоками */
    }

    /* Адаптивные стили */
    @media (max-width: 768px) {
      .text-analytics,
      .charts-grid {
        grid-template-columns: 1fr; /* Одна колонка на мобильных устройствах */
      }

      .chart-container {
        padding: 15px;
      }

      .chart-container h2 {
        font-size: 16px;
      }
    }
  </style>
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
          <li class="active">
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
          <h1>Аналитика</h1>
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

      <!-- Блок с аналитикой -->
      <div class="analytics-content">
        <!-- Текстовые блоки с аналитикой -->
        <div class="text-analytics">
          <div class="chart-container">
            <h2>Количество сотрудников: <span id="employeeCount"></span></h2>
          </div>
          <div class="chart-container">
            <h2>Количество клиентов: <span id="clientCount"></span></h2>
          </div>
          <div class="chart-container">
            <h2>Количество задач: <span id="taskCount"></span></h2>
          </div>
          <div class="chart-container">
            <h2>Количество заявок: <span id="requestCount"></span></h2>
          </div>
			<div class="chart-container">
            <h2>Количество отделов: <span id="otdels"></span></h2>
          </div>
		  	<div class="chart-container">
            <h2>Количество должностей: <span id="dolzh"></span></h2>
          </div>
        </div>

        <!-- Графики -->
        <div class="charts-grid">
          <div class="chart-container">
            <h2>Сотрудники по должностям</h2>
            <canvas id="employeesByPositionChart"></canvas>
          </div>
          <div class="chart-container">
            <h2>Заявки по статусам</h2>
            <canvas id="requestsByStatusChart"></canvas>
          </div>
          <div class="chart-container">
            <h2>Сотрудники с активными задачами</h2>
            <canvas id="activeTasksByEmployeeChart"></canvas>
          </div>
          <div class="chart-container">
            <h2>Задачи по статусам</h2>
            <canvas id="tasksByStatusChart"></canvas>
          </div>
          <div class="chart-container">
            <h2>Сотрудники с активными заявками</h2>
            <canvas id="activeRequestsByEmployeeChart"></canvas>
          </div>
          <div class="chart-container">
            <h2>Сотрудники по отделам</h2>
            <canvas id="employeesByDepartmentChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Данные из PHP
    const data = <?php echo $data; ?>;

    // Отображение текстовых данных
    document.getElementById('employeeCount').textContent = data.employeeCount;
    document.getElementById('clientCount').textContent = data.clientCount;
    document.getElementById('taskCount').textContent = data.taskCount;
    document.getElementById('requestCount').textContent = data.requestCount;
	document.getElementById('otdels').textContent = data.otdels;
	document.getElementById('dolzh').textContent = data.dolzh;

    // Функция для создания графиков
    function createChart(canvasId, type, labels, data, label) {
      const ctx = document.getElementById(canvasId).getContext('2d');
      new Chart(ctx, {
        type: type,
        data: {
          labels: labels,
          datasets: [{
            label: label,
            data: data,
            backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: label
            }
          }
        }
      });
    }

    // Создание графиков
    createChart('employeesByPositionChart', 'bar', 
      data.employeesByPosition.map(item => item.namedolzh), 
      data.employeesByPosition.map(item => item.count), 
      'Сотрудники по должностям'
    );

    createChart('requestsByStatusChart', 'pie', 
      data.requestsByStatus.map(item => item.name_stat), 
      data.requestsByStatus.map(item => item.count), 
      'Заявки по статусам'
    );

    createChart('activeTasksByEmployeeChart', 'bar', 
      data.activeTasksByEmployee.map(item => item.Familiya), 
      data.activeTasksByEmployee.map(item => item.count), 
      'Сотрудники с активными задачами'
    );

    createChart('tasksByStatusChart', 'pie', 
      data.tasksByStatus.map(item => item.name_stat), 
      data.tasksByStatus.map(item => item.count), 
      'Задачи по статусам'
    );

    // Новые графики
    createChart('activeRequestsByEmployeeChart', 'bar', 
      data.activeRequestsByEmployee.map(item => item.Familiya), 
      data.activeRequestsByEmployee.map(item => item.count), 
      'Сотрудники с активными заявками'
    );

    createChart('employeesByDepartmentChart', 'bar', 
      data.employeesByDepartment.map(item => item.name_otd), 
      data.employeesByDepartment.map(item => item.count), 
      'Сотрудники по отделам'
    );
  </script>
</body>
</html>