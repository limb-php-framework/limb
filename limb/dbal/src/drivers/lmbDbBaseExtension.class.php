<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */


/**
 * class lmbBaseDbExtension.
 * A base class for all vendor specific extensions
 *
 * @package dbal
 * @version $Id$
 */

class lmbDbBaseExtension
{
  protected $connection;

  function __construct(lmbDbBaseConnection $conn)
  {
    $this->connection = $conn;
  }

  function __call($m, $args=array())
  {
    throw new lmbDbException("Extension '" . get_class($this) . "' does not support method '$m'");
  }
}
