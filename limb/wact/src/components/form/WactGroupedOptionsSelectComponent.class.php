<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactGroupedOptionsSelectComponent.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/src/components/form/select.inc.php');

class WactGroupedOptionsSelectComponent extends WactFormElement
{
  protected $dataset = null;
  protected $option_renderer = null;

  function renderContents()
  {
    $this->_ensureDatasetAvailable();
    $this->_renderOptGroups($this->dataset, $level = 1);
  }

  protected function _renderOptGroups($groups, $level)
  {
    foreach($groups as $group)
    {
      if(!$options = $group->get('options'))
        continue;

      echo '<optgroup ';
      $this->_renderOptGroupTagAttributes($group, $level);
      echo '>';

      $this->_renderOptions($options, $level + 1);

      echo '</optgroup>';
    }
  }

  protected function _renderOptGroupTagAttributes($group, $level)
  {
    $attrs = $group->export();
    foreach($attrs as $key => $value)
    {
      if($key == 'options' || is_object($value))
        continue;

      echo $key . '="' . (($key == 'label') ? str_repeat('&nbsp;', $level) : '') . htmlspecialchars($value) . '" ';
    }
  }

  protected function _renderOptions($options, $level)
  {
    $this->_ensureOptionsRendererAvailable();

    $value = $this->getValue();

    if(!$select_field = $this->getAttribute('select_field'))
      $select_field = 'id';

    foreach($options as $key => $contents)
    {
      if(is_array($contents))
      {
        $this->_renderOptGroups(WactTemplate :: castToIterator(array($contents)), $level);
      }
      else
      {
        if(!is_scalar($value))
          $selected = $value[$select_field];
        else
          $selected = $value;

        $this->option_renderer->renderOption($key, $contents, $key == $selected);
      }
    }
  }

  function registerDataSet($dataset)
  {
    $this->dataset = WactTemplate :: castToIterator($dataset);
  }

  protected function _ensureOptionsRendererAvailable()
  {
    if(!is_object($this->option_renderer))
      $this->option_renderer = new WactOptionRenderer();
  }

  protected function _ensureDatasetAvailable()
  {
    if (!is_object($this->dataset))
       $this->registerDataSet(array());
  }
}
?>