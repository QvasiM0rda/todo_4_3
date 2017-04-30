<?php
namespace todo;
include 'function.php';
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Список дел</title>
  <style>
    table, tr, th, td {
      border: 1px solid #ccc;
      padding: 5px;
    }
    
    table {
      margin-top: 20px;
      border-collapse: collapse;
    }
    
    th {
      background-color: #eee;
    }
  </style>
</head>
<body>
  <form method="post">
    <?php if(!$_SESSION['is_logged']) { ?>
       <label for="log_in">Логин</label>
       <input type="text" name="login" id="log_in">
       <br>
       <label for="password">Пароль</label>
       <input type="password" name="password" id="password">
       <br>
       <input type="submit" name="log_in" value="Войти">
       <input type="submit" name="reg" value="Зарегистрироваться">
    <?php
        die;
    }
    ?>
  </form>
  <form method="post">
    <input type="text" name="task" placeholder="Описание задачи" value="<?= $taskValue; ?>">
    <input type="submit" name="<?= $taskButtonName; ?>"  value="<?= $taskButtonValue; ?>">
    <label for="sort">Сортировать по</label>
    <select name="sort" id="sort">
      <option value="description">Описанию</option>
      <option value="is_done">Статусу</option>
      <option value="date_added">Дате добавления</option>
    </select>
    <input type="submit" name="sort_button" value="Сортировать">
  </form>
  <h1>Задачи, которые добавили вы</h1>
  <table>
    <tr>
      <th>Описание</th>
      <th>Статус</th>
      <th>Дата добавления</th>
      <th>Редактирование задачи</th>
      <th>Автор</th>
      <th>Ответственный</th>
      <th>Закрепить задачу за пользователем</th>
    </tr>
    <?php
      functions\output($arrayTasksOwn, $user);
    ?>
  </table>
  <h2>Задачи, который передали вам другие пользователи</h2>
  <table>
    <tr>
      <th>Описание</th>
      <th>Статус</th>
      <th>Дата добавления</th>
      <th>Редактирование задачи</th>
      <th>Автор</th>
      <th>Ответственный</th>
      <th>Переложить задачу на следующего пользователя</th>
    </tr>
    <?php
      functions\output($arrayTasksTransferred, $user);
    ?>
  </table>
  <a href="?log_out=1">Выйти</a>
</body>
</html>