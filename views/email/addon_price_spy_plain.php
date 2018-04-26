<?php
/**
 * Product price customer spy was changed
 *
 * This template can be overridden by copying it to storefront/woocommerce/price-spy.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$link = get_permalink( $product->get_id() );

echo "= " . $email_heading . " =\n\n";

echo __( 'User started spying product price', 'addon-price-spy' );


if( !empty( $data ) && !empty( $data->percent ) ){
	echo sprintf( __( '%s - spying for decrease by %d ', 'addon-price-spy' ), $link, $data->percent ) . "%";
}else{
	printf( __( '%s - spying for change price', 'addon-price-spy' ), $link );
}
echo __( 'Name', 'addon-price-spy' ) . ': '. $userName;
echo __( 'Email', 'addon-price-spy' ) . ': '.  $userEmail;

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
