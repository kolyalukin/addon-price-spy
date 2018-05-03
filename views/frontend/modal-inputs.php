<?php if ( ! defined( 'WPINC' ) ) die; ?>

<p class="form-row form-row-wide">
	<label for="percent"><?php echo __( '(Optional)', 'addon-price-spy' ) . ' ' . __( 'When the price decreases by', 'addon-price-spy' )?>: </label>
	<input type="number" min="0" max="100" step="any" name="data[percent]" placeholder="%" id="percent" class="input-text"><br />
</p>
<?php if( ! is_user_logged_in() ): ?>
	<p class="form-row form-row-wide">
		<label for="username"> <?php _e( 'Name', 'addon-price-spy' ) ?>: </label>
		<input type="text" name="data[name]" placeholder="<?php _e( 'Name', 'addon-price-spy' ) ?>" id="username" class="input-text" required><br />
	</p>
<?php endif;?>