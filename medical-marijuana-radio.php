<?php
/*
Plugin Name: Medical Marijuana Radio by Time4Hemp
Plugin URI: https://wordpress.org/plugins/medical-marijuana-radio-by-time4hemp
Description: An easy way to embed 24/7 medical marijuana and hemp radio from the Time4Hemp Global Broadcasting Network into your WordPress website. A simple example: "[medical-marijuana-radio]". More options in the plugin page.
Author: Medical Marijuana Radio by Time4Hemp
Version: 1.0
Author URI: http://www.weedbiz.us
*/

function medical_marijuana_radio( $atts ) {
    $default_width = '100%';
    $debug_output = '';

    //ACCEPTED OPTIONS & DEFAULT VALUES
    extract( shortcode_atts( array(
        'type' => 'mini',
        'width' => $default_width,
        'autoplay' => 'false',
        'color' => null,
         'debug' => false,
        'coupon_code'=>null,
        'coupon_sid'=>null
    ), $atts ) );

    $content = 'episode_id=6132798';
    
    //CHECK TYPE
    if ($type!='mini' && $type!='standard') {
        $debug_output .= '   '. __('The dimensions of the player are incorrect and are different from those of the mini and standard players.', 'medical_marijuana_radio') . "\n";
        $type='mini';
    }

    $height=($type=="mini")?'71px':'131px';
    $min_width=($type=="mini")?200:250;

    //ACCEPT VALUE FOR WIDTH WITHOUT PX
    if (!is_numeric(str_replace("%","", str_replace("px", "", $width)))) {
        $width = $default_width;
        $debug_output .= '   '. __('The width does not correspond to a number.', 'medical_marijuana_radio') . "\n";
    }

    //ACCEPT VALUE FOR WIDTH WITHOUT PX
    if (strrpos($width, '%')===false){
        if (strrpos($width, 'px')===false) {
            $width= $width.'px';
        }

        if ((int) $width < $min_width){
            $debug_output .= '   '. __('The width is lower than the minimum value allowed.', 'medical_marijuana_radio') . "\n";
        }
    }

    //FORMAT THE COLOR (accepted #ffffff, #f00, f00, ff0000)
    if ($color && strlen($color)!=6){
        $color = str_replace("#", "", $color);
        if (strlen($color)==3){
            $right_color = str_repeat(substr($color, 0, 1), 2);
            $right_color .= str_repeat(substr($color, 1, 1), 2);
            $right_color .= str_repeat(substr($color, 2, 1), 2);
            $color = $right_color;
        } else if (strlen($color)!=6) {
            $color=null;
            $debug_output .= '   '. __('The color of the player is incorrect.', 'medical_marijuana_radio') . "\n";
        }
    }

    if ($autoplay!='true'){
        if ($autoplay!='false'){
            $debug_output .= '   '. __('The autoplay is not set on true or false.', 'medical_marijuana_radio') . "\n";
        }
        $autoplay='false';
    }

        ob_start();
	?>
<div class="mmj-radio">
<h3>Medical Marijuana Radio</h3>
<?php
echo '<img src="' . plugins_url( 'images/listen-widget.jpg', __FILE__ ) . '" > ';
?>
<?php
	$html = ob_get_clean();

	//GENERATE THE IFRAME CODE
    $html .= '<iframe src="http://www.spreaker.com/embed/player/';
    $html .= $type . '?autoplay='. $autoplay . '&';
    $html .= $content . (($color)?'&color='.$color:'');
    if ($coupon_code){
        $html .= '&coupon_code=' . $coupon_code;
        if ($coupon_sid){
            $html .= '&coupon_sid=' . $coupon_sid;
        }
    }
	$html .= '" style="width:'.$width.'; height:'.$height.'; min-width:'.$min_width.'px';
    $html .= '" frameborder="0" scrolling="no"></iframe></div>';

    return (($debug=='true')?"<pre style='width:auto'>Medical Marijuana Radio Debug Info:\n$debug_output</pre>":'') . $html;
}



add_shortcode('medical-marijuana-radio', 'medical_marijuana_radio');


//register stylesheet for plugin
function medical_marijuana_radio_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'mmr_css', $plugin_url . 'style.css' );
}
add_action( 'wp_enqueue_scripts', 'medical_marijuana_radio_css' );


//options page for user defined stylesheet

add_action('admin_menu', 'medical_marijuana_radio_settings');
 
function medical_marijuana_radio_settings() {
 
    add_menu_page('MMJ Radio', 'MMJ Radio', 'administrator', 'mmr_settings', 'mmr_display_settings');
}

function mmr_display_settings() {
 
    $user_css = (get_option('user_css') != '') ? get_option('user_css') : '';
 
 
    $html = '</pre>
<div class="wrap">
<div style="background: #efefef;padding: 10px 25px;border: 3px solid #333;">
<h2>How to use this plugin</h2>
<p>To quickly include the Time4Hemp Global Broadcasting Network Spreaker app in your sidebar simply use this shortcode in a text widget: <strong>`[medical-marijuana-radio]`</strong></p>
<p>To have the audio play automatically when the page loads: <strong>`[medical-marijuana-radio autoplay=true]`</strong></p>
<h3>More Options</h3>
<ul>
<li>type: `standard` or `mini` (defaults to `mini`)</li>
<li>autoplay: if `true` the player will automatically start playing (defaults to `false`)</li>
<li>debug: if `true` will print some debug information, which is useful if the player renders differently than expected (defaults to `false`)</li>
</ul>
</div>
<form action="options.php" method="post" name="options">
<h2>Custom CSS</h2>
' . wp_nonce_field('update-options') . '
<table class="form-table" width="100%" cellpadding="10">
<tbody>
<tr>
<td scope="row" align="left">
 <label>Add CSS Rules for Medical Marijuana Radio Widget</label><br><textarea rows="10" cols="50" name="user_css">'.$user_css.'</textarea></td>
</tr>
</tbody>
</table>
 <input type="hidden" name="action" value="update" />
 
 <input type="hidden" name="page_options" value="user_css" />
 
 <input type="submit" name="Submit" value="Update" /></form></div>
<pre>
';
 
    echo $html;
 
}


//apply user defined css
add_action('wp_head', 'mmr_user_defined_css');
function mmr_user_defined_css(){
	?>
	<style>
     <?php echo get_option('user_css'); ?> 
</style>
	<?php
	}
?>
