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
   jQuery("a").each(
    function()
    {
      if(this.href.indexOf('popup=1') > -1)
      {
        //skip self hrefs with popup(basically it's <a href="#"> alike tags)
        if(this.href.indexOf('#') > -1 && this.href.indexOf(window.location.href) > -1)
          return;

        jQuery(this).bind("click",
         function()
         {
           //we can specify explicitly not to popup, this is useful for onclick handlers in <a> tag
           if(this.popup === false)
             return false;
           new Limb.Window(this.href);
           return false;
         });
      }
    }
   );
  }
);