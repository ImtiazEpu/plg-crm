<?php
	/*
	Plugin Name: Sourcecypher CRM
	Plugin URL:
	Description: A Custom CRM plugin with custom role & capabilities. 
	Version: 1.0
	Author: SourceCypher
	Author URI: sourcecypher.net
	License: GPLv2 or later
	Text Domain: sc-crm
	Domain Path: /languages/
	*/
	
	/**
	 * Kick-in Class ScCrm
	 */
	class ScCrm {
		
		/**
		 * ScCrm constructor.
		 */
		public function __construct() {
			
			add_action( 'init', [ $this, 'sc_register_custom_post_type' ] );
			add_action( 'init', [ $this, 'sc_register_custom_taxonomy_companies' ] );
			add_action( 'init', [ $this, 'load_require_file' ] );
			register_activation_hook( __FILE__, [ $this, 'plugin_activation' ] );
			register_deactivation_hook( __FILE__, [ $this, 'plugin_deactivation' ] );
			add_action( 'plugins_loaded', [ $this, 'sc_load_text_domain' ] );
		}
		//End method construct
		
		
		/**
		 * Registers a Custom Post Type called contact
		 */
		public function sc_register_custom_post_type() {
			register_post_type( 'contact', [
				'labels'             => [
					'name'               => _x( 'Contacts', 'post type general name', 'sc-crm' ),
					'singular_name'      => _x( 'Contact', 'post type singular name', 'sc-crm' ),
					'menu_name'          => _x( 'Contacts', 'admin menu', 'sc-crm' ),
					'name_admin_bar'     => _x( 'Contact', 'add new on admin bar', 'sc-crm' ),
					'add_new'            => _x( 'Add New', 'contact', 'sc-crm' ),
					'add_new_item'       => __( 'Add New Contact', 'sc-crm' ),
					'new_item'           => __( 'New Contact', 'sc-crm' ),
					'edit_item'          => __( 'Edit Contact', 'sc-crm' ),
					'view_item'          => __( 'View Contact', 'sc-crm' ),
					'all_items'          => __( 'All Contacts', 'sc-crm' ),
					'search_items'       => __( 'Search Contacts', 'sc-crm' ),
					'parent_item_colon'  => __( 'Parent Contacts:', 'sc-crm' ),
					'not_found'          => __( 'No contacts found.', 'sc-crm' ),
					'not_found_in_trash' => __( 'No contacts found in Trash.', 'sc-crm' ),
				],
				// Front-end
				'has-archive'        => true,
				'public'             => true,
				'publicly_queryable' => false,
				
				// Admin
				'capabilities'       => [
					'edit_others_posts'      => 'edit_others_contacts',
					'delete_others_posts'    => 'delete_others_contacts',
					'delete_private_posts'   => 'delete_private_contacts',
					'edit_private_posts'     => 'edit_private_contacts',
					'read_private_posts'     => 'read_private_contacts',
					'edit_published_posts'   => 'edit_published_contacts',
					'publish_posts'          => 'publish_contacts',
					'delete_published_posts' => 'delete_published_contacts',
					'edit_posts'             => 'edit_contacts',
					'delete_posts'           => 'delete_contacts',
					'edit_post'              => 'edit_contact',
					'read_post'              => 'read_contact',
					'delete_post'            => 'delete_contact',
				],
				
				'map_meta_cap'  => true,
				'menu_icon'     => 'dashicons-businessman',
				'menu_position' => 10,
				'query_var'     => true,
				'show_in_menu'  => true,
				'show_ui'       => true,
				'supports'      => [
					'title',
					'editor',
					'thumbnail'
				],
			] );
		}
		
		// End method sc_register_custom_post_type
		
		
		/**
		 * Registers a Custom Taxonomy called companies
		 */
		public function sc_register_custom_taxonomy_companies() {
			$labels = [
				'name'              => __( 'Companies', 'sc-crm' ),
				'singular_name'     => __( 'Company', 'sc-crm' ),
				'search_items'      => __( 'Search Companies' ),
				'all_items'         => __( 'All Companies' ),
				'parent_item'       => __( 'Parent Company' ),
				'parent_item_colon' => __( 'Parent Company:' ),
				'edit_item'         => __( 'Edit Company' ),
				'update_item'       => __( 'Update Company' ),
				'add_new_item'      => __( 'Add New Company' ),
				'new_item_name'     => __( 'New Company Name' ),
				'menu_name'         => __( 'Company' ),
			];
			$args   = [
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_cloumn' => true,
				'query_var'         => true,
				'rewrite'           => [ 'slug' => 'companies' ],
				'capabilities'      => [
					'manage_terms' => 'manage_companies',
					'edit_terms'   => 'edit_companies',
					'delete_terms' => 'delete_companies',
					'assign_terms' => 'assign_companies',
				]
			];
			
			register_taxonomy( 'companies', [ 'contact' ], $args );
		}
		//End Method sc_register_custom_taxonomy_companies
		
		
		/**
		 * Activation hook to register a new Role and assign it our Contact Capabilities
		 */
		public function plugin_activation() {
			
			// Define our custom capabilities
			$customCaps = [
				'edit_others_contacts'      => true,
				'delete_others_contacts'    => true,
				'delete_private_contacts'   => true,
				'edit_private_contacts'     => true,
				'read_private_contacts'     => true,
				'edit_published_contacts'   => true,
				'publish_contacts'          => true,
				'delete_published_contacts' => true,
				'edit_contacts'             => true,
				'delete_contacts'           => true,
				'edit_contact'              => true,
				'read_contact'              => true,
				'delete_contact'            => true,
				'read'                      => true,
				'manage_companies'          => true,
				'edit_companies'            => true,
				'delete_companies'          => true,
				'assign_companies'          => true
			];
			
			// Create CRM role and assign the custom capabilities to it
			add_role( 'crm', __( 'CRM', 'sc-crm' ), $customCaps );
			
			
			// Add custom capabilities to Admin and Editor Roles
			$roles = array( 'administrator', 'editor' );
			
			foreach ( $roles as $roleName ) {
				$role = get_role( $roleName );
				
				// Check role exists
				if ( is_null( $role ) ) {
					continue;
				}
				
				foreach ( $customCaps as $capabilities => $enable ) {
					if ( $enable ) {
						// Add capability
						$role->add_cap( $capabilities );
					}
				}
			}
			//End admin and editor capabilities
			
			
			// Add some custom capabilities to the Author Role
			$role = get_role( 'author' );
			$role->add_cap( 'edit_contact' );
			$role->add_cap( 'edit_contacts' );
			$role->add_cap( 'publish_contacts' );
			$role->add_cap( 'read_contact' );
			$role->add_cap( 'delete_contact' );
			unset( $role );
		}
		// End method plugin_activation
		
		/**
		 * Deactivation hook to unregister our existing Contacts Role
		 */
		public function plugin_deactivation() {
			remove_role( 'crm' );
		}
		//end method plugin_deactivation
		
		
		/**
		 *
		 */
		public function load_require_file() {
			if ( file_exists( dirname( __FILE__ ) . '/inc/tax-meta.php' ) ) {
				require_once( dirname( __FILE__ ) . '/inc/tax-meta.php' );
			}
			
		}
		//End method ClassInitiate
		
		/**
		 * Load text domain
		 */
		public function sc_load_text_domain() {
			load_plugin_textdomain( 'sc-crm', false, dirname( __FILE__ ) . "/languages" );
		}
		//End method sc_load_text_domain
		
		
	}
	
	new ScCrm();