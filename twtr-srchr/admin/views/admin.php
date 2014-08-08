<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Twtr_Srchr
 * @author    Mark Hayden <hi@markhayden.me>
 * @license   GPL-2.0+
 * @link      https://github.com/markhayden/twtr-srchr
 * @copyright 2014 Mark Hayden
 */
?>

<?
	if ( $_POST ) {
		$api_key = $_POST['api_key'];
		$api_secret = $_POST['api_secret'];
		$number_of_tweets_to_save = $_POST['twtr_number_of_tweets_to_save'];
		$post_types = $_POST['post_types'];
		$query_buffer = $_POST['query_buffer'];

		if ( $api_key !== '' && $api_secret !== '' && $number_of_tweets_to_save !== '' && $post_types !== '' && $query_buffer !== '' ){
			update_option( "twtr_api_key", $api_key );
			update_option( "twtr_api_secret", $api_secret );
			update_option( "twtr_number_of_tweets_to_save", $number_of_tweets_to_save );
			update_option( "twtr_post_types", $post_types );
			update_option( "twtr_query_buffer", $query_buffer );
			$saved = true;
		} else {
			$saved = false;
		}
	}
?>
<div class="wrap">

	<? if ( $saved === true ) { ?>
	<div class="the-day-is-mine">
		Well would you look at that. Everything saved successfully!
	</div>
	<? } ?>

	<? if ( $saved === false ) { ?>
	<div class="you-borked-the-internet">
		Well crap. Something borked. Try again maybe?
	</div>
	<? } ?>

	<form method="post" action="">
		<h2 class="twtr-h2"><?php echo esc_html( get_admin_page_title() ); ?></h2>

		<h3 class="twtr-h3">Granting Plugin Access to Twitter</h3>
		<p class="twtr-p">
			For the Twtr Srchr plugin to work properly you must grant it access to twitter's api via an application. To do this follow these steps to obtain an API key and API secret from twitter.
			<ol>
				<li>Visit: <a href="https://dev.twitter.com/" target="blank">Twitter Developer Center</a></li>
				<li>Log in with any twitter account. This will not grant access to any account information, you just have to be a twitter user to create an app.</li>
				<li>Visit: <a href="https://apps.twitter.com/" target="blank">Twitter App Center</a></li>
				<li>Click the "Create New App" button at the top right.</li>
				<li>Fill in the required fields. This information is not accessible from the plugin but is useful in debugging and managing the app. Callback URL can be left blank.</li>
				<li>On the following page you will see an overview of your app. Click the "API Keys" tab at the top.</li>
				<li>You will now see a list of access credentials including your API key and API secret. Copy and paste them into the fields below.</li>
			</ol>
		</p>
		<table class="twtr-table">
			<tr>
				<td class="twtr-right"><label for="api_key">API key: </label></td>
				<td><input name="api_key" type="text" value="<?php echo get_option( 'twtr_api_key' ); ?>"></td>
			</tr>
			<tr>
				<td class="twtr-right"><label for="api_secret">API secret: </label></td>
				<td><input name="api_secret" type="text" value="<?php echo get_option( 'twtr_api_secret' ); ?>"></td>
			</tr>
		</table>

		<h3 class="twtr-h3">Automated Search Settings</h3>
		<p class="twtr-p">For each query performed we can control how many tweets are returned. The smaller the number the quicker queries / processing is performed. Ideally this will be the amount of tweets you would like to display.</p>

		<br/>
		<table>
			<tr>
				<td class="twtr-right">How many tweets should we save for each query? </td>
				<td><input name="twtr_number_of_tweets_to_save" type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" value="<?php echo get_option( 'twtr_number_of_tweets_to_save' ); ?>"></td>
			</tr>
			<tr>
				<td class="twtr-right">Post types (separate with comma, no spaces): </td>
				<td><input name="post_types" type="text" value="<?php echo get_option( 'twtr_post_types' ); ?>"> <span>default: post,page</span></td>
			</tr>
			<tr>
				<td class="twtr-right">Pull in new tweets every </td>
				<td><input name="query_buffer" type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" value="<?php echo get_option( 'twtr_query_buffer' ); ?>"> <span> minutes</span></td>
			</tr>
		</table>

		<!-- <h3 class="twtr-h3">Setting Up An Automated Job (cron)</h3>
		<p class="twtr-p">
			Twitters built in automation functionality is slightly flawed for high traffic sites. As a result, the best
			way to perform the necessary actions after any given period of time is to create a "cron" job from your hosting
			platform. As CPANEL is the most common hosting platform for wordpress, I have included instructions for that
			below. If you are using something other than Apache / CPANEL simply google how to set up a cron job for further
			instruction.

			<ol>
				<li>Log into cPanel. ( generally <a href="<? echo 'https://'.$_SERVER['HTTP_HOST']; ?>:2082" target="blank"><? echo 'https://'.$_SERVER['HTTP_HOST']; ?>:2082</a> )</li>
				<li>In the Advanced section, click Cron Jobs.</li>
				<li>Under Cron Email, make sure the current email address is valid. If not, enter a new, valid email and click Update Email. You will receive an email after the cron job has finished.</li>
				<li>Under Add New Cron Job, use the Common Settings drop-down menu to choose from a list of regularly used intervals; or set the frequency of your cron job by using the drop-down box next to each time unit. Common settings range from every minute to once a year.</li>
				<li>In the Command field, enter the following command: <b class="red">php <? echo $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/twtr-aggregatr/fetch.php'; ?>>/dev/null 2>&1</b></li>
				<li>Click Add New Cron Job.</li>
			</ol>
		</p> -->

		<?php submit_button(); ?>

	</form>

	<div>
		<h2>Plugin Testing</h2>
		<p>Use the button below <span class="red">AFTER SAVING</span> to test that everything is working properly. The test should return a success message with a list of ids if everything is working.</p>
		<button id="twtrClickTest" class="button button-red">Perform Test</button>
		<p id="twtrTestOutpt">Running tests. Please wait...</p>
	</div>
</div>
