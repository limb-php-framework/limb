<?php

/* ***** BEGIN LICENSE BLOCK *****
 *  
 * This file is part of FirePHP (http://www.firephp.org/).
 * 
 * Copyright (C) 2007-2008 Christoph Dorn
 * 
 * FirePHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * FirePHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with FirePHP.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * ***** END LICENSE BLOCK ***** */


require_once dirname(__FILE__).'/FirePHP.class.php';

/**
 * Sends the given data to FirePHP Firefox Extension.
 * The data can be displayed in the Firebug Console or in the
 * "Server" request tab.
 * 
 * Usage:
 * 
 * require('fb.php')
 * 
 * // NOTE: You must have Output Buffering enabled via
 * //       ob_start() or output_buffering ini directive.
 * 
 * fb('Hello World'); // Defaults to FirePHP::LOG
 * 
 * fb('Log message'  ,FirePHP::LOG);
 * fb('Info message' ,FirePHP::INFO);
 * fb('Warn message' ,FirePHP::WARN);
 * fb('Error message',FirePHP::ERROR);
 * 
 * fb('Message with label','Label',FirePHP::LOG);
 * 
 * fb(array('key1'=>'val1',
 *          'key2'=>array(array('v1','v2'),'v3')),
 *    'TestArray',FB_LOG);
 * 
 * function test($Arg1) {
 *   throw new Exception('Test Exception');
 * }
 * try {
 *   test(array('Hello'=>'World'));
 * } catch(Exception $e) {
 *   fb($e);
 * }
 * 
 * fb(array('2 SQL queries took 0.06 seconds',array(
 *    array('SQL Statement','Time','Result'),
 *    array('SELECT * FROM Foo','0.02',array('row1','row2')),
 *    array('SELECT * FROM Bar','0.04',array('row1','row2'))
 *   )),FirePHP::TABLE);
 * 
 * // Will show only in "Server" tab for the request
 * fb(apache_request_headers(),'RequestHeaders',FirePHP::DUMP);
 * 
 * 
 * @return Boolean  True if FirePHP was detected and headers were written, false otherwise
 * 
 * @copyright   Copyright (C) 2007-2008 Christoph Dorn
 * @author      Christoph Dorn <christoph@christophdorn.com>
 * @license     http://www.gnu.org/licenses/lgpl.html
 */
function fb() {

  $instance = FirePHP::getInstance(true);
  
  $args = func_get_args();
  return call_user_func_array(array($instance,'fb'),$args);
      
  return true;
}

?>