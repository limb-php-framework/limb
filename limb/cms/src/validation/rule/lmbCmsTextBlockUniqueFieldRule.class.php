<?php
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

class lmbCmsTextBlockUniqueFieldRule extends lmbSingleFieldRule
{
  protected $text_block;

  function __construct($field_name, $text_block)
  {
    $this->text_block = $text_block;
    parent :: __construct($field_name);
  }

  function check($value)
  {
    $criteria = new lmbSQLFieldCriteria($this->field_name, $value);
    if($this->text_block->getId())
      $criteria->addAnd('id <> '. $this->text_block->getId());

    if(lmbActiveRecord :: findOne('lmbCmsTextBlock', $criteria))
      $this->error('Тектовый блок со значением поля {Field} уже существует');
  }
}


