/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

jQuery(window).ready(
  function()
  {
   jQuery("a.modal").each(
    function()
    {      
      jQuery(this).bind("click",
       function()
       {
         //we can specify explicitly not to popup, this is useful for onclick handlers in <a> tag
         if(this.popup === false)
           return false;           
         modalWindow.loadByUrl(this.href);
         return false;
       });
    }
   );
  }
);