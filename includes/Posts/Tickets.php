<?php

/**
 *	Tickets Menu class of plugin
 *
 * @package   	Tickets
 * @author    	Sami Maxhuni <samimaxhuni510@gmail.com>
 * @license   	GPL-2.0+
 * @link      	http://devsolution.info
 * @copyright 	2017 Sami Maxhuni
 **/

namespace DevSupport\Posts;

use DevSupport\Dev_Agent;

class Tickets{

	public function __construct(){

		// Register Actions
		add_action( 'init', array( $this, 'post_type' ), 10, 0 );
		add_action( 'init', array( $this, 'secondary_post_type' ),  10, 0 );
		add_action( 'init', array( $this, 'register_post_status' ), 10, 0 );
		add_action( 'post_updated_messages', array( $this, 'updated_messages' ),10, 1 ); // Update the "post updated" messages for main post type
	}

	/**
	* Register the ticket post type.
	*
	* @since 1.0.0
	*/
	public function post_type() {

		$slug = sanitize_title(DEV_POST_TYPE);
		/* Supported components */
		$supports = array( 'title' );

		/* If the post is being created we add the editor */
		if( !isset( $_GET['post'] ) ) {
			array_push( $supports, 'editor' );
		}

		/* Post type labels */
		$labels = apply_filters( 'dev_ticket_type_labels', array(
			'name'               => _x( 'Tickets', 'post type general name', TEXTDOMAIN ),
			'singular_name'      => _x( 'Ticket', 'post type singular name', TEXTDOMAIN ),
			'menu_name'          => _x( 'Tickets', 'admin menu', TEXTDOMAIN ),
			'name_admin_bar'     => _x( 'Ticket', 'add new on admin bar', TEXTDOMAIN ),
			'add_new'            => _x( 'Add New', 'book', TEXTDOMAIN ),
			'add_new_item'       => __( 'Add New Ticket', TEXTDOMAIN ),
			'new_item'           => __( 'New Ticket', TEXTDOMAIN ),
			'edit_item'          => __( 'Edit Ticket', TEXTDOMAIN ),
			'view_item'          => __( 'View Ticket', TEXTDOMAIN ),
			'all_items'          => __( 'All Tickets', TEXTDOMAIN ),
			'search_items'       => __( 'Search Tickets', TEXTDOMAIN ),
			'parent_item_colon'  => __( 'Parent Ticket:', TEXTDOMAIN ),
			'not_found'          => __( 'No tickets found.', TEXTDOMAIN ),
			'not_found_in_trash' => __( 'No tickets found in Trash.', TEXTDOMAIN ),
		) );

		/* Post type capabilities */
		$cap = apply_filters( 'dev_ticket_type_cap', array(
			'read'					 => 'view_ticket',
			'read_post'				 => 'view_ticket',
			'read_private_posts' 	 => 'view_private_ticket',
			'edit_post'				 => 'edit_ticket',
			'edit_posts'			 => 'edit_ticket',
			'edit_others_posts' 	 => 'edit_other_ticket',
			'edit_private_posts' 	 => 'edit_private_ticket',
			'edit_published_posts' 	 => 'edit_ticket',
			'publish_posts'			 => 'create_ticket',
			'delete_post'			 => 'delete_ticket',
			'delete_posts'			 => 'delete_ticket',
			'delete_private_posts' 	 => 'delete_private_ticket',
			'delete_published_posts' => 'delete_ticket',
			'delete_others_posts' 	 => 'delete_other_ticket'
		) );

		/* Post type arguments */
		$args = apply_filters( 'dev_ticket_type_args', array(
			'labels'              => $labels,
			'public'              => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => apply_filters( 'dev_rewrite_slug', $slug ), 'with_front' => false ),
			'capability_type'     => 'view_ticket',
			'capabilities'        => $cap,
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-sos',
			'supports'            => $supports
		) );

		register_post_type( DEV_POST_TYPE, $args );
	}

	/**
	 * Ticket update messages.
	 *
	 * @since  1.0.0
	 * @param  array $messages Existing post update messages.
	 * @return array Amended post update messages with new CPT update messages.
	 */
	public function updated_messages( $messages ) {

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		if ( 'ticket' !== $post_type ) {
			return $messages;
		}

		$messages[$post_type] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Ticket updated.', TEXTDOMAIN ),
			2  => __( 'Custom field updated.', TEXTDOMAIN ),
			3  => __( 'Custom field deleted.', TEXTDOMAIN ),
			4  => __( 'Ticket updated.', TEXTDOMAIN ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Ticket restored to revision from %s', TEXTDOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Ticket published.', TEXTDOMAIN ),
			7  => __( 'Ticket saved.', TEXTDOMAIN ),
			8  => __( 'Ticket submitted.', TEXTDOMAIN ),
			9  => sprintf(
				__( 'Ticket scheduled for: <strong>%1$s</strong>.', TEXTDOMAIN ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', TEXTDOMAIN ), strtotime( $post->post_date ) )
			),
			10 => __( 'Ticket draft updated.', TEXTDOMAIN )
		);

		if ( $post_type_object->publicly_queryable ) {
			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View ticket', TEXTDOMAIN ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview ticket', TEXTDOMAIN ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;
		}

		return $messages;
	}

	/**
	 * Register secondary post types.
	 *
	 * These post types aren't used by the client
	 * but are used to store extra information about the tickets.
	 *
	 * @since  1.0.0
	 */
	public function secondary_post_type() {
		register_post_type( 'ticket_reply', array( 'public' => false, 'exclude_from_search' => true, 'supports' => array( 'editor' ) ) );
		register_post_type( 'ticket_history', array( 'public' => false, 'exclude_from_search' => true ) );
		register_post_type( 'ticket_log', array( 'public' => false, 'exclude_from_search' => true ) );
	}

	/**
	 * Register custom ticket status.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function register_post_status() {

		$status = self::get_post_status();

		foreach ( $status as $id => $custom_status ) {

			$args = array(
				'label'                     => $custom_status,
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( "$custom_status <span class='count'>(%s)</span>", "$custom_status <span class='count'>(%s)</span>", TEXTDOMAIN ),
			);

			register_post_status( $id, $args );
		}

		/**
		 * Hardcode the read and unread status used for replies.
		 */
		register_post_status( 'read',   array( 'label' => _x( 'Read', 'Reply status', TEXTDOMAIN ), 'public' => false ) );
		register_post_status( 'unread', array( 'label' => _x( 'Unread', 'Reply status', TEXTDOMAIN ), 'public' => false ) );
	}

	/**
	 * Get available ticket status.
	 *
	 * @since  1.0.0
	 * @return array List of filtered statuses
	 */
	public static function get_post_status() {

		$status = array(
			'queued'     => _x( 'New', 'Ticket status', TEXTDOMAIN ),
			'processing' => _x( 'In Progress', 'Ticket status', TEXTDOMAIN ),
			'hold'       => _x( 'On Hold', 'Ticket status', TEXTDOMAIN ),
		);

		return apply_filters( 'dev_ticket_statuses', $status );

	}

	/**
	 * Get tickets.
	 *
	 * Get a list of tickets matching the arguments passed.
	 * This function is basically a wrapper for WP_Query with
	 * the addition of the ticket status.
	 *
	 * @since  1.0.0
	 *
	 * @param string       $ticket_status Ticket status (open or closed)
	 * @param array        $args          Additional arguments (see WP_Query)
	 * @param string|array $post_status   Ticket state
	 * @param bool         $cache         Whether or not to cache the results
	 *
	 * @return array               Array of tickets, empty array if no tickets found
	 */
	public static function dev_get_tickets( $ticket_status = 'open', $args = array(), $post_status = 'any', $cache = false ) {

		$custom_post_status = self::get_post_status();
		$post_status_clean  = array();

		if ( empty( $post_status ) ) {
			$post_status = 'any';
		}

		if ( ! is_array( $post_status ) ) {
			if ( 'any' === $post_status ) {

				foreach ( $custom_post_status as $status_id => $status_label ) {
					$post_status_clean[] = $status_id;
				}

				$post_status = $post_status_clean;

			} else {
				if ( ! array_key_exists( $post_status, $custom_post_status ) ) {
					$post_status = ''; // This basically will return no result if the post status specified doesn't exist
				}
			}
		} else {
			foreach ( $post_status as $key => $status ) {
				if ( ! array_key_exists( $status, $custom_post_status ) ) {
					unset( $post_status[ $key ] );
				}
			}
		}

		$defaults = array(
			'post_type'              => 'ticket',
			'post_status'            => $post_status,
			'posts_per_page'         => - 1,
			'no_found_rows'          => ! (bool) $cache,
			'cache_results'          => (bool) $cache,
			'update_post_term_cache' => (bool) $cache,
			'update_post_meta_cache' => (bool) $cache,
			'wpas_query'             => true, // We use this parameter to identify our own queries so that we can remove the author parameter
		);

		$args  = wp_parse_args( $args, $defaults );

		if ( 'any' !== $ticket_status ) {
			if ( in_array( $ticket_status, array( 'open', 'closed' ) ) ) {
				$args['meta_query'][] = array(
						'key'     => '_dev_status',
						'value'   => $ticket_status,
						'compare' => '=',
						'type'    => 'CHAR'
				);
			}
		}

		$query = new \WP_Query( $args );

		if ( empty( $query->posts ) ) {
			return array();
		} else {
			return $query->posts;
		}
	}

	/**
	 * Add ticket count in admin menu item.
	 *
	 * @return boolean True if the ticket count was added, false otherwise
	 * @since  1.0.0
	 */
	public function tickets_count() {

		global $menu, $current_user;

		if ( 
		 	current_user_can( 'administrator' )  
		 	|| ! current_user_can( 'administrator' ) 
		 	&& current_user_can( 'edit_ticket' )
		){

		$agent = new Dev_Agent( $current_user->ID );
		$count = $agent->open_tickets();

		} else {
			$count = count( self::dev_get_tickets( 'open' ) );
		}

		if ( 0 === $count ) {
			return false;
		}

		foreach ( $menu as $key => $value ) {
			if ( $menu[$key][2] == 'edit.php?post_type='.DEV_POST_TYPE ) {
				$menu[$key][0] .= ' <span class="awaiting-mod count-' . $count . '"><span class="pending-count">' . $count . '</span></span>';
			}
		}

		return true;
	}



	public static function get_open_ticket_url( $ticket_id, $action = 'open' ) {

		$remove = array( 'post', 'message' );
		$args   = $_GET;

		foreach ( $remove as $key ) {

			if ( isset( $args[$key] ) ) {
				unset( $args[$key] );
			}

		}

		$args['post'] = intval( $ticket_id );

		return dev_url_add_custom_action( add_query_arg( $args, admin_url( 'post.php' ) ), $action );
	}

	public static function get_close_ticket_url( $ticket_id ) {
		return self::get_open_ticket_url( $ticket_id, 'close' );
	}
}