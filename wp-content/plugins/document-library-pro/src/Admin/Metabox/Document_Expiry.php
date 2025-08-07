<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin\Metabox;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Conditional;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Util;
use Barn2\Plugin\Document_Library_Pro\Post_Type;
use Barn2\Plugin\Document_Library_Pro\Document;

defined( 'ABSPATH' ) || exit;

/**
 * Document Expiry post setting
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Expiry implements Registerable, Standard_Service, Conditional {

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		return Util::is_admin();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'save_post_' . Post_Type::POST_TYPE_SLUG, [ $this, 'save' ] );
		add_action( 'post_submitbox_misc_actions', [ $this, 'render' ] );
	}

	/**
	 * Save the metabox values
	 *
	 * @param mixed $post_id
	 */
	public function save( $post_id ) {
		$expiry_date_changed = false;
		$expiry_status       = filter_input( INPUT_POST, 'dlp_expiry_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		try {
			$document = new Document( $post_id );
		} catch ( \Exception $exception ) {
			return;
		}

		// If the expiry status is set to never, delete the expiry timestamp amd bail
		if ( $expiry_status === 'never' ) {
			if ( $timestamp = wp_next_scheduled( 'document_library_pro_expire_document', [ $document->get_id() ] ) ) {
				wp_unschedule_event( $timestamp, 'document_library_pro_expire_document', [ $document->get_id() ] );
			}

			delete_post_meta( $post_id, '_dlp_expiry_timestamp' );
			return;
		}

		$expiry_date_values = [];

		foreach ( [ 'aa', 'mm', 'jj', 'hh', 'mn' ] as $timeunit ) {
			$hidden_value    = filter_input( INPUT_POST, 'hidden_expiry_' . $timeunit, FILTER_SANITIZE_NUMBER_INT );
			$submitted_value = filter_input( INPUT_POST, 'expiry_' . $timeunit, FILTER_SANITIZE_NUMBER_INT );

			if ( $hidden_value !== $submitted_value ) {
				$expiry_date_changed = true;
			}

			$expiry_date_values[ $timeunit ] = $submitted_value;
		}

		if ( ! $expiry_date_changed ) {
			return;
		}

		$expiry_date = $this->generate_timestamp( $expiry_date_values['aa'], $expiry_date_values['mm'], $expiry_date_values['jj'], $expiry_date_values['hh'], $expiry_date_values['mn'] );

		if ( ! wp_checkdate( $expiry_date_values['mm'], $expiry_date_values['jj'], $expiry_date_values['aa'], $expiry_date ) ) {
			return;
		}

		if ( empty( $expiry_date ) ) {
			return;
		}

		// bail if the expiry date is the same as the current one
		if ( $document->get_expiry_timestamp() === $expiry_date ) {
			return;
		}

		// maybe unschedule a previous expiry
		if ( $timestamp = wp_next_scheduled( 'document_library_pro_expire_document', [ $document->get_id() ] ) ) {
			wp_unschedule_event( $timestamp, 'document_library_pro_expire_document', [ $document->get_id() ] );
		}

		// save and set expiry event
		$document->set_expiry_timestamp( get_gmt_from_date( $expiry_date, 'U' ) );
		wp_schedule_single_event( $document->get_expiry_timestamp(), 'document_library_pro_expire_document', [ $document->get_id() ] );
	}

	/**
	 * Render the metabox.
	 *
	 * @param WP_Post $post
	 */
	public function render( $post ) {
		// check if dlp document
		if ( Post_Type::POST_TYPE_SLUG !== $post->post_type ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$can_publish      = $post_type_object && current_user_can( $post_type_object->cap->publish_posts );

		if ( ! $can_publish ) {
			return;
		}

		$document = new Document( $post->ID );
		/* translators: Publish box expiry date string. 1: Date, 2: Time. See https://www.php.net/manual/datetime.format.php */
		$date_string = __( '%1$s at %2$s', 'document-library-pro' );
		/* translators: Publish box expiry date format, see https://www.php.net/manual/datetime.format.php */
		$date_format = _x( 'M j, Y', 'publish box expiry date format', 'document-library-pro' );
		/* translators: Publish box expiry time format, see https://www.php.net/manual/datetime.format.php */
		$time_format = _x( 'H:i', 'publish box expiry time format', 'document-library-pro' );

		$stamp = sprintf(
			$date_string,
			wp_date( $date_format, $document->get_expiry_timestamp() ),
			wp_date( $time_format, $document->get_expiry_timestamp() )
		);
		?>
		<div class="misc-pub-section curtime misc-pub-expiry document-expiry-container">
			<div id="document-expiry">
				<span id="expiry-status">
					<?php if ( $document->get_expiry_status() === 'never' ) : ?>
						<?php esc_html_e( 'Never expires', 'document-library-pro' ); ?>
					<?php elseif ( $document->get_expiry_status() === 'expired' ) : ?>
						<?php esc_html_e( 'Expired on:', 'document-library-pro' ); ?>
					<?php elseif ( $document->get_expiry_status() === 'active' ) : ?>
						<?php esc_html_e( 'Expires on:', 'document-library-pro' ); ?>
					<?php endif; ?>
				</span>

				<span id="expiry-timestamp">
					<?php if ( $document->get_expiry_status() !== 'never' ) : ?>
						<b><?php echo esc_html( $stamp ); ?></b>
					<?php endif; ?>
				</span>
			</div>

			<a href="#edit_expiry_timestamp" class="edit-timestamp hide-if-no-js" role="button">
				<span aria-hidden="true"><?php esc_html_e( 'Edit', 'document-library-pro' ); ?></span>
				<span class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					esc_html_e( 'Edit expiry date and time', 'document-library-pro' );
					?>
				</span>
			</a>

			<fieldset id="expirytimestampdiv" class="hide-if-js">
				<legend class="screen-reader-text">
					<?php
					/* translators: Hidden accessibility text. */
					esc_html_e( 'Expiry date and time', 'document-library-pro' );
					?>
				</legend>
				<?php $this->touch_time( $document->get_id() ); ?>
			</fieldset>

			<input type="hidden" name="dlp_expiry_status" id="expiry_status" value="<?php echo esc_attr( $document->get_expiry_status() ); ?>" />
		</div>
		<?php
	}

	/**
	 * Adaptation of the touch_time function from WordPress core.
	 * We need custom HTML ID's to prevent conflicts with the scheduled publish date in the post editor.
	 *
	 * Prints out HTML form date elements.
	 *
	 * @global WP_Locale $wp_locale WordPress date and time locale object.
	 *
	 * @param int       $document_id   The post ID.
	 * @param int      $tab_index The tabindex attribute to add. Default 0.
	 */
	private function touch_time( $document_id, $tab_index = 0 ) {
		global $wp_locale;

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 ) {
			$tab_index_attribute = " tabindex=\"$tab_index\"";
		}

		$document = dlp_get_document( $document_id );

		if ( $document && $document->get_expiry_status() !== 'never' ) {
			$existing_timestamp = $document->get_expiry_timestamp();

			$jj = wp_date( 'd', $existing_timestamp );
			$mm = wp_date( 'm', $existing_timestamp );
			$aa = wp_date( 'Y', $existing_timestamp );
			$hh = wp_date( 'H', $existing_timestamp );
			$mn = wp_date( 'i', $existing_timestamp );
		} else {
			$jj = wp_date( 'd' );
			$mm = wp_date( 'm' );
			$aa = wp_date( 'Y' );
			$hh = wp_date( 'H' );
			$mn = wp_date( 'i' );
		}

		$timeunit_values = [
			'mm' => $mm,
			'jj' => $jj,
			'aa' => $aa,
			'hh' => $hh,
			'mn' => $mn,
		];

		$day = sprintf(
			'<label><span class="screen-reader-text">%1$s</span><input type="text" id="expiry_jj" name="expiry_jj" value="%2$s" size="2" maxlength="2" %3$s autocomplete="off" class="form-required" /></label>',
			/* translators: Hidden accessibility text. */
			esc_html__( 'Day', 'document-library-pro' ),
			$jj,
			$tab_index_attribute
		);

		$year = sprintf(
			'<label><span class="screen-reader-text">%1$s</span><input type="text" id="expiry_aa" name="expiry_aa" value="%2$s" size="4" maxlength="4" %3$s autocomplete="off" class="form-required" /></label>',
			/* translators: Hidden accessibility text. */
			esc_html__( 'Year', 'document-library-pro' ),
			$aa,
			$tab_index_attribute
		);

		$hour = sprintf(
			'<label><span class="screen-reader-text">%1$s</span><input type="text" id="expiry_hh" name="expiry_hh" value="%2$s" size="2" maxlength="2" %3$s autocomplete="off" class="form-required" /></label>',
			/* translators: Hidden accessibility text. */
			esc_html__( 'Hour', 'document-library-pro' ),
			$hh,
			$tab_index_attribute
		);

		$month = sprintf(
			'<label><span class="screen-reader-text">%1$s</span><select class="form-required" id="expiry_mm" name="expiry_mm"%2$s>',
			/* translators: Hidden accessibility text. */
			__( 'Month', 'document-library-pro' ),
			$tab_index_attribute
		) . "\n";

		for ( $i = 1; $i < 13; ++$i ) {
			$monthnum  = zeroise( $i, 2 );
			$monthtext = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );
			$month    .= "\t\t\t" . '<option value="' . $monthnum . '" data-text="' . $monthtext . '" ' . selected( $monthnum, $mm, false ) . '>';
			/* translators: 1: Month number (01, 02, etc.), 2: Month abbreviation. */
			$month .= sprintf( __( '%1$s-%2$s', 'document-library-pro' ), $monthnum, $monthtext ) . "</option>\n";
		}

		$month .= '</select></label>';

		$minute = sprintf(
			'<label><span class="screen-reader-text">%1$s</span><input type="text" id="expiry_mn" name="expiry_mn" value="%2$s" size="2" maxlength="2" %3$s autocomplete="off" class="form-required" /></label>',
			/* translators: Hidden accessibility text. */
			esc_html__( 'Minute', 'document-library-pro' ),
			$mn,
			$tab_index_attribute
		);

		echo '<div class="timestamp-wrap">';
		/* translators: 1: Month, 2: Day, 3: Year, 4: Hour, 5: Minute. */
		printf( wp_kses_post( '%1$s %2$s, %3$s at %4$s:%5$s', 'document-library-pro' ), $month, $day, $year, $hour, $minute ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo "\n\n";

		foreach ( $timeunit_values as $timeunit => $value ) {
			printf( '<input type="hidden" id="hidden_expiry_%1$s" name="hidden_expiry_%1$s" value="%2$s" />' . "\n", esc_attr( $timeunit ), esc_attr( $value ) );
		}
		?>

		<p>
			<a href="#edit_expiry_timestamp" class="save-timestamp hide-if-no-js button"><?php esc_html_e( 'OK', 'document-library-pro' ); ?></a>
			<a href="#edit_expiry_timestamp" class="cancel-timestamp hide-if-no-js button-cancel"><?php esc_html_e( 'Cancel', 'document-library-pro' ); ?></a>
			<a href="#edit_expiry_timestamp" class="clear-timestamp hide-if-no-js button-clear"><?php esc_html_e( 'Clear', 'document-library-pro' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Generate a timestamp from the given date and time values.
	 *
	 * @param int $aa
	 * @param int $mm
	 * @param int $jj
	 * @param int $hh
	 * @param int $mn
	 *
	 * @return string
	 */
	private function generate_timestamp( $aa, $mm, $jj, $hh, $mn ) {
		$aa = ( $aa <= 0 ) ? gmdate( 'Y' ) : $aa;
		$mm = ( $mm <= 0 ) ? gmdate( 'n' ) : $mm;
		$jj = ( $jj > 31 ) ? 31 : $jj;
		$jj = ( $jj <= 0 ) ? gmdate( 'j' ) : $jj;
		$hh = ( $hh > 23 ) ? $hh - 24 : $hh;
		$mn = ( $mn > 59 ) ? $mn - 60 : $mn;

		return sprintf( '%04d-%02d-%02d %02d:%02d', $aa, $mm, $jj, $hh, $mn );
	}
}
