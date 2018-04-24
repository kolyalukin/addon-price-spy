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

	public function __construct( FileManager $fileManager ) {
		$this->fileManager = $fileManager;

		$this->hooks();
	}

	public function hooks(){
		add_action( 'premmerce_price_spy_before_form_inputs', [ $this, 'addInputs' ] );
		add_action( 'premmerce_price_spy_loggin_before_form_inputs', [ $this, 'addInputs' ] );

		add_action( 'premmerce_price_spy_form_title', [ $this, 'titleFilter' ] );
		add_action( 'premmerce_price_spy_loggin_form_title', [ $this, 'titleFilter' ] );
	}

	public function addInputs(){
		?>
			<label for="percent">Percent change: </label>
			<input type="text" name="data[percent]" placeholder="optional" id="percent"></br>
		<?php
	}

	public function titleFilter(){
		return '<h3>' . __( 'Fill form', 'addon-price-spy' ) . '</h3>';
	}

}