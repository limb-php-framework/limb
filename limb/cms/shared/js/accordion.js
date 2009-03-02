jQuery.fn.accordion = function(options) {
    // options
    var SLIDE_DOWN_SPEED = 'slow';
    var SLIDE_UP_SPEED = 'fast';
    var startClosed = options && options.start && options.start == 'closed';
    var on = options && options.on && (typeof options.on == 'number' && options.on > 0) ? options.on - 1 : 0;
    return this.each(function() {
        jQuery(this).addClass('accordion'); // use to activate styling
        if(options && options.height)
        {
          jQuery(this).find('div.scroll').each(function() {

            this.style.height = options.height + 'px';
            this.height = options.height;
          });
        }

        jQuery(this).find('div.content').hide();

        jQuery(this).find('span.title').click(function() {
            var current = jQuery(this.parentNode).find('div.content:visible');
            var next = jQuery(this).find('+div.content');
            if(next[0] == current[0])
              return;

            if (current[0] != next[0]) {
                current.slideUp(SLIDE_UP_SPEED);
            }
            if (next.is(':visible')) {
                next.slideUp(SLIDE_UP_SPEED);
            } else {
                next.slideDown(SLIDE_DOWN_SPEED);
            }
        });
        if (!startClosed) {
            jQuery(this).find('div.content:eq(' + on + ')').slideDown(SLIDE_DOWN_SPEED);
        }
    });
};

jQuery.fn.accordion_cp = function(options) {
    // options
    var SLIDE_DOWN_SPEED = 'fast';
    var SLIDE_UP_SPEED = 'fast';
    var startClosed = options && options.start && options.start == 'closed';
    var on = options && options.on && (typeof options.on == 'number' && options.on > 0) ? options.on - 1 : 0;
    return this.each(function() {
        jQuery(this).addClass('accordion'); // use to activate styling
        jQuery(this).find('dd').hide();

        jQuery(this).find('dt').click(function() {
            var current = jQuery(this.parentNode).find('dd:visible');
            var next = jQuery(this).find('+dd');
            if(next[0] == current[0])
              return;

            if (current[0] != next[0]) {
                current.slideUp(SLIDE_UP_SPEED);
            }
            if (next.is(':visible')) {
                next.slideUp(SLIDE_UP_SPEED);
            } else {
                next.slideDown(SLIDE_DOWN_SPEED);
            }
        });
        if (!startClosed) {
            var elem = jQuery(this).find("dd > ul > li.current").parents('dd');
            elem.prev().addClass('current');
            elem.prev().css('color','#fff');
            if(!elem.get()[0])
            {
              jQuery(this).find('dd:first').slideDown(SLIDE_DOWN_SPEED);
              return;
            }
            elem.slideDown(SLIDE_DOWN_SPEED);
        }
    });
};