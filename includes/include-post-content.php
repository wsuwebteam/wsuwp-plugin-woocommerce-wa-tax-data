<?php namespace WSUWP\Plugin\WA_Tax_Query;


class Post_Content {

	public function init() {

		add_filter( 'the_content', array( __CLASS__, 'set_post_slug' ), 1 );

	}


	public static function set_post_slug( $content ) {

		if ( is_singular() && is_main_query() ) {

			$post_id = get_the_ID();
			$post_status = get_post_status( $post_id );

			if ( 'pending' === $post_status && ! is_preview() ) {

				$post_content = get_post_meta( $post_id, '_wsuwp_public_pending_content', true );

				if ( ! empty( $post_content ) ) {

					$content = wp_specialchars_decode( $post_content );

				}

				remove_filter( 'the_content', array( __CLASS__, 'set_post_slug' ), 1 );
			}
		}

		return $content;

	}


}


(new Post_Content)->init();
