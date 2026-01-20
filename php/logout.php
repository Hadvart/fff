<?php
session_start(); // Начинаем сессию

// Очищаем все данные сессии
session_unset();

// Уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на страницу логина
header("Location: /login");
exit();
?>
