<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="stylesheet" href="Styles\loginstyle.css" />
    <script src="Scripts\script.js"></script>
    <title>NG-CRM - Авторизация</title>
  </head>
  <body>
    <header>
      <div class="header-left">
        <img
          src="images\nglogo.png"
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

    <div class="login-form2">
      <form action="respasswrd/vostpas" method="post">
        <p class="explain">Введите почту к которой привязан аккаунт. <br>На неё вам придёт новый пароль</p>
        <div class="input-box">
          <span class="icon"><i class="fa-solid fa-envelope"></i></span>
          <input
            type="email"
            name="email"
            placeholder=" "
            required
          />  
          <label for="email">Почта</label>
        </div>
        <div class="button-container">
          <input type="submit" value="Восстановить" />
          <button class="cancel" onclick="window.location.href='login';">
            Отмена
          </button>
        </div>
      </form>
    </div>
  </body>
</html>
