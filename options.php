<?php
/*
Author: or p
Author URI: http://www.outbrain.com
Description: Administrative options for Outbrain plugin.
*/

$location = '?page=outbrain/options.php'; // Form Action URI
$maxPages = 4;

/*
option: outbrain_pages_list
pages list
0: is_home (home page)
1: is_single (single post)
2: is_page (page)
3: is_archive (some archive. Category, Author, Date based and Tag pages are all types of Archives)
*/

if (isset($_POST['outbrain_send'])){
	// form sent
	$value = (isset($_POST['lang_path'])? $_POST['lang_path'] : (isset($_POST['your_translation_path'])? $_POST['your_translation_path'] : ''));
	if ($value != ''){
		update_option("outbrain_lang",$value);
	}
	
	$selected_pages = (isset($_POST['select_pages']))? $_POST['select_pages']: array();
	
	//die ($selected_pages);
	
	update_option("outbrain_pages_list",$selected_pages);

	?>
	<div id="message" class="updated fade">
		<p>
			<strong><?php _e('Options saved.'); ?></strong>
		</p>
	</div>
<?php
} else {
	$selected_pages = (isset($_POST['select_pages']))? $_POST['select_pages']: get_option("outbrain_pages_list");
}
?>

<div class="wrap" style="text-align:left;direction:ltr;">
	<h2><?php _e('Outbrain options', 'outbrain') ?></h2>
	<form method="post" id="outbrain_form" name="outbrain_form" action="<?php echo $location; ?>">
		<?php wp_nonce_field('update-options') ?>
		<input type="hidden" name="outbrain_send" value="send" />
		<fieldset class="options">
			<legend>language file</legend>
			Select a language:
			<span style="margin-left:10px;">&nbsp;</span>
			<select name="lang_path" id="langs_list" onchange="outbrain_changeLang(language_list[this.selectedIndex])" onkeyup="outbrain_changeLang(language_list[this.selectedIndex])">
				<?php //JS print here the options ?>
			</select>
			<span style="margin-left:40px;">&nbsp;</span>
			<div id='translator_div'></div>
			<div style="clear:both;">
				<a href='http://www.outbrain.com/new/pages/add_translation.html'>Can't find your language here?</a>
			</div>
		</fieldset>
		<fieldset class="options">
			<legend>pages</legend>
			<?php
				$select_page_texts = array('home page','single post','page','archive (category page, author page, date page and also tag page in WP 2.3+)');
				for ($i=0;$i<$maxPages;$i++){
					$checked = '';
					if (in_array($i,$selected_pages)){
						$checked = " checked='checked' ";
					}
				?>
					<div class="one_option"><label><input type="checkbox" name="select_pages[]" <?php echo $checked; ?> value="<?php echo $i; ?>"> <?php echo $select_page_texts[$i]; ?> </label></div>
				<?php
				}
				?>
		</fieldset>
		<div id="getWidget" style="text-align:center;width:500px;margin:auto;border:1px solid red;padding:10px;">
			<a href="widgets.php">get outbrain linkroll widget - click here and add the widget</a>
		</div>
		<p class="submit options" style="text-align:left;">
			<input type="submit" name="Submit" value="<?php _e('Update Options »') ?>" />
		</p>
	</form>
</div>