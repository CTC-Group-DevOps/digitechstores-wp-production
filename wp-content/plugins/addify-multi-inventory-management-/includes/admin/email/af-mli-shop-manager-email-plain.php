<?php

defined( 'ABSPATH' ) || exit;

if ( $additional_content ) {

	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );

}
