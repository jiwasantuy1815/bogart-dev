import { __ } from '@wordpress/i18n';
import { debounce } from 'lodash';

jQuery( function ( $ ) {
	/**
	 * Grid JS
	 */
	class dlpGrid {
		constructor() {
			// init select2 on grid-loaded and folder-opened
			$( document ).on(
				'grid-loaded',
				'.dlp-grid-container',
				this.handleGridLoaded
			);
			$( document ).on( 'dlp_folder_opened', this.handleGridLoaded );

			$( document ).on(
				'click',
				'.dlp-grid-paginate-button',
				this.handleFetchGrid
			);

			$( document ).on(
				'click',
				'.dlp-grid-card-featured-img',
				this.openPhotoswipe
			);

			$( document ).on(
				'input',
				'.dlp-grid-search input[type="search"]',
				debounce( this.handleGridSearch, 300 )
			);

			$( document ).on(
				'click',
				'.dlp-grid-reset',
				this.handleGridReset
			);

			// handle filters
			$( document ).on(
				'select2:select',
				'.dlp-grid-taxonomy-filter',
				this.handleTaxonomyFilters
			);

			// handle page length
			$( document ).on(
				'select2:select',
				'.dlp-grid-length select',
				this.handlePageLength
			);

			// init grids
			$( '.dlp-grid-container' ).each( function ( index ) {
				dlpPopulateGridHtml( $( this ) );
			} );
		}

		/**
		 * handleGridLoaded
		 */
		handleGridLoaded() {
			$( 'select.dlp-grid-taxonomy-filter' ).select2( {
				dropdownCssClass: 'dlp-grid-dropdown',
				minimumResultsForSearch: 5,
				escapeMarkup: function ( markup ) {
					// Empty function to disable escaping - this is handled by WordPress and DLP.
					return markup;
				},
				language: {
					noResults: function () {
						return __( 'No results found', 'document-library-pro' );
					},
				},
			} );

			$( '.dlp-grid-length select' ).select2( {
				dropdownCssClass: 'dlp-grid-dropdown',
				minimumResultsForSearch: -1,
				escapeMarkup: function ( markup ) {
					// Empty function to disable escaping - this is handled by WordPress and DLP.
					return markup;
				},
				language: {
					noResults: function () {},
				},
			} );
		}

		/**
		 * handleTaxonomyFilters
		 */
		handleTaxonomyFilters( event ) {
			const $this = $( this );
			const $gridContainer = $this
				.parents( '.dlp-grid-container' )
				.first();
			const $reset_button = $gridContainer.find( '.dlp-grid-reset' );
			const gridId = $gridContainer.attr( 'id' );
			let searchQuery = $gridContainer
				.find( '.dlp-grid-search input[type="search"]' )
				.val();

			const blockConfig = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7,
				},
			};

			const searchFilters = getSearchFilters( $gridContainer );
			const pageLength = $( '.dlp-grid-length select' ).first().val();

			if (
				searchFilters.length === 0 &&
				searchQuery.length < dlp_grid_params.ajax_min_search_term_len
			) {
				if ( ! $reset_button.is( ':hidden' ) ) {
					searchQuery = '';
					$reset_button.hide();
				} else {
					return;
				}
			} else {
				$reset_button.show();
			}

			$gridContainer.block( blockConfig );

			$.ajax( {
				url: dlp_grid_params.ajax_url,
				type: 'POST',
				data: {
					grid_id: gridId,
					search_query: searchQuery,
					search_filters: searchFilters,
					page_number: 1,
					length: pageLength,
					action: dlp_grid_params.ajax_action,
					_ajax_nonce: dlp_grid_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function ( response ) {
				dlpPopulateGridHtml( $gridContainer, response );

				$gridContainer.unblock();
			} );

			$gridContainer.addClass( 'grid-loaded' );
		}

		// handlePageLength
		handlePageLength( event ) {
			const $this = $( this );
			const $gridContainer = $this
				.parents( '.dlp-grid-container' )
				.first();

			const gridId = $gridContainer.attr( 'id' );
			let searchQuery = $gridContainer
				.find( '.dlp-grid-search input[type="search"]' )
				.val();

			const blockConfig = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7,
				},
			};

			const searchFilters = getSearchFilters( $gridContainer );
			const pageLength = $this.val();

			// sync multiple page length
			$gridContainer
				.find( '.dlp-grid-length select' )
				.not( this )
				.val( pageLength );

			$gridContainer.block( blockConfig );

			$.ajax( {
				url: dlp_grid_params.ajax_url,
				type: 'POST',
				data: {
					grid_id: gridId,
					search_query: searchQuery,
					search_filters: searchFilters,
					length: pageLength,
					page_number: 1,
					action: dlp_grid_params.ajax_action,
					_ajax_nonce: dlp_grid_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function ( response ) {
				dlpPopulateGridHtml( $gridContainer, response );

				$gridContainer.unblock();
			} );

			$gridContainer.addClass( 'grid-loaded' );
		}

		/**
		 * Fetch Grid
		 *
		 * @param  event
		 */
		handleFetchGrid( event ) {
			const $this = $( this );

			if ( $this.hasClass( 'disabled' ) || $this.hasClass( 'current' ) ) {
				return;
			}

			const $gridContainer = $this
				.parents( '.dlp-grid-container' )
				.first();
			const gridId = $gridContainer.attr( 'id' );
			const pageNumber = $( this ).data( 'page-number' );
			const pageLength = $( '.dlp-grid-length select' ).first().val();

			let searchQuery = '';

			// if we are in a folder search then we need to get the search query from the folder search input
			if (
				$this.parents( '.dlp-folders-search-results' ).first()?.length
			) {
				searchQuery = $this
					.parents( '.dlp-folders-container' )
					.first()
					.find( '.dlp-folders-search input[type="search"]' )
					.val();
			} else {
				searchQuery = $gridContainer
					.find( '.dlp-grid-search input[type="search"]' )
					.val();
			}

			const blockConfig = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7,
				},
			};

			if (
				searchQuery &&
				searchQuery.length < dlp_grid_params.ajax_min_search_term_len
			) {
				searchQuery = '';
			}

			$gridContainer.block( blockConfig );

			$.ajax( {
				url: dlp_grid_params.ajax_url,
				type: 'POST',
				data: {
					grid_id: gridId,
					page_number: pageNumber,
					search_query: searchQuery,
					search_filters: getSearchFilters( $gridContainer ),
					length: pageLength,
					action: dlp_grid_params.ajax_action,
					_ajax_nonce: dlp_grid_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function ( response ) {
				$( 'html, body' ).animate(
					{
						scrollTop: $gridContainer.offset().top - 50,
					},
					300
				);

				dlpPopulateGridHtml( $gridContainer, response );

				$gridContainer.unblock();
			} );

			$gridContainer.addClass( 'grid-loaded' );
		}

		/**
		 * Fetch Grid Search
		 *
		 * @param  event
		 */
		handleGridSearch( event ) {
			const $this = $( this );
			const $gridContainer = $this
				.parents( '.dlp-grid-container' )
				.first();
			const $reset_button = $gridContainer.find( '.dlp-grid-reset' );
			const gridId = $gridContainer.attr( 'id' );
			let searchQuery = $gridContainer
				.find( '.dlp-grid-search input[type="search"]' )
				.val();

			const pageLength = $( '.dlp-grid-length select' ).first().val();

			const searchFilters = getSearchFilters( $gridContainer );
			const blockConfig = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7,
				},
			};

			// Sync Multiple Search Inputs
			$gridContainer
				.find( '.dlp-grid-search input[type="search"]' )
				.not( this )
				.each( function ( index ) {
					$( this ).val( $this.val() );
				} );

			if (
				searchFilters.length === 0 &&
				searchQuery.length < dlp_grid_params.ajax_min_search_term_len
			) {
				if ( ! $reset_button.is( ':hidden' ) ) {
					searchQuery = '';
					$reset_button.hide();
				} else {
					return;
				}
			} else {
				$reset_button.show();
			}

			$gridContainer.block( blockConfig );

			$.ajax( {
				url: dlp_grid_params.ajax_url,
				type: 'POST',
				data: {
					grid_id: gridId,
					search_query: searchQuery,
					search_filters: searchFilters,
					page_number: 1,
					length: pageLength,
					action: dlp_grid_params.ajax_action,
					_ajax_nonce: dlp_grid_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function ( response ) {
				dlpPopulateGridHtml( $gridContainer, response );

				$gridContainer.unblock();
			} );

			$gridContainer.addClass( 'grid-loaded' );
		}

		/**
		 * Fetch Grid Search
		 *
		 * @param  event
		 */
		handleGridReset( event ) {
			event.preventDefault();

			const $this = $( this );
			const $gridContainer = $this
				.parents( '.dlp-grid-container' )
				.first();
			const $reset_buttons = $gridContainer.find( '.dlp-grid-reset' );
			const gridId = $gridContainer.attr( 'id' );
			const $searchInputs = $gridContainer.find(
				'.dlp-grid-search input[type="search"]'
			);
			const $pageLength = $gridContainer.find(
				'.dlp-grid-length select'
			);

			const blockConfig = {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7,
				},
			};

			// clear search inputs
			$searchInputs.each( function ( index ) {
				$( this ).val( '' );
			} );

			// reset select2 searchFilters to default option
			$gridContainer
				.find( '.dlp-grid-taxonomy-filter' )
				.each( function ( index ) {
					$( this ).val( '' ).trigger( 'change' );
				} );

			// reset page length to first option
			$pageLength
				.val( $pageLength.find( 'option:first' ).val() )
				.trigger( 'change' );

			$reset_buttons.hide();

			$gridContainer.block( blockConfig );

			$.ajax( {
				url: dlp_grid_params.ajax_url,
				type: 'POST',
				data: {
					grid_id: gridId,
					search_query: '',
					search_filters: [],
					page_number: 1,
					length: $pageLength.val(),
					action: dlp_grid_params.ajax_action,
					_ajax_nonce: dlp_grid_params.ajax_nonce,
				},
				xhrFields: {
					withCredentials: true,
				},
			} ).done( function ( response ) {
				dlpPopulateGridHtml( $gridContainer, response );

				$gridContainer.unblock();
			} );
		}

		/**
		 * Open Lightbox
		 *
		 * @param  event
		 */
		openPhotoswipe( event ) {
			event.stopPropagation();

			const pswpElement = $( '.pswp' )[ 0 ];
			const $img = $( this ).find( 'img' );

			if ( $img.length < 1 ) {
				return;
			}

			const items = [
				{
					src: $img.attr( 'data-large_image' ),
					w: $img.attr( 'data-large_image_width' ),
					h: $img.attr( 'data-large_image_height' ),
					title:
						$img.attr( 'data-caption' ) &&
						$img.attr( 'data-caption' ).length
							? $img.attr( 'data-caption' )
							: $img.attr( 'title' ),
				},
			];

			const options = {
				index: 0,
				shareEl: false,
				closeOnScroll: false,
				history: false,
				hideAnimationDuration: 0,
				showAnimationDuration: 0,
			};

			// Initializes and opens PhotoSwipe
			const photoswipe = new PhotoSwipe(
				pswpElement,
				PhotoSwipeUI_Default,
				items,
				options
			);
			photoswipe.init();

			return false;
		}
	}

	function getSearchFilters( $gridContainer ) {
		const searchFilters = $gridContainer.find(
			'.dlp-grid-taxonomy-filter'
		);

		const selectedFilters = searchFilters
			.map( function () {
				const $selected = $( this ).find( ':selected' );

				console.log( $selected );

				return {
					taxonomy: $( this ).data( 'taxonomy' ),
					term: $( this ).find( ':selected' ).val(),
				};
			} )
			.toArray();

		// remove entries with empty term
		selectedFilters.forEach( ( filter, index ) => {
			if ( ! filter.term ) {
				selectedFilters.splice( index, 1 );
			}
		} );

		return selectedFilters;
	}

	function dlpPopulateGridHtml( $gridContainer, response = null ) {
		const $gridCardsContainer = $gridContainer.find(
			'.dlp-grid-documents'
		);
		let $gridPaginationFooter = $gridContainer.find(
			'footer .dlp-grid-pagination'
		);
		let $gridPaginationHeader = $gridContainer.find(
			'header .dlp-grid-pagination'
		);
		const $gridTotalsFooter = $gridContainer.find(
			'footer .dlp-grid-totals'
		);
		const $gridTotalsHeader = $gridContainer.find(
			'header .dlp-grid-totals'
		);

		if ( response && response.grid ) {
			$gridCardsContainer.replaceWith( response.grid );
		}

		if (
			$gridPaginationFooter.length > 0 &&
			response &&
			response.pagination
		) {
			$gridPaginationFooter.replaceWith( response.pagination.footer );
		}
		$gridPaginationFooter = $gridContainer.find(
			'footer .dlp-grid-pagination'
		);
		if ( $gridPaginationFooter.children().length > 0 ) {
			$gridPaginationFooter.show();
		} else {
			$gridPaginationFooter.hide();
		}

		if (
			$gridPaginationHeader.length > 0 &&
			response &&
			response.pagination
		) {
			$gridPaginationHeader
				.replaceWith( response.pagination.header )
				.show();
		}
		$gridPaginationHeader = $gridContainer.find(
			'header .dlp-grid-pagination'
		);
		if ( $gridPaginationHeader.children().length > 0 ) {
			$gridPaginationHeader.show();
		} else {
			$gridPaginationHeader.hide();
		}

		if ( $gridTotalsFooter.length > 0 && response && response.totals ) {
			$gridTotalsFooter.replaceWith( response.totals.footer );
		}

		if ( $gridTotalsHeader.length > 0 && response && response.totals ) {
			$gridTotalsHeader.replaceWith( response.totals.header );
		}

		$gridContainer.trigger( 'grid-loaded' );
	}

	/**
	 * Init dlpGrid.
	 */
	new dlpGrid();
} );
