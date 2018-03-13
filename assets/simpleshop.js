var Simpleshop = (function ($) {
    'use strict';

    var lang_id = $('html:first').data('lang-id');

    var result = {
        toggleAuth: function (_this, selector) {
            var $this = $(_this),
                $wrapper = $this.parents('.auth-wrapper');

            $wrapper.children('div').addClass('hide');
            $wrapper.children(selector).removeClass('hide');
        },
        addToCart: function (_this, vkey, amount, selector) {
            if (parseInt(amount) <= 0) {
                var chunks = selector.split('|');
                amount = (_this).parents(chunks[0]).find(chunks[0]).val();
            }

            $.ajax({
                url: rex.simpleshop.ajax_url,
                method: 'POST',
                data: {
                    'controller': 'Cart.addProduct',
                    'rex-api-call': 'simpleshop_api',
                    'lang': lang_id,
                    'product_key': vkey,
                    'quantity': parseInt(amount)
                }
            })
                .done(function (resp) {
                    $(document).trigger('simpleshop.addedToCart', resp, _this, selector);
                });
        },
        removeCartItem: function (_this, vkey, rowSelector) {
            var $this = $(_this),
                $row = $this.parents(rowSelector || '.cart-item'),
                $container = $row.parents('.cart-container'),
                $loading = addLoading($container);

            $.ajax({
                url: rex.simpleshop.ajax_url,
                method: 'POST',
                data: {
                    'controller': 'Cart.removeProduct',
                    'rex-api-call': 'simpleshop_api',
                    'product_key': vkey
                }
            })
                .done(function (resp) {
                    $container.html(resp.message.cart_html);
                    $loading.remove();
                });
        },
        changeCartAmount: function (_this, vkey, _max, rowSelector) {
            var $this = $(_this),
                max = _max || 999,
                $row = $this.parents(rowSelector || '.cart-item'),
                $container = $row.parents('.cart-container'),
                $input = $this.parents('.amount-increment').find('input');

            if (!$row.length) {
                alert('Cart row class not found [default = .cart-item]');
            }
            else if (!$container.length) {
                alert('Cart container class not found [default = .cart-container]');
            }

            if ($this.hasClass('amount-increment-minus')) {
                $input.val(parseInt($input.val()) - 1);
            }
            else if ($this.hasClass('amount-increment-plus')) {
                $input.val(parseInt($input.val()) + 1);
            }

            var num = parseInt($input.val());

            if (num < 1) {
                num = 1;
            }
            else if (num > max) {
                num = max;
            }
            $input.val(num);

            if (vkey) {
                var $loading = addLoading($container);

                $.ajax({
                    url: rex.simpleshop.ajax_url,
                    method: 'POST',
                    data: {
                        'controller': 'Cart.addProduct',
                        'rex-api-call': 'simpleshop_api',
                        'lang': lang_id,
                        'exact_qty': num,
                        'product_key': vkey
                    }
                })
                    .done(function (resp) {
                        $container.html(resp.message.cart_html);
                        $loading.remove();
                    });
            }
        }
    };

    function addLoading($container) {
        var css = $container.offset(),
            $loading = $(rex.simpleshop.loadingDiv);

        css.height = $container.outerHeight();
        css.width = $container.outerWidth();
        $('body').append($loading.addClass('show').css(css));

        return $loading;
    }

    return result;
})(jQuery);