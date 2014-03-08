jQuery(document).ready(function($) {

  $('#fgm-update-campaigns').click( function() {

    // Prevent defualt action - opening tag page
    if (event.preventDefault) {
      event.preventDefault();
    } else {
      event.returnValue = false;
    }

    $('.ajax-loader').fadeIn();
    $('.fgm-update-results').fadeOut();

    data = {
        action: 'vb_fgm_updater',
        fgm_nonce: fgm_var.fgm_nonce,
      };

    
    $.post( fgm_var.fgm_ajax_url, data, function(response) {

      if( response ) {
        $('.ajax-loader').fadeOut();
        $('.fgm-update-results').fadeIn().html( response );
      }
    });

  });

});
