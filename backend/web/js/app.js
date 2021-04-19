$(document).on('click', '#more-apples', function(){
	$.get('/site/get-apples', function(data) {
		if (data.result == 'ok') {
			document.location.reload();
		} else {
			Swal.fire(data.message);
		}
	});
});

$(document).on('click', '.apple-item .actions [data-action]', function(e){
	e.preventDefault();
	var item = $(this).closest('.apple-item');
	var params = {
		id: item.data('key'),
		action: $(this).data('action')
	};

	if (typeof $(this).data('value') != 'undefined') {
		params['value'] = $(this).data('value');
	}

	$.post('/site/apple', params, function(data) {
		if (data.result == 'error') {
			Swal.fire(data.message);
			return;
		}
			
		if (typeof data.content != 'undefined') {
			item.html(data.content);
		} else if (typeof data.refresh != 'undefined') {
			document.location.reload();
		}
	});
});