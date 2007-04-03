/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: Exception.js 5436 2007-03-30 07:30:57Z tony $
 * @package    js
 */

Limb.namespace('Limb.Exception');

Limb.Exception = function()
{
  if(arguments.length == 1 && Limb.is_object(arguments[0]))
  {
    this.type = arguments[0].type || 'LimbException';
    this.message = arguments[0].message;
    this.stack = arguments[0].stack || 'Stack is not available';
    this.file_name = arguments[0].fileName || 'File name is not available';
    this.line_number = arguments[0].lineNumber || 'Line number is not available';
  }
  else
  {
    this.type = arguments[0] || 'LimbException';
    this.message = arguments[1] || 'Unknown error';
    if(typeof(arguments[2]) == 'object')
    {
      this.stack = arguments[2].stack || e.stack;
      this.file_name = arguments[2].fileName || e.fileName;
      this.line_number = arguments[2].lineNumber || e.lineNumber;
    }
    else
    {
      this.stack = 'Stack is not available';
      this.file_name = 'File name is not available';
      this.line_number = 'Line number is not available';
    }
  }
}

Limb.Exception.prototype =
{
  getMessage: function()
  {
    return this.message;
  },

  getType: function()
  {
    return this.type;
  },

  getStack: function()
  {
    return this.stack;
  },

  getFileName: function()
  {
    return this.file_name;
  },

  getLineNumber: function()
  {
    return this.line_number;
  },

  toString: function()
  {
    return '[ exception ' + this.type + ' ]';
  }
}
