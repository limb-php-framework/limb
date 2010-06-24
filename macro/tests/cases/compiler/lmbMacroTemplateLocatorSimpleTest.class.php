<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
class lmbMacroTemplateLocatorSimpleTest extends lmbBaseMacroTest
{  
  public $template_name = 'foo.phtml';
   
  function testlocateSourceTemplate()
  {
    $config = $this->_createMacroConfig();
    $config->tpl_scan_dirs = array($config->tpl_scan_dirs[0]);
         
    $template_locator = new lmbMacroTemplateLocatorSimple($config);
    try
    {
      $template = $template_locator->locateSourceTemplate($this->template_name);
      $this->fail();
    } 
    catch (lmbMacroException $e) 
    {      
      $this->pass();
    }
    
    $this->_createMacroTemplate('bar',$this->template_name); 
    
    try
    {
      $template = $template_locator->locateSourceTemplate($this->template_name);
      $this->pass();
    } 
    catch (lmbMacroException $e) 
    {      
      $this->fail();
    }
    
    $this->assertEqual('bar', file_get_contents($template));    
  }
  
  function testLocateCompiledTemplate()
  {
    $template_locator = new lmbMacroTemplateLocatorSimple($config = $this->_createMacroConfig());    
    $compiled_file_name = lmbMacroTemplate::encodeCacheFileName($this->template_name);
    file_put_contents($config->cache_dir.'/'.$compiled_file_name, 'bar');
    
    $template = $template_locator->locateCompiledTemplate($this->template_name);
    
    $this->assertEqual('bar', file_get_contents($template));
  }
  
}


