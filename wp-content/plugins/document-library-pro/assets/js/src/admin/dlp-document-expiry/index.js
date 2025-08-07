import { __ } from '@wordpress/i18n';

/* eslint-disable camelcase */
jQuery( function ( $ ) {
	const $timestampdiv = $( '#expirytimestampdiv' );
	const $postVisibilitySelect = $( '#post-visibility-select' );

	/**
	 * Document Expiry JS
	 */
	const dlpDocumentExpiry = function () {
		// Hide the clear button if the timestamp is not set.
		$( document ).ready( this.handleClearVisibility );

		// Edit
		$timestampdiv
			.siblings( 'a.edit-timestamp' )
			.on( 'click', this.handleEditClick );

		// Cancel Edit
		$timestampdiv
			.find( '.cancel-timestamp' )
			.on( 'click', this.handleCancelClick );

		// Save the changed timestamp.
		$timestampdiv
			.find( '.save-timestamp' )
			.on( 'click', this.handleSaveClick );

		// Cancel submit when an invalid timestamp has been selected.
		$( '#post' ).on( 'submit', this.handlePostSubmitErrors );

		// Clear the timestamp.
		$timestampdiv
			.find( '.clear-timestamp' )
			.on( 'click', this.handleClearClick );

		// On change from private to any
		$postVisibilitySelect
			.find( 'input[name="visibility"]' )
			.on( 'change', this.handlePostVisibilityNoticeDisplay );

		// visibility okay or cancel
		$postVisibilitySelect
			.find( 'a.save-post-visibility' )
			.on( 'click', this.handlePostVisibilityNoticeClear );

		$postVisibilitySelect
			.find( 'a.cancel-post-visibility' )
			.on( 'click', this.handlePostVisibilityNoticeClear );
	};

	/**
	 * Handle visibility notice display.
	 *
	 * @param {Event} event
	 */
	dlpDocumentExpiry.prototype.handlePostVisibilityNoticeDisplay = function (
		event
	) {
		const newValue = event.target.value;
		const currentValue = $postVisibilitySelect
			.find( '#hidden-post-visibility' )
			.val();

		// remove warning message from label
		$( '.dlp-expiry-visibility-warning' ).remove();

		if (
			dlpDocumentExpiry.getExpiryStatus() !== 'never' &&
			currentValue === 'private' &&
			newValue !== 'private'
		) {
			// get label next to clicked radio button
			const label = $postVisibilitySelect
				.find( 'label[for="' + event.target.id + '"]' )
				.next( 'br' );

			const message = __(
				'Changing the visibility from Private will reset the document expiry.',
				'document-library-pro'
			);

			// add warning message to label
			label.after(
				`<div class="dlp-expiry-visibility-warning">${ message }</div>`
			);
		}
	};

	/**
	 * Handle visibility notice clear.
	 *
	 * @param {Event} event
	 */
	dlpDocumentExpiry.prototype.handlePostVisibilityNoticeClear = function (
		event
	) {
		$( '.dlp-expiry-visibility-warning' ).remove();
	};

	/**
	 * Handle Clear button visibility.
	 */
	dlpDocumentExpiry.prototype.handleClearVisibility = function () {
		if ( dlpDocumentExpiry.getExpiryStatus() !== 'never' ) {
			$( '.clear-timestamp' ).show();
		} else {
			$( '.clear-timestamp' ).hide();
		}
	};

	/**
	 * Handle Edit expiry time click.
	 */
	dlpDocumentExpiry.prototype.handleEditClick = function ( event ) {
		event.preventDefault();

		if ( $timestampdiv.is( ':hidden' ) ) {
			$timestampdiv.slideDown( 'fast', function () {
				$( 'input, select', $timestampdiv.find( '.timestamp-wrap' ) )
					.first()
					.trigger( 'focus' );
			} );

			$( this ).hide();
		}
	};

	/**
	 * Handle Cancel expiry time click.
	 */
	dlpDocumentExpiry.prototype.handleCancelClick = function ( event ) {
		event.preventDefault();

		$timestampdiv
			.slideUp( 'fast' )
			.siblings( 'a.edit-timestamp' )
			.show()
			.trigger( 'focus' );

		$( '#expiry_mm' ).val( $( '#hidden_expiry_mm' ).val() );
		$( '#expiry_jj' ).val( $( '#hidden_expiry_jj' ).val() );
		$( '#expiry_aa' ).val( $( '#hidden_expiry_aa' ).val() );
		$( '#expiry_hh' ).val( $( '#hidden_expiry_hh' ).val() );
		$( '#expiry_mn' ).val( $( '#hidden_expiry_mn' ).val() );
	};

	/**
	 * Handle Save expiry time click.
	 */
	dlpDocumentExpiry.prototype.handleSaveClick = function ( event ) {
		event.preventDefault();

		if ( dlpDocumentExpiry.updateText() ) {
			$timestampdiv.slideUp( 'fast' );
			$timestampdiv
				.siblings( 'a.edit-timestamp' )
				.show()
				.trigger( 'focus' );

			$timestampdiv.find( '.clear-timestamp' ).show();
		}
	};

	/**
	 * Handle clear expiry click.
	 */
	dlpDocumentExpiry.prototype.handleClearClick = function ( event ) {
		event.preventDefault();

		// Calculate the current server time based on GMT offset
		const utc =
			new Date().getTime() + new Date().getTimezoneOffset() * 60000;
		const serverDate = new Date(
			utc + dlpDocumentExpiryObject.gmtOffset * 1000
		);

		$( '#expiry_mm' ).val(
			dlpDocumentExpiry.addLeadingZero( serverDate.getMonth() + 1 )
		);
		$( '#expiry_jj' ).val(
			dlpDocumentExpiry.addLeadingZero( serverDate.getDate() )
		);
		$( '#expiry_aa' ).val( serverDate.getFullYear() );
		$( '#expiry_hh' ).val(
			dlpDocumentExpiry.addLeadingZero( serverDate.getHours() )
		);
		$( '#expiry_mn' ).val(
			dlpDocumentExpiry.addLeadingZero( serverDate.getMinutes() )
		);

		$timestampdiv.slideUp( 'fast' );
		$timestampdiv.siblings( 'a.edit-timestamp' ).show().trigger( 'focus' );

		// set timestamp to Never expires
		$( '#expiry-status' ).html(
			__( 'Never expires', 'document-library-pro' )
		);

		$( '#expiry-timestamp' ).html( '' );

		dlpDocumentExpiry.setExpiryStatus( 'never' );
		$( this ).hide();
	};

	/**
	 * Handle errors on post submit.
	 */
	dlpDocumentExpiry.prototype.handlePostSubmitErrors = function ( event ) {
		if (
			dlpDocumentExpiry.getExpiryStatus() !== 'never' &&
			! dlpDocumentExpiry.updateText()
		) {
			event.preventDefault();
			$timestampdiv.show();

			if ( wp.autosave ) {
				wp.autosave.enableButtons();
			}

			$( '#publishing-action .spinner' ).removeClass( 'is-active' );
		}
	};

	/**
	 * Attempt to update the text in the timestamp div.
	 *
	 * @return {boolean} False when an invalid timestamp has been selected, otherwise true.
	 */
	dlpDocumentExpiry.updateText = function ( action ) {
		if ( ! $timestampdiv.length ) {
			return true;
		}

		const aa = $( '#expiry_aa' ).val(),
			mm = $( '#expiry_mm' ).val(),
			jj = $( '#expiry_jj' ).val(),
			hh = $( '#expiry_hh' ).val(),
			mn = $( '#expiry_mn' ).val();

		const attemptedDate = new Date( aa, mm - 1, jj, hh, mn );

		// Remove the form-invalid class from the timestamp-wrap div.
		$timestampdiv.find( '.timestamp-wrap' ).removeClass( 'form-invalid' );

		// Catch invalid dates
		if ( ! this.checkAttemptedDate( attemptedDate ) ) {
			$timestampdiv.find( '.timestamp-wrap' ).addClass( 'form-invalid' );

			return false;
		}

		// Set expiry status label
		$( '#expiry-status' ).html(
			__( 'Expires on:', 'document-library-pro' )
		);

		// Set the human readable date.
		$( '#expiry-timestamp' ).html(
			'\n' +
				' <b>' +
				// translators: 1: Month, 2: Day, 3: Year, 4: Hour, 5: Minute.
				__( '%1$s %2$s, %3$s at %4$s:%5$s', 'document-library-pro' )
					.replace(
						'%1$s',
						$( 'option[value="' + mm + '"]', '#expiry_mm' ).attr(
							'data-text'
						)
					)
					.replace( '%2$s', parseInt( jj, 10 ) )
					.replace( '%3$s', aa )
					.replace( '%4$s', ( '00' + hh ).slice( -2 ) )
					.replace( '%5$s', ( '00' + mn ).slice( -2 ) ) +
				'</b> '
		);

		// Set the expiry status
		dlpDocumentExpiry.setExpiryStatus( 'active' );

		return true;
	};

	/**
	 * Check if the attempted date is valid.
	 *
	 * @param {string} attemptedDate The attempted date.
	 * @return {boolean} True if the date is valid, false otherwise.
	 */
	dlpDocumentExpiry.checkAttemptedDate = function ( attemptedDate ) {
		const aa = $( '#expiry_aa' ).val(),
			mm = $( '#expiry_mm' ).val(),
			jj = $( '#expiry_jj' ).val(),
			hh = $( '#expiry_hh' ).val(),
			mn = $( '#expiry_mn' ).val();

		const utc =
			new Date().getTime() + new Date().getTimezoneOffset() * 60000;
		const serverDate = new Date(
			utc + dlpDocumentExpiryObject.gmtOffset * 1000
		);

		if (
			attemptedDate.getFullYear() != aa ||
			1 + attemptedDate.getMonth() != mm ||
			attemptedDate.getDate() != jj ||
			attemptedDate.getMinutes() != mn
		) {
			return false;
		}

		// don't allow past dates
		if ( attemptedDate < serverDate ) {
			return false;
		}

		return true;
	};

	/**
	 * Get the expiry status.
	 */
	dlpDocumentExpiry.getExpiryStatus = function () {
		return $( '#expiry_status' ).val();
	};

	/**
	 * Set the expiry status.
	 *
	 * @param {string} status The status.
	 */
	dlpDocumentExpiry.setExpiryStatus = function ( status ) {
		$( '#expiry_status' ).val( status );
	};

	/**
	 * Get the expiry status.
	 *
	 * @param {string} number The number.
	 * @return {string} The number with leading zeroes.
	 */
	dlpDocumentExpiry.addLeadingZero = function ( number ) {
		return number < 10 ? '0' + number : number;
	};

	new dlpDocumentExpiry();
} );
