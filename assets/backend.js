var SimpleshopBackend = (function ($) {
    var searchHandle = null,
        blurHandle = null,
        functions = {},
        functionListParams = {};

    $(document).ready(function () {
        initProductSelect();
        initVariantSelect();
        initAddressSelect();
        initProductRexCategoryLink();
        initYformEditToggles();
    });

    function addLoading($container) {
        var css = $container.offset(),
            $loading = $('<div class="pjax-loading"><div class="spinner"><div></div><div></div><div></div></div></div>');

        css.height = $container.outerHeight();
        css.width = $container.outerWidth();
        $('body').append($loading.addClass('show').css(css));

        return $loading;
    }

    function initYformEditToggles() {
        var $toggles = $('[data-yform-edit-toggle-switch]');

        if ($toggles.length) {
            $toggles.each(function () {
                var $this = $(this),
                    toggle = $(this).data('yform-edit-toggle-switch');
                $this.on('change', function () {
                    var option = $(this).val();
                    $('[data-yform-edit-toggle^="' + toggle + ':"]').each(function () {
                        var $this = $(this),
                            $formEl = $this.parents('.form-group');

                        if ($this.data('yform-edit-toggle') == toggle + ':' + option) {
                            $formEl.removeClass('hide');
                        } else {
                            $formEl.addClass('hide');
                        }
                    });
                });
            }).trigger('change');
        }
    }

    function initProductRexCategoryLink() {
        var $container = $('#linking-container');

        if ($container.length) {
            var $input = $container.find('#REX_LINK_rex_category');

            functionListParams.cat_id = 0;

            window.setInterval(function () {
                if (functionListParams.cat_id != $input.val()) {
                    functionListParams.cat_id = $input.val();
                    showFunctionList();
                }
            }, 1000);
        }
    }

    function showFunctionList() {
        var $ajContainer = $('#linking-container .pjax-container'),
            $loading = addLoading($ajContainer);

        $.ajax({
            url: rex.simpleshop.ajax_url,
            cache: false,
            data: {
                'debug': rex.debug,
                'cat_id': functionListParams.cat_id,
                'controller': 'BeApi.list_functions',
                'fragment': 'link_product_rex_categories',
                'rex-api-call': 'simpleshop_be_api',
                'func': 'link_product_rex_categories',
                'search': functionListParams.search || '',
                'page': functionListParams.page || 0
            }
        }).done(function (resp) {
            $ajContainer.html(resp.message.html);
            $loading.remove();
        });
    }

    function initAddressSelect() {
        $('select.address-select').select2({
            width: 'style',
            ajax: {
                data: function (params) {
                    params.customer_id = $(this).data('customer-id');
                    return params;
                },
                processResults: function (data) {
                    return data.message;
                }
            }
        });
    }

    function initVariantSelect() {
        $('table.variants tbody').sortable({
            animation: 150,
            handle: '.sort-handle',
            update: function (e, ui) {
                updateVariantPrio($(this));
            }
        });
    }

    function updateVariantPrio($table) {
        $table.find('tr').each(function (index) {
            $(this).find('input.prio').val(index);
        });
    }

    function initProductSelect() {
        $('select.product-select2').select2({
            debug: true,
            width: 'style',
            ajax: {
                url: rex.simpleshop.ajax_url,
                data: function (params) {
                    return {
                        'rex-api-call': 'simpleshop_api',
                        controller: 'Package.selectProducts',
                        page: params.page,
                        term: params.term
                    }
                },
                processResults: function (data, params) {
                    params.page = params.page || 0;
                    return data.message.result
                }
            }
        });
    }

    function cloneInput(el, length) {
        var $this = $(el),
            name = $this.prop('name');

        if (name.match(/[a-z\d]+\[[a-z\d]+\]\[[a-z\d]\]/i)) {
            name = name.replace(/([a-z\d]+)\[([a-z\d]+)\]\[([a-z\d]+)\]/i, '$1[$2][' + length + ']');
        }
        $this.prop('name', name);
    };


    functions.saveVariants = function (_this) {
        var $form = $(_this),
            url = $form.prop('action'),
            data = $form.serialize();

        updateVariantPrio($form.find('table.variants'));
        var $loading = addLoading($form);

        $.ajax({
            url: url + '&func=save',
            method: 'POST',
            data: data
        }).done(function (resp) {
            if (resp.succeeded) {
                window.location.href = url + '&func=edit';
            } else {
                $('#callout-container').html(resp.callout);
                $loading.remove();
            }
        });
        return false;
    };

    functions.cloneCoupon = function (_this) {
        var $this = $(_this),
            $input = $this.parent().find('input.coupon-clone-count');

        $this.prop('href', $this.prop('href') + $input.val());
    };

    functions.addShippingPackage = function (el) {
        var $this = $(el),
            $tr = $this.parents('tr'),
            $pallett = $tr.find('.pallett:last'),
            $weights = $tr.find('.weights:last'),
            $dimensions = $tr.find('.dimensions:last'),
            index = $tr.find('.dimensions').length;

        var __dimensions = $dimensions.clone(),
            __weights = $weights.clone(),
            __pallett = $pallett.clone();

        __dimensions.find('input').val('').each(function () {
            cloneInput(this, index);
        });
        __weights.find('input').val('').each(function () {
            cloneInput(this, index);
        });
        __pallett.find('input').removeAttr('checked').each(function () {
            cloneInput(this, index);
        });

        $dimensions.after(__dimensions);
        $pallett.after(__pallett);
        $weights.after(__weights);

        return false;
    };

    functions.selectFunctionListItem = function (_this) {
        var $this = $(_this),
            $li = $this.parents('li');

        $li.toggleClass('active');

        $.ajax({
            url: rex.simpleshop.ajax_url,
            method: 'GET',
            data: {
                'debug': rex.debug,
                'id': _this.value,
                'cat_id': functionListParams.cat_id,
                'action': $li.hasClass('active') ? 'add' : 'remove',
                'controller': 'Product.be_toggleRexCategoryId',
                'rex-api-call': 'simpleshop_be_api',
            }
        }).done(function (resp) {
            if (!resp.succeeded) {
                for (var i in resp.message.errors) {
                    KreatifAddon.showAlert(resp.message.errors[i]);
                }
            }
        });
    };

    functions.showFunctionListItems = function (_this, type) {
        if (type === 'search') {
            if (searchHandle) {
                window.clearTimeout(searchHandle);
            }
            searchHandle = window.setTimeout(function () {
                functionListParams.search = _this.value;
                functionListParams.page = 0;
                showFunctionList();
            }, 800);
        } else if (type === 'paging') {
            var page = $(_this).data('page');

            if (page !== '') {
                functionListParams.page = $(_this).data('page');
                showFunctionList();
            }
        }
    };

    functions.addOrderProduct = function (_this, orderId) {
        var $table = $(_this).parents('table'),
            $container = $(_this).parents('#order-product-container');

        $table.append('<tr><td colspan="15"><select class="select2 form-control"></select></td></tr>');

        var $select = $table.find('select.select2').select2({
            debug: true,
            cache: false,
            width: 'style',
            ajax: {
                url: rex.simpleshop.ajax_url,
                data: function (params) {
                    return {
                        'rex-api-call': 'simpleshop_api',
                        controller: 'Package.selectProducts',
                        page: params.page,
                        term: params.term
                    }
                },
                processResults: function (data, params) {
                    params.page = params.page || 0;
                    return data.message.result
                }
            }
        });

        $select.on('change', function (e) {
            var loading = addLoading($container);

            $.ajax({
                url: rex.simpleshop.ajax_url,
                method: 'GET',
                cache: false,
                data: {
                    'debug': rex.debug,
                    'orderId': orderId,
                    'productId': $(this).val(),
                    'controller': 'Order.be__addProduct',
                    'rex-api-call': 'simpleshop_be_api'
                }
            }).done(function (resp) {
                if (resp.succeeded) {
                    $container.html(resp.message.html);
                    loading.remove();
                } else {
                    for (var i in resp.message.errors) {
                        KreatifAddon.showAlert(resp.message.errors[i]);
                    }
                }
            });
        });
    };

    functions.removeOrderProduct = function (_this, orderId, productId, oldAmount, msg) {
        if (productId == '') {
            $(_this).parents('tr').remove();
        } else if (confirm(msg)) {
            var $container = $(_this).parents('#order-product-container'),
                loading = addLoading($container);

            $.ajax({
                url: rex.simpleshop.ajax_url,
                method: 'GET',
                cache: false,
                data: {
                    'debug': rex.debug,
                    'orderId': orderId,
                    'productId': productId,
                    'old_amount': oldAmount,
                    'controller': 'Order.be__removeProduct',
                    'rex-api-call': 'simpleshop_be_api'
                }
            }).done(function (resp) {
                if (resp.succeeded) {
                    $container.html(resp.message.html);
                    loading.remove();
                } else {
                    for (var i in resp.message.errors) {
                        KreatifAddon.showAlert(resp.message.errors[i]);
                    }
                }
            });
        }
    };

    functions.saveTrackingUrl = function (_this) {
        var $this = $(_this),
            $container = $('[data-tracking-url-container]'),
            loading = addLoading($container),
            formData = $container.find('input').serialize(),
            pjaxUrl = $this.prop('href');

        window.setTimeout(function () {
            var sendMail = window.confirm('Möchtest du dem Kunden den Link via Mail zusenden und den Status auf "Versendet" setzen?');

            $.ajax({
                url: pjaxUrl,
                method: 'POST',
                cache: false,
                data: {
                    sendMail: sendMail ? 1 : 0,
                    formData: formData
                }
            }).done(function (resp) {
                loading.remove();

                if (typeof resp == 'string') {
                    var $resp = $(resp);
                    $container.html($resp.find('[data-tracking-url-container]').html());
                } else {
                    KreatifAddon.showAlert(resp.message);
                }
            });
        }, 500);
        return false;
    };

    functions.changeOrderProductQuantity = function (_this, orderId, productId, oldAmount, event) {
        var which = window.event.which;
        if (which == 0 || which == 13 || which == 27) {
            if (blurHandle !== null && blurHandle > 0) {
                window.clearTimeout(blurHandle);
            }
            if (blurHandle != -1) {
                blurHandle = window.setTimeout(function () {
                    var formData = {},
                        tabindex = $(which == 0 ? document.activeElement : _this).prop('tabindex'),
                        $container = $(_this).parents('#order-product-container'),
                        $tr = $(_this).parents('tr'),
                        serialized = $tr.find('input, select, textarea').serialize().split('&'),
                        loading = addLoading($container);

                    for (var i in serialized) {
                        var chunks = serialized[i].split('=');
                        formData[chunks[0]] = chunks[1];
                    }

                    blurHandle = -1;
                    $.ajax({
                        url: rex.simpleshop.ajax_url,
                        method: 'GET',
                        cache: false,
                        data: $.extend(formData, {
                            'debug': rex.debug,
                            'orderId': orderId,
                            'productId': productId,
                            'old_amount': oldAmount,
                            'controller': 'Order.be__changeProductQuantity',
                            'rex-api-call': 'simpleshop_be_api'
                        })
                    }).done(function (resp) {
                        if (resp.succeeded) {
                            $container.html(resp.message.html);
                            var $input = $('[tabindex=' + tabindex + ']'),
                                value = $input.val();
                            $input.focus();
                            $input.val('');
                            $input.val(value);
                            loading.remove();
                            blurHandle = null;
                        } else {
                            for (var i in resp.message.errors) {
                                KreatifAddon.showAlert(resp.message.errors[i]);
                            }
                        }
                    });
                }, 500);
            }
        }
        if (which == 13) {
            window.event.preventDefault();
        }
    };

    return functions;
})(jQuery);