<?php
namespace todo\classes;

class myPDO
{
  protected $pdo;
  
  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }
  //Выполнение скриптов
  public function executeStatement($script, $argument_1 = '', $argument_2 = '')
  {
    $statement = $this->pdo->prepare($script);
    
    if (!empty($argument_1)) {
      $statement->bindValue(1, $argument_1);
    }
    if (!empty($argument_2)) {
      $statement->bindValue(2, $argument_2);
    }
    $statement->execute();
    return $statement;
  }
}