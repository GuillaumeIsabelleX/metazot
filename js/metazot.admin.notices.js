jQuery(document).ready( function()
{

    jQuery(document).on( 'click', '.Metazot_update_notice .notice-dismiss', function()
    {
        jQuery.ajax({
            url: zpNoticesAJAX.ajaxurl,
            data: {
                action: 'zpNoticesViaAJAX'
            },
            xhrFields: {
                withCredentials: true
            }
        });
    });

});
