<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbIp.
 *
 * @package net
 * @version $Id: lmbIp.class.php 6878 2008-03-31 15:55:58Z wiliam $
 */
class lmbIp
{
  function encodeIpRange($ip_begin, $ip_end)
  {
    // Returns ip adressess array with in range $ip_begin - $ip_end
    if ( !self::isValid($ip_begin) || !self::isValid($ip_end) )
    {
      throw new lmbException("Invalid IP range", array('start' => $ip_begin,
                                                        'end' => $ip_end));
    }
    $data = array
    (
      lmbIp :: encode($ip_begin),
      lmbIp :: encode($ip_end)
    );
    $start = hexdec(dechex($data[0]));
    $end = hexdec(dechex($data[1]));
    $ip_list = array();
    for ( $i=$start; $i<=$end; $i++ )
    {
      if ( ($i & 0x000000FF) == 0x000000FF )
      {
        // Checking for 0.0.0.255
        continue;
      }elseif ( ($i & 0x0000FF00) == 0x0000FF00 )
      {
        // Checking for 0.0.255.0
        $i += 0xFF;
        continue;
      }elseif ( ($i & 0x00FF0000) == 0x00FF0000 )
      {
        // Checking for 0.255.0.0
        $i += 0xFFFF;
        continue;
      }elseif ( ($i & 0xFFFFFF) == 0 && $end - $i >= 0xFFFFFF )
      {
        $ip_list[] = $i|0xFFFFFF;
        $i = hexdec(dechex($i|0xFFFFFF));
      }elseif ( ($i & 0xFFFF) == 0 && $end - $i >= 0xFFFF )
      {
        $ip_list[] = $i|0xFFFF;
        $i = hexdec(dechex($i|0xFFFF));
      }elseif ( ($i & 0xFF) == 0 && $end - $i >= 0xFF )
      {
        $ip_list[] = $i|0xFF;
        $i = hexdec(dechex($i|0xFF));
      }else{
        $ip_list[] = $i|0;
      }
    }
    return $ip_list;
  }

  function encode($ip)
  {
    // 1.2.3.4 -> 0x01020304 as int
    return ip2long($ip);
  }

  function decode($numeric_ip)
  {
    // 0x01020304 as int -> 1.2.3.4
    return long2ip($numeric_ip);
  }

  function isValid($ip)
  {
    return ip2long($ip) !== false;
  }
  
  static function getRealIp()
  {
   if(!empty($_SERVER['HTTP_CLIENT_IP'])) 
     return $_SERVER['HTTP_CLIENT_IP'];
   
   elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    return $_SERVER['HTTP_X_FORWARDED_FOR'];
   
   else
     return $_SERVER['REMOTE_ADDR'];
  }
}


