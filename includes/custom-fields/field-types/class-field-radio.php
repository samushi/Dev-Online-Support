<?php
/**
 *	Custom Fields
 *
 * @package   	Custom Fields Class field - Radio
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

class CustomField_Radio extends CustomField {

	public $options = array();

	/**
	 * Return the field markup for the front-end.
	 *
	 * @return string Field markup
	 */
	public function display() {

		if ( ! isset( $this->field_args['options'] ) || empty( $this->field_args['options'] ) ) {
			return '<!-- No options declared -->';
		}

		$output        = '<legend class="dev-label-radio">{{label}}</legend>';
		$this->options = $this->field_args['options'];

		foreach ( $this->options as $option_id => $option_label ) {
			$selected = $option_id === $this->populate() ? 'checked' : '';
			$output .= sprintf( "<div class='dev-radio'><label><input type='radio' name='%s' value='%s' %s> %s</label></div>", $this->get_field_id(), $option_id, $selected, $option_label );
		}

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

}