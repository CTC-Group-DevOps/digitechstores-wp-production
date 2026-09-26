<?php
/**
 * Compatibility with "Members" WordPress plugin.
 *
 * @see https://wordpress.org/plugins/members
 *
 * @since 9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Vendor_Members
 *
 * @since 9.0
 */
class Vc_Vendor_Members {
	/**
	 * Capability prefix used by WPBakery.
	 *
	 * @var string
	 */
	const CAP_PREFIX = 'vc_access_rules_';

	/**
	 * Preserved capability states.
	 *
	 * @var array
	 */
	protected $preserved_states = [];

	/**
	 * Load Members plugin integration.
	 *
	 * @since 9.0
	 */
	public function load() {
		add_action( 'members_register_cap_groups', [
			$this,
			'register_cap_group',
		] );

		add_action( 'members_register_caps', [
			$this,
			'register_caps',
		] );

		add_action( 'admin_init', [
			$this,
			'preserve_states_before_save',
		], 5 );

		add_action( 'members_role_updated', [
			$this,
			'restore_states_after_save',
		] );
	}

	/**
	 * Register WPBakery capability group.
	 *
	 * @since 9.0
	 */
	public function register_cap_group() {
		if ( ! function_exists( 'members_register_cap_group' ) ) {
			return;
		}

		members_register_cap_group(
			'plugin-wpbakery',
			[
				'label'    => esc_html__( 'WPBakery Page Builder', 'js_composer' ),
				'icon'     => 'dashicons-screenoptions',
				'priority' => 11,
			]
		);
	}

	/**
	 * Register WPBakery capabilities.
	 *
	 * @since 9.0
	 */
	public function register_caps() {
		if ( ! function_exists( 'members_register_cap' ) ) {
			return;
		}

		global $wp_roles;

		if ( empty( $wp_roles ) ) {
			return;
		}

		$registered = [];

		foreach ( $wp_roles->roles as $role_data ) {
			if ( empty( $role_data['capabilities'] ) ) {
				continue;
			}

			foreach ( $role_data['capabilities'] as $cap => $granted ) {
				if ( isset( $registered[ $cap ] ) ) {
					continue;
				}

				if ( 0 !== strpos( $cap, self::CAP_PREFIX ) ) {
					continue;
				}

				members_register_cap(
					$cap,
					[
						'label' => $this->format_cap_label( $cap ),
						'group' => 'plugin-wpbakery',
					]
				);

				$registered[ $cap ] = true;
			}
		}
	}

	/**
	 * Preserve state capabilities before Members modifies them.
	 *
	 * @since 9.0
	 */
	public function preserve_states_before_save() {
		if ( ! $this->is_members_role_save_request() ) {
			return;
		}

		global $wp_roles;

		if ( empty( $wp_roles ) ) {
			return;
		}

		foreach ( $wp_roles->roles as $role_name => $role_data ) {
			if ( empty( $role_data['capabilities'] ) ) {
				continue;
			}

			foreach ( $role_data['capabilities'] as $cap => $value ) {
				if ( ! $this->is_state_capability( $cap ) || ! is_string( $value ) ) {
					continue;
				}

				if ( ! isset( $this->preserved_states[ $role_name ] ) ) {
					$this->preserved_states[ $role_name ] = [];
				}

				$this->preserved_states[ $role_name ][ $cap ] = $value;
			}
		}
	}

	/**
	 * Restore state capabilities after Members saves.
	 *
	 * @since 9.0
	 * @param string $role_name Role name.
	 */
	public function restore_states_after_save( $role_name ) {
		if ( ! isset( $this->preserved_states[ $role_name ] ) ) {
			return;
		}

		global $wp_roles;

		if ( empty( $wp_roles ) ) {
			return;
		}

		$needs_save = false;

		foreach ( $this->preserved_states[ $role_name ] as $state_cap => $original_value ) {
			if ( ! $this->has_granted_sub_capabilities( $role_name, $state_cap ) ) {
				continue;
			}

			$wp_roles->roles[ $role_name ]['capabilities'][ $state_cap ] = $original_value;

			if ( isset( $wp_roles->role_objects[ $role_name ] ) ) {
				$wp_roles->role_objects[ $role_name ]->capabilities[ $state_cap ] = $original_value;
			}

			$needs_save = true;
		}

		if ( $needs_save ) {
			update_option( $wp_roles->role_key, $wp_roles->roles );
		}

		unset( $this->preserved_states[ $role_name ] );
	}

	/**
	 * Check if current request is Members role save.
	 *
	 * @since 9.0
	 * @return bool
	 */
	protected function is_members_role_save_request() {
		if ( empty( $_POST['members_edit_role_nonce'] ) ) {
			return false;
		}

		return wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['members_edit_role_nonce'] ) ), 'edit_role' );
	}

	/**
	 * Check if capability is a state capability.
	 *
	 * @since 9.0
	 * @param string $cap Capability name.
	 * @return bool
	 */
	protected function is_state_capability( $cap ) {
		return 0 === strpos( $cap, self::CAP_PREFIX ) && false === strpos( $cap, '/' );
	}

	/**
	 * Check if role has granted sub-capabilities.
	 *
	 * @since 9.0
	 * @param string $role_name Role name.
	 * @param string $state_cap State capability.
	 * @return bool
	 */
	protected function has_granted_sub_capabilities( $role_name, $state_cap ) {
		global $wp_roles;

		if ( empty( $wp_roles->roles[ $role_name ]['capabilities'] ) ) {
			return false;
		}

		$prefix = $state_cap . '/';

		foreach ( $wp_roles->roles[ $role_name ]['capabilities'] as $cap => $granted ) {
			if ( 0 === strpos( $cap, $prefix ) && $granted ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Format capability label.
	 *
	 * @since 9.0
	 * @param string $cap Capability name.
	 * @return string
	 */
	protected function format_cap_label( $cap ) {
		$label = str_replace( self::CAP_PREFIX, '', $cap );
		$label = str_replace( [ '_', '/' ], ' ', $label );

		return ucwords( $label );
	}
}
