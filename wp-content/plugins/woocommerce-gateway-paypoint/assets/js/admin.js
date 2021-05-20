/* global wc_pay360_params */
(function ($) {
	$(document).ready(function () {
		$('#woocommerce_pay360_hosted_cashier_skin_default').on('change', pay360Admin.maybeHideSkin);

		$('#woocommerce_pay360_hosted_cashier_use_iframe').on('change', pay360Admin.maybeHideIframeFields);

		$('#woocommerce_pay360_integration').on('change', pay360Admin.onIntegrationChange);
	});

	var pay360Admin = {
		maybeHideSkin: function () {
			var custom_skin_field = $('#woocommerce_pay360_hosted_cashier_skin');
			var default_skin = $('#woocommerce_pay360_hosted_cashier_skin_default');

			if ('other' == default_skin.val()) {
				pay360Admin.showField(custom_skin_field);
				return true;
			}

			pay360Admin.hideField(custom_skin_field);
			return true;
		},

		maybeHideIframeFields: function () {
			var iframe_field = $('#woocommerce_pay360_hosted_cashier_use_iframe');

			var width = $('#woocommerce_pay360_hosted_cashier_iframe_width');
			var height = $('#woocommerce_pay360_hosted_cashier_iframe_height');
			var scroll = $('#woocommerce_pay360_hosted_cashier_iframe_scroll');

			if (iframe_field.is(':checked')) {
				pay360Admin.showField(width);
				pay360Admin.showField(height);
				pay360Admin.showField(scroll);
				return true;
			}

			pay360Admin.hideField(width);
			pay360Admin.hideField(height);
			pay360Admin.hideField(scroll);
			return true;
		},

		hideField: function (field) {
			var custom_skin_wrapper = field.closest('tr');

			field.hide();
			custom_skin_wrapper.hide();
		},

		showField: function (field) {
			var custom_skin_wrapper = field.closest('tr');

			field.show();
			custom_skin_wrapper.show();
		},

		onIntegrationChange: function () {
			var integrationEl = $('#woocommerce_pay360_integration');
			var integration = integrationEl.val();
			var type = integrationEl.data('initial-type');
			var manipulationEl = $('.pay360_integration');
			var errorMessageEl = $('.pay360_warning');

			console.log('integration', integration);
			console.log('integration', type);

			if (integration === type) {
				pay360Admin.showField(manipulationEl);
				errorMessageEl.hide();
			} else {
				errorMessageEl.find('p').html(wc_pay360_params.il8n_integration_changed);
				errorMessageEl.addClass('notice notice-error');
				errorMessageEl.show();

				pay360Admin.hideField(manipulationEl);
			}


			return true;
		},
	};

	var pay360Capture = {
		init: function () {
			$('#woocommerce-order-items')
				.on('click', 'button.wc-pay360-capture-payment-init', this.processCapture)

		},

		processCapture: function () {
			var orderItems = $('#woocommerce-order-items');
			orderItems.block();

			if (!window.confirm(wc_pay360_params.i18n_capture_payment)) {
				orderItems.unblock();
				return false;
			}

			var data = {
				action  : 'wc_pay360_capture_payment',
				order_id: woocommerce_admin_meta_boxes.post_id,
				security: wc_pay360_params.capture_payment
			};

			$.post(wc_pay360_params.ajax_url, data, function (response) {
				if (true === response.success) {
					// Redirect to same page for show the refunded status
					window.alert(response.data.message);

					window.location.href = window.location.href;
				} else {
					window.alert(response.data.message);
					orderItems.unblock();
				}
			});
		},
	};

	pay360Admin.maybeHideSkin();
	pay360Admin.maybeHideIframeFields();
	pay360Capture.init();
})(jQuery);
