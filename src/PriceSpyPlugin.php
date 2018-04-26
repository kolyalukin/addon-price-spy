<?php namespace Addon\PriceSpy;

use Premmerce\SDK\V1\FileManager\FileManager;
use Premmerce\SDK\V1\Notifications\AdminNotifier;
use Addon\PriceSpy\Admin\Admin;
use Addon\PriceSpy\Frontend\Frontend;

/**
 * Class PriceSpyPlugin
 *
 * @package Addon\PriceSpy
 */
class PriceSpyPlugin {

	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * PriceSpyPlugin constructor.
	 *
     * @param string $mainFile
	 */
    public function __construct($mainFile) {
        $this->fileManager = new FileManager($mainFile);
        $this->notifier    = new AdminNotifier();

        add_action('plugins_loaded', [ $this, 'loadTextDomain' ]);
        add_action('admin_init', [$this, 'checkRequirePlugins']);

	}

	/**
	 * Run plugin part
	 */
	public function run() {

		$valid = count( $this->validateRequiredPlugins() ) === 0;
		
		if( $valid ){
			if ( is_admin() ) {
				new Admin( $this->fileManager );
			} else {
				new Frontend( $this->fileManager );
			}

		}

	}

    /**
     * Load plugin translations
     */
    public function loadTextDomain()
    {
        $name = $this->fileManager->getPluginName();
        load_plugin_textdomain('addon-price-spy', false, $name . '/languages/');
    }

    /**
     * Check required plugins and push notifications
     */
    public function checkRequirePlugins(){
        $message = __( 'The %s plugin requires %s plugin to be active!', 'addon-price-spy' );

        $plugins = $this->validateRequiredPlugins();

        if( count( $plugins ) ){
            foreach($plugins as $plugin){
                $error = sprintf($message, 'Premmerce Price Spy addon', $plugin);
                $this->notifier->push($error, AdminNotifier::ERROR, false);
            }
        }

    }

	/**
	 * Validate required plugins
	 *
	 * @return array
	 */
	private function validateRequiredPlugins(){

		$plugins = [];

		if( ! function_exists('is_plugin_active' ) ){
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		/**
		 * Check if WooCommerce is active
		 **/
		if(!(is_plugin_active('woocommerce/woocommerce.php') || is_plugin_active_for_network('woocommerce/woocommerce.php'))){
			$plugins[] = '<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>';
		}

		/**
		 * Check if Price spy is active
		 **/
		if(! ( is_plugin_active( 'premmerce-price-spy/premmerce-price-spy.php' ) || 
			is_plugin_active_for_network( 'premmerce-price-spy/premmerce-price-spy.php' )
		)){
			$plugins[] = '<a target="_blank" href="#">Premmerce Price Spy</a>';
		}

		return $plugins;
	}

	/**
	 * Fired when the plugin is activated
	 */
	public function activate() {
		// TODO: Implement activate() method.
	}

	/**
	 * Fired when the plugin is deactivated
	 */
	public function deactivate() {

	}

	/**
	 * Fired during plugin uninstall
	 */
	public static function uninstall() {

	}
}