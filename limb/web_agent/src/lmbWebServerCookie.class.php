<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Web agent cookie
 *
 * @package web_agent
 * @version $Id: lmbWebServerCookie.class.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebServerCookie {
  protected $vars = array(
    'name' => '',
    'value' => '',
    'expires' => '',
    'path' => '',
    'domain' => '',
    'secure' => false,
  );

  function __construct($server_cookie)
  {
    $this->parse($server_cookie);
  }

  protected function parse($server_cookie)
  {
    $cook_params = explode(';', $server_cookie);
    if(!$cook_params[0]) continue;
    $cookie_name = '';
    foreach($cook_params as $n => $param)
    {
      $expl = explode('=', trim($param), 2);
      $name = $expl[0];
      $value = isset($expl[1]) ? $expl[1] : '';
      if($n == 0)
      {
        $this->name = $name;
        $this->value = rawurldecode($value);
      }
      else
      {
        switch($name)
        {
          case 'expires':
            $this->expires = $value;
            break;
          case 'path':
            $this->path = $value;
            break;
          case 'domain':
            $this->domain = $value;
            break;
          case 'secure':
            $this->secure = true;
            break;
        }
      }
    }
  }

  function __get($name)
  {
    return $this->vars[$name];
  }

  function __set($name, $value)
  {
    $this->vars[$name] = $value;
  }

}
