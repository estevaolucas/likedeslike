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
          console.log(response);
          if (response.success) {
            console.log("span.likedeslike[data-post-id='" + data.post_id + "'][data-type='" +  data.type + "'");
            $("span.likedeslike[data-post_id='" + data.post_id + "'][data-type='" + data.type + "']").text(response.count);
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