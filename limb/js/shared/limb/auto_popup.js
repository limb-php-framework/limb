jQuery(window).ready(
  function()
  {
   jQuery.find("a").each(
    function(a)
    {
      if(a.href.indexOf('popup=1') > -1)
      {
        jQuery(a).bind("click", function(){Limb.Url.click(this.href);return false});
      }
    }
   );
  }
);