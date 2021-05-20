if (typeof(jQuery) !== 'undefined') {
    /* global jQuery */
    "use strict";
    (function ($) {
        $(document).ready(function() {
            if ($('#msunifaun-setup').length) {
                if ($('#msunifaun-setup .applicable-applications').length) {
                    $('#msunifaun-setup .applicable-applications > li > label > input').click(function() {
                        var checked = $(this).attr('checked');
                        $('#msunifaun-setup .applicable-applications > li.selected').removeClass('selected');
                        if (checked) {
                            $(this).parent().parent().addClass('selected');
                        }
                    });
                }
                $('#msunifaun-setup .applicable-applications > li > label > input:checked').parent().parent().addClass('selected');

                if ($('#msunifaun-setup .selectable-products').length) {
                    $('#msunifaun-setup .selectable-products > ul > li > label > input').click(function() {
                        var checked = $(this).attr('checked');
                        if (checked) {
                            $(this).parent().parent().addClass('selected');
                        } else {
                            $(this).parent().parent().removeClass('selected');
                        }
                    });
                }
                $('#msunifaun-setup .selectable-products > ul > li > label > input:checked').parent().parent().addClass('selected');

                $('#msunifaun-setup .postal-address-same-as-delivery-address input').click(function() {
                    var checked = $(this).attr('checked');
                    if (checked) {
                        $('fieldset.postal-address').addClass('same-as');
                        $('fieldset.postal-address input, fieldset.postal-address select').removeAttr('required');
                    } else {
                        $('fieldset.postal-address').removeClass('same-as');
                        $('fieldset.postal-address input, fieldset.postal-address select').attr('required', 'required');
                    }
                });
                if ($('#msunifaun-setup .postal-address-same-as-delivery-address input').attr('checked')) {
                    $('fieldset.postal-address').addClass('same-as');
                    $('fieldset.postal-address input, fieldset.postal-address select').removeAttr('required');
                }

                $('#msunifaun-setup .invoice-address-same-as-postal-address input').click(function() {
                    var checked = $(this).attr('checked');
                    if (checked) {
                        $('fieldset.invoice-address').addClass('same-as');
                        $('fieldset.invoice-address input, fieldset.invoice-address select').removeAttr('required');
                    } else {
                        $('fieldset.invoice-address').removeClass('same-as');
                        $('fieldset.invoice-address input, fieldset.invoice-address select').attr('required', 'required');
                    }
                });
                if ($('#msunifaun-setup .invoice-address-same-as-postal-address input').attr('checked')) {
                    $('fieldset.invoice-address').addClass('same-as');
                    $('fieldset.invoice-address input, fieldset.invoice-address select').removeAttr('required');
                }
            }
        });
    }) (jQuery);
}
