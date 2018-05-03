<?php namespace Addon\PriceSpy\Email;

use Premmerce\SDK\V1\FileManager\FileManager;
use WC_Email;

if( ! class_exists('WC_Email') ){
    include_once( WC_ABSPATH . 'includes/emails/class-wc-email.php' );
}

class WC_PremmerceAddonPriceSpyEmail extends WC_Email {

	/**
	* @var FileManager $fileManager
	*/
	private $fileManager;

    /**
    * @var string userEmail
    */
    private $userEmail;

    /**
    * @var string userName
    */
    private $userName;

    /**
    * @var \WC_Product product
    */
    private $product;
	
    /**
    * @var string formData
    */
    private $formData;
    

    /**
     * Call when price was changed
     *
     * Register base options for email
     *
     * @param $fileManager
     */
	public function __construct( $fileManager ) {
		
		$this->fileManager = $fileManager;

        $this->id           = 'wc_premmerce_addon_price_spy_email';
        $this->title        = __( 'Percent addon price spy', 'addon-price-spy' );
        $this->description  = __( 'Emails send when a customer starts spy product price', 'addon-price-spy' );


        $this->heading = __( 'User has started spying product price', 'addon-price-spy' );
        $this->subject = __( 'User has started spying product price', 'addon-price-spy' );

        $this->template_base  = $this->fileManager->getPluginDirectory() . 'views/email/';
        $this->template_html  =  'addon_price_spy.php';
        $this->template_plain =  'addon_price_spy_plain.php';

        $this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );

        parent::__construct();
	}

	/**
     * get_content_html function.
     *
     * @return string
     */
    public function get_content_html() {

        return $this->fileManager->renderTemplate('/email/' . $this->template_html, [
            'email_heading' => $this->get_heading(),
            'userEmail'     => $this->userEmail,
            'userName'      => $this->userName,
            'product'       => $this->product,
            'data'          => $this->formData,
        ]);
    }

    /**
     * get_content_plain function.
     *
     * @return string
     */
    public function get_content_plain() {
        
        return $this->fileManager->renderTemplate('/email/' . $this->template_plain, [
            'email_heading' => $this->get_heading(),
            'userEmail'     => $this->userEmail,
            'userName'      => $this->userName,
            'product'       => $this->product,
            'data'          => $this->formData,
        ]);
    }

	/**
     * Initialize Settings Form Fields
     *
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'    => array(
                'title'   => __( 'Enable/Disable', 'addon-price-spy' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable this email notification', 'addon-price-spy' ),
                'default' => 'yes'
            ),
            'subject'    => array(
                'title'       => __( 'Subject', 'addon-price-spy' ),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => sprintf( __( 'Available placeholders: %s', 'addon-price-spy' ), '<code>{site_title}</code>' )
            ),
            'heading'    => array(
                'title'       => __( 'Email Heading', 'addon-price-spy' ),
                'type'        => 'text',
                'desc_tip'    => true,
                'placeholder' => '',
                'default'     => '',
                'description' => __('Available %site_title%', 'addon-price-spy' )
            ),
            'email_type' => array(
                'title'       => __( 'Email type', 'addon-price-spy' ),
                'type'        => 'select',
                'description' => __( 'Choose which format of email to send.', 'addon-price-spy' ),
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'     => __( 'Plain text', 'premmerce-price-spy' ),
                    'html'      => __( 'HTML', 'premmerce-price-spy' ),
                )
            ),
            'recipient' => array(
                'title'         => __( 'Recipient', 'addon-price-spy' ),
                'type'          => 'email',
                'description'   => __( 'Enter recipient for this notification about new price spy.', 'addon-price-spy' ),
                'placeholder'   => get_option( 'admin_email' ),
                'default'       => get_option( 'admin_email' ),
                'desc_tip'      => true,
            )
        );
    }

    /**
     * Call when price changed
     *
     * @param string $userEmail
     * @param string $userName
     * @param \WC_Product $product
     * @param object $formData
     */
    public function trigger( $userEmail, $userName, $product, $formData ){

        $this->userEmail = $userEmail;
        $this->userName  = $userName;
        $this->product   = $product;
        $this->formData  = $formData;

        $this->replaceShortCodes();

        if( $this->is_enabled() ){

            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(),  $this->get_headers(), $this->get_attachments() );
        }
    }

    /**
     * Replace shortcodes in subject and heading
     *
     */
    public function replaceShortCodes(){
        $this->placeholders[ '{site_title}' ] = get_bloginfo('name');
    }
}