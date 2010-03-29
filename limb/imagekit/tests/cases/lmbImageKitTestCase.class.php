<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/imagekit/src/lmbImageKit.class.php');

abstract class lmbImageKitTestCase extends UnitTestCase
{
  protected $driver;

  protected function _getInputImage()
  {
    return dirname(__FILE__) . '/../fixture/images/input.jpg';
  }

  protected function _getInputImageType()
  {
    return 'jpeg';
  }

  protected function _getInputPalleteImage()
  {
    return dirname(__FILE__) . '/../fixture/images/water_mark.gif';
  }

  protected function _getOutputImage($type = 'jpg')
  {
    return lmb_var_dir() . '/output.' . $type;
  }

  protected function _getClass($template)
  {
    return str_replace('%', lmb_camel_case($this->driver), $template);
  }

  function _getConvertor($params = array())
  {
    $class_name = $this->_getClass('lmb%ImageConvertor');
    lmb_require('limb/imagekit/src/'.$this->driver.'/'.$class_name.'.class.php');
    return new $class_name($params);
  }

  function _getContainer()
  {
    $class_name = $this->_getClass('lmb%ImageContainer');
    lmb_require('limb/imagekit/src/'.$this->driver.'/'.$class_name.'.class.php');
    $cont = new $class_name;
    $cont->load($this->_getInputImage());
    return $cont;
  }

  function _getPalleteContainer()
  {
    $class_name = $this->_getClass('lmb%ImageContainer');
    lmb_require('limb/imagekit/src/'.$this->driver.'/'.$class_name.'.class.php');
    $cont = new $class_name;
    $cont->load($this->_getInputPalleteImage());
    return $cont;
  }

  function tearDown()
  {
    @unlink($this->_getOutputImage());
  }
}
