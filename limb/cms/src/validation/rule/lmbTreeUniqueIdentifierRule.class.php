<?php
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

class lmbTreeUniqueIdentifierRule extends lmbSingleFieldRule
{
  protected $field_name;

  protected $node;

  protected $error_message;

  protected $parent_id;

  function __construct($field_name, $node, $error_message, $parent_id = false)
  {
    $this->node = $node;
    $this->field_name = $field_name;
    $this->error_message = $error_message;
    $this->parent_id = $parent_id;

    parent :: __construct($field_name);
  }

  function check($value)
  {
    $criteria = lmbSQLCriteria :: equal($this->field_name, $value)->addAnd('parent_id = ' . ($this->parent_id ? $this->parent_id : $this->node->getParent()->getId()));

    if(!$this->node->isNew())
      $criteria->addAnd('id <> '. $this->node->getId());

    if(lmbActiveRecord :: findOne(get_class($this->node), $criteria))
      $this->error($this->error_message);
  }
}

