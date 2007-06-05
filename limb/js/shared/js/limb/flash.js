/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

Limb.namespace('Limb.Flash');

Limb.Flash.detectVersion = function(requiredVersion)
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

  if(navigator.plugins["Shockwave Flash 2.0"]
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

