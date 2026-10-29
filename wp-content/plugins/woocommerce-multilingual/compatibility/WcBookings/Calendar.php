<?php

namespace WCML\Compatibility\WcBookings;

use WPML\LIB\WP\Hooks;

use function WPML\FP\spreadArgs;

class Calendar implements \IWPML_Action {

	private const PRIORITY_BEFORE_BOOKINGS_SYNC = 9;

	private const PRIORITY_AFTER_BOOKINGS_SYNC = 11;

	private ?int $bookingBeingSynced = null;

	public function add_hooks() {
		foreach ( $this->getSyncTriggerHooks() as $hook ) {
			Hooks::onAction( $hook, self::PRIORITY_BEFORE_BOOKINGS_SYNC )
				->then( spreadArgs( [ $this, 'startTrackingBooking' ] ) );
			Hooks::onAction( $hook, self::PRIORITY_AFTER_BOOKINGS_SYNC )
				->then( [ $this, 'stopTrackingBooking' ] );
		}

		Hooks::onFilter( 'option_wc_bookings_gcalendar_refresh_token' )
			->then( spreadArgs( [ $this, 'preventSyncForDuplicate' ] ) );
	}

	public function startTrackingBooking( $bookingId ) {
		$this->bookingBeingSynced = (int) $bookingId;
	}

	public function stopTrackingBooking() {
		$this->bookingBeingSynced = null;
	}

	public function preventSyncForDuplicate( $refreshToken ) {
		if ( $this->bookingBeingSynced && $this->isDuplicate( $this->bookingBeingSynced ) ) {
			return false;
		}

		return $refreshToken;
	}

	private function isDuplicate( $bookingId ) {
		return (bool) get_post_meta( $bookingId, \WCML_Bookings::BOOKING_DUPLICATE_OF_META, true );
	}

	private function getSyncTriggerHooks() {
		$paidStatuses = apply_filters( 'woocommerce_booking_is_paid_statuses', [ 'confirmed', 'paid', 'complete' ] );

		$hooks = array_map( fn( $status ) => 'woocommerce_booking_' . $status, $paidStatuses );

		$hooks[] = 'woocommerce_booking_in-cart_to_unpaid';

		return $hooks;
	}
}
