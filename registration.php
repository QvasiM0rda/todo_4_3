<?php
namespace todo;
include ('function.php');

//Регистрация пользователя
if (!empty($_POST['registration'])) {
  echo $user->addUser($_POST['login'], $_POST['password']);
  $_SESSION['id'] = $user->getUserID($_POST['login']);
  header('Refresh: 5; index.php');
}

//Возврат на страницу входа
if (!empty($_POST['back'])) {
  header('Location: index.php');
  die;
}
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Регистрация</title>
</head>
<body>
  <form method="post">
    <label for="login">Введите логин: </label>
    <input type="text" name="login" id="login">
    <br>
    <label for="password">Введите пароль: </label>
    <input type="password" name="password" id="password">
    <br>
    <input type="submit" name="registration" value="Зарегистрироваться">
    <input type="submit" name="back" value="Вернуться на страницу входа">
  </form>
</body>
</html>