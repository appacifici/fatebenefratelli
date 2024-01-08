
( function( $, window, document, undefined ) {
	"use strict";

	/******************************************
	 * CONSTRUCTOR
	 ******************************************/

	var PostsTable = function( $table ) {

		// Properties
		this.$table = $table;
		this.id = $table.attr( 'id' );
		this.$filters = [];
		this.$tableWrapper = [];
		this.$pagination = [];
		this.hasAdminBar = $( '#wpadminbar' ).length > 0;
		this.ajaxData = [];

		// Bind methods
		this.buildConfig = this.buildConfig.bind( this );
		this.getDataTable = this.getDataTable.bind( this );
		this.initClickSearch = this.initClickSearch.bind( this );
		this.initFilters = this.initFilters.bind( this );
		this.initPhotoswipe = this.initPhotoswipe.bind( this );
		this.initResetButton = this.initResetButton.bind( this );
		this.processAjaxData = this.processAjaxData.bind( this );
		this.scrollToTop = this.scrollToTop.bind( this );
		this.showHidePagination = this.showHidePagination.bind( this );

		// Register DataTables events
		$table.on( 'draw.dt', { table: this }, this.onDraw );
		$table.on( 'init.dt', { table: this }, this.onInit );
		$table.on( 'page.dt', { table: this }, this.onPage );
		$table.on( 'processing.dt', { table: this }, this.onProcessing );
		$table.on( 'responsive-display.dt', { table: this }, this.onResponsiveDisplay );
		$table.on( 'xhr.dt', { table: this }, this.onAjaxLoad );

		// Register load event
		$( window ).on( 'load', { table: this }, this.onWindowLoad );

		// Show the table - loading class removed on init.dt
		$table.addClass( 'loading' ).css( 'visibility', 'visible' );

		// Initialise the DataTable instance
		this.getDataTable();
	};

	/******************************************
	 * STATIC PROPERTIES
	 ******************************************/

	PostsTable.blockConfig = {
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	};

	/******************************************
	 * STATIC METHODS
	 ******************************************/

	PostsTable.addRowAttributes = function( $row ) {
		return function( key, value ) {
			if ( 'class' === key ) {
				$row.addClass( value );
			} else {
				$row.attr( key, value );
			}
		};
	};

	PostsTable.appendFilterOptions = function( $select, items, depth ) {
		depth = ( typeof depth !== 'undefined' ) ? depth : 0;

		// Add each term to filter drop-down
		$.each( items, function( i, item ) {
			var name = item.name;
			var value = 'slug' in item ? item.slug : name;
			var pad = '';

			if ( depth ) {
				// \u000a0 = &nbsp;  \u2013 = &ndash;
				pad = Array( ( depth * 2 ) + 1 ).join( '\u00a0' ) + '\u2013\u00a0';
			}

			$select.append( '<option value="' + value + '">' + pad + name + '</option>' );

			if ( 'children' in item ) {
				PostsTable.appendFilterOptions( $select, item.children, depth + 1 );
			}
		} );
	};

	PostsTable.initMedia = function( $el ) {
		if ( !$el || !$el.length ) {
			return;
		}

		if ( typeof WPPlaylistView !== 'undefined' ) {
			// Initialise audio and video playlists
			$el.find( '.wp-playlist' ).filter( function() {
				return $( '.mejs-container', this ).length === 0; // exclude playlists already initialized
			} ).each( function() {
				return new WPPlaylistView( { el: this } );
			} );
		}

		if ( 'wp' in window && 'mediaelement' in window.wp ) {
			$( window.wp.mediaelement.initialize );
		}

		// Run fitVids to ensure videos in table have correct proportions
		if ( $.fn.fitVids ) {
			$el.fitVids();
		}
	};

	PostsTable.responsiveRendererTableAll = function( api, rowIdx, columns ) {
		// Displays the child row when responsive_display="modal"
		var title = '';

		var data = $.map( columns, function( col, i ) {
			title = col.title ? col.title + ':' : '';
			return (
				api.column( col.columnIndex ).visible() ?
				'<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '"><td>' + title + '</td><td class="child">' + col.data + '</td></tr>' :
				''
				);
		} ).join( '' );

		if ( data ) {
			var $modal = $( '<table class="posts-data-table modal-table" />' ).append( data );
			PostsTable.initMedia( $modal );
			return $modal;
		} else {
			return false;
		}
	};

	/******************************************
	 * INSTANCE METHODS
	 ******************************************/

	PostsTable.prototype.buildConfig = function() {
		if ( this.config ) {
			return this.config;
		}

		var config = {
			retrieve: true, // so subsequent calls to DataTable() return the same API instance
			responsive: true,
			processing: true, // display 'processing' indicator when loading
			orderMulti: false, // disable ordering by multiple columns at once
			language: posts_table_params.language
		};

		// Get config for this table instance.
		var tableConfig = this.$table.data( 'config' );

		if ( tableConfig ) {
			// We need to do deep copy for the 'language' property to be merged correctly.
			config = $.extend( true, { }, config, tableConfig );
		}

		// Config for server side processing
		if ( config.serverSide && 'ajax_url' in posts_table_params ) {
			config.deferRender = true;
			config.ajax = {
				url: posts_table_params.ajax_url,
				type: 'POST',
				data: {
					table_id: this.id,
					action: 'ptp_load_posts',
					_ajax_nonce: posts_table_params.ajax_nonce
				},
				xhrFields: {
					withCredentials: true
				}
			};
		}

		// Set responsive display and renderer functions
		if ( typeof config.responsive.details === 'object' && 'display' in config.responsive.details ) {
			if ( 'child_row_visible' === config.responsive.details.display ) {
				config.responsive.details.display = $.fn.dataTable.Responsive.display.childRowImmediate;
				config.responsive.details.renderer = $.fn.dataTable.Responsive.renderer.listHidden();
			}
			if ( 'modal' === config.responsive.details.display ) {
				config.responsive.details.display = $.fn.dataTable.Responsive.display.modal();
				config.responsive.details.renderer = PostsTable.responsiveRendererTableAll;
			}
		}

		// Legacy config for language (we now use Gettext for translation).
		if ( 'lang_url' in posts_table_params ) {
			config.language = { url: posts_table_params.lang_url };
		}

		return config;
	};

	PostsTable.prototype.getDataTable = function() {
		if ( !this.dataTable ) {
			// Build table config.
			this.config = this.buildConfig();

			// Initialize DataTables instance.
			this.dataTable = this.$table.DataTable( this.config );
		}
		return this.dataTable;
	};

	PostsTable.prototype.initFilters = function() {
		var table = this,
			filters = table.$table.data( 'filters' );

		if ( !filters ) {
			return table;
		}

		var dataTable = table.getDataTable(),
			filtersAdded = 0,
			$filtersWrap = $( '<div class="posts-table-select-filters" id="' + table.id + '_select_filters"><label class="filter-label">' + posts_table_params.language.filterBy + '</label></div>' );

		// Build filters
		for ( var column in filters ) {
			var $select = $( '<select name="ptp_filter_' + column + '" data-column="' + column + '" data-tax="' + filters[column].taxonomy + '" data-search-column="' + column + '_hfilter" aria-label="' + filters[column].heading + '"></select>' )
				.append( '<option value="">' + filters[column].heading + '</option>' )
				.on( 'change', { table: table }, table.onFilterChange );

			var terms = filters[column].terms;

			// If not lazy-loading, restrict terms in dropdown to terms which appear in table
			if ( !table.config.serverSide ) {
				var searchData = dataTable
					.column( $select.data( 'searchColumn' ) + ':name' )
					.data();

				if ( searchData.any() ) {
					var termSlugs = searchData.join( ' ' ).split( ' ' );

					terms = $.grep( terms, function( term, i ) {
						// Include term in dropdown if it has children or appears in column data
						return 'children' in term || termSlugs.indexOf( term.slug ) > -1;
					} );
				}
			}

			// Don't add filter if we have no terms for it
			if ( !terms.length ) {
				continue;
			}

			// Add the <option> elements to filter
			PostsTable.appendFilterOptions( $select, terms );

			// Append select to wrapper
			$select.appendTo( $filtersWrap );
			filtersAdded++;
		}

		if ( filtersAdded > 0 ) {
			// Add filters to table - before search box or as first element above table
			var $searchBox = table.$tableWrapper.find( '.dataTables_filter' );

			if ( $searchBox.length ) {
				$filtersWrap.prependTo( $searchBox.closest( '.posts-table-controls' ) );
			} else {
				$filtersWrap.prependTo( table.$tableWrapper.children( '.posts-table-above' ) );
			}
		}

		// Store filter selects here as we use this when searching columns
		table.$filters = table.$tableWrapper.find( '.posts-table-select-filters select' );

		return table;
	};

	PostsTable.prototype.initPhotoswipe = function() {
		this.$table.on( 'click', '.posts-table-gallery__image a', this.onOpenPhotoswipe );
		return this;
	};

	PostsTable.prototype.initResetButton = function() {
		var table = this;

		if ( !table.config.resetButton ) {
			return table;
		}

		var dataTable = table.getDataTable();
		var $resetButton =
			$( '<span class="posts-table-reset"><a class="reset" href="#">' + posts_table_params.language.resetButton + '</a></span>' )
			.on( 'click', 'a', { table: table }, this.onReset );

		// Append reset button
		var $searchBox = table.$tableWrapper.find( '.dataTables_filter' );

		if ( table.$filters.length ) {
			$resetButton.appendTo( table.$tableWrapper.find( '.posts-table-select-filters' ) );
		} else if ( $searchBox.length ) {
			$resetButton.prependTo( $searchBox );
		} else {
			var $firstChild = table.$tableWrapper.children( 'div.posts-table-above' ).children( '.dataTables_length,.dataTables_info' ).eq( 0 );
			if ( $firstChild.length ) {
				$resetButton.appendTo( $firstChild );
			} else {
				$resetButton.prependTo( table.$tableWrapper.children( 'div.posts-table-above' ) );
			}
		}

		return table;
	};

	PostsTable.prototype.processAjaxData = function() {
		var table = this;

		if ( !table.config.serverSide || !table.ajaxData.length ) {
			return;
		}

		var $rows = table.$table.find( 'tbody tr' );

		// Add row attributes to each row in table
		for ( var i = 0; i < table.ajaxData.length; i++ ) {
			if ( '__attributes' in table.ajaxData[i] && $rows.eq( i ).length ) {
				$.each( table.ajaxData[i].__attributes, PostsTable.addRowAttributes( $rows.eq( i ) ) );
			}
		}

		return table;
	};

	PostsTable.prototype.initClickSearch = function() {
		if ( this.config.clickFilter ) {
			this.$table.on( 'click', 'a', { table: this }, this.onClickSearch );
		}

		return this;
	};

	PostsTable.prototype.scrollToTop = function() {
		var scroll = this.config.scrollOffset;

		if ( scroll !== false && !isNaN( scroll ) ) {
			var tableOffset = this.$tableWrapper.offset().top - scroll;

			if ( this.hasAdminBar ) { // Adjust offset for WP admin bar
				tableOffset -= 32;
			}
			$( 'html,body' ).animate( { scrollTop: tableOffset }, 300 );
		}

		return this;
	};

	PostsTable.prototype.showHidePagination = function() {
		// Hide pagination if we only have 1 page
		if ( this.$pagination.length ) {
			var pageInfo = this.getDataTable().page.info();

			if ( pageInfo && pageInfo.pages <= 1 ) {
				this.$pagination.hide( 0 );
			} else {
				this.$pagination.show();
			}
		}

		return this;
	};

	/******************************************
	 * EVENTS
	 ******************************************/

	PostsTable.prototype.onAjaxLoad = function( event, settings, json, xhr ) {
		var table = event.data.table;

		if ( null !== json && 'data' in json && $.isArray( json.data ) ) {
			table.ajaxData = json.data;
		}
		table.$table.trigger( 'lazyload.ptp', [table] );
	};

	PostsTable.prototype.onClickSearch = function( event ) {
		var $clicked = $( this ),
			table = event.data.table,
			dataTable = table.getDataTable();

		// Get nearest <td> cell for clicked link. If it's child row, we need to use the <li data-dt-row> to find the column.
		var $el = $clicked.closest( 'td' );
		if ( $el.hasClass( 'child' ) ) {
			$el = $clicked.closest( 'li[data-dt-row]', $el );
		}

		// Find the DataTables column
		var column = dataTable.column( $el.get( 0 ) );

		if ( column.any() && $( column.header() ).data( 'clickFilter' ) ) {
			// Column found and is filterable
			event.stopImmediatePropagation();

			var searchText = $clicked.text(),
				columnName = $( column.header() ).data( 'name' ),
				$filter = table.$filters ? table.$filters.filter( '[data-column="' + columnName + '"]' ) : [];

			// If we have filters, update selection to match the value being searched for
			if ( $filter.length ) {
				// Clear any hidden filter column search
				dataTable.column( $filter.data( 'searchColumn' ) + ':name' ).search( '' );

				$( 'option', $filter ).filter( function() {
					return $.trim( $( this ).text().replace( '\u2013', '' ) ) === searchText;
				} ).prop( 'selected', true );
			}

			// Escape value and redraw
			var searchVal = $.fn.dataTable.util.escapeRegex( searchText );

			if ( !table.config.serverSide ) {
				searchVal = searchVal ? '(^|, )' + searchVal + '(, |$)' : '';
			}

			// Do the column search and draw results
			column
				.search( searchVal, true, false )
				.draw();

			// Finally, scroll to top of table
			event.data.table.scrollToTop();
			return false;
		}

		return true;
	};

	PostsTable.prototype.onDraw = function( event ) {
		var table = event.data.table;

		// Add row attributes to each <tr> if using lazy load
		if ( table.config.serverSide ) {
			table.processAjaxData();
		}

		// If using server side processing, or not on first draw event, initialise content
		if ( table.config.serverSide || !table.$table.hasClass( 'loading' ) ) {
			PostsTable.initMedia( table.$table );
		}

		table.showHidePagination();
		table.$table.trigger( 'draw.ptp', [table] );
	};

	PostsTable.prototype.onFilterChange = function( event ) {
		var table = event.data.table;

		var searchVal = $( this ).val();

		if ( !table.config.serverSide ) {
			searchVal = searchVal ? '(^| )' + searchVal + '( |$)' : '';
		}

		var dataTable = table.getDataTable();

		dataTable
			// If we have the column for this filter, clear any column search first.
			.column( $( this ).data( 'column' ) + ':name' )
			.search( '' )
			// Now run filter search
			.column( $( this ).data( 'searchColumn' ) + ':name' )
			.search( searchVal, true, false )
			.draw();
	};

	PostsTable.prototype.onInit = function( event ) {
		var table = event.data.table;

		table.$tableWrapper = table.$table.parent();
		table.$pagination = table.$tableWrapper.find( '.dataTables_paginate' );

		table
			.initClickSearch()
			.initFilters()
			.initResetButton()
			.initPhotoswipe()
			.showHidePagination();

		// fitVids will run on every draw event for serverSide processing, but for standard processing
		// we need to run onInit as well as initialiseMedia only runs on subsequent draw events.
		if ( !table.config.serverSide && $.fn.fitVids ) {
			table.$table.fitVids();
		}

		table.$table
			.removeClass( 'loading' )
			.trigger( 'init.ptp', [table] );
	};

	PostsTable.prototype.onOpenPhotoswipe = function( event ) {
		event.preventDefault();

		var pswpElement = $( '.pswp' )[0],
			$target = $( event.target ),
			$galleryImage = $target.closest( '.posts-table-gallery__image' ),
			items = [];

		if ( $galleryImage.length > 0 ) {
			$galleryImage.each( function( i, el ) {
				var img = $( el ).find( 'img' ),
					large_image_src = img.attr( 'data-large_image' ),
					large_image_w = img.attr( 'data-large_image_width' ),
					large_image_h = img.attr( 'data-large_image_height' ),
					item = {
						src: large_image_src,
						w: large_image_w,
						h: large_image_h,
						title: ( img.attr( 'data-caption' ) && img.attr( 'data-caption' ).length ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
					};
				items.push( item );
			} );
		}

		var options = {
			index: 0,
			shareEl: false,
			closeOnScroll: false,
			history: false,
			hideAnimationDuration: 0,
			showAnimationDuration: 0
		};

		// Initializes and opens PhotoSwipe
		var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
		photoswipe.init();
	};

	PostsTable.prototype.onPage = function( event ) {
		// Animate back to top of table on next/previous page event
		event.data.table.scrollToTop();
	};

	PostsTable.prototype.onProcessing = function( event, settings, processing ) {
		if ( processing ) {
			event.data.table.$table.block( PostsTable.blockConfig );
		} else {
			event.data.table.$table.unblock();
		}
	};

	PostsTable.prototype.onReset = function( event ) {
		event.preventDefault();

		var table = event.data.table,
			dataTable = table.getDataTable();

		// Reset responsive child rows
		table.$table.find( 'tr.child' ).remove();
		table.$table.find( 'tr.parent' ).removeClass( 'parent' );

		// Reset select filters
		if ( table.$filters.length ) {
			table.$filters.val( '' );
		}

		// Clear search for all filtered columns
		dataTable.columns( 'th[data-searchable="true"]' ).search( '' );

		// Reset ordering
		var initialOrder = table.$table.attr( 'data-order' );
		if ( initialOrder.length ) {
			var orderArray = initialOrder.replace( /[\[\]\" ]+/g, '' ).split( ',' );
			if ( 2 === orderArray.length ) {
				dataTable.order( orderArray );
			}
		}

		// Find initial search term
		var searchTerm = ( 'search' in table.config && 'search' in table.config.search ) ? table.config.search.search : '';

		// Reset global search and page length
		dataTable
			.search( searchTerm )
			.page.len( table.config.pageLength )
			.draw();

		return false;
	};

	PostsTable.prototype.onResponsiveDisplay = function( event, datatable, row, showHide, update ) {
		if ( showHide && ( typeof row.child() !== 'undefined' ) ) {
			// Initialise elements in child row
			PostsTable.initMedia( row.child() );

			var table = event.data.table;
			table.$table.trigger( 'responsive-display.ptp', [table, row.child()] );
		}
	};

	PostsTable.prototype.onWindowLoad = function( event ) {
		var table = event.data.table;

		// Recalc column sizes on window load (e.g. to correctly contain media playlists)
		table.getDataTable()
			.columns.adjust()
			.responsive.recalc();

		table.$table.trigger( 'load.ptp', [table] );
	};

	/******************************************
	 * JQUERY PLUGIN
	 ******************************************/

	/**
	 * @deprecated 1.6 Use $.postsTable()
	 * @returns A jQuery object representing the post table (i.e. the table element)
	 */
	$.fn.posts_table = function() {
		new PostsTable( this );
		return this;
	};

	/**
	 * jQuery plugin to create a post table for the current set of matched elements.
	 *
	 * @returns jQuery object - the set of matched elements the function was called with (for chaining)
	 */
	$.fn.postsTable = function() {
		return this.each( function() {
			new PostsTable( $( this ) );
		} );
	};

	$( document ).ready( function() {
		if ( 'DataTable' in $.fn && $.fn.DataTable.ext ) {
			// Change DataTables error reporting to throw rather than alert
			$.fn.DataTable.ext.errMode = 'throw';
		}

		// Initialise all post tables
		$( '.posts-data-table' ).postsTable();
	} );

} )( jQuery, window, document );