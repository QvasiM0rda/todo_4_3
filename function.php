<?php
namespace todo\functions;
use todo\classes\tasks;
use todo\classes\user;
error_reporting(E_ALL);
session_start();

//Автозагрузка классов
function autoloadClass($className)
{
  $className = str_replace('\\',DIRECTORY_SEPARATOR, $className);
  $namespace = 'todo' . DIRECTORY_SEPARATOR;
  $fileName = str_replace($namespace, '', $className) . '.class.php';
  if (file_exists($fileName)) {
    require $fileName;
  } else {
    echo 'Файл не найден ' . $fileName .'<br>';
  }
}

spl_autoload_register('todo\functions\autoloadClass');

$pdo = new \PDO('mysql:host=localhost;dbname=kerimov;charset=utf8', 'kerimov', 'neto0990');
$tasks = new tasks($pdo);
$user = new user($pdo);
$arrayUsers = $user->getUsers();

//Чтобы не было ошибок
if (empty($_SESSION['id'])) {
  $_SESSION['id'] = 0;
}

if (empty($_SESSION['is_logged'])) {
  $_SESSION['is_logged'] = false;
}

//Вывод данных из БД
function output($arrayTasks, $users){
  foreach ($arrayTasks as $tasks) {
    if ($tasks['is_done'] == 0) {
      $status = 'Не выполнено';
    } else {
      $status = 'Выполнено';
    }
    $id = htmlspecialchars($tasks['id']);
    $description = htmlspecialchars($tasks['description']);
    $dateAdded = htmlspecialchars($tasks['date_added']);
    $author = htmlspecialchars($tasks['user']);
    $assignedUser = htmlspecialchars($tasks['assigned_user']);
  
    $return = <<<RETURN
<tr>
  <td>$description</td>
  <td>$status</td>
  <td>$dateAdded</td>
  <td>
    <a href="?id=$id&action=edit">Редактировать</a>
    <a href="?id=$id&action=execute">Выполнить</a>
    <a href="?id=$id&action=delete">Удалить</a>
  </td>
  <td>$author</td>
  <td>$assignedUser</td>
  <td>
    <form method="post">
      <select name="assigned_user_id">

RETURN;
    echo $return;
    $users->getUserList($id);
    $return = <<<RETURN2
      </select>
      <input type="submit" name="assign" value="Передать задачу пользователю">
    </form>
  </td>
</tr> \n
RETURN2;
    echo $return;
  }
}

//Добавление задания
if (!empty($_POST['add'])) {
  $tasks->insert($_POST['task'], $_SESSION['id']);
}

//Выполнение или удаление задания, в зависимости от нажатой ссылки
if (!empty($_GET['action'])) {
  if ($_GET['action'] === 'execute') {
    $tasks->updateIsDone($_GET['id']);
  }
  if ($_GET['action'] === 'delete') {
    $tasks->delete($_GET['id']);
  }
}

//Подгрузка описания задания, выбранного для редактирования
if (!empty($_GET['action']) && $_GET['action'] === 'edit') {
  $taskValue = $tasks->selectTaskById($_GET['id']);
  $taskButtonValue = 'Сохранить'; //Изменение текста кнопки для редактирования
  $taskButtonName = 'save'; //Изменение имени кнопки для редактирования
} else {
  $taskValue = ''; //Изменение содержимого текстового поля для добавления
  $taskButtonValue = 'Добавить'; //Изменение текста кнопки для добавления
  $taskButtonName = 'add'; //Изменение имени кнопки для добавления
}

//Редактирование описания задания
if (!empty($_POST['save'])) {
  $tasks->updateDescription($_POST['task'], $_GET['id']);
  header('Location: index.php');
  die;
}

//Выход по нажатию на ссылку
if (!empty($_GET['log_out'])) {
  $_SESSION['is_logged'] = false;
  $_SESSION['id'] = '0';
  header('Location: index.php');
}

//Проверка логина и пароля
if (!empty($_POST['log_in'])) {
  if ($user->checkUser($_POST['login'], $_POST['password'])) {
    $_SESSION['is_logged'] = true;
    $_SESSION['id'] = $user->getUserID($_POST['login']);
    header('Location: index.php');
  } else {
    echo 'Неправильный логин или пароль';
  }
}
//Переход к регистрации
if (!empty($_POST['reg'])) {
  header('Location: registration.php');
}

//Передача задачи
if (!empty($_POST['assigned_user_id'])) {
  $ids = explode('_', $_POST['assigned_user_id']);
  $userId = $ids[0];
  $taskId = $ids[1];
  $tasks->assignTaskToUser($userId, $taskId);
}

//Сортировка по одному из трёх параметров, если они выбраны, или по id
if (!empty($_POST['sort'])) {
  $arrayTasksOwn = $tasks->selectOwnTask($_POST['sort'], $_SESSION['id']);
} else {
  $arrayTasksOwn = $tasks->selectOwnTask('id', $_SESSION['id']);
}

//Вывод задач, перенадных другими пользователями
if (!empty($_POST['sort'])) {
  $arrayTasksTransferred = $tasks->selectTransferredTask($_POST['sort'], $_SESSION['id']);
} else {
  $arrayTasksTransferred = $tasks->selectTransferredTask('id', $_SESSION['id']);
}