<?php
namespace todo\classes;

class user extends myPDO
{
  //Возвращает массив пользователей
  public function getUsers()
  {
    $select = 'SELECT * FROM user';
    return $this->executeStatement($select);
  }
  
  public function getUserList($taskId)
  {
    $userList = $this->getUsers();
    foreach ($userList as $user) {
      echo '        <option value="' . $user['id'] . '_' . $taskId . '">' . $user['login'] . '</option>' . "\n";
    }
  }
  
  public function checkUser($login, $password)
  {
    $pass = hash('md5', $password);
    $userList = $this->getUsers();
    foreach ($userList as $users) {
      if ($users['login'] === $login) {
        if ($users['password'] === $pass) {
          return true;
        }
      }
    }
    return false;
  }
  
  public function getUserID($login) {
    $userList = $this->getUsers();
    foreach ($userList as $users) {
      if ($users['login'] === $login) {
        return $users['id'];
      }
    }
  }
  
  public function addUser($login, $password)
  {
    if (empty($login)) {
      return 'Не введен логин!';
    }
    if (empty($password)) {
      return 'Не введен пароль!';
    }
    if ($this->checkUser($login, $password)) {
      return 'Такой логин уже зарегистрирован! Выберите другой логин.';
    } else {
      $pass = hash('md5', $password);
      $insert = 'INSERT INTO user (login, password) VALUE (?, ?)';
      if ($this->executeStatement($insert, $login, $pass)) {
        return 'Пользовател успешно зарегистрирован! Вы будете перенаправлены на страницу входа через 5 секунд.';
      } else {
        return 'Ошибка при регистрации пользователя!';
      }
    }
  }
}