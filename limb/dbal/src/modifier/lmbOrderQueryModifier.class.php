<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOrderQueryModifier.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/core/src/exception/lmbInvalidValueException.class.php');
lmb_require('limb/dbal/src/modifier/lmbQueryModifier.interface.php');

class lmbOrderQueryModifier implements lmbQueryModifier
{
  protected $order_pairs = array();

  function __construct($order_string)
  {
    $this->order_pairs = self :: extractOrderPairsFromString($order_string);
  }

  static function extractOrderPairsFromString($order_string)
  {
    $order_items = explode(',', $order_string);
    $order_pairs = array();
    foreach($order_items as $order_pair)
    {
      $arr = explode('=', $order_pair);

      if(isset($arr[1]))
      {
        if(strtolower($arr[1]) == 'asc' || strtolower($arr[1]) == 'desc'
           || strtolower($arr[1]) == 'rand()')
          $order_pairs[$arr[0]] = strtoupper($arr[1]);
        else
          throw new lmbInvalidValueException('Wrong order type', array('order' => $arr[1]));
      }
      else
        $order_pairs[$arr[0]] = 'ASC';
    }

    return $order_pairs;
  }

  function applyTo($query)
  {
    foreach($this->order_pairs as $field => $type)
      $query->addOrder($field, $type);
  }
}

?>