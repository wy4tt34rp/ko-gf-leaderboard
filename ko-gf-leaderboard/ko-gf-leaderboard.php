<?php
/**
 * Plugin Name: KO GF Leaderboard
 * Description: Gravity Forms leaderboard (bar graph) for a specific choice field, usable as both a merge tag and shortcode.
 * Author: KO
 * Version: 1.3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CONFIG: Set your default Form ID, Field ID, and default bar color here.
 */
define( 'KO_2025_LEADERBOARD_FORM_ID', 23 );   // CHANGE to your form ID
define( 'KO_2025_LEADERBOARD_FIELD_ID', 5 );   // CHANGE to your field ID
define( 'KO_GF_LEADERBOARD_BAR_COLOR', '#0073aa' ); // Default bar color (used for merge tag / fallback)

/**
 * Core function: generate the leaderboard HTML.
 *
 * @param int    $form_id
 * @param int    $field_id
 * @param bool   $show_counts  Whether to show (N) counts beside percentages.
 * @param bool   $show_total   Whether to show the "Total submissions: X" line.
 * @param string $bar_color    HEX color for the bar (e.g. #ff0000).
 *
 * @return string
 */
function ko_2025_get_leaderboard_html( $form_id = 0, $field_id = 0, $show_counts = true, $show_total = true, $bar_color = '' ) {

	if ( ! class_exists( 'GFAPI' ) ) {
		return '<p><em>Leaderboard unavailable: Gravity Forms API not found.</em></p>';
	}

	$form_id  = $form_id  ? absint( $form_id )  : KO_2025_LEADERBOARD_FORM_ID;
	$field_id = $field_id ? absint( $field_id ) : KO_2025_LEADERBOARD_FIELD_ID;

	if ( ! $form_id || ! $field_id ) {
		return '<p><em>Leaderboard unavailable: Form ID or Field ID not configured.</em></p>';
	}

	$form = GFAPI::get_form( $form_id );
	if ( ! $form ) {
		return '<p><em>Leaderboard unavailable: Form not found.</em></p>';
	}

	// Make sure the field exists on the form.
	$field_obj = null;
	if ( ! empty( $form['fields'] ) ) {
		foreach ( $form['fields'] as $f ) {
			if ( (int) $f->id === (int) $field_id ) {
				$field_obj = $f;
				break;
			}
		}
	}
	if ( ! $field_obj ) {
		return '<p><em>Leaderboard unavailable: Field ID ' . esc_html( $field_id ) . ' not found on this form.</em></p>';
	}

	// Normalize bar color.
	$bar_color = ko_gf_leaderboard_normalize_hex_color( $bar_color );
	if ( ! $bar_color ) {
		$bar_color = ko_gf_leaderboard_normalize_hex_color( KO_GF_LEADERBOARD_BAR_COLOR );
	}

	// Tally entries.
	$counts = array();
	$total  = 0;

	$search_criteria = array(
		'status' => 'active',
	);
	$sorting = null;
	$paging  = array(
		'offset'    => 0,
		'page_size' => 200,
	);

	do {
		$entries = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging );

		if ( is_wp_error( $entries ) ) {
			return '<p><em>Leaderboard error: ' . esc_html( $entries->get_error_message() ) . '</em></p>';
		}

		if ( empty( $entries ) ) {
			break;
		}

		foreach ( $entries as $e ) {
			$val = rgar( $e, (string) $field_id );
			if ( $val === '' || $val === null ) {
				continue;
			}

			if ( ! isset( $counts[ $val ] ) ) {
				$counts[ $val ] = 0;
			}
			$counts[ $val ]++;
			$total++;
		}

		$paging['offset'] += $paging['page_size'];

	} while ( count( $entries ) === $paging['page_size'] );

	if ( $total === 0 ) {
		return '<div class="ko-2025-leaderboard"><p><em>No submissions yet.</em></p></div>';
	}

	// Sort highest to lowest.
	arsort( $counts );

	ob_start();
	?>

<style>
	.ko-2025-leaderboard {
		margin: 20px 0;
		padding: 15px;
		border-radius: 8px;
		background: transparent;
		font-family: 'Kiro', Helvetica, Arial, Lucida, sans-serif;
	}

	.ko-2025-leaderboard h3 {
		margin-top: 0;
		margin-bottom: 10px;
		font-size: 1.3em;
		font-family: 'Kiro', Helvetica, Arial, Lucida, sans-serif;
	}

	/* DESKTOP / DEFAULT */

	.ko-2025-leaderboard-row {
		display: flex;
		align-items: flex-start; /* top-align contents */
		gap: 4px;               /* tighter horizontal spacing */
		margin-bottom: 12px;     /* tighter vertical spacing */
		margin-top:24px;
	}

	.ko-2025-leaderboard-label {
		flex: 0 0 270px;        /* wider label column */
		font-weight: 600;
		font-size: 0.95em;
		line-height: 1.2;       /* reduces tall multi-line spacing */
	}

	/* Wraps bar + percentage on one line */
	.ko-2025-leaderboard-bar-and-value {
		display: flex;
		align-items: center;
		gap: 8px;
		width: 100%;
	}

	.ko-2025-leaderboard-bar-wrap {
		flex: 1 1 auto;         /* bar takes all remaining space */
		min-width: 250px;       /* safety so it doesn't collapse */
		background: #e1e1e1;
		border-radius: 999px;
		overflow: hidden;
		height: 18px;
	}

	.ko-2025-leaderboard-bar {
		height: 100%;
		border-radius: 999px;
		background: #0073aa; /* fallback – inline style overrides */
		transition: width 0.6s ease;
	}

	.ko-2025-leaderboard-value {
		flex: 0 0 70px;        /* closer to bar, less wasted space */
		text-align: right;
		font-size: 0.9em;
		white-space: nowrap;
		font-weight: 900;
	}

	/* MOBILE */

	@media (max-width: 800px) {

		/* Stack label on top, bar + percent under it */
		.ko-2025-leaderboard-row {
			flex-direction: column;
			align-items: stretch;
			gap: 4px;
			margin-bottom: 8px;
		}

		.ko-2025-leaderboard-label {
			flex: none;
			font-size: 1.1em;
			font-weight: 600;
			line-height: 1.2;
			margin: 12px 0 0;
		}

		.ko-2025-leaderboard-bar-and-value {
			display: flex;
			align-items: center;
			gap: 8px;
			width: 100%;
		}

		.ko-2025-leaderboard-bar-wrap {
			flex: 1;
			min-width: 0;
			width: 100%;
		}

		.ko-2025-leaderboard-value {
			flex: 0 0 auto;
			font-size: 1.1em;
			font-weight: 700;
			text-align: right;
			white-space: nowrap;
		}
	}
</style>

	<div class="ko-2025-leaderboard">
		<h3>Decorations Contest Leaderboard</h3>

		<?php foreach ( $counts as $label => $count ) :
			$percent = $total > 0 ? round( ( $count / $total ) * 100, 1 ) : 0;
		?>
			<div class="ko-2025-leaderboard-row">
				<div class="ko-2025-leaderboard-label">
					<?php echo esc_html( $label ); ?>
				</div>
<div class="ko-2025-leaderboard-bar-and-value">

    <div class="ko-2025-leaderboard-bar-wrap">
        <div class="ko-2025-leaderboard-bar" style="width: <?php echo esc_attr( $percent ); ?>%; background: <?php echo esc_attr( $bar_color ); ?>;"></div>
    </div>

    <div class="ko-2025-leaderboard-value">
        <?php echo esc_html( $percent ) . '%'; ?>
    </div>

</div>
			</div>
		<?php endforeach; ?>

		<?php if ( $show_total ) : ?>
			<p style="margin-top:10px;font-size:0.85em;opacity:0.7;">
				Total submissions: <?php echo esc_html( $total ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Normalize a HEX color (very basic sanitization).
 *
 * @param string $color
 * @return string '' or valid #rrggbb/#rgb
 */
function ko_gf_leaderboard_normalize_hex_color( $color ) {
	$color = trim( (string) $color );
	if ( $color === '' ) {
		return '';
	}

	// Remove everything except hex chars and '#'.
	$color = preg_replace( '/[^#a-fA-F0-9]/', '', $color );

	if ( $color === '' ) {
		return '';
	}

	// Ensure leading #.
	if ( $color[0] !== '#' ) {
		$color = '#' . $color;
	}

	// Length must be 4 (#rgb) or 7 (#rrggbb).
	$len = strlen( $color );
	if ( $len !== 4 && $len !== 7 ) {
		return '';
	}

	return $color;
}

/**
 * Register custom merge tag in the GF UI.
 */
add_filter( 'gform_custom_merge_tags', function( $merge_tags, $form_id, $fields, $element_id ) {
	$merge_tags[] = array(
		'label' => 'KO GF Leaderboard',
		'tag'   => '{ko_2025_leaderboard}',
	);
	return $merge_tags;
}, 10, 4 );

/**
 * Replace {ko_2025_leaderboard} in confirmations/notifications text.
 * Merge tag version uses default behavior: show counts + total, default bar color.
 */
add_filter( 'gform_replace_merge_tags', function( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

	$has_merge_tag = strpos( $text, '{ko_2025_leaderboard}' ) !== false;

	if ( ! $has_merge_tag ) {
		return $text;
	}

	$html = ko_2025_get_leaderboard_html( 0, 0, true, true, KO_GF_LEADERBOARD_BAR_COLOR );
	$text = str_replace( '{ko_2025_leaderboard}', $html, $text );

	return $text;
}, 10, 7 );

/**
 * Shortcode: [ko_2025_leaderboard form_id="23" field_id="5" show_counts="yes" show_total="yes" bar_color="#ff0000"]
 * Can be used on any page OR in confirmation text.
 */
add_shortcode( 'ko_2025_leaderboard', function( $atts ) {

	$atts = shortcode_atts(
		array(
			'form_id'     => KO_2025_LEADERBOARD_FORM_ID,
			'field_id'    => KO_2025_LEADERBOARD_FIELD_ID,
			'show_counts' => 'yes',
			'show_total'  => 'yes',
			'bar_color'   => '',
		),
		$atts
	);

	$form_id  = absint( $atts['form_id'] );
	$field_id = absint( $atts['field_id'] );

	$normalize_bool = function( $val, $default = true ) {
		$val = strtolower( trim( (string) $val ) );
		if ( in_array( $val, array( '0', 'no', 'false', 'off' ), true ) ) {
			return false;
		}
		if ( in_array( $val, array( '1', 'yes', 'true', 'on' ), true ) ) {
			return true;
		}
		return $default;
	};

	$show_counts = $normalize_bool( $atts['show_counts'], true );
	$show_total  = $normalize_bool( $atts['show_total'], true );
	$bar_color   = $atts['bar_color'];

	return ko_2025_get_leaderboard_html( $form_id, $field_id, $show_counts, $show_total, $bar_color );
} );

/**
 * Ensure shortcodes (including [ko_2025_leaderboard]) run in confirmation text.
 */
add_filter( 'gform_confirmation', function( $confirmation, $form, $entry, $is_ajax ) {

	if ( is_string( $confirmation ) ) {
		$confirmation = do_shortcode( $confirmation );
	}

	return $confirmation;
}, 15, 4 );
