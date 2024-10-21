<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar.php' );

/**
 *
 */
class Vc_Navbar_Frontend extends Vc_Navbar {
	/**
	 * @var array
	 */
	protected $controls = array(
		'add_element',
		'templates',
		'pages',
		'switch_sidebar',
		'toggle_mode',
		'custom_css',
		'screen_size',
		'light_dark',
		'help',
		'save_update',
		'view_post',
	);
	/**
	 * @var string
	 */
	protected $controls_filter_name = 'vc_nav_front_controls';
	/**
	 * @var string
	 */
	protected $brand_url = 'https://liquid-themes.com/?utm_campaign=VCplugin&utm_source=vc_user&utm_medium=frontend_editor';

	/**
	 * @var string
	 */
	protected $css_class = 'vc_navbar vc_navbar-frontend';

	/**
	 * @return string
	 */
	public function getControlScreenSize() {
		$disable_responsive = vc_settings()->get( 'not_responsive_css' );
		if ( '1' !== $disable_responsive ) {
			$screen_sizes = apply_filters( 'wpb_navbar_getControlScreenSize', array(
				array(
					'title' => esc_html__( 'Desktop', 'js_composer' ),
					'size' => '100%',
					'key' => 'default',
					'active' => true,
				),
				array(
					'title' => esc_html__( 'Tablet landscape mode', 'js_composer' ),
					'size' => '1024px',
					'key' => 'landscape-tablets',
				),
				array(
					'title' => esc_html__( 'Tablet portrait mode', 'js_composer' ),
					'size' => '768px',
					'key' => 'portrait-tablets',
				),
				// array(
				// 	'title' => esc_html__( 'Smartphone landscape mode', 'js_composer' ),
				// 	'size' => '480px',
				// 	'key' => 'landscape-smartphones',
				// ),
				array(
					'title' => esc_html__( 'Smartphone portrait mode', 'js_composer' ),
					'size' => '375px',
					'key' => 'portrait-smartphones',
				),
			));
			$output = '<li>' . '<div class="vc_dropdown" id="vc_screen-size-control">' . '<a href="#" class="vc_dropdown-toggle vc_icon-btn"' . ' title="' . esc_attr__( 'Responsive preview', 'js_composer' ) . '"><i class="la la-desktop vc_current-layout-icon"' . ' id="vc_screen-size-current"></i></a>' . '<ul class="vc_dropdown-list">';
			$screen = current( $screen_sizes );
			while ( $screen ) {
				$output .= '<li><a href="#" title="' . esc_attr( $screen['title'] ) . '"' . ' class="vc_screen-width la vc-c-icon-layout_' . esc_attr( $screen['key'] ) . ( isset( $screen['active'] ) && $screen['active'] ? ' active' : '' ) . '" data-size="' . esc_attr( $screen['size'] ) . '"></a></li>';
				next( $screen_sizes );
				$screen = current( $screen_sizes );
			}
			$output .= '</ul></div></li>';

			return $output;
		}

		return '';
	}


	/**
	 * @return string
	 */
	public function getControlHelp() {
		return '<li>' . '<a class="vc_icon-btn" title="' . esc_attr__( 'WPBakery Page Builder', 'js_composer' ) . '" href="' . esc_url( $this->brand_url ) . '" target="_blank"><i class="la la-question-circle"></i></a>' . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlLightDark() {
		return '<li>' . '<a class="vc_icon-btn lqd-switch-lightdark" title="' . esc_attr__( 'Switch Light/Dark Mode', 'js_composer' ) . '" href="javascript:;"><i class="la la-moon"></i><i class="la la-sun"></i></a>' . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlSwitchSidebar() {
		return '<li>' . '<a class="vc_icon-btn lqd-switch-sidebar" title="' . esc_attr__( 'Switch Sidebar', 'js_composer' ) . '" href="javascript:;"><i class="la la-columns"></i></a>' . '</li>';
	}

	/**
	 * @return string
	 */
	public function getControlToggleMode() {
		return '<li>' . '<a class="vc_icon-btn" id="vc_guides-toggle-button" title="' . esc_attr__( 'Toggle Controls', 'js_composer' ) . '" href="javascript:;"><i class="las la-border-none"></i></a>' . '</li>';
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getControlSaveUpdate() {
		$post = $this->post();
		$post_type = get_post_type_object( $this->post->post_type );
		$can_publish = current_user_can( $post_type->cap->publish_posts );
		ob_start();
		?>
		<li class="vc_navbar-saveupdate">
			<button data-url="<?php echo esc_attr( get_edit_post_link( $post->ID ) . '&wpb_vc_js_status=true&classic-editor' ); ?>"
					class="vc_btn vc_btn-default vc_btn-sm vc_navbar-btn vc_btn-backend-editor" id="vc_button-cancel"
					title="<?php esc_attr_e( 'Cancel all changes and return to WP dashboard', 'js_composer' ); ?>">
				<?php
				echo vc_user_access()->part( 'backend_editor' )->can()->get() ? esc_html__( 'Backend Editor', 'js_composer' ) : esc_html__( 'Edit', 'js_composer' );
				?>
			</button>
			<?php
			if ( ! in_array( $post->post_status, array(
				'publish',
				'future',
				'private',
			), true ) ) :
				?>
				<?php if ( 'draft' === $post->post_status ) : ?>
				<button type="button" class="vc_btn vc_btn-default vc_btn-sm vc_navbar-btn vc_btn-save-draft"
						id="vc_button-save-draft"
						title="<?php esc_attr_e( 'Save Draft', 'js_composer' ); ?>"><?php esc_html_e( 'Save Draft', 'js_composer' ); ?></button>
			<?php elseif ( 'pending' === $post->post_status && $can_publish ) : ?>
				<button type="button" class="vc_btn vc_btn-primary vc_btn-sm vc_navbar-btn vc_btn-save"
						id="vc_button-save-as-pending"
						title="<?php esc_attr_e( 'Save as Pending', 'js_composer' ); ?>"><?php esc_html_e( 'Save as Pending', 'js_composer' ); ?></button>
			<?php endif ?>
				<?php if ( $can_publish ) : ?>
				<button type="button" class="vc_btn vc_btn-primary vc_btn-sm vc_navbar-btn vc_btn-save"
						id="vc_button-update" title="<?php esc_attr_e( 'Publish', 'js_composer' ); ?>"
						data-change-status="publish"><?php esc_html_e( 'Publish', 'js_composer' ); ?></button>
			<?php else : ?>
				<button type="button" class="vc_btn vc_btn-primary vc_btn-sm vc_navbar-btn vc_btn-save"
						id="vc_button-update" title="<?php esc_attr_e( 'Submit for Review', 'js_composer' ); ?>"
						data-change-status="pending"><?php esc_html_e( 'Submit for Review', 'js_composer' ); ?></button>
			<?php endif ?>
			<?php else : ?>
				<button type="button" class="vc_btn vc_btn-primary vc_btn-sm vc_navbar-btn vc_btn-save"
						id="vc_button-update"
						title="<?php esc_attr_e( 'Update', 'js_composer' ); ?>"><?php esc_html_e( 'Update', 'js_composer' ); ?></button>
			<?php endif ?>
		</li>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * @return string
	 */
	public function getControlViewPost() {
		return '<li class="vc_navbar-back">' . '<a href="' . esc_url( get_permalink( $this->post() ) ) . '" class="vc_icon-btn vc_back-button"' . ' title="' . esc_attr__( 'Exit WPBakery Page Builder edit mode', 'js_composer' ) . '"><i class="la la-times"></i></a>' . '</li>';
	}
}
