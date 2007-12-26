<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    web_app
 */

lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/view/src/wact/lmbWactHighlightHandler.class.php');
require_once('limb/view/lib/XML/HTMLSax3.php');

abstract class lmbWactTemplateSourceController extends lmbController
{
  protected $template_for_hackers = 'template_source/error.html';
  protected $history = array();
  protected $highlight_page_url = 'wact_template_source';
  
  function doDisplay()
  {
    require_once('limb/wact/src/compiler/templatecompiler.inc.php');
    require_once('limb/view/src/lmbWactView.class.php');
    
    $this->setTemplate('template_source/display.html');
    
    if(($t = $this->request->get('t')) && is_array($t) && sizeof($t) > 0)
    {
      $this->history = $t;
      $template_path = end($this->history);
    }
    else
    {
      $this->setTemplate($this->template_for_hackers);
      return;
    }

    if(substr($template_path, -5,  5) != '.html')
      $template_path = $this->template_for_hackers;

    $wact_locator = $this->toolkit->getWactLocator();

    if(!$source_file_path = $wact_locator->locateSourceTemplate($template_path))
    {
      $this->setTemplate($this->template_for_hackers);
      return;
    }

    $template_contents = file_get_contents($source_file_path);

    if(sizeof($this->history) > 1)
    {
      $tmp_history = $this->history;

      $from_template_path = $tmp_history[sizeof($tmp_history) - 2];
      $tmp_history = array_splice($tmp_history, 0, sizeof($tmp_history) - 1);

      $history_query = 't[]=' . implode('&t[]=', $tmp_history);

      $this->view->set('history_query', $this->highlight_page_url . '?' .$history_query);
      $this->view->set('from_template_path', $from_template_path);
    }

    $this->view->set('template_path', $template_path);
    $this->view->set('this_template_path', $this->view->getTemplate());
    $this->view->set('template_content', $this->_processTemplateContent($template_contents));
  }

  function _processTemplateContent($template_contents)
  {
    $compiler = $this->view->getWactTemplate()->createCompiler();

    $tag_dictionary = $compiler->getTagDictionary();

    $parser = new XML_HTMLSax3();

    $handler = new lmbWactHighlightHandler($tag_dictionary, $this->highlight_page_url);

    $handler->setTemplatePathHistory($this->history);

    $parser->set_object($handler);

    $parser->set_element_handler('openHandler','closeHandler');
    $parser->set_data_handler('dataHandler');
    $parser->set_escape_handler('escapeHandler');

    $parser->parse($template_contents);

    $html = $handler->getHtml();

    return $html;
  }
}

?>
