var Simpleshop = (function ($) {
    'use strict';

    var lang_id,
        $body,
        $offcanvasCart;

    $(window).on('load', function (ev) {
        var $ctype = $('select[name=ctype]');

        if ($ctype.length) {
            $ctype.trigger('change');
        }
    });

    $(document).ready(init);

    function init() {
        lang_id = $('html:first').data('lang-id');
        $body = $('body');
        $offcanvasCart = $('.offcanvas-cart');

        $body.click(function (event) {
            if (!$(event.target).closest('.offcanvas-cart').length) {
                result.closeOffcanvasCart();
            }
        });

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
    }

    function addLoading($container) {
        var css = $container.offset(),
            $loading = $(rex.simpleshop.loadingDiv);

        css.height = $container.outerHeight();
        css.width = $container.outerWidth();
        $('body').append($loading.addClass('show').css(css));

        return $loading;
    }

    var result = {
        addLoading: addLoading,
        showOffcanvasCart: function () {
            $offcanvasCart.addClass('expanded');
            $body.addClass('offcanvas-cart-open');

            var $success = $offcanvasCart.find('.offcanvas-cart-success');

            if ($success.length) {
                window.setTimeout(function() {
                    $success.fadeOut();
                }, 3000);
            }
            window.event.stopPropagation();
        },

        closeOffcanvasCart: function () {
            $offcanvasCart.removeClass('expanded');
            $body.removeClass('offcanvas-cart-open');
            window.event.stopPropagation();
        },

        toggleAuth: function (_this, selector) {
            var $this = $(_this),
                $wrapper = $this.parents('[data-auth-wrapper]');

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
        toggleVariant: function (_this, selector, event) {
            var $this = $(_this),
                $container = $this.parents(selector),
                $loading = addLoading($container);

            if (!$container.length) {
                alert('Variant container class not found [' + selector + ']');
            }
            else if (typeof KreatifPjax == 'undefined') {
                alert('KreatifPjax is not defined - Gruntfile?');
                return false;
            }

            KreatifPjax.submit(event, {
                url: $this.val(),
                fragment: selector,
                container: selector
            }, function (event, xhr, options) {
                $loading.remove();
            });
        },
        addToCart: function (_this, vkey, amount, layout, selector) {

            if (parseInt(amount) <= 0) {
                var selector = selector || '[data-quantity-ctrl-button]|[data-amount-input]',
                    chunks = selector.split('|');
                amount = $(_this).parents(chunks[0]).find(chunks[1]).val();
            }

            var $this = $(_this),
                $loading = addLoading($this),
                $container = $offcanvasCart.find('[data-cart-container]');

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
                $container.html(resp.message.cart_html);
                $loading.remove();
                result.showOffcanvasCart();
            });
        },
        removeCartItem: function (_this, vkey, layout, selector) {
            var $this = $(_this),
                $container = $this.parents(selector || '[data-cart-container]'),
                $loading = addLoading($container);

            $.ajax({
                url: rex.simpleshop.ajax_url,
                method: 'POST',
                data: {
                    'controller': 'Cart.removeProduct',
                    'rex-api-call': 'simpleshop_api',
                    'lang': lang_id,
                    'product_key': vkey,
                    'layout': layout,
                }
            }).done(function (resp) {
                $container.html(resp.message.cart_html);
                $loading.remove();
            });
        },
        changeCartAmount: function (_this, vkey, _max, selector) {
            var $this = $(_this),
                max = _max || 999,
                chunks = selector ? selector.split('|') : ['[data-cart-container]', '[data-cart-item]'],
                $container = $this.parents(chunks[0]),
                $row = $this.parents(chunks[1]),
                $input = $this.parents('[data-amount-increment]').find('input'),
                sign = $this.data('amount-increment-sign');

            if (sign == 'minus') {
                $input.val(parseInt($input.val()) - 1);
            }
            else if (sign == 'plus') {
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
                    alert('Cart row not found [default = data-cart-item]');
                }
                else if (!$container.length) {
                    alert('Cart container not found [default = data-cart-container');
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

    return result;
})(jQuery);

