<?php

namespace WCML\Utilities\Suspend;

class PostsQueryFiltersFactory {

	public static function create() {
		global $wpml_query_filter;

		return new Filters( [
			[ 'posts_join', [ $wpml_query_filter, 'posts_join_filter' ], 10, 2 ],
			[ 'posts_where', [ $wpml_query_filter, 'posts_where_filter' ], 10, 2 ],
		] );
	}
}
