<?php
/**
 * Product price customer spy was changed
 *
 * This template can be overridden by copying it to storefront/woocommerce/price-spy.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p> <?php __( 'User started spying product price', 'addon-price-spy' ) ?> </p>

<?php $link = '<a href="' . get_permalink( $product->get_id() ) . '">' . $product->get_name() . '</a>'; ?>

<?php if( !empty( $data ) && !empty( $data->percent ) ): ?>
	<p> <?php printf( __( '%s - spying for decrease by %d ', 'addon-price-spy' ), $link, $data->percent ) ?>% </p>
<?php else: ?>
	<p> <?php printf( __( '%s - spying for change price', 'addon-price-spy' ), $link ) ?> </p>
<?php endif;?>

<p> <b><?php _e( 'Name', 'addon-price-spy' ) ?></b>: <?php echo $userName; ?></p>
<p> <b><?php _e( 'Email', 'addon-price-spy' ) ?></b>: <?php echo $userEmail; ?></p>

<?php do_action( 'woocommerce_email_footer', $email );