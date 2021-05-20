if (typeof(jQuery) !== 'undefined') {
    /* global jQuery, setTimeout */
    (function($) {
        "use strict";
        $(document).ready(function() {
            if (typeof(Mediastrategi_UnifaunOnline_AjaxObject) !== 'undefined') {
                /* global Mediastrategi_UnifaunOnline_AjaxObject */

                // NOTE we use namespace: Mediastrategi_UnifaunDCO_ and CamelCase

                /**
                 * @var {Boolean}
                 */
                var Mediastrategi_UnifaunOnline_LockedInterface = false;

                /**
                 * @var {Boolean}
                 */
                var Mediastrategi_UnifaunOnline_LastSessionString = '';

                /**
                 * @var {Object}
                 */
                var Mediastrategi_UnifaunOnline_LastSessionObject = {};

                /**
                 * @var {null|Object}
                 */
                var Mediastrategi_UnifaunOnline_AjaxTransaction = null;

                /**
                 * Refresh rate in ms to detect new server-side shipping methods
                 *
                 * @var {Number}
                 */
                var Mediastrategi_UnifaunOnline_RefreshRate = 250;

                /**
                 * @param {Function} callback
                 */
                function Mediastrategi_UnifaunOnline_Debug(callback) {
                    if (Mediastrategi_UnifaunOnline_AjaxObject.Config.Debug) {
                        callback();
                    }
                }

                /**
                 * Flag state as loaded
                 */
                function Mediastrategi_UnifaunOnline_Loaded()
                {
                    $(document.body).addClass('msuo-loaded');
                }

                /**
                 * @return {Boolean}
                 */
                function Mediastrategi_UnifaunOnline_IsLockedInterface()
                {
                    return Mediastrategi_UnifaunOnline_LockedInterface;
                }

                /**
                 *
                 */
                function Mediastrategi_UnifaunOnline_LockInterface()
                {
                    Mediastrategi_UnifaunOnline_LockedInterface = true;
                    if (!$(document.body).hasClass('msuo-locked')) {
                        $(document.body).addClass('msuo-locked');
                    }
                    if ($(document.body).hasClass('msuo-unlocked')) {
                        $(document.body).removeClass('msuo-unlocked');
                    }
                }

                /**
                 *
                 */
                function Mediastrategi_UnifaunOnline_UnlockInterface()
                {
                    Mediastrategi_UnifaunOnline_LockedInterface = false;
                    if (!$(document.body).hasClass('msuo-unlocked')) {
                        $(document.body).addClass('msuo-unlocked');
                    }
                    if ($(document.body).hasClass('msuo-locked')) {
                        $(document.body).removeClass('msuo-locked');
                    }
                }

                /**
                 * This will trigger WooCommerce to refresh checkout.
                 * Will be triggered after a session has been changed.
                 */
                function Mediastrategi_UnifaunOnline_UpdateCheckout()
                {
                    Mediastrategi_UnifaunOnline_Debug(function() {
                        console.log('Mediastrategi_UnifaunOnline_UpdateCheckout()');
                    });
                    $(document.body).trigger('update_checkout');
                }

                /**
                 * A session contains two things:
                 * - zip
                 * - agent
                 *
                 * @param {Object} session
                 */
                function Mediastrategi_UnifaunOnline_UpdateSession(session)
                {
                    Mediastrategi_UnifaunOnline_Debug(function() {
                        console.log('Mediastrategi_UnifaunOnline_UpdateSession()');
                        console.log(session);
                    });
                    if (!Mediastrategi_UnifaunOnline_IsLockedInterface()) {
                        Mediastrategi_UnifaunOnline_Debug(function() {
                            console.log('proceeding since unlocked');
                        });
                        var sessionString = JSON.stringify(session);
                        if (sessionString != Mediastrategi_UnifaunOnline_LastSessionString) {
                            Mediastrategi_UnifaunOnline_Debug(function() {
                                console.log('proceeding since session is new');
                            });
                            Mediastrategi_UnifaunOnline_LockInterface();
                            Mediastrategi_UnifaunOnline_AjaxTransaction = $.ajax({
                                data: {
                                    action: 'Mediastrategi_UnifaunOnline_UpdateShipping',
                                    session: session
                                },
                                dataType: 'json',
                                error: function(response) {
                                    Mediastrategi_UnifaunOnline_UnlockInterface();
                                    console.log('ajax error response');
                                    console.log(response);
                                },
                                success: function(response) {
                                    Mediastrategi_UnifaunOnline_UnlockInterface();
                                    Mediastrategi_UnifaunOnline_Debug(function() {
                                        console.log('ajax response:');
                                        console.log(response);
                                    });
                                    Mediastrategi_UnifaunOnline_LastSessionString = sessionString;
                                    Mediastrategi_UnifaunOnline_LastSessionObject = session;
                                    if (typeof(response.update_checkout) !== 'undefined' &&
                                        response.update_checkout
                                       ) {
                                        Mediastrategi_UnifaunOnline_Debug(function() {
                                            console.log('triggering update of checkout');
                                        });
                                        Mediastrategi_UnifaunOnline_UpdateCheckout();
                                    }
                                },
                                type: 'post',
                                url: Mediastrategi_UnifaunOnline_AjaxObject.AjaxUrl
                            });
                        } else {
                            Mediastrategi_UnifaunOnline_Debug(function() {
                                console.log('Session equals old session, not updating');
                            });
                        }
                    } else {
                        Mediastrategi_UnifaunOnline_Debug(function() {
                            console.log('ignoring since locked');
                        });
                    }
                }

                Mediastrategi_UnifaunOnline_Debug(
                    function() {
                        console.log('Ajax Object:');
                        console.log(Mediastrategi_UnifaunOnline_AjaxObject);
                    }
                );

                // Initialize session
                Mediastrategi_UnifaunOnline_LastSessionObject =
                    Mediastrategi_UnifaunOnline_AjaxObject.Session;
                Mediastrategi_UnifaunOnline_LastSessionString =
                    JSON.stringify(Mediastrategi_UnifaunOnline_LastSessionObject);

                // Custom region selector
                if (Mediastrategi_UnifaunOnline_AjaxObject.Config.CustomRegionSelector.zipCode) {
                    var html = '<div class="custom-region-selector">';

                    if (Mediastrategi_UnifaunOnline_AjaxObject.Config.CustomRegionSelector.showTitle) {
                        html += '<header>' + Mediastrategi_UnifaunOnline_AjaxObject.Config.CustomRegionSelector.title + '</header>';
                    }

                    html += '<input tabindex="2" class="action" value="' + Mediastrategi_UnifaunOnline_AjaxObject.Config.CustomRegionSelector.buttonLabel + '" type="button" />'
                        + '<div class="wrapper">'
                        + '<input tabindex="1" class="zip-code-selector" placeholder="' + Mediastrategi_UnifaunOnline_AjaxObject.Config.CustomRegionSelector.zipCodePlaceholder + '" value="' + Mediastrategi_UnifaunOnline_LastSessionObject.zip + '" type="text" />'
                        + '</div></div>';

                    var addedContainer = false;
                    if ($('#order_review .shop_table').length) {
                        $('#order_review .shop_table').before($(html));
                        addedContainer = true;
                    } else if ($('#kco-order-review .shop_table').length) {
                        $('#kco-order-review .shop_table').before($(html));
                        addedContainer = true;
                    } else if ($('.wc-svea-checkout-order-details .shop_table').length) {
                        $('.wc-svea-checkout-order-details .shop_table').before($(html));
                        addedContainer = true;
                    }

                    if (addedContainer) {
                        $('.custom-region-selector .action').click(function(event) {
                            event.preventDefault();
                            var session = Mediastrategi_UnifaunOnline_LastSessionObject;
                            session.zip = $('.custom-region-selector input[type="text"]').val();
                            Mediastrategi_UnifaunOnline_UpdateSession(session);
                        });
                        $('.custom-region-selector input[type="text"]').keyup(function(event) {
                            if (event.which == 13) {
                                event.preventDefault();
                                event.stopImmediatePropagation();
                                var session = Mediastrategi_UnifaunOnline_LastSessionObject;
                                session.zip = $('.custom-region-selector input').val();
                                Mediastrategi_UnifaunOnline_UpdateSession(session);
                            }
                        });
                    } else {
                        Mediastrategi_UnifaunOnline_Debug(function() {
                            console.log(
                                'Found no place to add custom region selector!'
                            );
                        });
                    }
                }

                /**
                 * Just enqueue the refresh function for detecting new shipping methods
                 */
                function Mediastrategi_UnifaunOnline_EnqueueRefresh()
                {
                    setTimeout(
                        Mediastrategi_UnifaunOnline_Refresh,
                        Mediastrategi_UnifaunOnline_RefreshRate
                    );
                }

                /**
                 * Check for a refresh of shipping methods
                 */
                function Mediastrategi_UnifaunOnline_Refresh()
                {
                    if ($('#msunifaun_online_updated_shipping').length) {
                        $('#msunifaun_online_updated_shipping').remove();
                        Mediastrategi_UnifaunOnline_Debug(function() {
                            console.log('Found refresh of shipping methods');
                        });
                        Mediastrategi_UnifaunOnline_AttachAgentEventHandlers();
                    }
                    Mediastrategi_UnifaunOnline_EnqueueRefresh();
                }

                /**
                 *
                 */
                function Mediastrategi_UnifaunOnline_AttachAgentEventHandlers()
                {
                    Mediastrategi_UnifaunOnline_Debug(function() {
                        console.log('Attached event handlers');
                    });
                    $('.shipping-extra select').change(function(event) {
                        event.preventDefault();
                        var radio = $('input[type="radio"]', $(this).parents('li').first());
                        var radioCount = $('.woocommerce-shipping-methods input[type="radio"]').length;

                        // Do we lack radios or are related radio checked?
                        if (!radioCount
                            || (radio
                                && $(radio).prop('checked'))
                           ) {
                            var service = $(this).data('service');
                            var packageId = $(this).data('package');
                            var agent = $('option:selected', this).val();
                            Mediastrategi_UnifaunOnline_Debug(function() {
                                console.log(
                                    'Custom pick up agent, service: "'
                                        + service + '", package: "' + packageId + '", agent:'
                                );
                                console.log(agent);
                            });
                            if (service && agent) {
                                var session = Mediastrategi_UnifaunOnline_LastSessionObject;
                                var agents = session.agents ? session.agents : {};
                                agents[packageId] = {
                                    agent: agent,
                                    service: service
                                };
                                session.agents = agents;
                                Mediastrategi_UnifaunOnline_UpdateSession(session);
                            }
                        } else {
                            Mediastrategi_UnifaunOnline_Debug(function() {
                                console.log('Shipping method is not selected');
                            });
                        }
                    });
                }

                Mediastrategi_UnifaunOnline_Loaded();
                Mediastrategi_UnifaunOnline_UnlockInterface();
                Mediastrategi_UnifaunOnline_EnqueueRefresh();
            }
        });
    })(jQuery);
}
