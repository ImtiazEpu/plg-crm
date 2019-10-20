<?php
	
	class TaxMeta {
		
		/**
		 * TaxMeta constructor.
		 */
		public function __construct() {
			add_action( 'init', [ $this, 'sc_bootstrap' ] );
			add_action( 'companies_add_form_fields', [ $this, 'sc_companies_form_field' ] );
			add_action( 'companies_edit_form_fields', [ $this, 'sc_companies_edit_form_field' ] );
			add_action( 'create_companies', [ $this, 'sc_companies_save_meta' ] );
			add_action( 'edited_companies', [ $this, 'sc_update_category_meta' ] );
		}
		//End method constructor
		
  
		/**
		 * Meta bootstrap
		 */
		public function sc_bootstrap() {
			$arguments = [
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'single'            => true,
				'description'       => 'sample meta field for contact tax',
				'show_in_rest'      => true
			];
			register_meta( 'term', 'sc_extra_info', $arguments );
		}
		//End method sc_bootstrap
		
		
		/**
		 *
		 *This will add the custom meta field to the add new term page
		 */
		public function sc_companies_form_field() {
			?>
            <div class="form-field form-required term-name-wrap">
                <label for="tag-name"><?php _e( 'Extra Info', 'sc-crm' ) ?></label>
                <input name="extra-info" id="extra-info" type="text" value="" size="40" aria-required="true">
                <p><?php _e( 'Some Extra info field', 'sc-crm' ); ?></p>
            </div>
			<?php
		}
		//End method sc_companies_form_field
		
		
		/**
		 * Term edit field
		 *
		 * @param $term
		 */
		public function sc_companies_edit_form_field( $term ) {
			$extra_info = get_term_meta( $term->term_id, 'sc_extra_info', true )
			?>
            <tr class="form-field form-required term-name-wrap">
                <th scope="row">
                    <label for="tag-name">
						<?php _e( 'Extra Info', 'sc-crm' ) ?>
                    </label>
                </th>
                <td>
                    <input name="extra-info" id="extra-info" type="text" value="<?php echo esc_attr( $extra_info ) ?>"
                           size="40" aria-required="true">
                    <p class="description"><?php _e( 'Some Extra info field', 'sc-crm' ); ?></p>
                </td>
            </tr>
			<?php
		}
		//End method sc_companies_edit_form_field
		
		/**
		 * Save extra taxonomy fields callback function
		 *
		 * @param $term_id
		 */
		public function sc_companies_save_meta( $term_id ) {
			if ( wp_nonce_field( $_POST['_wpnonce_add-tag'], 'add-tag' ) ) {
				$extra_info = sanitize_text_field( $_POST['extra-info'] );
				update_term_meta( $term_id, 'sc_extra_info', $extra_info );
			}
		}
		//End method sc_companies_save_meta
		
		/**
		 * Update extra taxonomy fields callback function
		 *
		 * @param $term_id
		 */
		public function sc_update_category_meta( $term_id ) {
			if ( wp_nonce_field( $_POST['_wpnonce'], 'update-tag_{$term_id}' ) ) {
				$extra_info = sanitize_text_field( $_POST['extra-info'] );
				update_term_meta( $term_id, 'sc_extra_info', $extra_info );
			}
		}
		//End method sc_update_category_meta
		
		
	}
	
	new TaxMeta();