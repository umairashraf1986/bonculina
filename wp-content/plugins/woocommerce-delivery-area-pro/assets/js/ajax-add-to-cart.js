(function ($) {

    $(document).on('submit','form.cart',function(e){
        e.preventDefault();
        alert();

    });

    $(document).on('click', '.add_to_cart_button ', function (e) {
        e.preventDefault();
                alert();

        var $thisbutton = $(this),
                $form = $thisbutton.closest('form.cart'),
                id = $thisbutton.val(),
                product_qty = $form.find('input[name=quantity]').val() || 1,
                product_id = $form.find('input[name=product_id]').val() || id,
                variation_id = $form.find('input[name=variation_id]').val() || 0;
                console.log(product_id);


        //$(".wdapzipsumit").trigger("click");
        var is_tested = $('.wdap_form_wrapper_'+product_id).find("#Chkziptestresult").val();
        if(product_id){
            var is_tested = $('.wdap_form_wrapper_'+product_id).find("#Chkziptestresult").val();
            if(is_tested){

                if(is_tested=="NO"){

                    alert("Your product is not available");

                }else{


                var data = {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: product_id,
                    product_sku: '',
                    quantity: product_qty,
                    variation_id: variation_id,
                };

                $(document.body).trigger('adding_to_cart', [$thisbutton, data]);

                $.ajax({
                    type: 'post',
                    url: wc_add_to_cart_params.ajax_url,
                    data: data,
                    beforeSend: function (response) {
                        $thisbutton.removeClass('added').addClass('loading');
                    },
                    complete: function (response) {
                        $thisbutton.addClass('added').removeClass('loading');
                    },
                    success: function (response) {

                        console.log(response);

                        /*if (response.error &amp; response.product_url) {
                            window.location = response.product_url;
                            return;
                        } else {
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
                        }*/
                    },
                });

                return false;

                }

            }else{
                $(".wdapzipsumit").trigger("click");
            }

        }

    });
})(jQuery);
