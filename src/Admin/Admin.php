<?php namespace Addon\PriceSpy\Admin;

use Premmerce\SDK\V1\FileManager\FileManager;

/**
 * Class Admin
 *
 * @package Addon\PriceSpy\Admin
 */
class Admin {

	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * Admin constructor.
	 *
	 * Register menu items and handlers
	 *
	 * @param FileManager $fileManager
	 */
	public function __construct( FileManager $fileManager ) {
		$this->fileManager = $fileManager;

		$this->hooks();
	}

	public function hooks(){

		add_action( 'wp_ajax_add_price_spy', [ $this, 'handleData' ] );
		add_action( 'wp_ajax_nopriv_add_price_spy', [ $this, 'handleData' ] );

		add_filter( 'premmerce_price_spy_custom_email_condition', [ $this, 'percentCondition' ] , 1, 3);
		add_filter( 'premmerce_price_spy_column_list', [ $this, 'addPercentColumn' ] );

		add_action( 'premmerce_price_spy_render_columns', [ $this, 'renderPercent' ], 1, 2 );
	
	}

	public function handleData( $form_data ){
		
		add_filter('premmerce_price_spy_form_data', function( $form_data ) {
			
			$data = [];

			if( isset( $form_data['percent'] ) && !empty( $form_data['percent'] ) ){
				$data['percent'] = (int) $form_data['percent'];
				
				if( $data['percent'] > 99 || $data['percent'] < 1 ){
					return null;
				}
			}

			return ! empty($data) ? json_encode( $data ) : null;
		});
	}

	public function percentCondition( $send, $spy, $product ){

		if ( $send === false ) return;

		if( $spy->data != null && isset( json_decode( $spy->data )->percent ) ){

		    $data = json_decode( $spy->data );

		    if( abs( $spy->old_price - $product->get_price() ) >= ( ($spy->old_price / 100) * $data->percent ) ){
		        return true;
		    }
		}
		return false;
	}

	public function addPercentColumn( $list ){

		$column['percent'] = __( 'Percent', 'addon-price-spy' );

		return $list + $column;
	}

	public function renderPercent( $item, $columnName ){

		if( $columnName == 'percent' ){

			$data = json_decode( $item->data );

			if( isset($data->percent) ) echo $data->percent . "%";
		}

	}

}