<?php
/*
Plugin Name: Contact Form 7 - Phone mask field
Description: This plugin adds a new field in which you can set the phone number entry mask or other to Contact Form 7.
Version: 1.1
Author: Ruslan Heorhiiev
Text Domain: cf7-phone-mask-field
Domain Path: /assets/languages/

Copyright © 2017 Ruslan Heorhiiev
*/

if ( ! ABSPATH ) exit;

/**
 * Функция инициализации плагина
 * Function init plugin
**/
function wpcf7mf_init(){
	add_action( 'wpcf7_init', 'wpcf7mf_add_shortcode_mask' );
	add_action( 'wp_enqueue_scripts', 'wpcf7mf_do_enqueue_scripts' );
	add_filter( 'wpcf7_validate_mask*', 'wpcf7mf_mask_validation_filter', 10, 2 );
    
    // загрузка language файла & load language file.
    load_plugin_textdomain( 'contact-form-7-mask-field', false, dirname( plugin_dir_path( __FILE__ ) ) . '/assets/languages/' );
}
add_action( 'plugins_loaded', 'wpcf7mf_init' , 20 );

/**
 * Функция подключения JS
 * Function enqueu script
 * @version 1.0 
**/
function wpcf7mf_do_enqueue_scripts() {
    wp_enqueue_script( 'wpcf7mf-mask', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.maskedinput.min.js', array('jquery'), '1.4', true );    
}
add_action( 'wp_enqueue_scripts', 'wpcf7mf_do_enqueue_scripts' );

/**
 * Функция добавления поля маски в wpcf7
 * Function add mask field in wpcf7
 * @version 1.0
**/
function wpcf7mf_add_shortcode_mask() {
    if ( ! function_exists('wpcf7_add_form_tag') ) return;    
    
	wpcf7_add_form_tag(
		array( 'mask' , 'mask*' ),
		'wpcf7mf_mask_shortcode_handler', true );
}

/**
 * Функция добавления шорткодов с участием маски
 * Function add shortcodes with mask
 * @version 1.0
**/
function wpcf7mf_mask_shortcode_handler( $tag ) {
    if ( ! class_exists( 'WPCF7_FormTag' ) ) return;
    
	$tag = new WPCF7_FormTag( $tag );

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type, 'wpcf7mf-mask' );


	if ( $validation_error )
		$class .= ' wpcf7-not-valid';

	$atts = array();

	$atts['size'] = $tag->get_size_option( '40' );
	$atts['maxlength'] = $tag->get_maxlength_option();
	$atts['minlength'] = $tag->get_minlength_option();

	if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
		unset( $atts['maxlength'], $atts['minlength'] );
	}

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	if ( $tag->has_option( 'readonly' ) )
		$atts['readonly'] = 'readonly';

	if ( $tag->is_required() )
		$atts['aria-required'] = 'true';

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
    
    /** @var array $tag->values $values */
    $values = $tag->values;
    
    $mask = $values[0];    
    $value = $mask;
    
    if (1 < count($values)) {
        // если value значений больше одного предпологается, что необходимо разделать маску и placeholder поле        
        foreach ($values as $val) {
            if (!strrpos($val, '_' )) {
                continue;
            }
            
            $mask = $val;
            $value = end($values) !== $mask ? end($values) : $values[0];
            
            break;            
        }
    }
    
    $atts['placeholder'] = $value;    
    $atts['data-mask'] = $mask;    
    $atts['name'] = $tag->name;    
    $atts['value'] = '';                        	
    $atts['type'] = 'text';	
    
	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error );

	return $html;
}

/**
 * Функция проверка поля маски
 * Function check mask field
 * @version 1.0
**/

function wpcf7mf_mask_validation_filter( $result, $tag ) {
    if ( ! class_exists( 'WPCF7_FormTag' ) ) return;
    
	$tag = new WPCF7_FormTag( $tag );

	$name = $tag->name;

	$value = isset( $_POST[$name] )
		? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
		: '';

	if ( 'mask' == $tag->basetype ) {
		if ( $tag->is_required() && '' == $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}
	}

	if ( ! empty( $value ) ) {
		$maxlength = $tag->get_maxlength_option();
		$minlength = $tag->get_minlength_option();

		if ( $maxlength && $minlength && $maxlength < $minlength ) {
			$maxlength = $minlength = null;
		}

		$code_units = wpcf7_count_code_units( $value );

		if ( false !== $code_units ) {
			if ( $maxlength && $maxlength < $code_units ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_long' ) );
			} elseif ( $minlength && $code_units < $minlength ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_short' ) );
			}
		}
	}

	return $result;
}


if ( is_admin() ) {
	add_action( 'wpcf7_admin_init' , 'wpcf7mf_add_tag_generator_field' , 100 );
}

/**
 * Функция вызова генератора тегов WPCF7
 * Function cell Tag GeneratorWPCF7
 * @version 1.0
**/
function wpcf7mf_add_tag_generator_field() {

	if ( ! class_exists( 'WPCF7_TagGenerator' ) ) return;

	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 
        'mask', 
        __( 'mask field', 'contact-form-7-mask-field' ),
		'wpcf7mf_tag_generator_field' 
    );
}


/**
 * Функция генерирования поля
 * Function generating new field
 * @version 1.0
**/
function wpcf7mf_tag_generator_field( $contact_form , $args = '' ){
	$args = wp_parse_args( $args, array() );
	$type = $args['id'];

?>
<div class="control-box">
    <fieldset>
    
        <legend><?php echo esc_html( __( 'Generate a form-tag for a single-line plain text input field in which you can set the input mask.', 'contact-form-7-mask-field' ) ); ?></legend>
        
        <table class="form-table">
        <tbody>
        	<tr>
        	   <th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
            	<td>
            		<fieldset>
            		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
            		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
            		</fieldset>
            	</td>
        	</tr>
        
        	<tr>
            	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
            	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
        	</tr>
            <!-- msk field -->
        	<tr>
            	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Mask', 'contact-form-7-mask-field' ) ); ?></label></th>
            	<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br />
            	<?php _e( 'Enter the mask for this field. <br /><code>Example: +1 (___) ___-__-__</code>', 'contact-form-7-mask-field' ); ?></td>
        	</tr>
        
        	<tr>
            	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
            	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
        	</tr>
        
        	<tr>
            	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
            	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
        	</tr>                       
            
        </tbody>
        </table>
    
    </fieldset>
</div>

<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	   <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>

	<br class="clear" />
</div>
<?php
}