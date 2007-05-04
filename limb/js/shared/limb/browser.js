/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: browser.js 5802 2007-05-04 11:37:52Z pachanga $
 * @package    js
 */

Limb.namespace('Limb.Browser');

var agt = navigator.userAgent.toLowerCase();
Limb.Browser.is_ie = (agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1);
Limb.Browser.is_gecko = navigator.product == "Gecko";
Limb.Browser.is_opera  = (agt.indexOf("opera") != -1);
Limb.Browser.is_mac    = (agt.indexOf("mac") != -1);
Limb.Browser.is_mac_ie = (Limb.Browser.is_ie && Limb.Browser.is_mac);
Limb.Browser.is_win_ie = (Limb.Browser.is_ie && !Limb.Browser.is_mac);

Limb.Browser.detectFlash = function(requiredVersion)
{
  var flashVersion = 0;

  if(!navigator.plugins)
    return false;

  if(Limb.Browser.is_win_ie)
  {
    var flashPresent = false;

    for(var version = requiredVersion; version<10; version++)
    {
      try
      {
        flashPresent = flashPresent || new ActiveXObject('ShockwaveFlash.ShockwaveFlash.' + version);
      }
      catch(e) {}
    }

    return flashPresent;
  }

  if (navigator.plugins["Shockwave Flash 2.0"]
      || navigator.plugins["Shockwave Flash"])
  {

    var isVersion2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
    var flashDescription = navigator.plugins["Shockwave Flash" + isVersion2].description;

    var flashVersion = parseInt(flashDescription.substring(16));
  }

  if(navigator.userAgent.indexOf("WebTV") != -1) actualVersion = 4;

  if(flashVersion < requiredVersion)
    return false;

  return true;
}

