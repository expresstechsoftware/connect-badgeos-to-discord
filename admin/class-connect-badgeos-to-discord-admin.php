<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Connect_Badgeos_To_Discord
 * @subpackage Connect_Badgeos_To_Discord/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Connect_Badgeos_To_Discord
 * @subpackage Connect_Badgeos_To_Discord/admin
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Connect_Badgeos_To_Discord_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Connect_Badgeos_To_Discord_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Connect_Badgeos_To_Discord_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$min_css = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
		wp_register_style( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . 'discord_tabs_css', plugin_dir_url( __FILE__ ) . 'css/skeletabs.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/connect-badgeos-to-discord-admin' . $min_css . '.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Connect_Badgeos_To_Discord_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Connect_Badgeos_To_Discord_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), $this->version, false );

		wp_register_script( $this->plugin_name . '-tabs-js', plugin_dir_url( __FILE__ ) . 'js/skeletabs.js', array( 'jquery' ), $this->version, false );
		$min_js = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/connect-badgeos-to-discord-admin' . $min_js . '.js', array( 'jquery' ), $this->version, false );
		$script_params = array(
			'admin_ajax'                => admin_url( 'admin-ajax.php' ),
			'permissions_const'         => CONNECT_BADGEOS_TO_DISCORD_OAUTH_SCOPES,
			'is_admin'                  => is_admin(),
			'ets_badgeos_discord_nonce' => wp_create_nonce( 'ets-badgeos-discord-ajax-nonce' ),
		);
		wp_localize_script( $this->plugin_name, 'etsBadgeOSParams', $script_params );

	}

	/**
	 * Method to add discord setting sub-menu under top level menu of BadgeOS
	 *
	 * @since    1.0.0
	 */
	public function ets_badgeos_discord_add_settings_menu() {
		add_submenu_page( 'badgeos_badgeos', __( 'Discord Settings', 'connect-badgeos-to-discord' ), __( 'Discord Settings', 'connect-badgeos-to-discord' ), 'manage_options', 'connect-badgeos-to-discord', array( $this, 'ets_badgeos_discord_setting_page' ) );
	}

	/**
	 * Callback to Display settings page
	 *
	 * @since    1.0.0
	 */
	public function ets_badgeos_discord_setting_page() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		wp_enqueue_style( $this->plugin_name . '-select2' );
		wp_enqueue_style( $this->plugin_name . 'discord_tabs_css' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name . '-select2' );
		wp_enqueue_script( $this->plugin_name . '-tabs-js' );
		wp_enqueue_script( $this->plugin_name );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'wp-color-picker' );
		require_once CONNECT_BADGEOS_TO_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/connect-badgeos-to-discord-admin-display.php';
	}

	/**
	 * Save application details
	 *
	 * @since    1.0.0
	 * @return NONE
	 */
	public function ets_badgeos_discord_application_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_badgeos_discord_client_id = isset( $_POST['ets_badgeos_discord_client_id'] ) ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_client_id'] ) ) : '';

		$ets_badgeos_discord_client_secret = isset( $_POST['ets_badgeos_discord_client_secret'] ) ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_client_secret'] ) ) : '';

		$ets_badgeos_discord_bot_token = isset( $_POST['ets_badgeos_discord_bot_token'] ) ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_bot_token'] ) ) : '';

		$ets_badgeos_discord_redirect_url = isset( $_POST['ets_badgeos_discord_redirect_url'] ) ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_redirect_url'] ) ) : '';

		$ets_badgeos_discord_redirect_page_id = isset( $_POST['ets_badgeos_discord_redirect_page_id'] ) ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_redirect_page_id'] ) ) : '';

		$ets_badgeos_discord_admin_redirect_url = isset( $_POST['ets_badgeos_discord_admin_redirect_url'] ) ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_admin_redirect_url'] ) ) : '';

		$ets_badgeos_discord_server_id = isset( $_POST['ets_badgeos_discord_server_id'] ) ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_server_id'] ) ) : '';

		$ets_current_url = sanitize_text_field( trim( $_POST['current_url'] ) );

		if ( isset( $_POST['submit'] ) ) {
			if ( isset( $_POST['ets_badgeos_discord_save_settings'] ) && wp_verify_nonce( $_POST['ets_badgeos_discord_save_settings'], 'save_badgeos_discord_general_settings' ) ) {
				if ( $ets_badgeos_discord_client_id ) {
					update_option( 'ets_badgeos_discord_client_id', $ets_badgeos_discord_client_id );
				}

				if ( $ets_badgeos_discord_client_secret ) {
					update_option( 'ets_badgeos_discord_client_secret', $ets_badgeos_discord_client_secret );
				}

				if ( $ets_badgeos_discord_bot_token ) {
					update_option( 'ets_badgeos_discord_bot_token', $ets_badgeos_discord_bot_token );
				}

				if ( $ets_badgeos_discord_redirect_url ) {
					update_option( 'ets_badgeos_discord_redirect_page_id', $ets_badgeos_discord_redirect_url );
					$ets_badgeos_discord_redirect_url = ets_get_badgeos_discord_formated_discord_redirect_url( $ets_badgeos_discord_redirect_url );
					update_option( 'ets_badgeos_discord_redirect_url', $ets_badgeos_discord_redirect_url );

				}

				if ( $ets_badgeos_discord_server_id ) {
					update_option( 'ets_badgeos_discord_server_id', $ets_badgeos_discord_server_id );
				}
				if ( $ets_badgeos_discord_admin_redirect_url ) {
					update_option( 'ets_badgeos_discord_admin_redirect_url', $ets_badgeos_discord_admin_redirect_url );
				}
				/**
				 * Call function to save bot name option.
				 */
				ets_badgeos_discord_update_bot_name_option();

				$message = esc_html__( 'Your settings are saved successfully.', 'connect-badgeos-to-discord' );

				$pre_location = $ets_current_url . '&save_settings_msg=' . $message . '#ets_badgeos_application_details';
				wp_safe_redirect( $pre_location );
			}
		}
	}

	/**
	 * Load discord roles from server
	 *
	 * @return OBJECT REST API response
	 */
	public function ets_badgeos_discord_load_discord_roles() {

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_badgeos_discord_nonce'], 'ets-badgeos-discord-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$user_id = get_current_user_id();

		$guild_id          = sanitize_text_field( trim( get_option( 'ets_badgeos_discord_server_id' ) ) );
		$discord_bot_token = sanitize_text_field( trim( get_option( 'ets_badgeos_discord_bot_token' ) ) );
		$client_id         = sanitize_text_field( trim( get_option( 'ets_badgeos_discord_client_id' ) ) );
		if ( $guild_id && $discord_bot_token ) {
			$discod_server_roles_api = CONNECT_BADGEOS_TO_DISCORD_API_URL . 'guilds/' . $guild_id . '/roles';
			$guild_args              = array(
				'method'  => 'GET',
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bot ' . $discord_bot_token,
				),
			);
			$guild_response          = wp_remote_post( $discod_server_roles_api, $guild_args );

			// ets_badgeos_discord_log_api_response( $user_id, $discod_server_roles_api, $guild_args, $guild_response );

			$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );

			if ( is_array( $response_arr ) && ! empty( $response_arr ) ) {
				if ( array_key_exists( 'code', $response_arr ) || array_key_exists( 'error', $response_arr ) ) {
					// Connect_badgeos_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				} else {
					$response_arr['previous_mapping'] = get_option( 'ets_badgeos_discord_role_mapping' );

					$discord_roles = array();
					foreach ( $response_arr as $key => $value ) {
						$isbot = false;
						if ( is_array( $value ) ) {
							if ( array_key_exists( 'tags', $value ) ) {
								if ( array_key_exists( 'bot_id', $value['tags'] ) ) {
									$isbot = true;
									if ( $value['tags']['bot_id'] === $client_id ) {
										$response_arr['bot_connected'] = 'yes';
									}
								}
							}
						}
						if ( $key != 'previous_mapping' && $isbot == false && isset( $value['name'] ) && $value['name'] != '@everyone' ) {
							$discord_roles[ $value['id'] ]       = $value['name'];
							$discord_roles_color[ $value['id'] ] = $value['color'];
						}
					}
					update_option( 'ets_badgeos_discord_all_roles', serialize( $discord_roles ) );
					update_option( 'ets_badgeos_discord_roles_color', serialize( $discord_roles_color ) );
				}
			}
				return wp_send_json( $response_arr );
		}

				exit();

	}

	/**
	 * Save Role mapping settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_badgeos_discord_save_role_mapping() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_discord_roles = isset( $_POST['ets_badgeos_discord_role_mapping'] ) ? sanitize_textarea_field( trim( $_POST['ets_badgeos_discord_role_mapping'] ) ) : '';

		$ets_badgeos_discord_default_role_id = isset( $_POST['badgeos_defaultRole'] ) ? sanitize_textarea_field( trim( $_POST['badgeos_defaultRole'] ) ) : '';
		$ets_discord_roles                   = stripslashes( $ets_discord_roles );
		$save_mapping_status                 = update_option( 'ets_badgeos_discord_role_mapping', $ets_discord_roles );
		$ets_current_url                     = sanitize_text_field( trim( $_POST['current_url'] ) );
		if ( isset( $_POST['ets_badgeos_discord_role_mappings_nonce'] ) && wp_verify_nonce( $_POST['ets_badgeos_discord_role_mappings_nonce'], 'badgeos_discord_role_mappings_nonce' ) ) {
			if ( ( $save_mapping_status || isset( $_POST['ets_badgeos_discord_role_mapping'] ) ) && ! isset( $_POST['flush'] ) ) {
				if ( $ets_badgeos_discord_default_role_id ) {
					update_option( 'ets_badgeos_discord_default_role_id', $ets_badgeos_discord_default_role_id );
				}

				$message = esc_html__( 'Your mappings are saved successfully.', 'connect-badgeos-to-discord' );
			}
			if ( isset( $_POST['flush'] ) ) {
				delete_option( 'ets_badgeos_discord_role_mapping' );
				delete_option( 'ets_badgeos_discord_default_role_id' );

				$message = esc_html__( 'Your settings flushed successfully.', 'connect-badgeos-to-discord' );
			}
			$pre_location = $ets_current_url . '&save_settings_msg=' . $message . '#ets_badgeos_discord_role_mapping';
			wp_safe_redirect( $pre_location );
		}
	}

	/**
	 * Update redirect url
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_badgeos_discord_update_redirect_url() {

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_badgeos_discord_nonce'], 'ets-badgeos-discord-ajax-nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$page_id = sanitize_text_field( $_POST['ets_badgeos_page_id'] );
		if ( isset( $page_id ) ) {
			$formated_discord_redirect_url = ets_get_badgeos_discord_formated_discord_redirect_url( $page_id );
			update_option( 'ets_badgeos_discord_redirect_page_id', $page_id );
			update_option( 'ets_badgeos_discord_redirect_url', $formated_discord_redirect_url );
			$res = array(
				'formated_discord_redirect_url' => $formated_discord_redirect_url,
			);
			wp_send_json( $res );

		}
		exit();

	}

	/**
	 * Save advanced settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_badgeos_discord_save_advance_settings() {

		if ( ! current_user_can( 'administrator' ) || ! wp_verify_nonce( $_POST['ets_badgeos_discord_advance_settings_nonce'], 'badgeos_discord_advance_settings_nonce' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

			$ets_badgeos_discord_send_welcome_dm           = isset( $_POST['ets_badgeos_discord_send_welcome_dm'] ) ? sanitize_textarea_field( trim( $_POST['ets_badgeos_discord_send_welcome_dm'] ) ) : '';
			$ets_badgeos_discord_welcome_message           = isset( $_POST['ets_badgeos_discord_welcome_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_badgeos_discord_welcome_message'] ) ) : '';
			$ets_badgeos_discord_award_rank_message        = isset( $_POST['ets_badgeos_discord_award_rank_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_badgeos_discord_award_rank_message'] ) ) : '';
			$ets_badgeos_discord_award_user_points_message = isset( $_POST['ets_badgeos_discord_award_user_points_message'] ) ? sanitize_textarea_field( trim( $_POST['ets_badgeos_discord_award_user_points_message'] ) ) : '';

			$retry_failed_api     = isset( $_POST['retry_failed_api'] ) ? sanitize_textarea_field( trim( $_POST['retry_failed_api'] ) ) : '';
			$kick_upon_disconnect = isset( $_POST['kick_upon_disconnect'] ) ? sanitize_textarea_field( trim( $_POST['kick_upon_disconnect'] ) ) : '';
			$retry_api_count      = isset( $_POST['ets_badgeos_retry_api_count'] ) ? sanitize_textarea_field( trim( $_POST['ets_badgeos_retry_api_count'] ) ) : '';
			$set_job_cnrc         = isset( $_POST['set_job_cnrc'] ) ? sanitize_textarea_field( trim( $_POST['set_job_cnrc'] ) ) : '';
			$set_job_q_batch_size = isset( $_POST['set_job_q_batch_size'] ) ? sanitize_textarea_field( trim( $_POST['set_job_q_batch_size'] ) ) : '';
			$log_api_res          = isset( $_POST['log_api_res'] ) ? sanitize_textarea_field( trim( $_POST['log_api_res'] ) ) : '';
			$ets_current_url      = sanitize_text_field( trim( $_POST['current_url'] ) );

		if ( isset( $_POST['ets_badgeos_discord_advance_settings_nonce'] ) && wp_verify_nonce( $_POST['ets_badgeos_discord_advance_settings_nonce'], 'badgeos_discord_advance_settings_nonce' ) ) {
			if ( isset( $_POST['adv_submit'] ) ) {

				if ( isset( $_POST['ets_badgeos_discord_send_welcome_dm'] ) ) {
					update_option( 'ets_badgeos_discord_send_welcome_dm', true );
				} else {
					update_option( 'ets_badgeos_discord_send_welcome_dm', false );
				}
				if ( isset( $_POST['ets_badgeos_discord_welcome_message'] ) && $_POST['ets_badgeos_discord_welcome_message'] != '' ) {
					update_option( 'ets_badgeos_discord_welcome_message', $ets_badgeos_discord_welcome_message );
				} else {
					update_option( 'ets_badgeos_discord_welcome_message', '' );
				}

				if ( isset( $_POST['ets_badgeos_discord_send_award_rank_dm'] ) ) {
					update_option( 'ets_badgeos_discord_send_award_rank_dm', true );
				} else {
					update_option( 'ets_badgeos_discord_send_award_rank_dm', false );
				}
				if ( isset( $_POST['ets_badgeos_discord_award_rank_message'] ) && $_POST['ets_badgeos_discord_award_rank_message'] != '' ) {
					update_option( 'ets_badgeos_discord_award_rank_message', $ets_badgeos_discord_award_rank_message );
				} else {
					update_option( 'ets_badgeos_discord_award_rank_message', '' );
				}

				if ( isset( $_POST['ets_badgeos_discord_send_award_user_points_dm'] ) ) {
					update_option( 'ets_badgeos_discord_send_award_user_points_dm', true );
				} else {
					update_option( 'ets_badgeos_discord_send_award_user_points_dm', false );
				}
				if ( isset( $_POST['ets_badgeos_discord_award_user_points_message'] ) && $_POST['ets_badgeos_discord_award_user_points_message'] != '' ) {
					update_option( 'ets_badgeos_discord_award_user_points_message', $ets_badgeos_discord_award_user_points_message );
				} else {
					update_option( 'ets_badgeos_discord_award_user_points_message', '' );
				}

				if ( isset( $_POST['retry_failed_api'] ) ) {
					update_option( 'ets_badgeos_discord_retry_failed_api', true );
				} else {
					update_option( 'ets_badgeos_discord_retry_failed_api', false );
				}
				if ( isset( $_POST['kick_upon_disconnect'] ) ) {
					update_option( 'ets_badgeos_discord_kick_upon_disconnect', true );
				} else {
					update_option( 'ets_badgeos_discord_kick_upon_disconnect', false );
				}
				if ( isset( $_POST['ets_badgeos_retry_api_count'] ) ) {
					if ( $retry_api_count < 1 ) {
						update_option( 'ets_badgeos_discord_retry_api_count', 1 );
					} else {
						update_option( 'ets_badgeos_discord_retry_api_count', $retry_api_count );
					}
				}
				if ( isset( $_POST['set_job_cnrc'] ) ) {
					if ( $set_job_cnrc < 1 ) {
						update_option( 'ets_badgeos_discord_job_queue_concurrency', 1 );
					} else {
						update_option( 'ets_badgeos_discord_job_queue_concurrency', $set_job_cnrc );
					}
				}
				if ( isset( $_POST['set_job_q_batch_size'] ) ) {
					if ( $set_job_q_batch_size < 1 ) {
						update_option( 'ets_badgeos_discord_job_queue_batch_size', 1 );
					} else {
						update_option( 'ets_badgeos_discord_job_queue_batch_size', $set_job_q_batch_size );
					}
				}
				if ( isset( $_POST['log_api_res'] ) ) {
					update_option( 'ets_badgeos_discord_log_api_response', true );
				} else {
					update_option( 'ets_badgeos_discord_log_api_response', false );
				}

				$message      = esc_html__( 'Your settings flushed successfully.', 'connect-badgeos-to-discord' );
				$pre_location = $ets_current_url . '&save_settings_msg=' . esc_html( $message ) . '#ets_badgeos_discord_advanced';
				wp_safe_redirect( $pre_location );

			}
		}

	}

	/**
	 * Save apearance settings
	 *
	 * @param NONE
	 * @return NONE
	 */
	public function ets_badgeos_discord_save_appearance_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}

		$ets_badgeos_discord_connect_button_bg_color    = isset( $_POST['ets_badgeos_discord_connect_button_bg_color'] ) && $_POST['ets_badgeos_discord_connect_button_bg_color'] !== '' ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_connect_button_bg_color'] ) ) : '#77a02e';
		$ets_badgeos_discord_disconnect_button_bg_color = isset( $_POST['ets_badgeos_discord_disconnect_button_bg_color'] ) && $_POST['ets_badgeos_discord_disconnect_button_bg_color'] != '' ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_disconnect_button_bg_color'] ) ) : '#ff0000';
		$ets_badgeos_loggedin_btn_text                  = isset( $_POST['ets_badgeos_loggedin_btn_text'] ) && $_POST['ets_badgeos_loggedin_btn_text'] != '' ? sanitize_text_field( trim( $_POST['ets_badgeos_loggedin_btn_text'] ) ) : 'Connect To Discord';
		$ets_badgeos_loggedout_btn_text                 = isset( $_POST['ets_badgeos_loggedout_btn_text'] ) && $_POST['ets_badgeos_loggedout_btn_text'] != '' ? sanitize_text_field( trim( $_POST['ets_badgeos_loggedout_btn_text'] ) ) : 'Login With Discord';
		$ets_badgeos_discord_disconnect_btn_text        = $_POST['ets_badgeos_discord_disconnect_btn_text'] ? sanitize_text_field( trim( $_POST['ets_badgeos_discord_disconnect_btn_text'] ) ) : 'Disconnect From Discord';

		if ( isset( $_POST['appearance_submit'] ) ) {

			if ( isset( $_POST['ets_badgeos_discord_save_appearance_settings'] ) && wp_verify_nonce( $_POST['ets_badgeos_discord_save_appearance_settings'], 'save_ets_badgeos_discord_appearance_settings' ) ) {
				if ( $ets_badgeos_discord_connect_button_bg_color ) {
					update_option( 'ets_badgeos_discord_connect_button_bg_color', $ets_badgeos_discord_connect_button_bg_color );
				}
				if ( $ets_badgeos_discord_disconnect_button_bg_color ) {
					update_option( 'ets_badgeos_discord_disconnect_button_bg_color', $ets_badgeos_discord_disconnect_button_bg_color );
				}
				if ( $ets_badgeos_loggedout_btn_text ) {
					update_option( 'ets_badgeos_discord_non_login_button_text', $ets_badgeos_loggedout_btn_text );
				}
				if ( $ets_badgeos_loggedin_btn_text ) {
					update_option( 'ets_badgeos_discord_loggedin_button_text', $ets_badgeos_loggedin_btn_text );
				}
				if ( $ets_badgeos_discord_disconnect_btn_text ) {
					update_option( 'ets_badgeos_discord_disconnect_button_text', $ets_badgeos_discord_disconnect_btn_text );
				}
				$message = esc_html__( 'Your settings flushed successfully.', 'connect-badgeos-to-discord' );
				if ( isset( $_POST['current_url'] ) ) {
					$pre_location = sanitize_text_field( $_POST['current_url'] ) . '&save_settings_msg=' . $message . '#ets_badgeos_discord_appearance';
					wp_safe_redirect( $pre_location );
				}
			}
		}

	}

}
