jQuery(document).ready(function($) {
	
	function fields_name_reset(){
		$('.multi-fields .multi-field').each(function(index, value){	
			$(this).find('input').attr('name','af_option[field_whitelist' + (++index) + ']');
		});
	}
	
	$('.multi-field-wrapper').each(function() {
		
		var $wrapper = $('.multi-fields', this);
		
		$(".add-field", $(this)).click(function(e) {
				$('.multi-field:first-child', $wrapper).clone(true).appendTo($wrapper).find('input').val('').focus();
				fields_name_reset();
		});
		
		$('.multi-field .remove-field', $wrapper).click(function() {
			if ($('.multi-field', $wrapper).length > 1)
            $(this).parent('.multi-field').remove();
			fields_name_reset();
		});
		
	});

});
