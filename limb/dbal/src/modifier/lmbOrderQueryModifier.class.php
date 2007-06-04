<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOrderQueryModifier.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
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
          throw new lmbException('Wrong order type', array('order' => $arr[1]));
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