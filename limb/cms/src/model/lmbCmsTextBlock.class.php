<?php
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

class lmbCmsTextBlock extends lmbActiveRecord
{

  /**
   * @return lmbValidator
   */
  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('identifier', 'Поле "Идентификатор" обязательно для заполнения');
    $validator->addRequiredRule('content', 'Поле "Текст" обязательно для заполнения');

    lmb_require('limb/cms/src/validation/rule/lmbCmsTextBlockUniqueFieldRule.class.php');
    $validator->addRule(new lmbCmsTextBlockUniqueFieldRule('identifier', $this, 'Текстовый блок со значением поля "Идентификатор" уже существует'));

    return $validator;
  }

  static function getRawContent($identifier)
  {
    $block = lmbActiveRecord :: findOne('lmbCmsTextBlock', lmbSQLCriteria :: equal('identifier', $identifier));

    return $block ? $block->getContent() : null;
  }

  static function findOneByIdentifier($identifier)
  {
    if($block = lmbActiveRecord::findOne('lmbCmsTextBlock', lmbSQLCriteria::equal('identifier', $identifier)))
      return $block;

    if(!$default_content = lmbToolkit::instance()->getConf('text_blocks')->get($identifier))
      return null;

    $block = new lmbCmsTextBlock();
    $block->import($default_content);
    $block->setIdentifier($identifier);

    return $block;
  }

}
