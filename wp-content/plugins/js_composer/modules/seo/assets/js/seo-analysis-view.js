/* =========================================================
 * Copyright 2023 Wpbakery
 *
 * WPBakery Page Builder SEO Analysis panel in the navbar
 *
 * ========================================================= */
/* global Backbone, vc, _ */
if ( !window.vc ) {
	window.vc = {};
}
( function ( $ ) {
	'use strict';

	// Update the Content Analysis panel contents via Backbone View
	vc.SeoAnalysisView = Backbone.View.extend({
		$wpbContentWrapper: null,
		$navbarIcon: null,
		currentBadge: '',
		events: {
			'click [data-vc-ui-element="seo-results-toggle"]': 'toggleSection'
		},
		initialize: function () {
			this.$wpbContentWrapper = this.getContentWrapper();
			this.$navbarIcon = $( '.vc_seo-button' );
			vc.seo_utils.createMeasurementElement();
			vc.seo_checks.analyzeContent( this.$wpbContentWrapper );
			this.render();
			this.setIconBadge();
			this.setEvents();
		},
		render: function () {
			vc.seo_checks.analyzeContent( this.$wpbContentWrapper );
			var resultsElements = this.getNotificationsHtml();
			this.$el.html( resultsElements );
			this.setIconBadge();
		},
		setEvents: function () {
			this.debouncedRender = _.debounce( this.render, 200 );
			this.listenTo( this.model, 'formData', this.debouncedRender );
			var isFrontendEditor = 'admin_frontend_editor' === window.vc_mode;
			if ( isFrontendEditor ) {
				vc.events.on( 'afterRender', this.debouncedRender, this );
				vc.events.on( 'shortcodeView:updated', this.debouncedRender, this );
				vc.events.on( 'afterLoadShortcode', this.debouncedRender, this );
			} else {
				vc.events.on( 'shortcodes:update', this.debouncedRender, this );
				vc.events.on( 'shortcodes:add', this.debouncedRender, this );
				vc.events.on( 'undoredo:undo', this.debouncedRender, this );
				vc.events.on( 'undoredo:redo', this.debouncedRender, this );
			}
			vc.events.on( 'shortcodes:destroy', this.debouncedRender, this );
		},
		getNotificationsHtml: function () {
			var _this = this;
			var results = this.model.get( 'results' );
			var newResults = {
				success: [],
				problems: [],
				warnings: []
			};
			// Assign each item for corresponding result type
			results.forEach( function ( result ) {
				newResults[result.state].push( result );
			});
			var resultsElements = [];
			var resultsOrder = [ 'problems', 'warnings', 'success' ];
			var resultsKeys = Object.keys( newResults );
			var sortedKeys = resultsKeys.sort( function ( a, b ) {
				// Find the index of the names in the customOrder array
				var orderA = resultsOrder.indexOf( a );
				var orderB = resultsOrder.indexOf( b );

				// Compare the order values
				return orderA - orderB;
			});
			sortedKeys.forEach( function ( key ) {
				if ( newResults[key].length ) {
					var html = _this.getResultsHtml( key, newResults[key]);
					resultsElements.push( html );
				}
			});
			return resultsElements;
		},
		setIconBadge: function () {
			var state = 'success';
			var allResults = this.model.get( 'results' );
			var getIssue = function ( state ) {
				return allResults.find( function ( result ) {
					return state === result.state;
				});
			};
			if ( getIssue( 'problems' ) ) {
				state = 'problems';
			} else if ( getIssue( 'warnings' ) ) {
				state = 'warnings';
			}
			this.$navbarIcon.removeClass( this.currentBadge );
			this.currentBadge = 'vc_ui-badge--' + state;
			this.$navbarIcon.addClass( this.currentBadge );
		},
		toggleSection ( e ) {
			$( e.currentTarget ).closest( '.vc_ui-seo-results-section' )
				.toggleClass( 'vc_ui-seo-results-section--collapsed' );
		},
		getResultsHtml ( type, data ) {
			const $section = $(
				`<div class="vc_ui-seo-results-section">
					<div class="vc_ui-seo-results-header" data-vc-ui-element="seo-results-toggle">
						<span class="vc_ui-seo-results-title">${window.i18nLocale[type]}</span>
						<span class="vc_ui-seo-results-toggle"></span>
					</div>
					<div class="vc_ui-seo-results-body">
						<ul class="vc_ui-seo-results-list"></ul>
					</div>
				</div>`
			);
			const $resultList = $section.find( 'ul' );
			$.each( data, ( i, val ) => {
				const $item = $( `<li class="vc_ui-seo-results-list-item vc_ui-seo-results-list-item--${val.state}">${val.title}: ${val.description}</li>` );
				$resultList.append( $item );
			});
			return $section;
		},
		getContentWrapper: function () {
			return 'admin_frontend_editor' === window.vc_mode ? vc.$frame.contents().find( '.wpb-content-wrapper' ) : $( '#wpbakery_content' );
		}
	});

	// Initialize SEO Content Analysis Editor
	vc.events.on( 'app.render', function () {
		// Add timeout to let content render before analyzing it
		setTimeout( function () {
			vc.seo_analysis_view = new vc.SeoAnalysisView({
				el: '#vc_ui-seo-analysis',
				model: vc.seo_storage
			});
		}, 1000 );
	});

})( window.jQuery );
