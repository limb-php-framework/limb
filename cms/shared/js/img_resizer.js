/**
   Resize Image

   Все изображения с атрибутом "resize", ресайзит до размера указанного в значении атрибута "resize".
   Если значение атрибута:
      "xЧисло" то изображение меняет пропорционально свои размеры, где высота равна значению атрибут.
      "Числоx" то изображение меняет пропорционально свои размеры, где ширина равна значению атрибут.
      "Число" то изображение меняет пропорционально свои размеры, в зависимости от того что больше высота или ширина,
      то и принимает значение атрибута.

   TODO: продублировать описание на английском. показать пример использования.
*/


function initImgResize(){
  jQuery('img[@resize]')
  .one('load', function()
  {
    var current_img = jQuery(this);
    var size = current_img.attr('resize').replace('x','');
    var width_bool = false;
    var height_bool = false;
    var width = current_img.width();
    var height = current_img.height();

    current_img.css({position:'absolute',top:'3000'});

    if (current_img.attr('resize').indexOf('x') == 0)
        height_bool = true;
    else if (current_img.attr('resize').indexOf('x') == current_img.attr('resize').length - 1)
      width_bool = true;
    else if (current_img.attr('resize').indexOf('x') < 0)
        if (width > height)
          height_bool = true;
        else
          width_bool = true;

    var link = jQuery('<a class="magnifier" href="' + this.src + '" target="_blank"><img src="/shared/cms/images/icon/magnifier.gif" width="13" height="13" alt="magnifier"/></a>').get()[0];

    if(height_bool)
    {
      current_img.height(size);
    }
    else if (width_bool)
    {
      current_img.width(size);
    }

    jQuery(link).css('top', current_img.height() - 13);
    jQuery(link).css('left', current_img.width() - 16);

    current_img.css({position : 'static', top : '0'});
    current_img.before(link);

  });
};
