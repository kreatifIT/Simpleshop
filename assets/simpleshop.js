var Simpleshop = (function () {

    cloneInput = function(el, length) {
        var $this = $(el),
            name = $this.prop('name');

        if (name.match(/[a-z\d]+\[[a-z\d]+\]\[[a-z\d]\]/i)) {
            name = name.replace(/([a-z\d]+)\[([a-z\d]+)\]\[([a-z\d]+)\]/i, '$1[$2]['+ length +']');
        }
        $this.prop('name', name);
    };

    return {
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

            __dimensions.find('input').val('').each(function () { cloneInput(this, index); });
            __weights.find('input').val('').each(function () { cloneInput(this, index); });
            __pallett.find('input').removeAttr('checked').each(function () { cloneInput(this, index); });

            $dimensions.after(__dimensions);
            $pallett.after(__pallett);
            $weights.after(__weights);

            return false;
        }
    }
})();