<?php
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/lmbMacroTemplateLocator.class.php');
lmb_require('limb/macro/src/lmbMacroConfig.class.php');

abstract class lmbAbstractConstructor
{
  /**
   * @var lmbProjectConstructor
   */
  protected $_project;
  /**
   * @var lmbDbInfo
   */
  protected $_database_info;
  /**
   * @var lmbDbTableInfo
   */
  protected $_table;
  
  protected $_templates_dir; 

  protected $_messages;

  protected $_meta_fields = array('ctime', 'utime', 'kind');

  /**
   * @param lmbProjectConstructor $project
   * @param lmbDbInfo $database_info
   * @param lmbDbTableInfo $table
   * @param string $templates_dir
   */
  function __construct($project, $database_info, $table, $templates_dir = null)
  {
    $this->_project = $project;
    $this->_database_info = $database_info;
    $this->_table = $table;
    $this->_templates_dir = ($templates_dir != '') ? $templates_dir : (realpath(dirname(__FILE__).'/../') . '/template/');
  }

  function addMessage($message)
  {
    $this->_messages = $message;
  }

  function getMessages()
  {
    return $this->_messages;
  }

  abstract function create();

  /**
   * @param string $file_path
   * @return lmbMacroTemplate
   */
  protected function _createMacroTemplate($file_path)
  {
    $templates_dir = $this->_templates_dir;

    $config = new lmbMacroConfig(lmbToolkit::instance()->getConf('macro'));
    return new lmbMacroTemplate($templates_dir.$file_path, $config,new lmbMacroTemplateLocator($config));
  }

  protected function _createContentFromTemplate($template, $vars, $tags_needed = true)
  {
    $template = $this->_createMacroTemplate($template); 
    $template->setVars($vars);
    $content = $template->render(); 

    if($tags_needed)
      $content = '<?php'. PHP_EOL . $content; 
    return $content; 
  }
}