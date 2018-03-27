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
        toggleShipping: function (_this, selector) {
            var $this = $(_this),
                chunks = selector.split('|'),
                $address = $this.parents(chunks[0]).find(chunks[1]);

            if ($this.is(':checked')) {
                $address.addClass('hide');
            }
            else {
                $address.removeClass('hide');
            }
        },
        changeCType: function (_this, selector) {
            var $this = $(_this),
                $container = $this.parents(selector);

            if ($this.val() === 'person') {
                $container.find('.company-field').addClass('hide');
                $container.find('.person-field').removeClass('hide');
            }
            else {
                $container.find('.person-field').addClass('hide');
                $container.find('.company-field').removeClass('hide');
            }
        },
        addToCart: function (_this, vkey, amount, layout) {

            if (parseInt(amount) <= 0) {
                var selector = '.quantity-ctrl-button|.amount-input',
                    chunks = selector.split('|');
                amount = $(_this).parents(chunks[0]).find(chunks[1]).val();
            }

            var $this = $(_this),
                $loading = addLoading($this),
                $offcanvasCart = $('.offcanvas-cart'),
                $container = $offcanvasCart.find('[data-cart-item-container]');

            $.ajax({
                url: rex.simpleshop.ajax_url,
                method: 'POST',
                data: {
                    'controller': 'Cart.addProduct',
                    'rex-api-call': 'simpleshop_api',
                    'lang': lang_id,
                    'product_key': vkey,
                    'quantity': parseInt(amount),
                    'layout': layout
                }
            }).done(function (resp) {
                if ($container.length !== 0) {
                    $container.html(resp.message.cart_html);
                }
                $loading.remove();
                $(document).trigger('simpleshop.addedToCart', resp, _this, selector);
                $('.offcanvas-cart').addClass('expanded');
            });
        },
        removeCartItem: function (_this, vkey, layout) {
            var $this = $(_this);
            var $item = $this.parents('[data-cart-item]'),
                $container = $item.parents('[data-cart-item-container]');

            if ($container.length !== 0) {
                var $loading = addLoading($container);
            }

            $.ajax({
                url: rex.simpleshop.ajax_url,
                method: 'POST',
                data: {
                    'controller': 'Cart.removeProduct',
                    'rex-api-call': 'simpleshop_api',
                    'product_key': vkey,
                    'layout': layout
                }
            }).done(function (resp) {
                console.log(resp);
                if ($container.length !== 0) {
                    $container.html(resp.message.cart_html);
                    $loading.remove();
                }
            });
        },
        changeCartAmount: function (_this, vkey, _max, rowSelector) {
            var $this = $(_this),
                max = _max || 999,
                chunks = rowSelector ? rowSelector.split('|') : ['.cart-container', '.cart-item'],
                $container = $this.parents(chunks[0]),
                $row = $this.parents(chunks[1]),
                $input = $this.parents('.amount-increment').find('input');

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
                if (!$row.length) {
                    alert('Cart row class not found [default = .cart-item]');
                }
                else if (!$container.length) {
                    alert('Cart container class not found [default = .cart-container]');
                }

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
                }).done(function (resp) {
                    $container.html(resp.message.cart_html);
                    $loading.remove();
                });
            }
        },
        loadMore: function (_this, container, fragment, event) {
            var $this = $(_this),
                $fragment = $(fragment),
                $container = $(container),
                $loading = addLoading($container);

            if (typeof KreatifPjax == 'undefined') {
                alert('KreatifPjax is not defined - Gruntfile?');
                return false;
            }
            else if ($fragment.length == 0) {
                alert('Element container "' + fragment + '" is not set!');
                return false;
            }
            else if ($container.length == 0) {
                alert('Load-More container "' + container + '" is not set!');
                return false;
            }

            KreatifPjax.submit(event, {
                url: $this.attr('href'),
                fragment: fragment,
                container: container
            }, function (event, xhr, options) {
                $loading.remove();
                $(document).trigger('simpleshop:loadMoreDone');
            });
            return false;
        }
    };

    $('.checkout-radio-panel').on('click', function () {
        var $radio = $(this).find('input');
        if ($radio.is(':checked') === false) {
            $radio.prop('checked', true);
            $(this).addClass('selected');
        }
        $('.checkout-radio-panel').each(function () {
            if ($(this).find('input').is(':checked') === false) {
                $(this).removeClass('selected');
            }
        });
    });

    $('.offcanvas-cart-continue-shopping').on('click', function () {
        $(this).parent().removeClass('expanded');
    });

    function addLoading($container) {
        var css = $container.offset(),
            $loading = $(rex.simpleshop.loadingDiv);

        css.height = $container.outerHeight();
        css.width = $container.outerWidth();
        $('body').append($loading.addClass('show').css(css));

        return $loading;
    }

    $(window).on('load', function (e) {
        var $ctype = $('select[name=ctype]');

        if ($ctype.length) {
            $ctype.trigger('change');
        }
    });

    return result;
})(jQuery);

