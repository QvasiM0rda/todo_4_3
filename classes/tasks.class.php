<?php
namespace todo\classes;

class tasks extends myPDO
{
  //Выбор и сортировка собственных задач
  public function selectOwnTask($description, $id)
  {
    $description = 't.' . $description;
    $query = 'SELECT t.id, t.description, t.is_done, t.date_added, u.login AS user, aui.login AS assigned_user
              FROM task AS t
              JOIN user AS u ON u.id = t.user_id
              JOIN user AS aui ON aui.id = t.assigned_user_id
              WHERE t.user_id = ' . (int) $id .
              ' ORDER BY ?';
    return $this->executeStatement($query, $description);
  }
  
  //Выбор и сортировка задач, переданных другими пользователями
  public function selectTransferredTask($description, $id)
  {
    $description = 't.' . $description;
    $query = 'SELECT t.id, t.description, t.is_done, t.date_added, u.login AS user, aui.login AS assigned_user
              FROM task AS t
              JOIN user AS u ON u.id = t.user_id
              JOIN user AS aui ON aui.id = t.assigned_user_id
              WHERE t.assigned_user_id = ' . (int) $id .
      ' ORDER BY ?';
    return $this->executeStatement($query, $description);
  }
  
  //Добавление задачи
  public function insert($description, $id)
  {
    $query = 'INSERT INTO task (user_id, assigned_user_id, description, is_done, date_added)
              VALUE (' . $id . ', ' . $id . ', ?, 0, NOW())';
    $this->executeStatement($query, $description);
  }
  
  //Редактирование статуса задачи
  public function updateIsDone($id)
  {
    $query = 'UPDATE task SET is_done = 1 WHERE id = ?';
    $this->executeStatement($query, $id);
  }
  
  //Возвращает описание задачи по id
  public function selectTaskById($id)
  {
    $query = 'SELECT * FROM task WHERE id = ?';
    $statement = $this->executeStatement($query, $id);
    foreach ($statement as $row) {
      return $row['description'];
    }
  }
  
  //Редактирование описания задачи
  public function updateDescription($description, $id)
  {
    $query = 'UPDATE task SET description = ? WHERE id = ?';
    $this->executeStatement($query, $description, $id);
  }
  
  //Удаление задачи
  public function delete($id)
  {
    $query = 'DELETE FROM task WHERE id = ?';
    $this->executeStatement($query, $id);
  }
  
  //Передача задачи другому пользователю
  public function assignTaskToUser($userId, $taskId)
  {
    $update = 'UPDATE task SET assigned_user_id = ? WHERE id = ?';
    if ($this->executeStatement($update, $userId, $taskId)) {
      echo 'Задачу передана';
    } else {
      echo 'Задачу не передана';
    }
  }
}