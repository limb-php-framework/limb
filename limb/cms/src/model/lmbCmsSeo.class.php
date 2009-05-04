<?php

class lmbCmsSeo extends lmbActiveRecord
{
  protected $_db_table_name = 'lmb_cms_seo';
  protected static $_meta;
  
  protected function _createValidator()
  {
    lmb_require('limb/cms/src/validation/rule/lmbCmsUniqueFieldRule.class.php');
    
    $validator = new lmbValidator();

    $validator->addRequiredRule('title', '"Title" обязательное поле');
    
    $validator->addRequiredRule('url', '"Url" обязательное поле');
    $validator->addRule(new lmbCmsUniqueFieldRule('url', 'lmbCmsSeo', $this, '"Url" должен быть уникальным'));

    return $validator;
  }

  public static function getMetaKeywords()
  {
    if(empty(self :: $_meta))
      self :: _getMetaDataForUrl();

    return self :: $_meta->get('keywords');
  }

  public static function getMetaTitle()
  {
    if(empty(self :: $_meta))
      self :: _getMetaDataForUrl();

    return self :: $_meta->get('title');
  }

  public static function getMetaDescription()
  {
    if(empty(self :: $_meta))
      self :: _getMetaDataForUrl();

    return self :: $_meta->get('description');
  }

  public static function getMetaForCurrentUrl()
  {
    if(empty(self :: $_meta))
       self :: _getMetaDataForUrl();

    return self :: $_meta;
  }

  public static function getMetaForUrl($uri)
  {
    self :: _getMetaDataForUrl($uri);
    return self :: $_meta;
  }

  protected static function _getMetadataForUrl($uri = null)
  {
    if(!$uri)
      $uri = lmbToolkit :: instance()->getRequest()->getUri();

    $count_path = $uri->countPath();
    $meta = null;
    $sql = 'SELECT keywords, description, title FROM lmb_cms_seo WHERE url = \'/\' OR ';

    for($i = 1; $i < $count_path; $i++)
      $sql .= ' url = \'' . self :: getDefaultConnection()->escape($uri->getPathToLevel($i)) . '\'' . ($i < $count_path - 1? ' OR ':''); 
    
    $sql .= ' ORDER BY url DESC LIMIT 1';
    $meta = lmbDBAL :: fetchOneRow($sql);

    if(!empty($meta))
      self :: $_meta = $meta;
    else
      self :: $_meta = new lmbObject(array('title' => '', 'description' => '', 'keywords' => ''));
  }
}