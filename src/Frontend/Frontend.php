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
	}

	/**
	*
	* Add inputs to modal price spy box
	* @return string
	*/
	public function addInputs(){
		$this->fileManager->includeTemplate('frontend/modal-inputs.php');
	}

	/**
	*
	* Return new title of modal price spy box
	* @return string
	*/
	public function titleFilter(){
		$this->fileManager->includeTemplate('frontend/modal-title.php');
	}

}