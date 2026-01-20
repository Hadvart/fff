<?php
session_start();

// Если пользователь уже авторизован – сразу редиректим
if (isset($_SESSION['user_id'])) {
    header("Location: applications.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="stylesheet" href="Styles/loginstyle.css" />
    <script src="Scripts/script.js"></script>
    <title>NG-CRM - Авторизация</title>
  </head>
  <body>
    <header>
      <div class="header-left">
        <img
          src="images/nglogo.png"
          alt="Логотип NG-CRM"
          class="logo"
          id="logo"
        />
      </div>
      <span class="header-text">NG-CRM</span>
      <button class="theme-toggle" id="themeToggle">
        <i class="fas fa-moon"></i>
      </button>
    </header>

    <div class="login-form">
      <form action="php/accsess.php" method="post">
        <div class="input-box">
          <span class="icon"><i class="fas fa-user"></i></span>
          <input
            type="text"
            name="username"
            id="username"
            placeholder=" "
            required
          />
          <label for="username">Имя пользователя</label>
        </div>
        <div class="input-box">
          <span class="icon"><i class="fas fa-lock"></i></span>
          <input
            type="password"
            name="password"
            id="password"
            placeholder=" "
            required
          />
          <label for="password">Пароль</label>
          <span class="eye-icon" id="togglePassword">
            <i class="fas fa-eye"></i>
          </span>
        </div>
        <p
          class="forgotPassword"
          onclick="window.location.href='restore';"
        >
          Забыли пароль?
        </p>
        <input type="submit" value="Войти" />
      </form>
    </div>
  </body>
</html>