/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: cookie.js 5436 2007-03-30 07:30:57Z tony $
 * @package    js
 */

Limb.namespace('Limb.cookie');

Limb.cookie.set_multi_cookie = function (cookiename, id, val)
{
  var cookie = "";
  cookie = Limb.cookie.get_cookie(cookiename);

  found = 0;
  newcookie = Array();

  if(cookie != null)
  {
    cookies = cookie.split("_DIV_");
    for(i=0;i<cookies.length;i++)
    {
      c = cookies[i];
      cc = c.split("_EQ_");
      if(cc[0]==id)
      {
        c = id+'_EQ_'+val;
        found=1;
      }
      newcookie[i]=c;
    }
  }
  if(!found)
  {
    if(newcookie.length==0)
      newcookie[0] = id+'_EQ_'+val;
    else
      newcookie[i] = id+'_EQ_'+val;
  }

  newcookie = newcookie.join("_DIV_");
  Limb.cookie.set_cookie(cookiename, newcookie)//,expires,COOKIE_PATH, COOKIE_DOMAIN);
}

Limb.cookie.get_multi_cookie = function (cookiename,id)
{
  var cookie = "";
  cookie = Limb.cookie.get_cookie(cookiename);
  if(cookie==''||cookie==null)
    return;

  var found = 0;

  cookies = cookie.split("_DIV_");

  for(i=0;i<cookies.length;i++)
  {
     cc = cookies[i].split("_EQ_");

    if(cc[0] == id)
    {
      found = 1;
      break;
    }
  }
  if(!found) return;
  return cc[1];
}

Limb.cookie.get_cookie = function (name)
{
  var a_cookie = document.cookie.split("; ");
  for (var i=0; i < a_cookie.length; i++)
  {
    var a_crumb = a_cookie[i].split("=");
    if (name == a_crumb[0])
      return unescape(a_crumb[1]);
  }
  return null;
}

Limb.cookie.set_cookie = function (name, value, path, expires)
{
  path_str = (path) ? '; path=' + path : '; path=/';
  expires_str = (expires) ? '; expires=' + expires : '';

  cookie_str = name + '=' + value + path_str + expires_str;

  document.cookie = cookie_str;
}

Limb.cookie.remove_cookie = function (name, path)
{
  Limb.cookie.set_cookie(name, 0, path, '1/1/1980');
}

Limb.cookie.add_cookie_element = function (cookie_name, element)
{
  cookie_elements = Limb.cookie.get_cookie(cookie_name);
  if(cookie_elements == null || cookie_elements == 'undefined')
    cookie_elements_array = new Array();
  else
    cookie_elements_array = cookie_elements.split(',');

  present = false;
  for(i=0; i<cookie_elements_array.length; i++)
  {
    if(cookie_elements_array[i] == element)
    {
      present = true;
      break;
    }
  }
  if(!present)
  {
    cookie_elements_array.push(element);
    new_cookie_elements = cookie_elements_array.join(',');
    Limb.cookie.set_cookie(cookie_name, new_cookie_elements);
  }
}

Limb.cookie.remove_cookie_element = function (cookie_name, element)
{
  cookie_elements = Limb.cookie.get_cookie(cookie_name);
  if (cookie_elements == null || cookie_elements == 'undefined')
    cookie_elements_array = new Array();
  else
    cookie_elements_array = cookie_elements.split(',');
  new_cookie_elements_array = new Array();
  present = 0;
  for(i=0; i<cookie_elements_array.length; i++)
  {
    if (cookie_elements_array[i] != element)
      new_cookie_elements_array.push(cookie_elements_array[i]);
    else
      present = 1;
  }
  if (present)
  {
    new_cookie_elements = new_cookie_elements_array.join(',');
    Limb.cookie.set_cookie(cookie_name, new_cookie_elements);
  }
}
