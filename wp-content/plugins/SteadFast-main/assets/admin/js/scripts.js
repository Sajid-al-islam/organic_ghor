/**
 * Admin Scripts
 */


jQuery(document).ready(function () {
	
	var check = jQuery("#api_settings_tab_checkbox").is(':checked');

    if (check === true) {
        jQuery('#api_settings_tab_api_secret_key').removeAttr('disabled');
        jQuery('#api_settings_tab_api_key').removeAttr('disabled');
    } else {
        jQuery('#api_settings_tab_api_secret_key').attr('disabled', 'disabled');
        jQuery('#api_settings_tab_api_key').attr('disabled', 'disabled');
    }

    jQuery('#api_settings_tab_checkbox').click(function () {

        var pageselected = jQuery(this).is(':checked');

        if (pageselected === true) {
            jQuery('#api_settings_tab_api_secret_key').removeAttr('disabled');
            jQuery('#api_settings_tab_api_key').removeAttr('disabled');
        }
        if (pageselected === false) {
            jQuery('#api_settings_tab_api_secret_key').attr('disabled', 'disabled');
            jQuery('#api_settings_tab_api_key').attr('disabled', 'disabled');
        }
    });
	
	
    jQuery(document).on('click', '.steadfast_send', function (e) {
		e.preventDefault();
        var thisButton = jQuery(this);
        var orderId = thisButton.data('order-id');
		
		thisButton.html('Sending...');
		
        jQuery.ajax({
			type: 'post',
            url: ajaxurl, 
			data: {
                'action': 'api_ajax_call', 
				'order_id': orderId,
            }, 

            success: function (response) {

                if (response.data.message === 'success') {
                    thisButton.attr('data-is-sent', 'true');
                    thisButton.html('Sent');
					setTimeout(function(){
						location.reload();
					},2000);
                } else {
                    thisButton.html('Failed').addClass('steadfast-failed');
                }
            }
        });
		
    });
	
    jQuery(document).on('focusout', "#steadfast-amount", function () {

        var thisField = jQuery(this);
        var inputValue = thisField.val();
        var inputId = thisField.data('order-id');
	
        jQuery.ajax({
            url: ajaxurl, 
			data: {
                "action": "input_amount", 
				"input_value": inputValue, 
				"input_id": inputId,
            }, type: 'post',

            success: function (response) {

                if (response.data.message === 'success') {				
                   console.log('Update');
                }
            }
        });
    });

       jQuery(document).on('click', ".print-order-detail", function () {
        var thisButton = jQuery(this);
        var printId = thisButton.data('print-id');

        jQuery.ajax({
            url: ajaxurl,
            data: {
                "action": "print_invoice_id",
                "invoice_id": printId,
            }, type: 'post',

            success: function (response) {
                if (response.success) {
                    console.log(printId)
                    alert('Update Coming Soon!')

                }
            }
        });
    });


    jQuery('.amount-disable').attr('disabled', 'disabled');
    jQuery('.steadfast-send-success').html('Success').attr('disabled', 'disabled').addClass('tooltip');
    jQuery('.tooltip').append('<span class="tooltip-text">This parcel is already send to SteadFast!</span>');
});


