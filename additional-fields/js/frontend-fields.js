jQuery(document).ready(function($) {
	
	
	/**
	 * Get favicon.
	 */
	function getFavicon(elem){
	
		var faviconUrl = $(elem).val(); // select if current page is "Home"
	
		if( !faviconUrl ){
			faviconUrl = $(elem).attr('href'); // select if current page is "View"
		}
		
		
		faviconUrl = faviconUrl.replace(/http:\/\//gi, '');  // remove http://
		faviconUrl = faviconUrl.replace(/https:\/\//gi, ''); // remove https://
		faviconUrl = faviconUrl.replace(/www./gi, '');		 // remove www.
		faviconUrl = faviconUrl.split('/', 1)[0]; 			 // cut "host" from url
		
		faviconUrl = 'https://www.google.com/s2/favicons?domain=' + faviconUrl;
		$(elem).siblings('.af-favicon, .af-favicon-view').css('background-image', 'url(' + faviconUrl + ')');
	}
	
	
	/**
	 * Add icons after loading the "View user" or "Home" page.
	 */
	$('.af-valid').each(function(){ // select if current page is "Home"
		getFavicon(this);
	});
	$('.af-url-view').each(function(){ // select if current page is "View"
		getFavicon(this);
	});
	
	
	/**
	 * Add, remove fields.
	 */
	$(document).on('click', '.btn-add', function(e){
		e.preventDefault();
		
		var controlForm = $('.controls .af-wrap:first'),
			currentEntry = $(this).parents('.entry:first'),
			entriesLength = $('.af-wrap > .entry').length,
			allowableFields = $('#allowable-number-of-fields').val(),
			newEntry = $(currentEntry.clone()).appendTo( entriesLength < allowableFields ? controlForm : '' );
		
		newEntry.find('input').val('').removeClass('af-invalid af-valid').siblings('.af-favicon').css('background-image', '');
		
		if( entriesLength == 1 ){
			newEntry.find('.input-group-btn').empty()
				.html('<button class="btn btn-remove btn-danger after-last-input" type="button"><span class="glyphicon glyphicon-minus half-height"></span></button><button class="btn btn-success btn-add half-button" type="button"><span class="glyphicon glyphicon-plus minus-place"></span></button>');
		}
		
		controlForm.find('.entry:not(:last) .input-group-btn').empty()
			.html('<button class="btn btn-remove btn-danger" type="button"><span class="glyphicon glyphicon-minus"></span></button>');
		
		if( $('.entry input').length == allowableFields ){ // if element is last 
			$('.entry:last', controlForm).find('.input-group-btn').empty()
				.html('<button class="btn btn-remove btn-danger" type="button"><span class="glyphicon glyphicon-minus"></span></button>');
		}
		
	}).on('click', '.btn-remove', function(e)
	{		
		var entries = $(this).parents('.entry').siblings();
		
		if( $(entries).length == 1 ){
			$(entries).find('.input-group-btn').empty()
				.html('<button class="btn btn-success btn-add" type="button"><span class="glyphicon glyphicon-plus"></span></button>');
		}
		else{
			$(entries).find('.input-group-btn').last().empty()
				.html('<button class="btn btn-remove btn-danger after-last-input" type="button"><span class="glyphicon glyphicon-minus half-height"></span></button><button class="btn btn-success btn-add half-button" type="button"><span class="glyphicon glyphicon-plus minus-place"></span></button>');
		}
		
		$(this).closest('.entry').remove();
			
		e.preventDefault();
		return false;
	});
	
	
	
	/**
	 * Whitelist Ajax validation.
	 */
	$('#af-form').on('input','.input-group input', function(e) {
		
		var elem = this;

		if( $(elem).val() != '' ){
			
			var data = {
				'action': 'af_frontend',
				'params': $(this).val()
			};
		
			$.ajax({
				type: "POST",
				url: af_ajax.url,
				data: data,
				success: function(data) {
				
					$(elem).siblings('.af-favicon').css('background-image', ''); // remove icon before checking
					
					if( data === 'false' ){
						$(elem).removeClass('af-valid').addClass('af-invalid');
					}
					else if( data === 'true' ){
						$(elem).removeClass('af-invalid').addClass('af-valid');
						getFavicon(elem);
					}
					else if( data === 'empty' ){
						$(elem).removeClass('af-invalid af-valid');
					}
					
				}
			});
		
		}
		else {
			$(elem).removeClass('af-invalid af-valid').siblings('.af-favicon').css('background-image', ''); // also remove icon before checking
		}
		
		return false;
	});
	
	
	
	
	/**
	 * Validation URLs before submit.
	 */
	$('#af-form input[type="button"]').on('click', function(){
	
		// get current form
		var af_form = $(this).parent();
		
		
		// trim fields
		$('.af-wrap input').each(function(){
			var inputForTream = $(this);
			inputForTream.val($.trim(inputForTream.val()));
		});
		
		
		// function checks if empty fields exists
		function is_empty_fields(){
		
			var valid = false;
			$('.af-wrap input').each(function(){
				if( $(this).val() == ''){
					return valid = true;
				}	
			});
			
			return valid;		
		}   
		
		// clear warnings before recheck
		//$('.warning-empty, .warning-whitelist', af_form).remove();
		
		// check if fields empty
		// if( is_empty_fields() ) {
		//	 $('.warning-messages', af_form).append('<div style="color:red;" class="warning-empty">Please enter empty fields</div>');
		// }
		
		// check whitelist match
		// if( $('.entry .af-invalid', af_form).length != 0 ){
		// $('.warning-messages', af_form).append('<div style="color:red;" class="warning-whitelist">Please enter a valid URL</div>');
		// }
		
		// if all fields filled correct
		// if( $('.warning-empty, .warning-whitelist').length != 0 ){
		//	return false;
		// }
		
		// checks if empty fields exists
		if(  $('.af-wrap input:not(.af-valid, .af-invalid)', af_form).length != 0 ) {
			return false;
		}
		
		// checks if all fields filled correct
		if(  $('.af-invalid', af_form).length != 0 ) {
			return false;
		}
		
		$('#af-form').submit();
		
	});

	
});
