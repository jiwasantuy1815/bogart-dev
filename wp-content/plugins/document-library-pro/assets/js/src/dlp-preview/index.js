( function ( $ ) {
	/**
	 * Bind Events
	 */
	const dlpPreview = function () {
		// handle open modal
		$( document ).on(
			'click',
			'.dlp-preview-button',
			this.handleModalInit
		);

		// handle close modal
		$( document ).on(
			'click',
			'.dlp-preview-modal-close, .dlp-preview-modal-overlay',
			this.handleCloseModal
		);
	};

	/**
	 * Handle Preview Button / Open Modal
	 *
	 * @param  event
	 */
	dlpPreview.prototype.handleModalInit = function ( event ) {
		const type = $( this ).data( 'download-type' );
		const view = $( this ).data( 'view' );
		const url = $( this ).data( 'download-url' );
		const title = $( this ).data( 'title' );
		let modalId;

		switch ( view ) {
			case 'table':
				modalId = `modal_${ $( this )
					.parents( '.posts-data-table' )
					.first()
					.attr( 'id' ) }`;
				break;

			case 'grid':
				modalId = `modal_${ $( this )
					.parents( '.dlp-grid-container' )
					.first()
					.attr( 'id' ) }`;
				break;

			case 'single':
				modalId = $( '.dlp-preview-modal' ).attr( 'id' );
				break;

			default:
				break;
		}

		if (
			! $( '#' + modalId )
				.parent()
				.is( 'body' )
		) {
			$( '#' + modalId ).appendTo( 'body' );
		}

		MicroModal.show( modalId, {
			onShow: ( modal ) =>
				dlpPreview.loadContent( modal, { title, type, url } ),
			onClose: ( modal ) => dlpPreview.destroyContent( modal ),
			openTrigger: 'data-dlp-preview-open',
			closeTrigger: 'data-dlp-preview-close',
		} );
	};

	dlpPreview.prototype.handleCloseModal = function ( event ) {
		event.stopPropagation();

		if (
			$( event.target ).parents( '.dlp-preview-modal-container' ).length >
			0
		) {
			return;
		}

		const modalId = $( this ).data( 'dlp-preview-close' ).substring( 1 );

		MicroModal.close( modalId );
	};

	dlpPreview.loadContent = function ( modal, data ) {
		const $modalContent = $( modal )
			.find( '.dlp-preview-modal-content' )
			.first();
		const $modalFooter = $( modal )
			.find( '.dlp-preview-modal-footer' )
			.first();
		const embedHtml = dlpPreview.getEmbedHtml( data );
		const loadingSvg = `<img class="dlp-preview-spinner" src="${ dlp_preview_params.spinner_url }" />`;

		$modalContent.html( loadingSvg + embedHtml );
		$modalFooter.html( data.title );

		// remove spinner when content is loaded or fails to load
		const $content = $modalContent.children().last();
		if ( $content.is( 'img' ) || $content.is( 'iframe' ) ) {
			$content.on( 'load error', function () {
				$( this ).prev( '.dlp-preview-spinner' ).remove();
			} );
		} else if ( $content.is( 'audio' ) || $content.is( 'video' ) ) {
			$content.on( 'loadeddata error', function () {
				$( this ).prev( '.dlp-preview-spinner' ).remove();
			} );
		}

		$( document.body ).addClass( 'dlp-preview-modal-open' );
	};

	dlpPreview.destroyContent = function ( modal ) {
		const $modalContent = $( modal )
			.find( '.dlp-preview-modal-content' )
			.first();

		$modalContent.html( '' );

		$( document.body ).removeClass( 'dlp-preview-modal-open' );
	};

	dlpPreview.getEmbedHtml = function ( data ) {
		let embedHtml = '';

		switch ( data.type ) {
			case 'application/pdf':
			case 'application/x-pdf':
				embedHtml = `<iframe src="${ data.url }" width="100%" height="100%">`;
				break;

			// msi files
			case 'application/msword':
			case 'application/vnd.ms-powerpoint':
			case 'application/vnd.ms-write':
			case 'application/vnd.ms-excel':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
			case 'application/vnd.ms-word.document.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
			case 'application/vnd.ms-word.template.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			case 'application/vnd.ms-excel.sheet.macroEnabled.12':
			case 'application/vnd.ms-excel.sheet.binary.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template':
			case 'application/vnd.ms-excel.template.macroEnabled.12':
			case 'application/vnd.ms-excel.addin.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
			case 'application/vnd.ms-powerpoint.presentation.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow':
			case 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.presentationml.template':
			case 'application/vnd.ms-powerpoint.template.macroEnabled.12':
			case 'application/vnd.ms-powerpoint.addin.macroEnabled.12':
			case 'application/vnd.openxmlformats-officedocument.presentationml.slide':
			case 'application/vnd.ms-powerpoint.slide.macroEnabled.12':
				embedHtml = `<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=${ data.url }" width="100%" height="100%">`;
				break;

			case 'image/jpeg':
			case 'image/gif':
			case 'image/png':
			case 'image/webp':
			case 'image/svg+xml':
				embedHtml = `<img class="dlp-preview-img" src="${ data.url }" />`;
				break;

			case 'video/mp4':
			case 'video/ogg':
				embedHtml = `<video
                    controls type="${ data.type }"
                    src="${ data.url }">
                    ${ dlp_preview_params.video_error }
                </video>`;
				break;

			case 'audio/mp3':
			case 'audio/mp4':
			case 'audio/mpeg':
			case 'audio/ogg':
			case 'audio/aac':
			case 'audio/aacp':
			case 'audio/flac':
			case 'audio/wav':
			case 'audio/webm':
				embedHtml = `<audio
                    controls
                    src="${ data.url }">
                    ${ dlp_preview_params.audio_error }
                </audio>`;
				break;
		}

		return embedHtml;
	};

	/**
	 * Init dlpPreview.
	 */
	new dlpPreview();
} )( jQuery, window );
