<?php
/*
Plugin Name: outbrain
Plugin URI: http://wordpress.org/extend/plugins/outbrain/
Description: A WordPress plugin to deal with the <a href="http://www.outbrain.com">Outbrain</a> blog posting rating system.
Author: outbrain
Version: 1.0.0.0
Author URI: http://www.outbrain.com
*/

// consts
$outbrain_start_comment = "//OBSTART:do_NOT_remove_this_comment";
$outbrain_end_comment = "//OBEND:do_NOT_remove_this_comment";


// add admin options page
function outbrain_add_options_page(){
	add_options_page('Outbrain Options', 'Outbrain Options', 8, 'outbrain/options.php', 'outbrain_admin_script');
}

// display the plugin
function outbrain_display ($content)
{
	global $post_ID, $outbrain_start_comment, $outbrain_end_comment;
	
	$where = array();
	$fromDB = get_option("outbrain_pages_list");
	if ((isset($fromDB)) && (is_array($fromDB))){
			$where = $fromDB;
	}
	
	if
	(
		(!(is_feed())) &&
		(
			((is_home()) && (in_array(0,$where))) || 
			((is_single()) && (in_array(1,$where))) || 
			((is_page()) && (in_array(2,$where))) || 
			((is_archive()) && (in_array(3,$where)))
		)
	)
	{
		$content .= '<SCRIPT LANGUAGE=\'JavaScript\'>
		' . $outbrain_start_comment . '
		<!--
		var OutbrainPermaLink="' . get_permalink( $post_ID ) . '";
		var OB_demoMode = false;
		if(typeof(OB_Script)!=\'undefined\'){
			OutbrainStart();
		}else{
			var OB_Script = true;
			var OB_langJS ="' . get_option("outbrain_lang") . '";
			document.write ("<script src=\'http://widgets.outbrain.com/OutbrainRater.js\' type=\'text/javascript\'><\/script>"); 
		}
		//-->
		' . $outbrain_end_comment . '
		</SCRIPT>
		';
	}
	return $content;
}

// change the plugin on the_excerpt call
function outbrain_display_excerpt($content){
	global $outbrain_start_comment,$outbrain_end_comment;
	$pos = strpos($content,$outbrain_start_comment);
	$posEnd = strpos($content,$outbrain_end_comment);
	if ($pos){
		if ($posEnd == false){
			$content = str_replace(substr($content,$pos,strlen($content)),'',$content);
		} else {
			$content = str_replace(substr($content,$pos,$posEnd-$pos+strlen($outbrain_end_comment)),'',$content);
		}
	}
	$content = $content . outbrain_display('');
	return $content;
}

// print the css / js functions of the options page
function outbrain_admin_script(){

	if ((strpos($_SERVER['QUERY_STRING'],'outbrain') == false) || (strpos($_SERVER['QUERY_STRING'],'options') == false)){
		// no outbrain's options page.
		return;
	}
	
	$src = 'http://widgets.outbrain.com/language_list.js';
?>

	<style type="text/css">
	.outbrain_or_div{
		margin-top:10px;
		margin-bottom:10px;
	}

	.hiddenDiv{
		margin-top:-10px;
	}

	.hiddenDiv input{
		margin-top:-10px;
	}
	
	.one_option{
		margin-bottom:5px;
	}
	</style>
	<script language="javascript" type="text/javascript" src="<?php echo $src; ?>"></script>
	<script language="javascript" type="text/javascript">
		var langs_div = "langs_list";
		var user_lang_div = "user_lang_div";
		var translator_div = "translator_div";
		var current;
		
		function outbrain_$(id){ return document.getElementById(id); }
		
		function outbrain_changeLang( langInfo ){
			var name = langInfo[0];
			var path = langInfo[1];
			var translator = unescape(langInfo[2]);
			
			if (translator != ''){
				translator = "translator/s: " + translator;
				outbrain_$(translator_div).innerHTML = translator;
				outbrain_$(translator_div).style.display="inline";
			}else{
				outbrain_$(translator_div).style.display="none";
			}
		}
		
		onload = function(){
			var langInfo = null;
			var con = '';
			var selectedIdx = -1;
			var defaultIdx = 0;
			
		<?php if (isset($_POST['lang_path'])){ ?>
				current = "<?php echo $_POST['lang_path']?>";
		<?php } else { ?>
				current = "<?php echo get_option('outbrain_lang')?>";
		<?php } ?>
			
			var langSelect =  outbrain_$('langs_list');
			for (i=0;i<language_list.length;i++){
				var option = document.createElement('option');
				option.value = language_list[i][1];
				option.text = language_list[i][0];
				try{
			    	langSelect.add( option , null );// standards compliant
			    }catch(ex){
				    langSelect.add( option ); // IE only
			    }
				
				if (current == language_list[i][1]){
					selectedIdx = i;
				}
				
				if( language_list[i][3] == true ){
					defaultIdx = i;
				}
			}			
			
			if( selectedIdx == -1 ){
				selectedIdx = defaultIdx;
			}
			
			langSelect.options[selectedIdx].selected = true;
			langInfo = language_list[selectedIdx];
			
			outbrain_changeLang( langInfo );
		}
	</script>

<?php
}

//--------------------------------------------------------------------------------------------------------
//	linkroll widget
//--------------------------------------------------------------------------------------------------------
$outbrain_widget_dbdatafieldname = 'outbrain_linkroll_data';

function outbrain_linkroll_widget_control(){
	global $outbrain_widget_dbdatafieldname;
	$curr_options = $new_options = get_option($outbrain_widget_dbdatafieldname);
	
	if ( $_POST["outbrain_widget_sent"] ) {
		$new_options['title'] = trim(strip_tags(stripslashes($_POST["outbrain_widget_title"])));
		$new_options['postsCount'] = $_POST["outbrain_widget_postsCount"];
		if (!is_numeric($new_options['postsCount'])){
			$new_options['postsCount'] = $curr_options['postsCount'];
		}
	}
	
	if ( $curr_options != $new_options ) {
		$curr_options = $new_options;
		update_option($outbrain_widget_dbdatafieldname, $curr_options);
	}
	?>
		<input type="hidden" name="outbrain_widget_sent" value="1" />
		<div style="width:100%;margin-bottom:15px;">
			<label name="outbrain_widget_title">title</label>
			<div style="margin-left:15px;">
				<input type="text" name="outbrain_widget_title"	value="<?php	echo $curr_options['title'];	?>" />
			</div>
		</div>
		<div style="width:100%;margin-bottom:15px;">
			<label name="outbrain_widget_postsCount">how many posts to display</label>
			<div style="margin-left:15px;">
				<select name="outbrain_widget_postsCount">
					<?php
						for ($i=1;$i<=10;$i++){
							if ($curr_options['postsCount'] == $i){
								echo "<option value='$i' selected='selected'>$i</option>";
							} else {
								echo "<option value='$i'>$i</option>";
							}
						}
					?>
				</select>
				
				<!-- input type="text" name="outbrain_widget_postsCount" value="<?php	echo $curr_options['postsCount'];	?>" / -->
			</div>
		</div>
	<?php
}

function outbrain_linkroll_widget($args) {

	global $outbrain_widget_dbdatafieldname;
	$options = get_option($outbrain_widget_dbdatafieldname);
	$title = $options['title'];
	$count = $options['postsCount'];
	
	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ) return;
	
    extract($args);
	
	$text .= '';
	
	$text	.=	$before_widget
			.	$before_title
			.	$title
			.	$after_title
			.	'<script type="text/javascript">'
			.	"\r\n"
			.	'var OB_hideTitle = true; // hide the widget\'s title from js. we have it from wordpress'
			.	"\r\n";

	if (is_numeric($count)){
	$text	.=	''
			.	'var OB_itemsCount =' . $count . ';'
			.	"\r\n";
	}

	$text	.=	''
			.	'var OB_langJS ="' . get_option("outbrain_lang") . '";'
			.	"\r\n"
			.	'</script>'
			.	'<script type="text/javascript" src="http://www.outbrain.com/plugins/widgets/linkroll.V2/outbrainLinkroll.js"></script>'
			.	$after_widget;

	echo $text;
}

function outbrain_linkroll_widget_init(){
	global $outbrain_widget_dbdatafieldname;
	$defaults = array();
	$defaults['title'] = 'Most popular posts';
	$defaults['postsCount'] = 3;
	
	add_option($outbrain_widget_dbdatafieldname, $defaults); 

	register_sidebar_widget('outbrain linkroll','outbrain_linkroll_widget');
	register_widget_control('outbrain linkroll', 'outbrain_linkroll_widget_control', 250, 150);
}

add_action('plugins_loaded', 'outbrain_linkroll_widget_init');
// add filters 
add_filter('the_content', 'outbrain_display');
add_filter('the_excerpt', 'outbrain_display_excerpt');
add_filter('admin_head', 'outbrain_add_options_page');
add_filter('admin_head', 'outbrain_admin_script');
add_option('outbrain_pages_list',array(0,1,2,3));
?>