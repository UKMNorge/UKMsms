jQuery(document).on('click', '#SMS_send', function(e) { validateLength(e) });
jQuery(document).ready(function(){
	// ÅPNE CREDITS-LOGG
	jQuery('#credits_log').click(function(){
		container = '#credits > #log';
		loading(container, 'logg');
		jQuery.post(ajaxurl, 'action=UKMSMS_ajax&SMSaction=log', 
			function(response){
				response = response + '<a href="#" id="lukkloggnedetilhoyre" style="float: right;">lukk logg</a><div class="clear"></div>';
				loaded(container, response);
				jQuery('li.trans:even').addClass('odd');
				jQuery('#lukkloggnedetilhoyre').click(function(){
					jQuery('#credits_log_hide').hide();
					jQuery('#credits_log').show();
					jQuery('#credits > #log').slideUp();
				});
			}
		);
		jQuery(this).hide();
		jQuery('#credits_log_hide').show();
	});
	
	jQuery('#credits_log_hide').click(function(){
		jQuery(this).hide();
		jQuery('#credits > #log').hide();
		jQuery('#credits_log').show();
	})
	
	jQuery('#selected_sender').change(function(){
		preview_message();
		jQuery('#iphone_preview > #sender').effect('shake',{times:2},50);
	});
	jQuery('#the_message').keyup(function(){preview_message()});
	jQuery('#the_message').keydown(function(){preview_message()});
	jQuery('#the_message').change(function(){preview_message()});
	
	preview_message();

	jQuery('#SMSform input[type="text"]').keypress(function(e){
		if(e.which == 13) return false;
	});

	var options = {
		'maxCharacterSize': -2,
		'originalStyle': 'originalTextareaInfo',
		'warningStyle' : 'warningTextareaInfo',
		'warningNumber': 40,
		'displayFormat': '#input tegn | #sms sms | #mottakere unike mottakere | totalt kr #kr' 
	};
	jQuery('#the_message').textareaCount(options);
	
	jQuery('.logwarningread').click(function(){
		jQuery('#logwarning_more').slideToggle();
		jQuery('#logwarningreadmorelink').slideToggle();
	});
});
function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
function preview_message(){
		svar = jQuery('#selected_sender').find('option:selected');
		jQuery('#iphone_preview > #sender').html(svar.val());
		noreply = '<br>(denne SMS kan ikke besvares)';
		if(svar.attr('data-svar')=='false') {
			jQuery('#sender > #obs').html('<strong>MERK:</strong> Mottakeren kan ikke svare hvis du bruker denne avsenderen');
			jQuery('#iphone_preview > #reply').html('');//'Svar ikke mulig');
			jQuery('#message_from_value').val(jQuery('#message_really_from').val() + noreply);
		} else {
			jQuery('#sender > #obs').html('<strong>MERK:</strong> Eventuelle svar vil bli sendt til '+svar.attr('data-name')+'s mobiltelefon');
			jQuery('#iphone_preview > #reply').html('Svar går til ' + svar.val());
			jQuery('#message_from_value').val(jQuery('#message_really_from').val());
		}

		jQuery('#message_from').html(jQuery('#message_from_value').val());
		
		length = jQuery('#the_message').val().length + jQuery('#message_from').html().length ;
		if( length > 612 ) {
			jQuery('#iphone_preview > #message').html('<h3>!! ERROR !!</h3>SMS kan maks være 612 tegn lang, altså 4 SMS');	
		} else {
			jQuery('#iphone_preview > #message').html(nl2br(jQuery('#the_message').val() + '<br />'+jQuery('#message_from').html()));	
		}
}

function loaded(id, response){
	jQuery(id).html(response);
}
function loading(id, loading) {
	jQuery(id).show();
	jQuery(id).html('<span class="loading">'
					+  '<img src="/UKM/ico/loading.gif" width="32" />'
					+  '<div>Vennligst vent, laster inn '+loading+'...</div>'
					+  '</span>');	
}
function validateLength(e) {
	length = jQuery('#the_message').val().length + jQuery('#message_from').html().length ;
	if( length > 612 ) {
		alert('SMS kan maks være 612 tegn lang, altså 4 SMS');
		e.preventDefault();
		return false;
	}
}