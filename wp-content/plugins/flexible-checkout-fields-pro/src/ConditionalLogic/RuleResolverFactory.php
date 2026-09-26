<?php

namespace WPDesk\FCF\Pro\ConditionalLogic;

use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\DateResolver;
use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\NullResolver;
use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\RuleResolver;
use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\FPFRuleResolver;
use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\CartRuleResolver;
use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\UserRuleResolver;
use WPDesk\FCF\Pro\ConditionalLogic\Resolvers\ShippingRuleResolver;

/**
 * Factory class for rule resolvers.
 */
class RuleResolverFactory {

	/**
	 * Create a rule resolver.
	 */
	public function create( string $category ): RuleResolver {
		switch ( $category ) {
			case 'user':
				return new UserRuleResolver();
			case 'cart':
			case 'cart_contains':
				return new CartRuleResolver();
			case 'shipping_method':
				return new ShippingRuleResolver();
			case 'date':
				return new DateResolver();
			case 'fpf_field':
				return new FPFRuleResolver();
			default:
				return new NullResolver();
		}
	}
}
