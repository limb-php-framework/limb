/*
 * jQuery ifixpng plugin
 * (previously known as pngfix)
 * with another plugin
 * Version 1.9  (27/09/2007)
 * @requires jQuery v1.1.3 or above
 *
 * Examples at: http://jquery.khurshid.com
 * Copyright (c) 2007 Kush M.
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */

 /**
  *
  * @example
  *
  * optional if location of pixel.gif if different to default which is images/pixel.gif
  * $.ifixpng('media/pixel.gif');
  *
  * $('img[@src$=.png], #panel').ifixpng();
  *
  * @apply hack to all png images and #panel which icluded png img in its css
  *
  * @name ifixpng
  * @type jQuery
  * @cat Plugins/Image
  * @return jQuery
  * @author jQuery Community
  */

(function(jQuery) {

  /**
   * helper variables and function
   */
  jQuery.ifixpng = function(customPixel) {
    jQuery.ifixpng.pixel = customPixel;
  };

  jQuery.ifixpng.getPixel = function() {
    return jQuery.ifixpng.pixel || 'images/1x1.gif';
  };

  var hack = {
    ltie7  : jQuery.browser.msie && /MSIE\s(5\.5|6\.)/.test(navigator.userAgent),
    filter : function(src) {
      return "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='"+src+"')";
    }
  };

  /**
   * Applies ie png hack to selected dom elements
   *
   * $('img[@src$=.png]').ifixpng();
   * @desc apply hack to all images with png extensions
   *
   * $('#panel, img[@src$=.png]').ifixpng();
   * @desc apply hack to element #panel and all images with png extensions
   *
   * @name ifixpng
   */

  jQuery.fn.ifixpng = hack.ltie7 ? function() {
      return this.each(function() {
        var that = jQuery(this);
        var base = jQuery('base').attr('href'); // need to use this in case you are using rewriting urls
        if (that.is('img') || that.is('input'))// hack image tags present in dom
        {
          /* skip images. drop this line if u wanna fix them
            if (that.attr('src'))
            {
              if (that.attr('src').match(/.*\.png([?].*)?$/i))// make sure it is png image
              {
                alert(that.attr('src')+' ' + that.height());
                // use source tag value if set
                var source = (base && that.attr('src').substring(0,1)!='/') ? base + that.attr('src') : that.attr('src');
                // apply filter
                that.css({filter:hack.filter(source), width:that.width(), height:that.height()})
                  .attr({src:jQuery.ifixpng.getPixel()})
                  .positionFix();
              }
            }
          //**/

        } else { // hack png css properties present inside css
          var image = that.css('backgroundImage');
          if (image.match(/url\("(.+\.png)"\)/i))
          {
            image = RegExp.$1;
            that.css({backgroundImage:'none', filter:hack.filter(image)})
              .children().positionFix();
          }
        }
    });
  } : function() { return this; };

  /**
   * Removes any png hack that may have been applied previously
   *
   * $('img[@src$=.png]').iunfixpng();
   * @desc revert hack on all images with png extensions
   *
   * $('#panel, img[@src$=.png]').iunfixpng();
   * @desc revert hack on element #panel and all images with png extensions
   *
   * @name iunfixpng
   */

  jQuery.fn.iunfixpng = hack.ltie7 ? function() {
      return this.each(function() {
      var that = jQuery(this);
      var src = that.css('filter');
      if (src.match(/src=["']?(.*\.png([?].*)?)["']?/i)) { // get img source from filter
        src = RegExp.$1;
        if (that.is('img') || that.is('input')) {
          that.attr({src:src}).css({filter:''});
        } else {
          that.css({filter:'', background:'url('+src+')'});
        }
      }
    });
  } : function() { return this; };

  /**
   * positions selected item relatively
   */

  jQuery.fn.positionFix = function() {
    return this.each(function() {
      var that = jQuery(this);
      var position = that.css('position');
      if (position != 'absolute' && position != 'relative') {
        that.css({position:'relative'});
      }
    });
  };

})(jQuery);
