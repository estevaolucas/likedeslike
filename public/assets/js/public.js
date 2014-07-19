(function ( $ ) {
	"use strict";

	$(function () {
    var busy = false; 

    $('button.likedeslike').on('click', function() {
      var data = $(this).data();

      if (busy) {
        return;
      }

      busy = true;

      $.ajax( {
        url: data.url, 
        data: data,
        type: 'POST',
        success: function(response) {
          if (response.success) {
            $("span.likedeslike[data-token='" + data.token + "']").text(response.count);
          }

          busy = false;
        },
        error: function() {
          busy = false;
        }
      })
    })
	});

}(jQuery));