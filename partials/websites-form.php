<div id="message-container"></div>

<form name="websites-cpt" id="websites-cpt-form">
	<?php
		wp_nonce_field('adding-websites-cpt-post', 'websites-cpt-form-nonce');
	?>
	<label for="websites-cpt-form__name">Name:</label>
	<input type="text" class="websites-cpt-form__name" name="websites-cpt-form__name" value="" />

	<label for="websites-cpt-form__url">Website URL:</label>
	<input type="text" class="websites-cpt-form__url" name="websites-cpt-form__url" value="" />

	<input type="submit" class="websites-cpt-form__submit" name="websites-cpt-form__submit" value="Submit" />
</form>
