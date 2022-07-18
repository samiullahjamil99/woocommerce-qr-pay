<?php
add_action("admin_menu","woo_qr_options_page");
function woo_qr_options_page() {
    add_menu_page(
        'CAM-ON Options',
        'CAM-ON',
        'manage_options',
        'woo-qr',
        'woo_qr_options_html',
    );
}
add_action( 'admin_init', 'register_woo_qr_settings' );
function register_woo_qr_settings() {
    register_setting( 'woo-qr','qr-hostnames' );
    add_settings_section('woo-main-setting','Main Settings','woo_qr_main_settings_description_html','woo-qr');
    add_settings_field('woo-hostnames','Hostnames','woo_qr_main_fields_cb','woo-qr', 'woo-main-setting', array('label_for' => 'hostnames'));
}
function woo_qr_main_settings_description_html() {
    ?>
    <p>For hostnames, enter hostnames separated by commas</p>
    <?php
}
function woo_qr_main_fields_cb($args) {
    $options = get_option("qr-hostnames");
    ?>
    <input type="text" name="qr-hostnames[<?php echo $args['label_for']; ?>]" value="<?php echo isset($options) ? $options[$args['label_for']] : ''; ?>">
    <?php
}
function woo_qr_options_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'woo-qr' ), 'updated' );
    }
 
    // show error/update messages
    settings_errors( 'wporg_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "wporg"
            settings_fields( 'woo-qr' );
            // output setting sections and their fields
            // (sections are registered for "wporg", each field is registered to a specific section)
            do_settings_sections( 'woo-qr' );
            // output save settings button
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}