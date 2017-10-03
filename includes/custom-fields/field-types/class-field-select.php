<?php
/**
 *	Custom Fields
 *
 * @package   	Custom Fields Class field - Select Box
 * @author    	Sami Maxhuni <samimaxhuni510@gmail.com>
 * @license   	GPL-2.0+
 * @link      	http://devsolution.info
 * @copyright 	2017 Sami Maxhuni
 **/

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

namespace DevSupport\Fields\Field;

class CustomField_Select extends CustomField {

	public $options = array();

	public function __construct( $field_id, $field ) {

		/* Call the parent constructor */
		parent::__construct( $field_id, $field );

		/* Set the additional parameters */
		if ( ! isset( $this->field_args['multiple'] ) ) {
			$this->field_args['multiple'] = false;
		}

		/* Change the field name if multiple upload is enabled */
		if ( true === $this->field_args['multiple'] ) {
			add_filter( 'dev_cf_field_atts', array( $this, 'edit_field_atts' ), 10, 3 );
		}

	}

	/**
	 * Return the field markup for the front-end.
	 *
	 * @return string Field markup
	 */
	public function display() {

		if ( ! isset( $this->field_args['options'] ) || empty( $this->field_args['options'] ) ) {
			return '<!-- No options declared -->';
		}

		$multiple      = true === (bool) $this->field_args['multiple'] ? 'multiple' : '';
		$output        = sprintf( '<label {{label_atts}}>{{label}}</label><select {{atts}} %s>', $multiple );
		$this->options = $this->field_args['options'];
		$value         = array_filter( (array) $this->populate() );

		foreach ( $this->options as $option_id => $option_label ) {
			$selected = in_array( $option_id, $value ) ? 'selected' : '';
			$output .= sprintf( "<option value='%s' %s>%s</option>", $option_id, $selected, $option_label );
		}

		/* Close the select */
		$output .= '</select>';

		return $output;

	}

	/**
	 * Return the field markup for the admin.
	 *
	 * This method is only used if the current user
	 * has the capability to edit the field.
	 */
	public function display_admin() {
		return $this->display();
	}

	/**
	 * Return the field markup for the admin.
	 *
	 * This method is only used if the current user
	 * doesn't have the capability to edit the field.
	 */
	public function display_no_edit() {
		return sprintf( '<div class="dev-cf-noedit-wrapper"><div id="%s-label" class="dev-cf-label">%s</div><div id="%s-value" class="dev-cf-value">%s</div></div>', $this->get_field_id(), $this->get_field_title(), $this->get_field_id(), $this->get_field_value() );
	}

	/**
	 * Add the brackets to field name if multiple select is enabled
	 *
	 * @since  1.0.0
	 *
	 * @param array  $atts   Field attributes
	 * @param string $field  Field markup
	 * @param array  $option The custom field attributes
	 *
	 * @return mixed
	 */
	public function edit_field_atts( $atts, $field, $option ) {

		if ( $option['name'] !== $this->field_id ) {
			return $atts;
		}

		if ( false === $this->field_args['multiple'] ) {
			return $atts;
		}

		foreach ( $atts as $key => $att ) {
			if ( 'name' === substr( $att, 0, 4 ) ) {
				$att = substr( $att, 0, - 1 ); // Get rid of the last char (closing backtick)
				$att .= '[]\''; // Add the brackets for handling multiple files
				$atts[ $key ] = $att; // Update the array of attributes
			}
		}

		return $atts;

	}

}