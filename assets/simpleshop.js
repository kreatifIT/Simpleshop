var Simpleshop = (function ($) {
    'use strict';

    var result = {
        toggleAuth: function(_this, selector) {
            var $this = $(_this),
                $wrapper = $this.parents('.auth-wrapper');

            $wrapper.children('div').addClass('hide');
            $wrapper.children(selector).removeClass('hide');
        }
    };
    return result;
})(jQuery);