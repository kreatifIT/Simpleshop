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

        $('[data-init-form-toggle]').each(function () {
            $(this).trigger('change');
        });

        $(document).on('pjax:end', function() {
            $('[data-init-form-toggle]').each(function () {
                $(this).trigger('change');
            });
        });

        $body.click(function (event) {
            if ($body.hasClass('offcanvas-cart-open') && !$(event.target).closest('.offcanvas-cart').length) {
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

    function addLoading($container, position) {
        var $loading = $('<div class="pjax-loading"><div class="spinner"><div></div><div></div><div></div></div></div>');

        if (position === 'relative') {
            $container.append($loading.addClass('show relative'));
        } else {
            var css = $container.offset();
            css.height = $container.outerHeight();
            css.width = $container.outerWidth();
            $('body').append($loading.addClass('show').css(css));
        }
        return $loading;
    }

    var result = {
        addLoading: addLoading,
        showOffcanvasCart: function (e) {
            $offcanvasCart.addClass('expanded');
            $body.addClass('offcanvas-cart-open');

            var $success = $offcanvasCart.find('.offcanvas-cart-success');

            if ($success.length) {
                window.setTimeout(function () {
                    $success.fadeOut();
                }, 3000);
            }
        },

        closeOffcanvasCart: function (e) {
            $offcanvasCart.removeClass('expanded');
            $body.removeClass('offcanvas-cart-open');
        },

        toggleShipping: function (_this) {
            var $this = $(_this),
                showNew = $this.is(':checked'),
                $container = $this.parents('[data-address-toggle-container]'),
                $toggles = $container.find('[data-address-toggle]');

            $container.find('[data-shipping-addresses] input').prop('checked', false);

            $toggles.each(function() {
                var $this = $(this),
                    toggle = $this.data('address-toggle');

                if (showNew) {
                    if (toggle == 'new-address') {
                        $this.removeClass('hide');
                    } else {
                        $this.addClass('hide');
                    }
                } else {
                    if (toggle == 'new-address') {
                        $this.addClass('hide');
                    } else {
                        $this.removeClass('hide');
                    }
                }
            });
        },
        changeCType: function (_this, selector) {
            var $this = $(_this),
                value = $this.val(),
                $container = selector ? $this.parents(selector) : $this.parents('form');

            $container.find('[data-form-toggle]').each(function () {
                var $field = $(this),
                    data = $field.data('form-toggle'),
                    values = data.split(',');

                if (values.indexOf(value) < 0) {
                    $field.addClass('hide');
                } else {
                    $field.removeClass('hide');
                }
            });
        },
        toggleVariant: function (_this, selector, event) {
            var $this = $(_this),
                $container = $this.parents(selector),
                $loading = addLoading($container);

            if (!$container.length) {
                alert('Variant container class not found [' + selector + ']');
            } else if (typeof KreatifPjax == 'undefined') {
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
                cache: false,
                data: {
                    'controller': 'Cart.addProduct',
                    'rex-api-call': 'simpleshop_api',
                    'lang': lang_id,
                    'product_key': vkey,
                    'exact_qty': parseInt(amount),
                    'layout': layout
                }
            }).done(function (resp) {
                $container.html(resp.message.cart_html);
                $loading.remove();
                result.showOffcanvasCart();

                $('#header-cart').html(resp.message.cartButton);

                $(document).trigger('simpleshop:addToCart', [$container, resp]);
                $(document).trigger('pjax:end', [$container, resp]);
            });
        },
        removeCartItem: function (_this, vkey, layout, selector) {
            var $this = $(_this),
                $container = $this.parents(selector || '[data-cart-container]'),
                $loading = addLoading($container);

            $.ajax({
                url: rex.simpleshop.ajax_url,
                method: 'POST',
                cache: false,
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

                $('#header-cart').html(resp.message.cartButton);

                $(document).trigger('simpleshop:removeCartItem', [$container, resp]);
                $(document).trigger('pjax:end', [$container, resp]);
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
            } else if (sign == 'plus') {
                $input.val(parseInt($input.val()) + 1);
            }

            var num = parseInt($input.val());

            if (num < 1) {
                num = 1;
            } else if (num > max) {
                num = max;
            }
            $input.val(num);

            if (vkey) {
                if (!$row.length) {
                    alert('Cart row not found [default = data-cart-item]');
                } else if (!$container.length) {
                    alert('Cart container not found [default = data-cart-container');
                }

                var $loading = addLoading($container);

                $.ajax({
                    url: rex.simpleshop.ajax_url,
                    method: 'POST',
                    cache: false,
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

                    $('#header-cart').html(resp.message.cartButton);

                    $(document).trigger('simpleshop:changeCartAmount', [$container, resp]);
                    $(document).trigger('pjax:end', [$container, resp]);
                });
            }
        },
        applyCoupon: function (_this, selector, container, event) {
            var $this = $(_this),
                $container = $(container),
                chunks = selector.split('|'),
                $loading = addLoading($container),
                $input = $(_this).parents(chunks[0]).find(chunks[1]),
                code = $input.val(),
                url = $input.data('link');

            if (typeof KreatifPjax == 'undefined') {
                alert('KreatifPjax is not defined - Gruntfile?');
                return false;
            } else if ($container.length == 0) {
                alert('Load-More container "' + container + '" is not set!');
                return false;
            } else if (url == '' || typeof url == 'undefined') {
                alert('PJax url is not defined!');
                return false;
            }

            KreatifPjax.submit(event, {
                url: url + '?action=redeem_coupon&coupon_code=' + code,
                push: false,
                noLoading: true,
                fragment: container,
                container: container
            }, function (event, xhr, options) {
                $loading.remove();
                $(document).trigger('simpleshop:loadMoreDone');
            });
        },
        loadMore: function (_this, container, fragment, event) {
            var $this = $(_this),
                $fragment = $(fragment),
                $container = $(container),
                $loading = addLoading($container);

            if (typeof KreatifPjax == 'undefined') {
                alert('KreatifPjax is not defined - Gruntfile?');
                return false;
            } else if ($fragment.length == 0) {
                alert('Element container "' + fragment + '" is not set!');
                return false;
            } else if ($container.length == 0) {
                alert('Load-More container "' + container + '" is not set!');
                return false;
            }

            KreatifPjax.submit(event, {
                url: $this.attr('href'),
                noLoading: true,
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

