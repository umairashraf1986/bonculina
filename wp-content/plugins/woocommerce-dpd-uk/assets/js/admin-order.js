
jQuery(document).on('click','.dpd_uk-button-add-package', function() {
	var count = jQuery(this).attr('data-count');
	if ( typeof count != 'undefined' ) {
        var count_additional = parseInt(jQuery(this).attr('data-count_additional')) + 1;
        jQuery(this).attr('data-count_additional', count_additional);
        var html = jQuery('#dpd_uk_additional_package_template_' + count).html()
        html = html.split('_additional_key_').join(count_additional);
        html = html.split('_count_').join(count);
        jQuery(this).parent().before(html);
    }
    var id = jQuery(this).attr('data-id');
    if ( typeof id != 'undefined' ) {
        var count_additional = parseInt(jQuery(this).attr('data-count_additional')) + 1;
        jQuery(this).attr('data-count_additional', count_additional);
        var html = jQuery('#dpd_uk_additional_package_template_' + id).html()
        html = html.split('_additional_key_').join(count_additional);
        html = html.split('_count_').join(count);
        jQuery(this).parent().before(html);
    }
})

jQuery(document).on('click','.dpd_uk-button-delete-package', function() {
    if ( jQuery(this).attr('disabled') == 'disabled' ) {
        return;
    }
    jQuery(this).parent().remove();
})

jQuery(document).on('click','.dpd_uk-button-delete-created', function( e ) {
    var count = jQuery(this).attr('data-count');
    if ( typeof count != 'undefined' ) {
        if (jQuery(this).attr('disabled') == 'disabled') {
            return;
        }
        jQuery('.dpd_uk-button').attr('disabled', 'disabled');
        jQuery(this).parent().find('.spinner').css({visibility: 'visible'});

        var count = jQuery(this).attr('data-count');

        dpd_uk_ajax('delete_created_package', count);
    }

    var id = jQuery(this).attr('data-id');
    if ( typeof id != 'undefined' ) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var id = fs_id(this);
        var fs_action = 'cancel';
        fs_ajax( this, id, fs_action );
    }
})


jQuery(document).on('click','.dpd_uk-button-create', function(e) {

    var count = jQuery(this).attr('data-count');
    if ( typeof count != 'undefined' ) {

        if (jQuery(this).attr('disabled') == 'disabled') {
            return;
        }
        jQuery('.dpd_uk-button').attr('disabled', 'disabled');
        jQuery(this).parent().find('.spinner').css({visibility: 'visible'});

        dpd_uk_ajax('create_package', count);
    }

    var id = jQuery(this).attr('data-id');
    if ( typeof id != 'undefined' ) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var id = fs_id(this);
        var fs_action = 'send';
        fs_ajax( this, id, fs_action );
    }

})

jQuery(document).on('click','.dpd_uk-button-save', function(e) {
    var count = jQuery(this).attr('data-count');
    if ( typeof count != 'undefined' ) {
        if (jQuery(this).attr('disabled') == 'disabled') {
            return;
        }
        jQuery('.dpd_uk-button').attr('disabled', 'disabled');
        jQuery(this).parent().find('.spinner').css({visibility: 'visible'});

        var count = jQuery(this).attr('data-count');

        dpd_uk_ajax('save_package', count);
    }

    var id = jQuery(this).attr('data-id');
    if ( typeof id != 'undefined' ) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var id = fs_id(this);
        var fs_action = 'save';
        fs_ajax( this, id, fs_action );
    }
})

jQuery(document).on('click','.dpd_uk-button-delete', function() {
    if ( jQuery(this).attr('disabled') == 'disabled' ) {
        return;
    }
    jQuery('.dpd_uk-button').attr('disabled','disabled');
    jQuery(this).parent().find('.spinner').css({visibility: 'visible'});

    var count = jQuery(this).attr('data-count');

    dpd_uk_ajax( 'delete_package', count );

})


jQuery(document).on('click','.dpd_uk-button-print', function() {
    if ( jQuery(this).attr('disabled') == 'disabled' ) {
        return;
    }
    jQuery('.dpd_uk-button').attr('disabled','disabled');
    jQuery(this).parent().find('.spinner').css({visibility: 'visible'});
    var count = jQuery(this).attr('data-count');

    var ajax_data = {
        'action'		: 'wpdesk_dpd_uk',
        'dpd_uk_action'	: 'print_label',
        'security'		: jQuery("#dpd_uk_ajax_nonce").val(),
        'post_id'		: jQuery("input[name=post_id]").val(),
        'shipment_id'	: jQuery(this).attr('data-shipment-id'),
		'key'			: jQuery("#dpd_uk_"+count+"_key").val(),
    };

    jQuery.ajax({
        url		: dpd_uk_ajax_object.ajax_url,
        data	: ajax_data,
        method	: 'POST',
        dataType: 'JSON',
        success: function( data ) {
			jQuery('#print_message_' + data.post.shipment_id).html(data.message);
        },
        error: function ( xhr, ajaxOptions, thrownError ) {
            alert( xhr.status + ': ' + thrownError );
        },
        complete: function() {
            jQuery('.dpd_uk-button').removeAttr('disabled','disabled');
            jQuery('.dpd_uk-spinner').css({visibility: 'hidden'});
        }
    });
})

jQuery(document).on('click','.dpd_uk-button-label', function() {
	var shipment_id = jQuery(this).attr('data-shipment-id');
	var url = dpd_uk_ajax_object.ajax_url + '?action=wpdesk_dpd_uk&dpd_uk_action=get_label&security=' + jQuery("#dpd_uk_ajax_nonce").val();
	url = url + '&shipment_id=' + shipment_id;
	url = url + '&post_id=' + jQuery("input[name=post_id]").val();
	var count = jQuery(this).attr('data-count');
	url = url + '&key=' + jQuery("#dpd_uk_"+count+"_key").val(),
	window.open( url, '_blank');
})

jQuery(document).on('change','.dpd_uk-product', function() {
})


jQuery(document).on('change','.dpd_uk-declared_value', function() {
    if ( jQuery(this).is(':checked') ) {
        jQuery(this).closest('.dpd_uk-package').find('.dpd_uk-declared_value-value').show();
    }
    else {
        jQuery(this).closest('.dpd_uk-package').find('.dpd_uk-declared_value-value').hide();
    }
})

jQuery(document).on('change','.dpd_uk-liability', function() {
    if ( jQuery(this).is(':checked') ) {
        jQuery(this).closest('.dpd_uk-package').find('.dpd_uk-liability-value').show();
    }
    else {
        jQuery(this).closest('.dpd_uk-package').find('.dpd_uk-liability-value').hide();
    }
})


function dpd_uk_init() {
    jQuery('.dpd_uk-product').each(function(){
        jQuery(this).change();
    })
	jQuery('.dpd_uk-disabled').each(function(){
		jQuery(this).prop('disabled',true);
	})
    jQuery('.dpd_uk-declared_value').each(function(){
        jQuery(this).change();
    })
    jQuery('.dpd_uk-liability').each(function(){
        jQuery(this).change();
    })

}

dpd_uk_init();
