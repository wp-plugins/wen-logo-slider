var wls_file_frame;

(function( $ ) {
	'use strict';

	 $(function() {

	 	// Sortable
	 	$( "#main-slides-list-wrap" ).sortable();

	 	// Uploads

	 	jQuery(document).on('click', 'input.wls-select-img', function( event ){

	 	  var $this = $(this);

	 	  event.preventDefault();

	 	  // Create the media frame.
	 	  wls_file_frame = wp.media.frames.wls_file_frame = wp.media({
	 	    title: jQuery( this ).data( 'uploader_title' ),
	 	    button: {
	 	      text: jQuery( this ).data( 'uploader_button_text' ),
	 	    },
	 	    multiple: false  // Set to true to allow multiple files to be selected
	 	  });



	 	  // When an image is selected, run a callback.
	 	  wls_file_frame.on( 'select', function() {
	 	    // We set multiple to false so only get one image from the uploader
	 	    var attachment = wls_file_frame.state().get('selection').first().toJSON();
	 	    // console.log(attachment.sizes.thumbnail.url); return;
	 	    var image_field = $this.siblings('.wls-slide-image-id');
	 	    image_field.val(attachment.id);
	 	    var imgurl = attachment.url;
	 	    if( 'undefined' != typeof attachment.sizes.thumbnail ){
  	 	    imgurl = attachment.sizes.thumbnail.url;
	 	    }
	 	    var image_preview_wrap = $this.siblings('.image-preview-wrap');
	 	    image_preview_wrap.show();
	 	    image_preview_wrap.find('.img-preview').attr('src',imgurl);
	 	    // Hide upload button
	 	    $this.hide();

	 	  });

	 	  // Finally, open the modal
	 	  wls_file_frame.open();
	 	});

		// Image remove button handler
		$(document).on('click', 'a.btn-wls-remove-image-upload', function(evt){
		  evt.preventDefault();
		  var $this = $(this);

		  var image_field_temp = $this.parent().parent().parent().find('input.wls-slide-image-id');
		  var upload_button = $this.parent().parent().parent().find('input.wls-select-img');
		  var image_preview_wrap = $this.parent().parent().parent().find('.image-preview-wrap');
		  var cur_image_value = image_field_temp.val();

		  image_field_temp.val('');
		  image_preview_wrap.fadeOut('slow',function(){
			  image_preview_wrap.hide();
			  image_preview_wrap.find('.img-preview').attr('src','');
			  upload_button.fadeIn();
		  });
		  return;
		});




	 	// Remove Handler
	 	$(document).on('click','input.btn-remove-slide-item', function(e){

	 		e.preventDefault();
	 		// Confirmation
	 		var confirmation = confirm(WLS_OBJ.lang.are_you_sure);
	 		if( ! confirmation ){
	 			return false;
	 		}

	 		var $this = $(this);
	 		var $wrap = $this.parent().parent();
	 		$wrap.fadeOut('slow',function(){
        $wrap.remove();
      });

	 	})



	 	// Add Handler
	 	$('input.btn-add-slide-item').on('click', function(e){

	 		e.preventDefault();
	 		var mytemplate = $("#template-wls-slider-item").html();
	 		$('#main-slides-list-wrap').append(mytemplate);
	 		$('.txt-slide-title').focus();
 	 		return;

	 	});


 	 });


})( jQuery );
