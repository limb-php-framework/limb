<?php
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

class lmbCmsUniqueFieldRule extends lmbSingleFieldRule
{
  protected $class;
  protected $object;

  function __construct($field, $class, $object, $custom_error = '')
  {
    $this->object = $object;
    $this->class = $class;

    parent :: __construct($field, $custom_error);
  }

  function check($value)
  {
    $criteria = lmbSQLCriteria :: equal($this->field_name, $value);
    if(!$this->object->isNew())
      $criteria->addAnd(new lmbSQLFieldCriteria('id', $this->object->getId(), lmbSQLFieldCriteria :: NOT_EQUAL));

    $records = lmbActiveRecord :: find($this->class, $criteria);

    if($records->count())
      $this->error('Значение в поле {Field} уже занято');
  }
}
