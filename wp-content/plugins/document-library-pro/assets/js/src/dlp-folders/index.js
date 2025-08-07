import { debounce } from 'lodash';

jQuery( function ( $ ) {
	/**
	 * Folders JS
	 */
	const dlpFolders = function () {
		$( '.dlp-category' ).on( 'click', this.handleFolderToggle );
		$( document ).on(
			'input keyup',
			'.dlp-folders-search input[type="search"]',
			debounce( this.handleFolderSearch, 300 )
		);
		$( document ).on(
			'click',
			'.dlp-folders-reset',
			this.handleFolderSearchReset
		);
		$( '.dlp-folders-container' ).each( ( i, e ) => {
			$( e ).show();
		} );
		$( '.dlp-folder:not(.closed)' ).each( ( i, e ) => {
			this.openFolder( e );
		} );

		// Add keyboard handler
		$( '.dlp-category' ).on( 'keydown', this.handleFolderKeydown );
	};

	/**
	 * Handle Folder Open / Close
	 */
	dlpFolders.prototype.handleFolderToggle = function ( event ) {
		const $this = $( this );
		const $folder = $this.parents( '.dlp-folder' ).first();
		const $table_div = $folder.find( '.dlp-category-table' ).first();
		const category_id = $folder.data( 'category-id' );
		const shortcode_atts = $this
			.parents( '.dlp-folders-root' )
			.data( 'shortcode-atts' );
		const blockConfig = {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.7,
			},
		};
		// Open Folder
		if (
			$folder.hasClass( 'closed' ) &&
			! $folder.hasClass( 'table-loaded' )
		) {
			$table_div.block( blockConfig );

			$.ajax( {
				url: dlp_folders_params.ajax_url,
				type: 'POST',
				data: {
					category_id: category_id,
					shortcode_atts: shortcode_atts,
					action: dlp_folders_params.ajax_action,
					_ajax_nonce: dlp_folders_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function ( response ) {
				$table_div.html( response.html );

				if ( response.layout === 'table' ) {
					$table_div.children( 'table' ).first().postsTable();
				}

				$table_div.unblock();

				$( document ).trigger( 'dlp_folder_opened', $folder );
			} );

			$folder.addClass( 'table-loaded' );
		}

		// Close Folder
		if ( ! $folder.hasClass( 'closed' ) ) {
			$folder
				.find( '.dlp-folder' )
				.each( function ( index, childElement ) {
					const $childElement = $( childElement );

					if ( ! $childElement.hasClass( 'closed' ) ) {
						$childElement.addClass( 'closed' );
					}
				} );
		}

		// toggle status
		$folder.toggleClass( 'closed' );

		// close siblings
		const $sibling_folders = $folder
			.siblings( '.dlp-folder' )
			.not( $folder );

		// If not holding meta/ctrl key, close all other folders
		if (
			( navigator.userAgent.indexOf( 'Mac' ) !== -1 &&
				! event.metaKey ) ||
			( navigator.userAgent.indexOf( 'Mac' ) === -1 && ! event.ctrlKey )
		) {
			// Set all other folders' aria-expanded to false
			$('.dlp-category').not($this).attr('aria-expanded', 'false');

			$sibling_folders.each( function ( index, siblingElement ) {
				const $siblingElement = $( siblingElement );

				if ( ! $siblingElement.hasClass( 'closed' ) ) {
					$siblingElement.addClass( 'closed' );

					// close sibling children
					$siblingElement
						.find( '.dlp-folder' )
						.each( function ( index, childElement ) {
							const $childElement = $( childElement );
							if ( ! $childElement.hasClass( 'closed' ) ) {
								$childElement.addClass( 'closed' );
							}
						} );
				}
			} );
		}

		// Update aria-expanded based on closed state
		$this.attr('aria-expanded', !$folder.hasClass('closed'));
	};

	dlpFolders.prototype.openFolder = function ( element ) {
		const $folder = $( element );
		const $category = $folder.find('.dlp-category').first();

		// Update aria-expanded when opening folder
		$category.attr('aria-expanded', 'true');

		const $table_div = $folder.find( '.dlp-category-table' ).first();
		const category_id = $folder.data( 'category-id' );
		const shortcode_atts = $folder
			.parents( '.dlp-folders-root' )
			.data( 'shortcode-atts' );
		const blockConfig = {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.7,
			},
		};
		// Open Folder
		if ( ! $folder.hasClass( 'table-loaded' ) ) {
			if ( $folder.find( '.dlp-folder:not(.closed)' ).length === 0 ) {
				$table_div.block( blockConfig );
			}

			$.ajax( {
				url: dlp_folders_params.ajax_url,
				type: 'POST',
				data: {
					category_id: category_id,
					shortcode_atts: shortcode_atts,
					action: dlp_folders_params.ajax_action,
					_ajax_nonce: dlp_folders_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function ( response ) {
				$table_div.html( response.html );

				if ( response.layout === 'table' ) {
					$table_div.children( 'table' ).first().postsTable();
				}

				$table_div.unblock();

				$( document ).trigger( 'dlp_folder_opened', $folder );
			} );

			$folder.addClass( 'table-loaded' );
		}
	};

	dlpFolders.prototype.handleFolderSearch = function ( event ) {
		const $this = $( this );
		const $folder_container = $this
			.parents( '.dlp-folders-container' )
			.first();
		const $folders_html = $folder_container.find( '.dlp-folders' );
		const $results_container = $folder_container.find(
			'.dlp-folders-search-results'
		);
		const $reset_button = $folder_container.find( '.dlp-folders-reset' );
		const shortcode_atts = $folder_container
			.find( '.dlp-folders-root' )
			.data( 'shortcode-atts' );

		const libraryId = $results_container.hasClass( 'search-loaded' )
			? $results_container.find( '> div' ).attr( 'id' )
			: false;

		const searchQuery = $this.val();
		const blockConfig = {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.7,
			},
		};

		if (
			searchQuery.length < dlp_folders_params.ajax_min_search_term_len
		) {
			$results_container.hide();
			$folders_html.show();
			$reset_button.hide();

			return;
		}

		$reset_button.show();

		// Sync Multiple Search Inputs
		$folder_container
			.find( '.dlp-folders-search input[type="search"]' )
			.not( this )
			.each( function ( index ) {
				$( this ).val( $this.val() );
			} );

		$folder_container.block( blockConfig );

		$.ajax( {
			url: dlp_folders_params.ajax_url,
			type: 'POST',
			data: {
				library_id: libraryId,
				search_query: searchQuery,
				shortcode_atts: shortcode_atts,
				action: dlp_folders_params.ajax_folder_search,
				_ajax_nonce: dlp_folders_params.ajax_nonce,
			},
			xhrFields: {
				withCredentials: true,
			},
		} ).done( function ( response ) {
			$folders_html.hide();
			$results_container.show();

			$results_container.html( response.html );

			if ( response.layout === 'table' ) {
				$results_container.children( 'table' ).first().postsTable();
			}

			$results_container.addClass( 'search-loaded' );

			$folder_container.unblock();
		} );
	};

	dlpFolders.prototype.handleFolderSearchReset = function ( event ) {
		event.preventDefault();

		const $this = $( this );
		const $folder_container = $this
			.parents( '.dlp-folders-container' )
			.first();
		const $folders_html = $folder_container.find( '.dlp-folders' );
		const $search_inputs = $folder_container.find(
			'.dlp-folders-search input[type="search"]'
		);
		const $reset_buttons = $folder_container.find( '.dlp-folders-reset' );
		const $results_container = $folder_container.find(
			'.dlp-folders-search-results'
		);

		$search_inputs.each( function ( index ) {
			$( this ).val( '' );
		} );
		$reset_buttons.each( function ( index ) {
			$( this ).hide();
		} );
		$folders_html.show();
		$results_container.hide();
	};


	/**
	 * Handles keyboard interactions for folder elements.
	 *
	 * When a folder element receives keyboard focus, this handler allows users to
	 * activate the folder using either the Enter or Space key, providing keyboard
	 * accessibility equivalent to clicking.
	 *
	 * @param {Event} event - The keyboard event object
	 */
	dlpFolders.prototype.handleFolderKeydown = function (event) {
		// Check for Enter (13) or Space (32) keys
		if (event.key === 'Enter' || event.key === ' ' || event.keyCode === 13 || event.keyCode === 32) {
			event.preventDefault(); // Prevent page scroll on space
			$(this).trigger('click'); // Trigger the click handler
		}
	};

	/**
	 * Init dlpFolders.
	 */
	new dlpFolders();
} );
