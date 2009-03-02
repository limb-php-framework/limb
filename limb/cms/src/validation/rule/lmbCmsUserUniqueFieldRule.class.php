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
    $criteria = new lmbSQLFieldCriteria($this->field_name, $value);
    if(!$this->user->isNew())
      $criteria->addAnd('id <> '. $this->user->getId());

    if(lmbActiveRecord :: findOne('lmbCmsUser', $criteria))
      $this->error('Пользователь со значением поля {Field} уже существует');
  }
}


