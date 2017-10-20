jQuery( document ).ready( function ( $ ) {

    /**
     * wp_localize_script object: iwImageActionArgs
     *
     * Params:
     *
     * _nonce
     * __applied_none => 	'Watermark could not be applied to selected files or no valid images (JPEG, PNG) were selected.'
     * __applied_one => 	'Watermark was succesfully applied to 1 image.
     * __applied_multi => 	'Watermark was succesfully applied to %s images.'
     * __removed_none => 	'Watermark could not be removed from selected files or no valid images (JPEG, PNG) were selected.'
     * __removed_one => 	'Watermark was succesfully removed from 1 image.'
     * __removed_multi => 	'Watermark was succesfully removed from %s images.'
     * __skipped => 		'Skipped files'
     * __running => 		'Bulk action is currently running, please wait.'
     * __dismiss => 		'Dismiss this notice.' // Wordpress default string
     *
     */



    watermarkImageActions = {
	running: false,
	action_location: '',
	action: '',
	response: '',
	selected: [ ],
	successCount: 0,
	skippedCount: 0,
	init: function () {

	    // Normal (list) mode
	    $( document ).on( 'click', '.bulkactions input#doaction, .bulkactions input#doaction2', function ( e ) {
		// Get the selected bulk action
		action = $( this ).parent().children( 'select' ).val();

		if ( !iwImageActionArgs.backup_image && action === 'removewatermark' ) {
		    return;
		}

		// Validate action
		if ( 'applywatermark' == action || 'removewatermark' == action ) {
		    // Stop default
		    e.preventDefault();

		    // Is this script running?
		    if ( false === watermarkImageActions.running ) {
			// No! set it on running
			watermarkImageActions.running = true;

			// store current action
			watermarkImageActions.action = action;

			// store current location where the action was fired
			watermarkImageActions.action_location = 'upload-list';

			// store selected attachment id's
			$( '.wp-list-table .check-column input:checkbox:checked' ).each( function () {
			    watermarkImageActions.selected.push( $( this ).val() );
			} );

			// remove current notices
			$( '.iw-notice' ).slideUp( 'fast', function () {
			    $( this ).remove();
			} );

			// begin the update!
			watermarkImageActions.post_loop();

		    } else {
			// script is running, can't run two at the same time
			watermarkImageActions.notice( 'iw-notice error', iwImageActionArgs.__running, false );
		    }

		}
	    } );

	    // Media modal or edit attachment screen mode
	    $( document ).on( 'click', '#image_watermark_buttons a.iw-watermark-action', function ( e ) {
		// Get the selected bulk action
		action = $( this ).attr( 'data-action' );
		id = $( this ).attr( 'data-id' );
    src = $( this ).attr( 'data-src' );
    pubid = $( this ).attr( 'data-pubid' );
    skerpPoints = $('input[name=points]').val();
    //alert(pubid);

    $.cloudinary.config({ cloud_name: 'tmsoftware', api_key: '376895436531487'})

    //alert($.cloudinary.url(pubid + '.jpg', { alt: "Sample Image", effect: 'sepia' }));
    if ( 'orginal' == action ){
      $('.thumbnail-image img').attr('src', $.cloudinary.url(pubid + '.jpg', { alt: "Sample Image" }));
    }
    if ( 'auto' == action ){
      $('.thumbnail-image img').attr('src', $.cloudinary.url(pubid + '.jpg', { alt: "Sample Image", effect: "improve" }));
    }
    if ( 'sharpen' == action & skerpPoints.length > 0 ){
      $('.thumbnail-image img').attr('src', $.cloudinary.url(pubid + '.jpg', { alt: "Sample Image", effect: "sharpen:"+skerpPoints }));
    }
    if ( 'sepia' == action ){
    $('.thumbnail-image img').attr('src', $.cloudinary.url(pubid + '.jpg', { alt: "Sample Image", effect: 'sepia' }));
    }
    if ( 'cartoonify' == action ){
      $('.thumbnail-image img').attr('src', $.cloudinary.url(pubid + '.jpg', { alt: "Sample Image", effect: 'cartoonify' }));
    }
    if ( 'face' == action ){
        $('.thumbnail-image img').attr('src', $.cloudinary.url(pubid + '.jpg', { alt: "Sample Image", crop: 'crop', gravity: 'face'  }));
    }



		// Validate action
	if ( !isNaN( id ) ){	 //if ( 'applywatermark' == action || 'removewatermark' == action && !isNaN( id ) ) {
		    // Stop default
		    e.preventDefault();

		    // store current action
		    watermarkImageActions.action = action;

		    // Is this script running?
		    if ( false === watermarkImageActions.running ) {
			// No! set it on running
			watermarkImageActions.running = true;

			// store current action
			watermarkImageActions.action = action;

			// store current location where the action was fired
			if ( $( this ).parents( '.media-modal ' ).length ) {
			    watermarkImageActions.action_location = 'media-modal';
			} else {
			    watermarkImageActions.action_location = 'edit';
			}

			// store attachment id
			watermarkImageActions.selected.push( id );

			// remove current notices
			$( '.iw-notice' ).slideUp( 'fast', function () {
			    $( this ).remove();
			} );

			// begin the update!
			watermarkImageActions.post_loop();
		    } else {
			// script is running, can't run two at the same time
			watermarkImageActions.notice( 'iw-notice error', iwMediaModal.__running, false );
		    }
		}
	    } );

	    // Since these are added later we'll need to enable dismissing again
	    $( document ).on( 'click', '.iw-notice.is-dismissible .notice-dismiss', function () {
		$( this ).parents( '.iw-notice' ).slideUp( 'fast', function () {
		    $( this ).remove();
		} );
	    } );

	},
	post_loop: function () {




	    // do we have selected attachments?
	    if ( watermarkImageActions.selected.length ) {
       //  alert(watermarkImageActions.selected);
		// take the first id
		id = watermarkImageActions.selected[ 0 ];

		// check for a valid ID (needs to be numeric)
		if ( !isNaN( id ) ) {
//console.log(watermarkImageActions);
		    // Show loading icon

/** loading!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! **/

       // watermarkImageActions.row_image_feedback( 'loading', id );

		    // post data
		    data = {
			'_iw_nonce': iwImageActionArgs._nonce,
			'action': 'iw_watermark_bulk_action',
      //'img-action': action,
			'iw-action': watermarkImageActions.action,
			'attachment_id': id
		    };

		    if ( watermarkImageActions.action_location == 'upload-list' ) {
			watermarkImageActions.scroll_to( '#post-' + id, 'bottom' );
		    }


        jQuery.ajax({
                    url : iwImageActionArgs.ajax_url,
                    type : 'post',
                    data : {
            			'_iw_nonce': iwImageActionArgs._nonce,
            			'action': 'read_me_later', // 'iw_watermark_bulk_action',
                  'iw-action': watermarkImageActions.action,
                //	'iw-action': watermarkImageActions.action,
            			'attachment_id': id
            		    },
                    success : function( response ) {
                        jQuery('.rml_contents').html(response);
                    }
                });





		    // the ajax post!
		/*    $.post( ajaxurl, data, function ( response ) {
			// show result
			watermarkImageActions.result( response, id );
			// remove this ID/key from the selected attachments
			watermarkImageActions.selected.splice( 0, 1 );
			// Redo this function
			watermarkImageActions.post_loop();

			$( '.iw-overlay' ).first().each( function () {
			    $( this ).fadeOut( 'fast', function () {
				$( this ).remove();

				if ( response.data === 'watermarked' ) {

				    $( '#image_watermark_buttons .value' ).append( '<span class="dashicons dashicons-yes" style="font-size: 24px;float: none;min-width: 28px;padding: 0;margin: 0; display: none;"></span>' );
				    $( '#image_watermark_buttons .value .dashicons' ).fadeIn( 'fast' );
				} else if ( response.data === 'watermarkremoved' ) {
				    $( '#image_watermark_buttons .value' ).append( '<span class="dashicons dashicons-yes" style="font-size: 24px;float: none;min-width: 28px;padding: 0;margin: 0; display: none;"></span>' );
				    $( '#image_watermark_buttons .value .dashicons' ).fadeIn( 'fast' );
				}

				$( '#image_watermark_buttons .value .dashicons' ).delay( 1500 ).fadeOut( 'fast', function () {
				    $( this ).remove();
				} );
			    } );
			} );
    } ); */

		} else {
		    // ID is not valid so remove this key from the selected attachments
		    watermarkImageActions.selected.splice( 0, 1 );
		    // Redo this function
		    watermarkImageActions.post_loop();
		}
	    } else {
		// All is done, reset this "class"
		watermarkImageActions.reset();
	    }
	},
	result: function ( response, id ) {

	    // Was the ajax post successful?
	    if ( true === response.success ) {

		// defaults
		var type = false;
		var message = '';
		var overwrite = true;
		// store response data
		watermarkImageActions.response = response.data;

		// Check what kind of action is done (watermarked, watermarkremoved or skipped)
		switch ( response.data ) {
		    case 'watermarked':
			// The css classes for the notice
			type = 'iw-notice updated iw-watermarked';
			// another successful update
			watermarkImageActions.successCount += 1;
			// did we have more success updates?
			if ( 1 < watermarkImageActions.successCount ) {
			    //yes
			    message = iwImageActionArgs.__applied_multi.replace( '%s', watermarkImageActions.successCount );
			} else {
			    //no
			    message = iwImageActionArgs.__applied_one;
			}
			// update the row feedback
			watermarkImageActions.row_image_feedback( 'success', id );
			// reload the image
			watermarkImageActions.reload_image( id );
			break;
		    case 'watermarkremoved':
			// The css classes for the notice
			type = 'iw-notice updated iw-watermarkremoved';
			// another successful update
			watermarkImageActions.successCount += 1;
			// did we have more success updates?
			if ( 1 < watermarkImageActions.successCount ) {
			    //yes
			    message = iwImageActionArgs.__removed_multi.replace( '%s', watermarkImageActions.successCount );
			} else {
			    //no
			    message = iwImageActionArgs.__removed_one;
			}
			// update the row feedback
			watermarkImageActions.row_image_feedback( 'success', id );
			// reload the image
			watermarkImageActions.reload_image( id );
			break;
		    case 'skipped':
			// The css classes for the notice
			type = 'iw-notice error iw-skipped';
			// another skipped update
			watermarkImageActions.skippedCount += 1;
			// adjust the message with the number of skipped updates
			message = iwImageActionArgs.__skipped + ': ' + watermarkImageActions.skippedCount;
			// update the row feedback
			watermarkImageActions.row_image_feedback( 'error', id );
			break;
		    default:
			// The css classes for the notice
			type = 'iw-notice error iw-message';
			// The error message
			message = response.data;
			// update the row feedback
			watermarkImageActions.row_image_feedback( 'error', id );
			// This can be anything so don't overwrite
			overwrite = false;
			break;
		}
		if ( false !== type ) {
		    // we have a valid terun type, show the notice! (Overwrite current notice if available)
		    watermarkImageActions.notice( type, message, overwrite );
		}
	    } else {
		// No success...
		watermarkImageActions.notice( 'iw-notice error', response.data, false );
		// update the row feedback
		watermarkImageActions.row_image_feedback( 'error', id );
	    }

	},
  // af stað með loading icon
  row_image_feedback: function ( type, id ) {
	    var css = { },
		cssinner = { },
		container_selector;

	    switch ( watermarkImageActions.action_location ) {
		case 'upload-list':
		    container_selector = '.wp-list-table #post-' + id + ' .media-icon';
		    css = {
			display: 'table',
			width: $( container_selector ).width() + 'px',
			height: $( container_selector ).height() + 'px',
			top: '0',
			left: '0',
			position: 'absolute',
			font: 'normal normal normal dashicons',
			background: 'rgba(255,255,255,0.75)',
			content: ''
		    };
		    cssinner = {
			'vertical-align': 'middle',
			'text-align': 'center',
			display: 'table-cell',
			width: '100%',
			height: '100%',
		    };
		    break;

		case 'edit':
		    container_selector = '.wp_attachment_holder #thumbnail-head-' + id + '';
		    css = {
			display: 'table',
			width: $( container_selector + ' img' ).width() + 'px',
			height: $( container_selector + ' img' ).height() + 'px',
			top: '0',
			left: '0',
			position: 'absolute',
			font: 'normal normal normal dashicons',
			background: 'rgba(255,255,255,0.75)',
			content: ''
		    };
		    cssinner = {
			'vertical-align': 'middle',
			'text-align': 'center',
			display: 'table-cell',
			width: '100%',
			height: '100%',
		    };
		    break;

		case 'media-modal':
		    container_selector = '.media-modal #image_watermark_buttons[data-id="' + id + '"] .value';
		    css = {
			'float': 'none'
		    };
		    cssinner = {
			'float': 'none'
		    };
		    break;

		default:
		    return false;
	    }

	    // css rules
	    $( container_selector ).css( 'position', 'relative' );

	    // Only create the element if it doesn't exist
	    if ( !$( container_selector + ' .iw-overlay' ).length ) {
		$( container_selector ).append( '<span class="iw-overlay"><span class="iw-overlay-inner"></span></span>' );
	    }

	    // Overwrite with new data
	    $( container_selector + ' .iw-overlay' ).css( css );
	    $( container_selector + ' .iw-overlay .iw-overlay-inner' ).css( cssinner );
	    $( container_selector + ' .iw-overlay .iw-overlay-inner' ).html( '<span class="spinner is-active"></span>' );

	    if ( watermarkImageActions.action_location === 'media-modal' ) {
		$( container_selector + ' .iw-overlay .iw-overlay-inner .spinner' ).css( { 'float': 'none', 'padding': 0, 'margin': '-4px 0 0 10px' } );
	    }
	}




}; // end object

    // We need that nonce!
  //  if ( typeof iwImageActionArgs._nonce != 'undefined' ) {
	watermarkImageActions.init();
  //  }

} );
