/**
 * @var {Boolean}
 */
var mediastrategi_unifaunonline_debug_flag = false;

/**
 * @var {Object}
 */
var mediastrategi_unifaunonline_meta_partners = {};

/**
 * @var {Object}
 */
var mediastrategi_unifaunonline_selected_addons = {};

/**
 * @var {String}
 */
var mediastrategi_unifaunonline_custompackages_prefix = 'msunifaunonline_custom_packages';

/**
 * @param {String} message
 */
function mediastrategi_unifaun_debug(message)
{
    if (message !== ''
        && typeof(console) !== 'undefined'
        && typeof(console.log) !== 'undefined'
        && mediastrategi_unifaunonline_debug_flag
       ) {
        console.log(message);
    }
}

if (typeof(jQuery) !== 'undefined') {
    /* global jQuery */
    (function($) {

        $(document).ready(function() {

            if ($('#woocommerce_msunifaun_online__selected_addons').length) {

                // Load meta partners data via Ajax
                $('#mainform').addClass('ajax-spinner');
                $.ajax(
                    window.location.href,
                    {
                        cache: false,
                        data: {
                            msunifaun_online_meta_partners: true
                        },
                        dataType: 'json',
                        error: function(response) {
                            alert('Failed to load Unifaun Online meta partners data! See console for more information.');
                            console.error(response);
                            mediastrategi_unifaunonline_meta_partners = false;
                            $('#mainform').removeClass('ajax-spinner');
                        },
                        method: 'POST',
                        success: function(response) {
                            if (!response.length) {
                                alert('Found no partners on account! Verify your Unifaun Online API credentials and account.');
                            }
                            mediastrategi_unifaunonline_meta_partners = response;
                            mediastrategi_unifaun_debug('Meta Partners:');
                            mediastrategi_unifaun_debug(response);
                            $('#mainform').removeClass('ajax-spinner');
                            mediastrategi_unifaun_online_displayCarrierServices();
                        }
                    }
                );

                /**
                 *
                 */
                function mediastrategi_unifaun_online_displayCarrierServices()
                {
                    var carrierService =
                        $('#woocommerce_msunifaun_online__service').val();
                    var split = carrierService.split('_');
                    var carrierId = null, serviceId = null;
                    if (split.length == 2) {
                        carrierId = split[0];
                        serviceId = split[1];
                    }
                    var hasSelected = (carrierId && serviceId);
                    if (typeof(mediastrategi_unifaunonline_meta_partners) !== 'undefined') {
                        var html = '<select>';
                        for (var carrierIndex in mediastrategi_unifaunonline_meta_partners) {
                            if (mediastrategi_unifaunonline_meta_partners.hasOwnProperty(carrierIndex)) {
                                var carrier = mediastrategi_unifaunonline_meta_partners[carrierIndex];
                                html += '<optgroup label="' + carrier.description + '">';
                                if (typeof(carrier.services) !== 'undefined') {
                                    for (var serviceIndex in carrier.services) {
                                        if (carrier.services.hasOwnProperty(serviceIndex)) {
                                            var service = carrier.services[serviceIndex];
                                            var isSelected = (carrier.id == carrierId
                                                              && service.id == serviceId);
                                            html += '<option value="' + carrier.id + '_' + service.id + '"';
                                            if (isSelected) {
                                                html += ' selected="selected"';
                                            }
                                            html += '>' + service.description + ' (' + service.id + ')</option>';
                                        }
                                    }
                                }
                                html += '</optgroup>';
                            }
                        }
                        html += '</select>';

                        var carrierServiceSelector = $(html);
                        $(carrierServiceSelector).change(function(event) {
                            $('#woocommerce_msunifaun_online__service').val($(this).val());
                            $('#woocommerce_msunifaun_online__service').trigger('change');
                        });
                        $('#woocommerce_msunifaun_online__service').after(carrierServiceSelector);

                        $('#woocommerce_msunifaun_online__service').trigger('change');
                    }
                }

                /**
                 * @param {String} carrierService
                 * @param {Object} container
                 */
                function mediastrategi_unifaun_online_changeSelectedAddon(carrierService, container)
                {
                    mediastrategi_unifaun_debug('mediastrategi_unifaun_online_changeSelectedAddon: ' + carrierService);
                    var split = carrierService.split('_');
                    if (split.length == 2) {
                        var carrierId = split[0];
                        var serviceId = split[1];
                        if (typeof(mediastrategi_unifaunonline_meta_partners) !== 'undefined'
                            && carrierId
                            && serviceId
                           ) {
                            var foundCarrier = false, foundService = false;
                            for (var carrierIndex in mediastrategi_unifaunonline_meta_partners) {
                                if (mediastrategi_unifaunonline_meta_partners.hasOwnProperty(carrierIndex)) {
                                    var carrier = mediastrategi_unifaunonline_meta_partners[carrierIndex];
                                    if (carrier.id == carrierId) {
                                        foundCarrier = true;
                                        if (typeof(carrier.services) !== 'undefined') {
                                            for (var serviceIndex in carrier.services) {
                                                if (carrier.services.hasOwnProperty(serviceIndex)) {
                                                    var service = carrier.services[serviceIndex];
                                                    if (service.id == serviceId) {
                                                        var html = '';
                                                        foundService = true;
                                                        if (typeof(service.addons) !== 'undefined') {
                                                            for (var addonIndex in service.addons) {
                                                                if (service.addons.hasOwnProperty(addonIndex)) {
                                                                    var addon = service.addons[addonIndex];
                                                                    var addonId = addon.id;
                                                                    var selected = typeof(mediastrategi_unifaunonline_selected_addons[addonId]) !== 'undefined';
                                                                    html += '<div class="addon' + (selected ? ' selected' : '') + '" data-addon="' + addonId + '"><label>'
                                                                        + '<input id="service-addon-' + addonId + '" type="checkbox" value="1" data-carrier="' + carrier + '" data-service="' + service + '" data-addon="' + addonId + '"' + (selected ? ' checked="checked"' : '') + ' />'
                                                                        + addon.description + ' (' + addonId + ')</label>';
                                                                    if (addon.hasOwnProperty('values')
                                                                        && typeof(addon.values) !== 'undefined'
                                                                        && addon.values !== null
                                                                       ) {
                                                                        html += '<div class="fields">';
                                                                        for (var fieldIndex in addon.values) {
                                                                            if (addon.values.hasOwnProperty(fieldIndex)) {
                                                                                var field = addon.values[fieldIndex];
                                                                                var fieldId = field.id;
                                                                                html += '<label>';
                                                                                var value = (selected
                                                                                             && typeof(mediastrategi_unifaunonline_selected_addons[addonId][fieldId]) !== 'undefined'
                                                                                             ? mediastrategi_unifaunonline_selected_addons[addonId][fieldId] : false);
                                                                                if (field.dataType == 'string'
                                                                                    || field.dataType == 'number'
                                                                                   ) {
                                                                                    if (field.validValues) {
                                                                                        html += field.description + '<select data-field="' + fieldId + '">';
                                                                                        for (var valueIndex in field.validValues) {
                                                                                            if (field.validValues.hasOwnProperty(valueIndex)) {
                                                                                                var validValue = field.validValues[valueIndex];
                                                                                                html += '<option' + (value && validValue == value ? ' selected="selected"' : '') + '>' + validValue + '</option>';
                                                                                            }
                                                                                        }
                                                                                        html += '</select>';
                                                                                    } else {
                                                                                        html += field.description + '<input type="text" data-field="' + fieldId + '"' + (value ? ' value="' + value + '"' : '') + ' />';
                                                                                    }
                                                                                } else if (field.dataType == 'boolean') {
                                                                                    html += '<input type="checkbox" data-field="' + fieldId + '" value="' + field.value + '"' + (value ? ' checked="checked"' : '') + ' />'
                                                                                        + field.description;
                                                                                } else {
                                                                                    console.error('Field type "' + field.dataType + '" not supported!', field);
                                                                                }
                                                                                html += '</label>';
                                                                            }
                                                                        }
                                                                        html += '</div>';
                                                                    }
                                                                    html += '</div>';
                                                                }
                                                            }
                                                        }
                                                        container.html(html);

                                                        // Update list of package codes from API
                                                        if (typeof(service.packageCodes) !== 'undefined') {
                                                            $('select', $('#woocommerce_msunifaun_online__package_type').parent()).remove();
                                                            var selectedPackageCode =
                                                                $('#woocommerce_msunifaun_online__package_type').val();
                                                            html = '<select>';
                                                            var isSelectedPackageCode = false;
                                                            if (service.packageCodes) {
                                                                for (var packageIndex in service.packageCodes) {
                                                                    if (service.packageCodes.hasOwnProperty(packageIndex)) {
                                                                        var package = service.packageCodes[packageIndex];
                                                                        isSelectedPackageCode = (package.id == selectedPackageCode);
                                                                        html += '<option value="' + package.id + '"';
                                                                        if (isSelectedPackageCode) {
                                                                            html += ' selected="selected"';
                                                                        }
                                                                        html += '>' + package.description + '</option>';
                                                                    }
                                                                }
                                                            } else {
                                                                html += '<option></option>';
                                                            }
                                                            html += '</select>';
                                                            $('#woocommerce_msunifaun_online__package_type').after($(html));
                                                        }
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                            }

                            if (!foundCarrier) {
                                console.error('Did not find carrier id: ' + carrierId);
                            }
                            if (!foundService) {
                                console.error('Did not find service id: ' + serviceId);
                            }
                        }
                    }
                }

                // Selected add-ons
                if ($('#woocommerce_msunifaun_online__selected_addons').length) {

                    // Create container for available service add-ons
                    $('#woocommerce_msunifaun_online__selected_addons').parent().append(
                        '<div id="msunifaun_online_addons_container"></div>'
                    );
                    var container = $('#msunifaun_online_addons_container');

                    if ($('#woocommerce_msunifaun_online__selected_addons').val() !== '') {
                        try {
                            mediastrategi_unifaunonline_selected_addons = $.parseJSON($('#woocommerce_msunifaun_online__selected_addons').val());
                            // console.log('Decoded selected add-ons:');
                            // console.log(mediastrategi_unifaunonline_selected_addons);
                        } catch (e) {
                            console.error(e);
                        }
                    }

                    // When changing service, refresh available service add-ons
                    $('#woocommerce_msunifaun_online__service').change(function(event) {
                        mediastrategi_unifaun_online_changeSelectedAddon(
                            $(this).val(),
                            container
                        );
                    });

                    // When user toggles selected add-on change container class
                    $('#msunifaun_online_addons_container').on('change', '.addon > label > input[type="checkbox"]', function(event) {
                        var selected = $(this).parents('.addon').first().hasClass('selected');
                        if (selected) {
                            $(this).parents('.addon').first().removeClass('selected');
                        } else {
                            $(this).parents('.addon').first().addClass('selected');
                        }
                    });

                    // When a value changes inside selected add-ons update the JSON data
                    $('#msunifaun_online_addons_container').on('change', null, function(event) {
                        var values = {};
                        $('.addon.selected', this).each(function(i, object) {
                            var addon = $(object).attr('data-addon');
                            if (!values.hasOwnProperty(addon)) {
                                values[addon] = {};
                            }
                            $('input, textarea, select', $('.fields', object)).each(function(j, subobject) {
                                var field = $(subobject).attr('data-field');
                                var value = $(subobject).val();
                                if (value !== '') {
                                    values[addon][field] = value;
                                }
                            });
                        });
                        $('#woocommerce_msunifaun_online__selected_addons').val(
                            JSON.stringify(values)
                        );
                        mediastrategi_unifaunonline_selected_addons = values;
                    });
                }
            }

            // Custom Packages
            if ($('#msunifaunonline-shipping-custom-packages').length) {
                $('#msunifaunonline-shipping-custom-packages input[type="checkbox"]').change(function(event) {
                    if ($(this).attr('checked')) {
                        $(this).parent().parent().addClass('customizable');
                    } else {
                        $(this).parent().parent().removeClass('customizable');
                    }
                });
                $('#msunifaunonline-shipping-custom-packages input[type="checkbox"]').trigger('change');

                // Parse and present packages
                $('#msunifaunonline-shipping-custom-packages script').each(function(i, obj) {
                    var packageIndex = $(obj).data('package-index');
                    var packages = [];
                    try {
                        packages = JSON.parse($(obj).html());
                    } catch (e) {
                        console.error(e);
                    }
                    var parent = $(obj).parents('#msunifaunonline-shipping-custom-packages').first();
                    $(obj).remove();
                    mediastrategi_unifaun_debug(packages);

                    var html = '';
                    for (var i in packages) {
                        if (packages.hasOwnProperty(i)) {
                            var package = packages[i];
                            // Populate default fields in not set
                            if (typeof(package.copies) === 'undefined') {
                                package.copies = 1;
                            }
                            if (typeof(package.packageCode) === 'undefined') {
                                package.packageCode = '';
                            }
                            if (typeof(package.contents) === 'undefined') {
                                package.contents = '';
                            }
                            if (typeof(package.height) === 'undefined') {
                                package.height = '';
                            }
                            if (typeof(package.length) === 'undefined') {
                                package.length = '';
                            }
                            if (typeof(package.weight) === 'undefined') {
                                package.weight = '';
                            }
                            if (typeof(package.width) === 'undefined') {
                                package.width = '';
                            }
                            html += '<tr><td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + i + '][copies]" value="' + package.copies + '" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + i + '][packageCode]" value="' + package.packageCode + '" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + i + '][contents]" value="' + package.contents + '" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + i + '][height]" value="' + package.height + '" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + i + '][length]" value="' + package.length + '" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + i + '][weight]" value="' + package.weight + '" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + i + '][width]" value="' + package.width + '" /></td>'
                                + '</tr>';
                        }
                    }
                    var j = packages.length;
                    if (!j) {
                        html += '<tr><td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][copies]" value="" /></td>'
                            + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][packageCode]" value="" /></td>'
                            + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][contents]" value="" /></td>'
                            + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][height]" value="" /></td>'
                            + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][length]" value="" /></td>'
                            + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][weight]" value="" /></td>'
                            + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][width]" value="" /></td>'
                            + '</tr>';
                    }
                    $('tbody', parent).html(html);
                    mediastrategi_unifaun_debug('Packages HTML for ' + packageIndex + ': ' + html);

                    // Add and remove rows
                    $('.add-row', parent).click(function(event) {
                        event.preventDefault();
                        j = $('tbody tr', parent).length;
                        $('tbody', parent).append(
                            '<tr><td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][copies]" value="" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][packageCode]" value="" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][contents]" value="" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][height]" value="" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][length]" value="" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][weight]" value="" /></td>'
                                + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][' + j + '][width]" value="" /></td>'
                                + '</tr>'
                        );
                    });
                    $('.remove-row', parent).click(function(event) {
                        event.preventDefault();
                        j = $('tbody tr', parent).length;
                        if (j > 1) {
                            $('tbody tr:last-child', parent).remove();
                        } else {
                            $('tbody', parent).html(
                                '<tr><td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][0][copies]" value="" /></td>'
                                    + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][0][packageCode]" value="" /></td>'
                                    + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][0][contents]" value="" /></td>'
                                    + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][0][height]" value="" /></td>'
                                    + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][0][length]" value="" /></td>'
                                    + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][0][weight]" value="" /></td>'
                                    + '<td><input type="text" name="' + mediastrategi_unifaunonline_custompackages_prefix + '[' + packageIndex + '][0][width]" value="" /></td>'
                                    + '</tr>'
                            );
                        }
                    });
                });
            }

            mediastrategi_unifaun_debug('Mediastrategi Unifaun Online admin script loaded');
        });
    })(jQuery);
}
