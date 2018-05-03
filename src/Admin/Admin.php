<?php namespace Addon\PriceSpy\Admin;

use Premmerce\SDK\V1\FileManager\FileManager;
use Addon\PriceSpy\Email\WC_PremmerceAddonPriceSpyEmail;

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

	/**
	*
	* Register hooks
	*/
	public function hooks(){

		add_filter( 'premmerce_price_spy_custom_email_condition', [ $this, 'percentCondition' ] , 1, 3);
		add_filter( 'premmerce_price_spy_column_list', [ $this, 'addPercentColumn' ] );

		add_action( 'premmerce_price_spy_render_columns', [ $this, 'renderPercentColumn' ], 1, 2 );

		add_filter( 'woocommerce_email_classes', [ $this, 'addEmailClass' ] );

		add_action( 'premmerce_price_spy_loggin_price_spy_added', [ $this, 'notify' ], 1, 4 );
		add_action( 'premmerce_price_spy_price_spy_added', [ $this, 'notify' ], 1, 4 );

		add_filter( 'premmerce_price_spy_form_data', [ $this, 'handleData' ], 1, 1 );
	}
	/**
	*
	* Data handler, call when price spy form has been submitted.
	* Adds filter to form_data to get percent information;
	*
	* @param array $form_data
    * @return array $form_data
	*/
	public function handleData( $form_data ){

			$data = [];

			if( isset( $form_data['percent'] ) && !empty( $form_data['percent'] ) ){
				$data['percent'] = (int) $form_data['percent'];
				
				if( $data['percent'] > 99 || $data['percent'] < 1 ){
					$data['percent'] = 0;
				}
			}else{
				$data['percent'] = 0;
			}

			if( is_user_logged_in() ){
				$current_user = wp_get_current_user();
				$data['name'] = $current_user->user_firstname;
			}else{

				if( isset( $form_data['name'] ) && !empty( $form_data['name'] ) ){
					$data['name'] = substr( wp_strip_all_tags( $form_data['name'] ), 0, 20 );
				}else{
					$data['name'] = __( 'Customer', 'addon-price-spy' );
				}
			}

			return $data;
	}

	/**
	*
	* Condition to email send. In this case check if price changed more than on data.percent
	*
	* @param bool $send
	* @param object $spy
    * @param object $product
	* @return bool $result
	*/
	public function percentCondition( $send, $spy, $product ){

		// if prev condition was false than all conditions must be false
		if ( $send === false ) return false;

		$data = $spy->data;

		if( !is_null( $data ) && isset( $data->percent ) && $data->percent != 0 ){
		    if( ( $spy->old_price - $product->get_price() ) >= ( ( $spy->old_price / 100 ) * $data->percent ) ){
		        return true;
		    } else {
		    	return false;
		    }
		}
		
		return true;
	}

	/**
	*
	* Add percent column to price spy table at admin page
    *
	* @param array $list
	* @return array $list
	*/
	public function addPercentColumn( $list ){

		$column['percent'] = __( 'Spying for decrease by', 'addon-price-spy' );

		return $list + $column;
	}

	/**
	*
	* Render percent column.
    *
	* @param object $item
	* @param string $columnName
	*/
	public function renderPercentColumn( $item, $columnName ){

		if( $columnName == 'percent' ){

			$data = $item->data;

			if( isset( $data->percent ) && $data->percent != 0 ) echo $data->percent . "%";
		}

	}
	/**
	 * 
	 * Add WC_PremmerceAddonPriceSpyEmail class to woocommerce email classes
	 *
	 * @param array $email_classes
     * @return array $email_classes
	 */
	public function addEmailClass( $email_classes ) {
		
		$email_classes['WC_PremmerceAddonPriceSpyEmail'] = new WC_PremmerceAddonPriceSpyEmail( $this->fileManager );

		return $email_classes;
	}

	/**
	 * 
	 * Send email when someone starts spy product price
	 *
	 * @param \WC_Product $product
	 * @param mixed $info
	 * @param int $variation_id
	 * @param array $data
	 */
	public function notify( $product, $info, $variation_id, $data ){

	    //convert array $data to stdObject
        $data = (object) $data;

		$user_email = is_string( $info ) ? $info : $info->user_email;
		$user_name 	= is_string( $info ) ? $data->name : $info->user_firstname;

		WC()->mailer();

		$email = new WC_PremmerceAddonPriceSpyEmail( $this->fileManager );

		$email->trigger( $user_email, $user_name, $product, $data );
	}

}