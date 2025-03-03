<?php
/**
 * Frontend Class for showing contributors below post content.
 *
 * @package WAC\Includes
 * @since 1.0.0
 */

namespace WAC\Includes;

use WAC\Includes\Traits\Get_Instance;

if ( ! defined( constant_name: 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend Class for showing contributors below post content.
 *
 * @package WAC\Includes
 * @since 1.0.0
 */
class Frontend {
	use Get_Instance;

	/**
	 * Plugin loader constructor
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'the_content', array( $this, 'display_contributors' ) );
		add_filter( 'posts_where', array( $this, 'wac_posts_where_filter' ), 10, 2 );
		add_filter( 'posts_join', array( $this, 'wac_posts_join_filter' ), 10, 2 );
		add_filter( 'posts_distinct', array( $this, 'wac_search_distinct' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Display contributors below post content
	 *
	 * @access public
	 * @hooked the_content
	 * @param string $content Post content.
	 * @since 1.0.0
	 * @return string Modified content with contributors.
	 */
	public function display_contributors( $content ) {
		// Check if we're on a single post page.
		if ( ! is_singular( 'post' ) ) {
			return $content;
		}

		$post_id               = get_the_ID();
		$wac_editor_meta       = get_post_meta( $post_id, 'wac_editor_meta', true ) ?: array();
		$contributors_list     = $wac_editor_meta['contributors_list'] ?? array();
		$contributor_box_title = $wac_editor_meta['contributor_box_title'] ?? __( 'Contributors', 'wac' );

		// Get display options with defaults.
		$options = get_option(
			'wac_display_options',
			array(
				'show_avatar'   => 1,
				'show_name'     => 1,
				'show_bio'      => 1,
				'show_website'  => 1,
				'element_order' => array( 'name', 'bio', 'website' ),
			)
		);

		// Ensure element_order is always an array.
		$element_order = isset( $options['element_order'] ) && is_array( $options['element_order'] )
						? $options['element_order']
						: explode( ',', $options['element_order'] ?? 'name,bio,website' );

		if ( ! empty( $contributors_list ) ) {
			$output  = '<div class="wac-contributors-container">';
			$output .= '<h3 class="wac-contributors-title">' . esc_html( $contributor_box_title ) . '</h3>';
			$output .= '<div class="wac-contributors-list">';

			foreach ( $contributors_list as $contributor_id ) {
				$user = get_userdata( $contributor_id );
				if ( ! $user ) {
					continue;
				}

				$avatar = get_avatar( $contributor_id, 150 );

				$elements = array(
					'name'    => function () use ( $user ) {
						return '<h2 aria-label="' . __( 'View Profile', 'wac' ) . '" aria-describedby="wac-contributor-view-profile" class="wac-contributor-name"><a href="' . esc_url( get_author_posts_url( $user->ID ) ) . '">' .
								esc_html( $user->display_name ) . '</a></h2>';
					},
					'bio'     => function () use ( $user ) {
						return ! empty( $user->description ) ?
								'<div class="wac-contributor-bio">' . wp_kses_post( $user->description ) . '</div>' : '';
					},
					'website' => function () use ( $user ) {
						return ! empty( $user->user_url ) ?
								'<div class="wac-contributor-website"><a href="' . esc_url( $user->user_url ) . '" target="_blank" rel="noopener noreferrer"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-link"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></a></div>' : '';
					},
				);

				$output .= '<div class="wac-contributor-card" tabindex="0" aria-label="' . esc_html( $user->display_name ) . '" aria-describedby="wac-contributor-info">';
				if ( ! empty( $options['show_avatar'] ) ) {
					$output .= '<div class="wac-contributor-avatar">' . $avatar . '</div>';
				}

				// Loop through elements in the specified order and render them.
				$output .= '<div class="wac-contributor-info">';
				foreach ( $element_order as $element ) {
					if ( ! empty( $options[ 'show_' . $element ] ) && isset( $elements[ $element ] ) ) {
						$output .= $elements[ $element ]();
					}
				}
				$output .= '</div></div>';
			}

			$output  .= '</div></div>';
			$content .= $output;
		}

		return $content;
	}

	/**
	 * Modify the author archive query to include posts where the user is a contributor
	 *
	 * @access public
	 * @hooked posts_where
	 * @param string   $where Original WHERE clause.
	 * @param WP_Query $query Query object.
	 * @since 1.0.0
	 * @return string Modified WHERE clause.
	 */
	public function wac_posts_where_filter( $where, $query ) {
		global $wpdb;

		// Check if we're on an author archive page.
		if ( is_author() && $query->is_main_query() && ! is_admin() ) {
			$user_id = get_queried_object_id();

			$where .= $wpdb->prepare(
				" OR (EXISTS (SELECT 1 FROM {$wpdb->postmeta} pm 
					WHERE pm.post_id = {$wpdb->posts}.ID 
					AND pm.meta_key = 'wac_editor_meta' 
					AND pm.meta_value LIKE %s))",
				'%"contributors_list";a:%:' . $user_id . ';%'
			);
		}

		return $where;
	}

	/**
	 * Modify join clause for author archive query
	 *
	 * @access public
	 * @hooked posts_join
	 * @param string   $join Original JOIN clause.
	 * @param WP_Query $query Query object.
	 * @since 1.0.0
	 * @return string Modified JOIN clause.
	 */
	public function wac_posts_join_filter( $join, $query ) {
		global $wpdb;
		// Check if we're on an author archive page.
		if ( is_author() && $query->is_main_query() && ! is_admin() ) {
			$join .= " LEFT JOIN {$wpdb->postmeta} AS wac_meta ON ({$wpdb->posts}.ID = wac_meta.post_id)";
		}
		return $join;
	}

	/**
	 * Modify distinct clause for author archive search
	 *
	 * @access public
	 * @hooked posts_distinct
	 * @param string $distinct Original DISTINCT clause.
	 * @since 1.0.0
	 * @return string Modified DISTINCT clause.
	 */
	public function wac_search_distinct( $distinct ) {
		// Check if we're on an author archive page.
		if ( is_author() && ! is_admin() ) {
			$distinct = 'DISTINCT';
		}
		return $distinct;
	}

	/**
	 * Enqueue assets for the frontend
	 *
	 * @access public
	 * @hooked wp_enqueue_scripts
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'wac-frontend-css', WAC_ASSETS . '/css/frontend.css', array(), WAC_VERSION, 'all' );
	}
}
