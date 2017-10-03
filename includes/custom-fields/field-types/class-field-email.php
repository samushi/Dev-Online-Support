<?php
/**
 *	Custom Fields
 *
 * @package   	Custom Fields Class field - Email
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

class CustomField_Email extends CustomField {

	/**
	 * Return the field markup for the front-end.
	 *
	 * @return string Field markup
	 */
	public function display() {
		return sprintf( '<label {{label_atts}}>{{label}}</label><input type="email" value="%s" {{atts}}>', $this->populate() );
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