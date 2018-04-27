<?php namespace Addon\PriceSpy\Frontend;

use Premmerce\SDK\V1\FileManager\FileManager;

/**
 * Class Frontend
 *
 * @package Addon\PriceSpy\Frontend
 */
class Frontend {


	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	*
	* Frontend constructor. Call main mathods
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
		add_action( 'premmerce_price_spy_before_form_inputs', [ $this, 'addInputs' ] );
		add_action( 'premmerce_price_spy_loggin_before_form_inputs', [ $this, 'addInputs' ] );

		add_action( 'premmerce_price_spy_form_title', [ $this, 'titleFilter' ] );
		add_action( 'premmerce_price_spy_loggin_form_title', [ $this, 'titleFilter' ] );

		add_action( 'premmerce_price_spy_frontend_tableheader', [ $this, 'addPercentColumn' ] );
		add_action( 'premmerce_price_spy_frontend_tablebody', [ $this, 'renderPercentColumn' ] );
	}

	/**
	*
	* Add inputs to modal price spy box
	*/
	public function addInputs(){
		$this->fileManager->includeTemplate('frontend/modal-inputs.php');
	}

	/**
	*
	* Return new title of modal price spy box
	*/
	public function titleFilter(){
		$this->fileManager->includeTemplate('frontend/modal-title.php');
	}

	/**
	*
	* Render title of percent column
	*/
	public function addPercentColumn(){
		echo '<th>' . __( 'Spying for decrease by', 'addon-price-spy' ) . '</th>';
	}

	/**
	*
	* Render value of percent column
	* @param object $item
	*/
	public function renderPercentColumn( $item ){
		
		$value = '';

		if( !empty( $item->data ) ){
			$data = json_decode( $item->data );
			$value = empty( $data->percent ) ? '' : $data->percent . ' %';
		}

		echo '<td>' . $value . '</td>';
	}
}