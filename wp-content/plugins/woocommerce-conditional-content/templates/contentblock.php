<?php
/**
 * The main template for displaying conditional content blocks.
 */
?>

<?php
/* @var WP_Post $content */
?>

<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wccc-content-block wccc-content-block-<?php echo esc_attr( $content->ID ); ?>">
    <?php if ( apply_filters( 'woocommerce_conditional_content_apply_the_content_filter', true, $content ) ) : ?>
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Post content is intentionally HTML and filtered through the_content
        echo apply_filters( 'woocommerce_conditional_content_the_content', apply_filters( 'the_content', $content->post_content ) );
        ?>
    <?php else : ?>
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Post content is intentionally HTML
        echo apply_filters( 'woocommerce_conditional_content_the_content', $content->post_content );
        ?>
    <?php endif; ?>
</div>
