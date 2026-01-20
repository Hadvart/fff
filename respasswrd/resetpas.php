<?php
// Старт сессии
session_start();

// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверяем наличие токена в URL
if (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']); // Получаем токен и экранируем его
} else {
    // Если токен не передан, перенаправляем на страницу восстановления пароля
    echo "<script>
            alert('Ошибка: токен не найден. Пожалуйста, повторите попытку.');
            window.location.href = '/restore';
          </script>";
    exit; // Прекращаем выполнение скрипта
}

// Проверяем наличие переменных сессии
if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_code'])) {
    // Если переменные сессии отсутствуют, перенаправляем на страницу восстановления пароля
    echo "<script>
            alert('Ошибка: сессия истекла или данные не найдены. Пожалуйста, повторите попытку.');
            window.location.href = '/restore';
          </script>";
    exit;
}

// Подключаемся к базе данных через файл subd.php
include 'subd.php'; // Этот файл должен содержать подключение к БД

// Проверяем, установилось ли соединение
if (!isset($conn)) {
    die("Ошибка подключения к базе данных. Пожалуйста, проверьте настройки подключения.");
}

// Подготавливаем SQL-запрос для проверки токена и сессии
$user_id = $_SESSION['user_id']; // Получаем user_id из сессии
$session_code = $_SESSION['session_code']; // Получаем session_code из сессии

// Запрос на проверку токена, session_code и user_id в таблице vosstanovlenie
$query = "SELECT * FROM vosstanovlenie WHERE token = ? AND session = ? AND user_id = ?";

// Подготовка запроса
$stmt = $conn->prepare($query);

// Проверка на ошибку при подготовке
if ($stmt === false) {
    die('Ошибка подготовки запроса: ' . $conn->error); // Показываем подробную ошибку при проблемах с подготовкой
}

// Привязываем параметры
$stmt->bind_param('ssi', $token, $session_code, $user_id); // s - строка, i - целое число

// Выполняем запрос
if (!$stmt->execute()) {
    die('Ошибка выполнения запроса: ' . $stmt->error); // Показываем ошибку при выполнении запроса
}

// Получаем результат
$result = $stmt->get_result();

// Проверяем, есть ли совпадение
if ($result->num_rows == 0) {
    // Если данных нет, выводим ошибку и перенаправляем
    echo "<script>
            alert('Ошибка: данные не совпадают или токен истек.');
            window.location.href = '/restore';
          </script>";
    exit;
} else {
    // Если данные совпадают, продолжаем выполнение
    // Например, можно показать форму для восстановления пароля
    // Получаем почту из таблицы userscr
    $query_user = "SELECT mailc FROM userscr WHERE id_use = ?";
    $stmt_user = $conn->prepare($query_user);
    $stmt_user->bind_param('i', $user_id); // Привязываем id_use как целое число
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    // Если почта найдена
    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $mailc = $user['mailc']; // Сохраняем почту в переменную
    } else {
        die('Ошибка: пользователь не найден в базе.');
    }
}

// Закрываем соединение
$stmt->close();
$stmt_user->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Styles/loginstyle.css">
    <link rel="stylesheet" href="images/style.css">
    <script src="/Scripts/script.js"></script>
    <title>NG-CRM - Восстановление пароля</title>
</head>
<body>
    <header>
        <div class="header-left">
            <img src="../images/nglogo.png" alt="Логотип NG-CRM" class="logo" id="logo">
        </div>
        <span class="header-text">NG-CRM</span>
        <button class="theme-toggle" id="themeToggle">
            <i class="fas fa-moon"></i>
        </button>
    </header>

    <div class="login-form2">
		<form action="endreset" method="post">
			<p class="explain">Введите новый пароль для вашего аккаунта.</p>
			<p class="account-info"><strong>Аккаунт:</strong> <span class="highlight"><?= htmlspecialchars($mailc) ?></span></p>

			<div class="input-box">
				<span class="icon"><i class="fa-solid fa-lock"></i></span>
				<input type="password" id="password" name="password" placeholder=" " required minlength="6" autocomplete="new-password" />  
				<label for="password">Новый пароль</label>
			</div>

            <!-- Блок с политиками пароля -->
            <div id="password-policies">
                <ul>
                    <li id="length-policy" class="policy red">Минимум 6 символов</li>
                    <li id="uppercase-policy" class="policy red">Как минимум одна заглавная буква</li>
                    <li id="number-policy" class="policy red">Как минимум одна цифра</li>
                    <li id="special-policy" class="policy red">Как минимум один специальный символ</li>
                </ul>
            </div>

			<div class="button-container">
				<input type="submit" value="Сохранить" id="save-button" disabled />
				<button type="button" class="cancel" onclick="logout()">Отмена</button>
				<script>
				function logout() {
					// Отправляем запрос на сервер для завершения сессии
					window.location.href = 'stopsession'; // Перенаправляем на страницу для завершения сессии
				}
				</script>
			</div>
		</form>

    </div>

    <script>
        // Функция для проверки пароля
        function checkPassword() {
            var password = document.getElementById('password').value;
            var saveButton = document.getElementById('save-button');
            var policies = {
                length: document.getElementById('length-policy'),
                uppercase: document.getElementById('uppercase-policy'),
                number: document.getElementById('number-policy'),
                special: document.getElementById('special-policy')
            };

            // Проверка длины
            if (password.length >= 6) {
                policies.length.classList.remove('red');
                policies.length.classList.add('green');
            } else {
                policies.length.classList.remove('green');
                policies.length.classList.add('red');
            }

            // Проверка на заглавные буквы
            if (/[A-Z]/.test(password)) {
                policies.uppercase.classList.remove('red');
                policies.uppercase.classList.add('green');
            } else {
                policies.uppercase.classList.remove('green');
                policies.uppercase.classList.add('red');
            }

            // Проверка на цифры
            if (/\d/.test(password)) {
                policies.number.classList.remove('red');
                policies.number.classList.add('green');
            } else {
                policies.number.classList.remove('green');
                policies.number.classList.add('red');
            }

            // Проверка на специальные символы
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                policies.special.classList.remove('red');
                policies.special.classList.add('green');
            } else {
                policies.special.classList.remove('green');
                policies.special.classList.add('red');
            }

            // Включаем кнопку "Сохранить", если все требования выполнены
            if (password.length >= 6 && /[A-Z]/.test(password) && /\d/.test(password) && /[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                saveButton.disabled = false;
            } else {
                saveButton.disabled = true;
            }
        }

        // Добавляем обработчик на ввод в поле пароля
        document.getElementById('password').addEventListener('input', checkPassword);
    </script>
</body>
</html>
