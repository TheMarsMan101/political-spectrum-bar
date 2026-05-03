<?php
/**
 * Plugin Name:       Political Spectrum Bar
 * Plugin URI:        https://example.com/political-spectrum-bar
 * Description:       Create horizontal Liberal ↔ Conservative (or whatever you want) spectrum bars with a positioned marker and a pointer triangle. Build them on a dedicated admin page and drop them anywhere with a shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            TheMarsMan101
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       political-spectrum-bar
 *
 * @package Political_Spectrum_Bar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PSB_VERSION', '1.0.0' );
define( 'PSB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PSB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PSB_CPT', 'psb_spectrum' );

/**
 * ──────────────────────────────────────────────────────────────────
 * Register the custom post type — each "Spectrum Bar" is a post.
 * ──────────────────────────────────────────────────────────────────
 */
function psb_register_cpt() {
	register_post_type(
		PSB_CPT,
		array(
			'labels' => array(
				'name'               => __( 'Spectrum Bars', 'political-spectrum-bar' ),
				'singular_name'      => __( 'Spectrum Bar', 'political-spectrum-bar' ),
				'menu_name'          => __( 'Spectrum Bars', 'political-spectrum-bar' ),
				'add_new'            => __( 'Add New Bar', 'political-spectrum-bar' ),
				'add_new_item'       => __( 'Add New Spectrum Bar', 'political-spectrum-bar' ),
				'edit_item'          => __( 'Edit Spectrum Bar', 'political-spectrum-bar' ),
				'new_item'           => __( 'New Spectrum Bar', 'political-spectrum-bar' ),
				'view_item'          => __( 'View Spectrum Bar', 'political-spectrum-bar' ),
				'all_items'          => __( 'All Spectrum Bars', 'political-spectrum-bar' ),
				'search_items'       => __( 'Search Spectrum Bars', 'political-spectrum-bar' ),
				'not_found'          => __( 'No spectrum bars found.', 'political-spectrum-bar' ),
				'not_found_in_trash' => __( 'No spectrum bars found in Trash.', 'political-spectrum-bar' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'has_archive'         => false,
			'rewrite'             => false,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-leftright',
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'psb_register_cpt' );

/**
 * ──────────────────────────────────────────────────────────────────
 * Defaults — used when a bar has no saved value for a field.
 * ──────────────────────────────────────────────────────────────────
 */
function psb_defaults() {
	return array(
		'position'       => 50,
		'left_label'     => __( 'Liberal', 'political-spectrum-bar' ),
		'right_label'    => __( 'Conservative', 'political-spectrum-bar' ),
		'bar_color'      => '#0F2A44',
		'bar_gradient'   => 1,
		'bar_color_left' => '#2E6BB8',
		'bar_color_right'=> '#C8392E',
		'marker_color'   => '#FFFFFF',
		'marker_border'  => '#0F2A44',
		'arrow_color'    => '#0F2A44',
		'label_color'    => '#1C1C1C',
		'pointer_color'  => '#0F2A44',
		'pointer_label'  => '',
		'show_scale'     => 0,
	);
}

/**
 * ──────────────────────────────────────────────────────────────────
 * Meta box on the edit screen.
 * ──────────────────────────────────────────────────────────────────
 */
function psb_add_meta_boxes() {
	add_meta_box(
		'psb_settings',
		__( 'Spectrum Bar Settings', 'political-spectrum-bar' ),
		'psb_render_meta_box',
		PSB_CPT,
		'normal',
		'high'
	);
	add_meta_box(
		'psb_shortcode',
		__( 'Shortcode', 'political-spectrum-bar' ),
		'psb_render_shortcode_box',
		PSB_CPT,
		'side',
		'high'
	);
	add_meta_box(
		'psb_preview',
		__( 'Live Preview', 'political-spectrum-bar' ),
		'psb_render_preview_box',
		PSB_CPT,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'psb_add_meta_boxes' );

/**
 * Shortcode sidebar meta box — shows the user exactly what to copy.
 */
function psb_render_shortcode_box( $post ) {
	if ( 'auto-draft' === $post->post_status ) {
		echo '<p>' . esc_html__( 'Save this bar to get its shortcode.', 'political-spectrum-bar' ) . '</p>';
		return;
	}
	$shortcode = sprintf( '[spectrum_bar id="%d"]', $post->ID );
	?>
	<p><?php esc_html_e( 'Paste this anywhere (posts, pages, widgets):', 'political-spectrum-bar' ); ?></p>
	<input type="text"
		readonly
		onclick="this.select();"
		value="<?php echo esc_attr( $shortcode ); ?>"
		style="width:100%; padding:8px; font-family: Menlo, Consolas, monospace; font-size: 13px; background: #f0f0f1; border: 1px solid #c3c4c7;" />
	<p style="margin-top:10px; font-size:12px; color:#646970;">
		<?php esc_html_e( 'Click the field to select, then copy with Ctrl/⌘+C.', 'political-spectrum-bar' ); ?>
	</p>
	<?php
}

/**
 * Live preview meta box — renders the bar with current saved values.
 */
function psb_render_preview_box( $post ) {
	if ( 'auto-draft' === $post->post_status ) {
		echo '<p>' . esc_html__( 'Save this bar to see a preview.', 'political-spectrum-bar' ) . '</p>';
		return;
	}
	echo '<p style="color:#646970; font-size:12px; margin-top:0;">' . esc_html__( 'Save changes to refresh the preview below.', 'political-spectrum-bar' ) . '</p>';
	echo '<div style="padding: 16px; background: #fff; border: 1px solid #dcdcde; border-radius: 4px;">';
	echo psb_render_bar( $post->ID );
	echo '</div>';
}

/**
 * Main settings meta box — all configurable fields for the bar.
 */
function psb_render_meta_box( $post ) {
	wp_nonce_field( 'psb_save_meta', 'psb_nonce' );
	$defaults = psb_defaults();
	$v        = psb_get_meta( $post->ID );

	// Guard — make sure every default exists.
	foreach ( $defaults as $k => $d ) {
		if ( ! isset( $v[ $k ] ) ) {
			$v[ $k ] = $d;
		}
	}
	?>
	<style>
		.psb-field-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px 24px; margin-top: 8px; }
		.psb-field { display: block; }
		.psb-field label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 13px; }
		.psb-field .psb-help { display: block; color: #646970; font-size: 12px; font-weight: normal; margin-top: 2px; }
		.psb-field input[type="text"],
		.psb-field input[type="number"],
		.psb-field input[type="color"] { width: 100%; box-sizing: border-box; }
		.psb-field input[type="color"] { height: 36px; padding: 2px; cursor: pointer; }
		.psb-row-full { grid-column: 1 / -1; }
		.psb-group-heading {
			grid-column: 1 / -1;
			margin: 18px 0 -4px;
			padding: 6px 10px;
			background: #0F2A44; color: #F5F0E8;
			font-size: 11px; letter-spacing: 0.15em; text-transform: uppercase; font-weight: 600;
			border-radius: 3px;
		}
		.psb-position-slider { display: flex; align-items: center; gap: 12px; }
		.psb-position-slider input[type="range"] { flex: 1; }
		.psb-position-slider input[type="number"] { width: 80px; flex-shrink: 0; }
		.psb-checkbox-row { display: flex; align-items: center; gap: 8px; }
		.psb-checkbox-row input { margin: 0; }
	</style>

	<div class="psb-field-grid">

		<div class="psb-group-heading"><?php esc_html_e( 'Position & Labels', 'political-spectrum-bar' ); ?></div>

		<div class="psb-field psb-row-full">
			<label for="psb_position"><?php esc_html_e( 'Marker Position', 'political-spectrum-bar' ); ?></label>
			<div class="psb-position-slider">
				<input type="range" id="psb_position_range" min="0" max="100" step="0.1"
					value="<?php echo esc_attr( $v['position'] ); ?>"
					oninput="document.getElementById('psb_position').value = this.value;" />
				<input type="number" id="psb_position" name="psb_position" min="0" max="100" step="0.1"
					value="<?php echo esc_attr( $v['position'] ); ?>"
					oninput="document.getElementById('psb_position_range').value = this.value;" />
				<span>%</span>
			</div>
			<span class="psb-help"><?php esc_html_e( '0 = far left (Liberal end). 100 = far right (Conservative end). Decimals allowed.', 'political-spectrum-bar' ); ?></span>
		</div>

		<div class="psb-field">
			<label for="psb_left_label"><?php esc_html_e( 'Left Label', 'political-spectrum-bar' ); ?></label>
			<input type="text" id="psb_left_label" name="psb_left_label" value="<?php echo esc_attr( $v['left_label'] ); ?>" />
		</div>

		<div class="psb-field">
			<label for="psb_right_label"><?php esc_html_e( 'Right Label', 'political-spectrum-bar' ); ?></label>
			<input type="text" id="psb_right_label" name="psb_right_label" value="<?php echo esc_attr( $v['right_label'] ); ?>" />
		</div>

		<div class="psb-field psb-row-full">
			<label for="psb_pointer_label"><?php esc_html_e( 'Marker Label (optional)', 'political-spectrum-bar' ); ?></label>
			<input type="text" id="psb_pointer_label" name="psb_pointer_label" value="<?php echo esc_attr( $v['pointer_label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. "You are here" or a candidate name', 'political-spectrum-bar' ); ?>" />
			<span class="psb-help"><?php esc_html_e( 'Appears below the pointer triangle. Leave blank for none.', 'political-spectrum-bar' ); ?></span>
		</div>

		<div class="psb-group-heading"><?php esc_html_e( 'Bar Colors', 'political-spectrum-bar' ); ?></div>

		<div class="psb-field psb-row-full">
			<div class="psb-checkbox-row">
				<input type="checkbox" id="psb_bar_gradient" name="psb_bar_gradient" value="1" <?php checked( 1, $v['bar_gradient'] ); ?> />
				<label for="psb_bar_gradient" style="margin-bottom:0;"><?php esc_html_e( 'Use left-to-right gradient (blue → red)', 'political-spectrum-bar' ); ?></label>
			</div>
			<span class="psb-help"><?php esc_html_e( 'Uncheck to use a single solid bar color instead.', 'political-spectrum-bar' ); ?></span>
		</div>

		<div class="psb-field">
			<label for="psb_bar_color_left"><?php esc_html_e( 'Gradient — Left Color', 'political-spectrum-bar' ); ?></label>
			<input type="color" id="psb_bar_color_left" name="psb_bar_color_left" value="<?php echo esc_attr( $v['bar_color_left'] ); ?>" />
		</div>

		<div class="psb-field">
			<label for="psb_bar_color_right"><?php esc_html_e( 'Gradient — Right Color', 'political-spectrum-bar' ); ?></label>
			<input type="color" id="psb_bar_color_right" name="psb_bar_color_right" value="<?php echo esc_attr( $v['bar_color_right'] ); ?>" />
		</div>

		<div class="psb-field psb-row-full">
			<label for="psb_bar_color"><?php esc_html_e( 'Solid Bar Color (when gradient is off)', 'political-spectrum-bar' ); ?></label>
			<input type="color" id="psb_bar_color" name="psb_bar_color" value="<?php echo esc_attr( $v['bar_color'] ); ?>" />
		</div>

		<div class="psb-group-heading"><?php esc_html_e( 'Arrows, Marker & Pointer', 'political-spectrum-bar' ); ?></div>

		<div class="psb-field">
			<label for="psb_arrow_color"><?php esc_html_e( 'End Arrow Color', 'political-spectrum-bar' ); ?></label>
			<input type="color" id="psb_arrow_color" name="psb_arrow_color" value="<?php echo esc_attr( $v['arrow_color'] ); ?>" />
		</div>

		<div class="psb-field">
			<label for="psb_label_color"><?php esc_html_e( 'End Labels Color', 'political-spectrum-bar' ); ?></label>
			<input type="color" id="psb_label_color" name="psb_label_color" value="<?php echo esc_attr( $v['label_color'] ); ?>" />
		</div>

		<div class="psb-field">
			<label for="psb_marker_color"><?php esc_html_e( 'Marker Ball — Fill', 'political-spectrum-bar' ); ?></label>
			<input type="color" id="psb_marker_color" name="psb_marker_color" value="<?php echo esc_attr( $v['marker_color'] ); ?>" />
		</div>

		<div class="psb-field">
			<label for="psb_marker_border"><?php esc_html_e( 'Marker Ball — Border', 'political-spectrum-bar' ); ?></label>
			<input type="color" id="psb_marker_border" name="psb_marker_border" value="<?php echo esc_attr( $v['marker_border'] ); ?>" />
		</div>

		<div class="psb-field psb-row-full">
			<label for="psb_pointer_color"><?php esc_html_e( 'Pointer Triangle Color', 'political-spectrum-bar' ); ?></label>
			<input type="color" id="psb_pointer_color" name="psb_pointer_color" value="<?php echo esc_attr( $v['pointer_color'] ); ?>" />
			<span class="psb-help"><?php esc_html_e( 'The filled triangle below the marker that points up at it.', 'political-spectrum-bar' ); ?></span>
		</div>

		<div class="psb-group-heading"><?php esc_html_e( 'Display Options', 'political-spectrum-bar' ); ?></div>

		<div class="psb-field psb-row-full">
			<div class="psb-checkbox-row">
				<input type="checkbox" id="psb_show_scale" name="psb_show_scale" value="1" <?php checked( 1, $v['show_scale'] ); ?> />
				<label for="psb_show_scale" style="margin-bottom:0;"><?php esc_html_e( 'Show numeric scale below the bar (0 / 25 / 50 / 75 / 100)', 'political-spectrum-bar' ); ?></label>
			</div>
		</div>

	</div>
	<?php
}

/**
 * Save meta values on post save.
 */
function psb_save_meta( $post_id ) {
	if ( ! isset( $_POST['psb_nonce'] ) || ! wp_verify_nonce( $_POST['psb_nonce'], 'psb_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( get_post_type( $post_id ) !== PSB_CPT ) {
		return;
	}

	// Text fields.
	$text_fields = array( 'left_label', 'right_label', 'pointer_label' );
	foreach ( $text_fields as $f ) {
		if ( isset( $_POST[ 'psb_' . $f ] ) ) {
			update_post_meta( $post_id, '_psb_' . $f, sanitize_text_field( wp_unslash( $_POST[ 'psb_' . $f ] ) ) );
		}
	}

	// Position (float 0–100).
	if ( isset( $_POST['psb_position'] ) ) {
		$pos = floatval( $_POST['psb_position'] );
		if ( $pos < 0 )   { $pos = 0; }
		if ( $pos > 100 ) { $pos = 100; }
		update_post_meta( $post_id, '_psb_position', $pos );
	}

	// Color fields.
	$color_fields = array( 'bar_color', 'bar_color_left', 'bar_color_right', 'marker_color', 'marker_border', 'arrow_color', 'label_color', 'pointer_color' );
	foreach ( $color_fields as $f ) {
		if ( isset( $_POST[ 'psb_' . $f ] ) ) {
			$val = sanitize_hex_color( $_POST[ 'psb_' . $f ] );
			if ( $val ) {
				update_post_meta( $post_id, '_psb_' . $f, $val );
			}
		}
	}

	// Checkboxes.
	$checkboxes = array( 'bar_gradient', 'show_scale' );
	foreach ( $checkboxes as $f ) {
		$val = ! empty( $_POST[ 'psb_' . $f ] ) ? 1 : 0;
		update_post_meta( $post_id, '_psb_' . $f, $val );
	}
}
add_action( 'save_post', 'psb_save_meta' );

/**
 * ──────────────────────────────────────────────────────────────────
 * Data helpers.
 * ──────────────────────────────────────────────────────────────────
 */

/**
 * Get all meta values for a given bar, falling back to defaults.
 */
function psb_get_meta( $post_id ) {
	$defaults = psb_defaults();
	$out      = array();
	foreach ( $defaults as $k => $d ) {
		$stored = get_post_meta( $post_id, '_psb_' . $k, true );
		if ( '' === $stored || null === $stored ) {
			$out[ $k ] = $d;
		} else {
			$out[ $k ] = $stored;
		}
	}
	return $out;
}

/**
 * ──────────────────────────────────────────────────────────────────
 * Render the bar as HTML (shared between shortcode, preview, and
 * admin list column).
 * ──────────────────────────────────────────────────────────────────
 */
function psb_render_bar( $post_id, $overrides = array() ) {
	$post = get_post( $post_id );
	if ( ! $post || get_post_type( $post ) !== PSB_CPT ) {
		return '';
	}

	$v = psb_get_meta( $post_id );
	if ( is_array( $overrides ) && ! empty( $overrides ) ) {
		$v = array_merge( $v, $overrides );
	}

	$position = max( 0, min( 100, floatval( $v['position'] ) ) );
	$uid      = 'psb-' . $post_id . '-' . wp_rand( 1000, 9999 );

	// Bar fill — gradient or solid.
	$bar_fill = ( ! empty( $v['bar_gradient'] ) )
		? sprintf( 'linear-gradient(90deg, %s 0%%, %s 100%%)', esc_attr( $v['bar_color_left'] ), esc_attr( $v['bar_color_right'] ) )
		: esc_attr( $v['bar_color'] );

	ob_start();
	?>
	<div class="psb-wrap" id="<?php echo esc_attr( $uid ); ?>"
		style="--psb-bar-fill: <?php echo $bar_fill; ?>;
			--psb-arrow: <?php echo esc_attr( $v['arrow_color'] ); ?>;
			--psb-label: <?php echo esc_attr( $v['label_color'] ); ?>;
			--psb-marker: <?php echo esc_attr( $v['marker_color'] ); ?>;
			--psb-marker-border: <?php echo esc_attr( $v['marker_border'] ); ?>;
			--psb-pointer: <?php echo esc_attr( $v['pointer_color'] ); ?>;
			--psb-position: <?php echo esc_attr( $position ); ?>%;">

		<div class="psb-labels">
			<span class="psb-label psb-label-left"><?php echo esc_html( $v['left_label'] ); ?></span>
			<span class="psb-label psb-label-right"><?php echo esc_html( $v['right_label'] ); ?></span>
		</div>

		<div class="psb-bar-row">
			<span class="psb-arrow psb-arrow-left" aria-hidden="true">
				<svg viewBox="0 0 12 16" width="12" height="16" xmlns="http://www.w3.org/2000/svg">
					<polygon points="12,0 0,8 12,16" fill="currentColor"/>
				</svg>
			</span>

			<div class="psb-bar-track">
				<div class="psb-bar-fill"></div>
				<div class="psb-marker"
					role="img"
					aria-label="<?php echo esc_attr( sprintf( __( 'Position: %s%% from left', 'political-spectrum-bar' ), round( $position, 1 ) ) ); ?>"
				></div>
			</div>

			<span class="psb-arrow psb-arrow-right" aria-hidden="true">
				<svg viewBox="0 0 12 16" width="12" height="16" xmlns="http://www.w3.org/2000/svg">
					<polygon points="0,0 12,8 0,16" fill="currentColor"/>
				</svg>
			</span>
		</div>

		<div class="psb-pointer-row" aria-hidden="true">
			<span class="psb-pointer-spacer"></span>
			<div class="psb-pointer-track">
				<div class="psb-pointer"></div>
				<?php if ( ! empty( $v['pointer_label'] ) ) : ?>
					<div class="psb-pointer-label"><?php echo esc_html( $v['pointer_label'] ); ?></div>
				<?php endif; ?>
			</div>
			<span class="psb-pointer-spacer"></span>
		</div>

		<?php if ( ! empty( $v['show_scale'] ) ) : ?>
			<div class="psb-scale" aria-hidden="true">
				<span class="psb-scale-spacer"></span>
				<div class="psb-scale-track">
					<span>0</span><span>25</span><span>50</span><span>75</span><span>100</span>
				</div>
				<span class="psb-scale-spacer"></span>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * ──────────────────────────────────────────────────────────────────
 * Shortcode: [spectrum_bar id="123"]
 *
 * Optional inline overrides:
 *   [spectrum_bar id="123" position="42" pointer_label="Here"]
 * ──────────────────────────────────────────────────────────────────
 */
function psb_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'id'             => 0,
			// Optional per-instance overrides:
			'position'       => '',
			'left_label'     => '',
			'right_label'    => '',
			'pointer_label'  => '',
			'bar_color'      => '',
			'bar_color_left' => '',
			'bar_color_right'=> '',
			'marker_color'   => '',
			'marker_border'  => '',
			'arrow_color'    => '',
			'label_color'    => '',
			'pointer_color'  => '',
			'show_scale'     => '',
			'bar_gradient'   => '',
		),
		$atts,
		'spectrum_bar'
	);

	$id = absint( $atts['id'] );
	if ( $id <= 0 ) {
		return '<!-- spectrum_bar: missing or invalid id -->';
	}

	// Build overrides array (only include non-empty values).
	$overrides = array();
	foreach ( $atts as $k => $val ) {
		if ( 'id' === $k || '' === $val ) {
			continue;
		}
		if ( 'position' === $k ) {
			$overrides[ $k ] = max( 0, min( 100, floatval( $val ) ) );
		} elseif ( in_array( $k, array( 'show_scale', 'bar_gradient' ), true ) ) {
			$overrides[ $k ] = ( '1' === (string) $val || 'true' === strtolower( $val ) || 'yes' === strtolower( $val ) ) ? 1 : 0;
		} elseif ( in_array( $k, array( 'bar_color', 'bar_color_left', 'bar_color_right', 'marker_color', 'marker_border', 'arrow_color', 'label_color', 'pointer_color' ), true ) ) {
			$hex = sanitize_hex_color( $val );
			if ( $hex ) { $overrides[ $k ] = $hex; }
		} else {
			$overrides[ $k ] = sanitize_text_field( $val );
		}
	}

	return psb_render_bar( $id, $overrides );
}
add_shortcode( 'spectrum_bar', 'psb_shortcode' );

/**
 * ──────────────────────────────────────────────────────────────────
 * Front-end stylesheet.
 * ──────────────────────────────────────────────────────────────────
 */
function psb_enqueue_styles() {
	wp_enqueue_style(
		'psb-style',
		PSB_PLUGIN_URL . 'assets/style.css',
		array(),
		PSB_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'psb_enqueue_styles' );
// Also load on admin screens so the live preview matches the front end.
add_action( 'admin_enqueue_scripts', 'psb_enqueue_styles' );

/**
 * ──────────────────────────────────────────────────────────────────
 * Admin columns — show a tiny visual indicator + shortcode per bar.
 * ──────────────────────────────────────────────────────────────────
 */
function psb_admin_columns( $cols ) {
	$new = array(
		'cb'             => $cols['cb'],
		'title'          => $cols['title'],
		'psb_position'   => __( 'Position', 'political-spectrum-bar' ),
		'psb_shortcode'  => __( 'Shortcode', 'political-spectrum-bar' ),
		'date'           => isset( $cols['date'] ) ? $cols['date'] : __( 'Date', 'political-spectrum-bar' ),
	);
	return $new;
}
add_filter( 'manage_' . PSB_CPT . '_posts_columns', 'psb_admin_columns' );

function psb_admin_column_content( $column, $post_id ) {
	$v = psb_get_meta( $post_id );
	switch ( $column ) {
		case 'psb_position':
			echo esc_html( round( floatval( $v['position'] ), 1 ) ) . '%';
			break;
		case 'psb_shortcode':
			printf(
				'<input type="text" readonly onclick="this.select();" value="%s" style="width:180px; padding:4px 6px; font-family: Menlo, monospace; font-size:12px; background:#f0f0f1; border:1px solid #c3c4c7;" />',
				esc_attr( sprintf( '[spectrum_bar id="%d"]', $post_id ) )
			);
			break;
	}
}
add_action( 'manage_' . PSB_CPT . '_posts_custom_column', 'psb_admin_column_content', 10, 2 );

/**
 * Help link below the plugin title in the plugins page.
 */
function psb_plugin_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'edit.php?post_type=' . PSB_CPT ) ),
		esc_html__( 'Manage Bars', 'political-spectrum-bar' )
	);
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'psb_plugin_action_links' );
