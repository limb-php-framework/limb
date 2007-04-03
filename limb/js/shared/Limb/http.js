/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: http.js 5436 2007-03-30 07:30:57Z tony $
 * @package    js
 */

Limb.namespace('Limb.Http');

Limb.Http.getQueryItem = function (page_href, item_name)
{
  arr = Limb.Http.getQueryItems(page_href);

  if(arr[item_name])
    return arr[item_name];
  else
    return null;
}

Limb.Http.buildQuery = function (items)
{
  query = '';
  for(index in items)
    query = query + index + '=' + items[index] + '&';

  return query;
}

Limb.Http.getQueryItems = function (uri)
{
  query_items = new Array();

  arr = uri.split('?');
  if(!arr[1])
    return query_items;

  query = arr[1];

  arr = query.split('&');

  for(index in arr)
  {
    if(typeof(arr[index]) == 'string')
    {
      key_value = arr[index].split('=');
      if(!key_value[1])
        continue;

      query_items[key_value[0]] = key_value[1];
    }
  }

  return query_items;
}

Limb.Http.addUrlQueryItem = function (uri, parameter, val)
{
  uri_pieces = uri.split('?');

  items = Limb.Http.getQueryItems(uri);
  items[parameter] = val;

  return uri_pieces[0] + '?' + Limb.Http.buildQuery(items);
}

Limb.Http.addRandomToUrl = function (page_href)
{
  if(page_href.indexOf('?') == -1)
    page_href = page_href + '?';

  page_href = page_href.replace(/&*rn=[^&]+/g, '');

  items = page_href.split('#');

  page_href = items[0] + '&rn=' + Math.floor(Math.random()*10000);

  if(items[1])
    page_href = page_href + '#' + items[1];

  return page_href;
}

Limb.Http.jump = function (href, w)
{
  if(!w) w = window;
  w.location.href = href;
}

Limb.Http.click = function (href, window_name)
{
  is_popup = href.indexOf('popup=1');

  if(is_popup > -1)
    new Limb.Window(href, window_name);

  return !(is_popup > -1);
}
