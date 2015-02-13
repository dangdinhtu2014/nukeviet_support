$(document).ready(function() {
	$(document).delegate('a[data-toggle=\'image\']', 'click', function(e) {
		e.preventDefault();	
		var element = this;
		var rel = $(this).attr('rel');	
		$(element).popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" onclick="select_image( \'input-image' + rel + '\' )" class="btn btn-primary "><i class="fa fa-pencil rmbutton" id="button-close"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});
	
		$(element).popover('toggle');		
 
		$('#button-close').on('click', function() {
			$(element).popover('hide');
		});
		$('#button-clear').on('click', function() {
			$(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));
			
			$(element).parent().find('input').attr('value', '');
	
			$(element).popover('hide');
		});
		
	});
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});
 
	//$('button[type=\'submit\']').on('click', function() {
	//		$("form[id*='form-']").submit();
	//});
 
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();
		
		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});
	
	$('.close').on('click', function() {
		$('.alert-danger').remove();
	});

});