var Simpleshop = (function ($) {

    $(document).ready(function () {
        initProductSelect();
        initVariantSelect();
    });

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

    cloneInput = function (el, length) {
        var $this = $(el),
            name = $this.prop('name');

        if (name.match(/[a-z\d]+\[[a-z\d]+\]\[[a-z\d]\]/i)) {
            name = name.replace(/([a-z\d]+)\[([a-z\d]+)\]\[([a-z\d]+)\]/i, '$1[$2][' + length + ']');
        }
        $this.prop('name', name);
    };

    return {
        saveVariants: function(_this) {
            updateVariantPrio($(_this).find('table.variants'));
        },
        cloneCoupon: function(_this) {
            var $this = $(_this),
                $input = $this.parent().find('input.coupon-clone-count');

            $this.prop('href', $this.prop('href') + $input.val());
        },
        addShippingPackage: function (el) {
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
        }
    }
})(jQuery);