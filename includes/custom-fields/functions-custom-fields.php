<?php
/**
 * Submission Form Functions.
 *
 * This file contains all the functions related to the ticket submission form.
 * Those functions are being used on the front-end only and aren't used anywhere
 * else than the submission form.
 */

/**
 * Custom callback for updating terms count.
 *
 * The function is based on the original WordPress function
 * _update_post_term_count but adapted to work with the plugin
 * custom status.
 *
 * @since  1.0.0
 * @param  array  $terms    List of terms attached to the post
 * @param  object $taxonomy Taxonomy of update
 * @return void
 */
function dev_update_ticket_tag_terms_count( $terms, $taxonomy ) {

	global $wpdb;

	$object_types   = (array) $taxonomy->object_type;
	$post_status    = dev_get_post_status();
	$allowed_status = array();

	foreach ( $post_status as $status => $label ) {
		if ( !in_array( $status, $allowed_status ) ) {
			array_push( $allowed_status, $status );
		}
	}

	foreach ( $object_types as &$object_type ) {
		list( $object_type ) = explode( ':', $object_type );
	}

	$object_types = array_unique( $object_types );

	if ( false !== ( $check_attachments = array_search( 'attachment', $object_types ) ) ) {
		unset( $object_types[ $check_attachments ] );
		$check_attachments = true;
	}

	if ( $object_types ) {
		$object_types = esc_sql( array_filter( $object_types, 'post_type_exists' ) );
	}

	foreach ( (array) $terms as $term ) {
		$count = 0;

		// Attachments can be 'inherit' status, we need to base count off the parent's status if so
		if ( $check_attachments ) {
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts p1 WHERE p1.ID = $wpdb->term_relationships.object_id AND ( post_status = 'publish' OR ( post_status = 'inherit' AND post_parent > 0 AND ( SELECT post_status FROM $wpdb->posts WHERE ID = p1.post_parent ) = 'publish' ) ) AND post_type = 'attachment' AND term_taxonomy_id = %d", $term ) );
		}

		if ( $object_types ) {
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id AND post_status IN ('" . implode( "', '", $allowed_status ) . "') AND post_type IN ('" . implode( "', '", $object_types ) . "') AND term_taxonomy_id = %d", $term ) );
		}

		/** This action is documented in wp-includes/taxonomy.php */
		do_action( 'edit_term_taxonomy', $term, $taxonomy );
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );

		/** This action is documented in wp-includes/taxonomy.php */
		do_action( 'edited_term_taxonomy', $term, $taxonomy );
	}

}

/**
 * Return a custom field value.
 *
 * @param  string  $name    Option name
 * @param  integer $post_id Post ID
 * @param  mixed   $default Default value
 *
 * @return mixed  Meta value
 * @since  1.0.0
 */
function dev_get_cf_value( $name, $post_id, $default = false ) {

	$field = new DevSupport\Field\CustomField( $name );

	return $field->get_field_value( $default, $post_id );
}

/**
 * Echo a custom field value.
 *
 * This function is just a wrapper function for dev_get_cf_value()
 * that echoes the result instead of returning it.
 *
 * @param  string  $name    Option name
 * @param  integer $post_id Post ID
 * @param  mixed   $default Default value
 *
 * @return mixed            Meta value
 * @since  1.0.0
 */
function dev_cf_value( $name, $post_id, $default = false ) {
	echo dev_get_cf_value( $name, $post_id, $default );
}

/**
 * Add a new custom field.
 *
 * @since  1.0.0
 *
 * @param  string $name The ID of the custom field to add
 * @param  array  $args Additional arguments for the custom field
 *
 * @return boolean        Returns true on success or false on failure
 */
function dev_add_custom_field( $name, $args = array() ) {

	global $dev_cf;

	if ( ! isset( $dev_cf ) || ! class_exists( 'DevSupport\Field\CustomFields' ) ) {
		return false;
	}

	return $dev_cf->add_field( $name, $args );

}

/**
 * Add a new custom taxonomy.
 *
 * @since  1.0.0
 *
 * @param  string $name The ID of the custom field to add
 * @param  array  $args Additional arguments for the custom field
 *
 * @return boolean        Returns true on success or false on failure
 */
function dev_add_custom_taxonomy( $name, $args = array() ) {

	global $dev_cf;

	if ( ! isset( $dev_cf ) || ! class_exists( 'DevSupport\Field\CustomFields' ) ) {
		return false;
	}

	/* Force the custom fields type to be a taxonomy. */
	$args['field_type']      = 'taxonomy';
	$args['column_callback'] = 'dev_show_taxonomy_column';

	/* Add the taxonomy. */
	$dev_cf->add_field( $name, $args );

	return true;

}

add_action( 'init', 'dev_register_core_fields' );
/**
 * Register the cure custom fields.
 *
 * @since  1.0.0
 * @return void
 */
function dev_register_core_fields() {

	global $dev_cf;

	if ( ! isset( $dev_cf ) ) {
		return;
	}

	$dev_cf->add_field( 'assignee',   array( 'core' => true, 'show_column' => false, 'log' => true, 'title' => __( 'Support Staff', TEXTDOMAIN ) ) );
	// $dev_cf->add_field( 'ccs',        array( 'core' => true, 'show_column' => false, 'log' => true ) );
	$dev_cf->add_field( 'status',     array( 'core' => true, 'show_column' => true, 'log' => false, 'field_type' => false, 'column_callback' => 'dev_cf_display_status', 'save_callback' => null ) );
	$dev_cf->add_field( 'ticket-tag', array(
			'core'                  => true,
			'show_column'           => true,
			'log'                   => true,
			'field_type'            => 'taxonomy',
			'taxo_std'              => true,
			'column_callback'       => 'dev_cf_display_status',
			'save_callback'         => null,
			'label'                 => __( 'Tag', TEXTDOMAIN ),
			'name'                  => __( 'Tag', TEXTDOMAIN ),
			'label_plural'          => __( 'Tags', TEXTDOMAIN ),
			'taxo_hierarchical'     => false,
			'update_count_callback' => 'dev_update_ticket_tag_terms_count'
		)
	);

	$options = maybe_unserialize( get_option( 'dev_options', array() ) );

	if ( isset( $options['support_products'] ) && true === boolval( $options['support_products'] ) ) {

		$slug = defined( 'DEV_PRODUCT_SLUG' ) ? DEV_PRODUCT_SLUG : 'product';

		/* Filter the taxonomy labels */
		$labels = apply_filters( 'dev_product_taxonomy_labels', array(
				'label'        => __( 'Product', TEXTDOMAIN ),
				'name'         => __( 'Product', TEXTDOMAIN ),
				'label_plural' => __( 'Products', TEXTDOMAIN )
			)
		);

		$dev_cf->add_field( 'product', array(
				'core'                  => false,
				'show_column'           => true,
				'log'                   => true,
				'field_type'            => 'taxonomy',
				'taxo_std'              => false,
				'column_callback'       => 'dev_show_taxonomy_column',
				'label'                 => $labels['label'],
				'name'                  => $labels['name'],
				'label_plural'          => $labels['label_plural'],
				'taxo_hierarchical'     => true,
				'update_count_callback' => 'dev_update_ticket_tag_terms_count',
				'rewrite'               => array( 'slug' => $slug )
			)
		);

	}

}