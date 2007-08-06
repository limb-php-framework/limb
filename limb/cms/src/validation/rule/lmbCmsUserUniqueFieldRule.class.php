<?php

lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

class lmbCmsUserUniqueFieldRule extends lmbSingleFieldRule
{
  protected $user;

  function __construct($field, $user)
  {
    $this->user = $user;
    parent :: __construct($field);
  }

  function check($value)
  {
    $rs = $this->user->findAllRecords(new lmbSQLFieldCriteria($this->field_name, $value));
    $rows = $rs->count();

    if($this->user->isNew() && $rows > 0)
    {
      $this->error('Пользователь со значением поля {Field} уже существует');
    }
    elseif(!$this->user->isNew() && $rows >= 1)
    {
      foreach($rs as $record)
      {
        if($record->get('id') != $this->user->getId())
          $this->error('Пользователь со значением поля {Field} уже существует');
      }
    }
  }
}

?>
