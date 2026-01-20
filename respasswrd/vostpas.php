<?php
session_start();
require 'config.php'; // Подключение к файлу конфигурации
require 'subd.php';   // Подключение к базе данных
require 'PHPMailer/src/Exception.php';  // Подключение классов PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$config = require 'config.php'; // Получаем конфигурацию

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Фильтрация и валидация email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Некорректный email!'); window.location.href = '/restore';</script>";
        exit;
    }

    // Подготовка и выполнение запроса
    $stmt = $conn->prepare("SELECT id_use, mailc FROM userscr WHERE mailc = ?");
    
    if ($stmt === false) {
        echo "<script>alert('Ошибка запроса к базе данных!'); window.location.href = '/restore';</script>";
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        echo "<script>alert('Ошибка при получении данных!'); window.location.href = '/restore';</script>";
        exit;
    }
    
    $user = $result->fetch_assoc();

    if ($user) {
        // Генерация уникального токена и сессионного кода
        $token = bin2hex(random_bytes(16));
        $sessionCode = bin2hex(random_bytes(32));

        // Записываем данные в сессию
        $_SESSION['user_id'] = $user['id_use'];
        $_SESSION['session_code'] = $sessionCode;

        // Подготовка SQL-запроса
        $stmt_insert = $conn->prepare("INSERT INTO vosstanovlenie (user_id, token, session, try) VALUES (?, ?, ?, ?)");
        
        if ($stmt_insert === false) {
            echo "<script>alert('Ошибка запроса к базе данных!'); window.location.href = '/restore';</script>";
            exit;
        }
        
        $try = 1;
        $stmt_insert->bind_param("isss", $user['id_use'], $token, $sessionCode, $try);
        
        if ($stmt_insert->execute()) {
            // Отправка токена на email
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.mail.ru';
                $mail->SMTPAuth = true;
                $mail->Username = $config['username'];
                $mail->Password = $config['password'];
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->CharSet = 'UTF-8'; // Устанавливаем корректную кодировку
                $mail->setFrom($config['username'], 'CRM NG-SOFT');
                $mail->addAddress($email);

                // Контент письма
                $mail->isHTML(true);
                $mail->Subject = 'Восстановление пароля';
                $mail->Body = '
                    <html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    </head>
                    <body>
                        <p>Здравствуйте!</p>
                        <p>Вы запросили восстановление пароля. Чтобы продолжить, перейдите по следующей одноразовой ссылке:</p>
                        <p><a href="https://ngsoftcrm.ru/respasswrd/resetpas?token=' . $token . '" target="_blank">Восстановить пароль</a></p>
                        <p>Эта ссылка работает <strong>только с того устройства</strong>, с которого был подан запрос на восстановление пароля.</p>
                        <p>После того как вы перейдете по ней и смените пароль, ссылка станет недействительной.</p>
                        <p>Если вы не запрашивали восстановление пароля, проигнорируйте это сообщение.</p>
                        <p>С уважением, ngsoftcrm.ru</p>
                    </body>
                    </html>
                ';

                if ($mail->send()) {
                    echo "<script>alert('Инструкция по восстановлению пароля отправлена на почту!'); window.location.href = '/login';</script>";
                } else {
                    echo "<script>alert('Ошибка отправки email!'); window.location.href = '/restore';</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Ошибка: " . $e->getMessage() . "'); window.location.href = '/restore';</script>";
            }
        } else {
            echo "<script>alert('Ошибка при сохранении данных!'); window.location.href = '/restore';</script>";
        }
    } else {
        echo "<script>alert('Email не найден!'); window.location.href = '/restore';</script>";
    }
}
?>
