<?php
/**
 *	Custom Fields
 *
 * @package   	Custom Fields Class field - Textarea
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

class CustomField_Textarea extends CustomField {

	public $cols = 20;
	public $rows = 8;

	/**
	 * Return the field markup for the front-end.
	 *
	 * @return string Field markup
	 */
	public function display() {

		$cols = isset( $this->field_args['cols'] ) ? (int) $this->field_args['cols'] : $this->cols;
		$rows = isset( $this->field_args['rows'] ) ? (int) $this->field_args['rows'] : $this->rows;

		return sprintf( '<label {{label_atts}}>{{label}}</label><textarea cols="%d" rows="%d" {{atts}}>%s</textarea>', $cols, $rows, $this->populate() );

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