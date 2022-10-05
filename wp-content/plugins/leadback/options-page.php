
<div class="wrap">
    <h2><?php _e('Leadback Settings', 'leadback') ?></h2>

    <form method="post" action="options.php">

        <?php settings_fields( 'leadback-settings-group' ); ?>
    	<?php settings_errors(); ?>	

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="widget-code-field"><?php _e('Widget code', 'leadback') ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <p>
                                <?php _e('Paste your LeadBack Widget code or click &quot;Get Widget code&quot; button below. You need to have a <a href="https://leadback.ru/?utm_source=wp-plugin" target="_blank">Leadback.ru account</a>.', 'leadback') ?>
                            </p>
                            <p>
                                <a href="javascript:void(0);" id="leadback-select-widget" data-ldbk-btn-type="select-code" class="button-secondary"><?php _e('Get Widget code', 'leadback') ?></a>
                            </p>
                            <p>
                                <textarea name="leadback_widget_code" placeholder="" cols="65" rows="15" id="widget-code-field" class="code large-text"><?php echo esc_textarea(get_option('leadback_widget_code')); ?></textarea>
                            </p>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
    	</table>

        <p class="submit">
    		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>

<script>
    window.onload = function () {
        (new LeadbackSDK({url_params: {utm_source: 'wp-plugin'}}).setCodeSelectCallback(function (code) {
            document.getElementById('widget-code-field').value = code;
        }));
    }
</script>
<script src="<?= plugin_dir_url(__FILE__); ?>assets/js/LeadbackSDK.js"></script>

<?php
load_plugin_textdomain( 'leadback', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
?>
