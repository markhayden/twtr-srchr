<?
/**
 * The Class.
 */
class addTwtrSearchTermField {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
			$post_type_options = get_option( 'twtr_post_types', 'post,page' );

            $post_types = explode(',', $post_type_options);     //limit meta box to certain post types
            if ( in_array( $post_type, $post_types )) {
		add_meta_box(
			'twtr_search_term_input',
			__( 'Twitter Serch Term', 'twtr_search_term' ),
			array( $this, 'render_meta_box_content' ),
			$post_type,
			'normal',
			'high'
		);
            }
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['twtr_srch_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['twtr_srch_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'twtr_srch_inner_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$mydata = sanitize_text_field( $_POST['twtr_srch_new_field'] );

		// Update the meta field.
		update_post_meta( $post_id, 'twtr_search_query', $mydata );
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'twtr_srch_inner_custom_box', 'twtr_srch_inner_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$value = get_post_meta( $post->ID, 'twtr_search_query', true );

		// Display the form, using the current value.
		echo '<label for="twtr_srch_new_field">';
		_e( 'Terms to be used in twitter search. Use @ symbol if searching username.', 'twtr_search_term' );
		echo '</label> ';
		echo '<input type="text" id="twtr_srch_new_field" name="twtr_srch_new_field"';
                echo ' value="' . esc_attr( $value ) . '" size="25" />';
	}
}
?>