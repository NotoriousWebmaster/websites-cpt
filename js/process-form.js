jQuery(document).ready( function($) {

	var submit = $('.websites-cpt-form__submit');

	$(submit).click(function(ev) {
		ev.preventDefault();

		// identify name and URL fields
		var name = ev.currentTarget.form.querySelector('.websites-cpt-form__name').value;
		var url = ev.currentTarget.form.querySelector('.websites-cpt-form__url').value;

		// call endpoint to create a new WEBSITES CPT post, and add the name as title and URL as metadata
		var data = {
			action: 'websites_cpt_create_post',
			name: name,
			url: url
		};

		var messageContainer = document.querySelector('#message-container');

		$.ajax({
			url: phpvars.ajaxurl,
			type: 'post',
			data: data,
			success: function(res) {
				res = JSON.parse(res);
				// response messages and clearing the form
				if (res.status === 'success') {
					messageContainer.innerHTML = '<div class="success">Successfully posted!</div>';
					document.getElementById("websites-cpt-form").reset();
				} else {
					var str = res.err.map(function(item) {
						return '<div class="error">' + item + '</div>';
					}).reduce(function(accum, item) { return accum + item }, '');
					messageContainer.innerHTML = str;
				}
			},
			error: function(jqXHR, status, error) {
				messageContainer.innerHTML = '<div class="error">ERROR: ' + status + ' - ' + error + '</div>';
			}
		});
	});

});
