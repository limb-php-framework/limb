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

        jQuery(this).bind("click", function()
                       {
                         new Limb.Window(this.href);
                         return false;
                       });
      }
    }
   );
  }
);