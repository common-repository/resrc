<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   ReSRC_For_WordPress
 * @author    ReSRC <team@resrc.it>
 * @license   GPL-2.0+
 * @link      http://www.resrc.it/wordpress
 * @copyright 2014 ReSRC
 */
?>

<div class="wrap">
	<div class="wrap">
        <h2>ReSRC for WordPress</h2>
        <form action="options.php" method="POST">
            <?php settings_fields( 'resrc-options-group' ); ?>
            <?php do_settings_sections( 'ReSRC' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
</div>