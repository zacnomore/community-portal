<?php 
	include("{$theme_directory}/countries.php");
	$languages = array(
		'de' => 'Deutsch',
		'fr' => 'Français',
		'en' => 'English',
		'es' => 'Español',
		'pt' => 'Português do Brasil',
		'ru' => 'русский язык',
	);
	$user = wp_get_current_user();
	$user_meta = get_user_meta($user->ID, 'community-meta-fields', true);
	$user_language = is_array($user_meta) && sizeof($user_meta) > 0 && isset($user_meta['languages']) && sizeof($user_meta['languages']) > 0 ? $user_meta['languages'][0] : '';
	$user_country = is_array($user_meta) && sizeof($user_meta) > 0 && isset($user_meta['country']) ? $user_meta['country'] : '';
?>

<div class="content newsletter__container">
	<div class="row">
		<div class="col-lg-6 newsletter__details">
			<h2>Get Updates</h2>
			<p class="newsletter__text">Subscribe to our newsletter and join Mozillians all around the world and learn about impactful opportunities to support Mozilla’s mission.</p>
		</div>
		<div class="col-lg-6 newsletter__signup">
			<form id="newsletter_form" name="newsletter__form" action="https://www.mozilla.org/en-US/newsletter/" method="post" class="newsletter__form" novalidate>
				<input type="hidden" id="fmt" name="fmt" value="H">
				<input type="hidden" id="newsletters" name="newsletters" value="about-mozilla">
				<div id="newsletter_email" class="newsletter__fields">
					<label class="newsletter__label" for="newsletter-email">Email</label>
					<input class="newsletter__input" aria-label="Enter your e-mail" aria-required="true" type="email" id="newsletter-email" name="email" required="" value="<?php echo (isset($user->user_email) && strlen($user->user_email) > 0 ? $user->user_email : '')?>">
					<p class="newsletter__error">Invalid email.</p>
				</div>
				<div id="newsletter_details">
					<div class="newsletter__fields">
						<label class="newsletter__label" for="newsletter-country">Country or Region</label>
						<select class="newsletter__dropdown" id="newsletter-country" name="newsletter-country">
							<option value="" disabled="" selected="">Select country or region</option>
							<?php foreach($countries as $index=>$country): ?>
								<option value="<?php echo strtolower($index) ?>" <?php echo ($user_country === $index ? 'selected' : '') ?>><?php echo $country ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="newsletter__fields">
						<label class="newsletter__label" for="newsletter-language">Language</label>
						<select id="newsletter-language" class="newsletter__dropdown" name="newsletter-language">
							<option value="" disabled="" selected="">Select language</option>
							<?php foreach($languages as $index=>$language): ?>
								<option value="<?php echo $index ?>" <?php echo (strtoupper($user_language) === strtoupper($index) ? 'selected' : '') ?>><?php echo $language ?></option>
							<?php endforeach; ?>
						</select>
						<p class="newsletter__subtext">We’ll default to English but send in these languages whenever we can.</p>
					</div>
				</div>
				<div id="newsletter_privacy" class="newsletter__cpg cpg">
					<input class="checkbox--hidden" type="checkbox" id="privacy" required>
					<label class="cpg__label" for="privacy">
						<?php echo __('I\'m okay with Mozilla handling my info as explained in this') ?>
						<a target="_blank" class="newsletter__link" rel="noopener noreferrer" href="https://www.mozilla.org/privacy/websites/"><?php echo __('Privacy Policy') ?></a>.
					</label>
					<p class="newsletter__cpg__error">You must agree to the privacy notice.</p>
				</div>
				<div class="newsletter__submit">
					<input class="btn btn--dark btn--submit" type="submit" value="Subscribe">
			</div>
			</form>
			<div class="newsletter__success">
				<h2>Thanks!</h2>
				<p class="newsletter__text">If you haven’t previously confirmed a subscription to a Mozilla-related newsletter you may have to do so. Please check your inbox and spam filter for an email from us.</p>
			</div>
			<div class="newsletter__failure">
				<h2>Sorry!</h2>
				<p class="newsletter__text">Looks like something went wrong.</p>
			</div>
		</div>
	</div>
</div>