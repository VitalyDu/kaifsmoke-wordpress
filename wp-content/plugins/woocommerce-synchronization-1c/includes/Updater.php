<?php

namespace Itgalaxy\Wc\Exchange1c\Includes;

class Updater
{
    /**
     * @var int|string
     */
    private $pluginID;

    /**
     * @var string
     */
    private $option;

    /**
     * @var string
     */
    private $version;

    /**
     * Updater constructor.
     *
     * @param object $bootstrap
     */
    public function __construct($bootstrap)
    {
        $this->pluginID = $bootstrap::PLUGIN_ID;
        $this->option = $bootstrap::PURCHASE_CODE_OPTIONS_KEY;
        $this->version = $bootstrap::PLUGIN_VERSION;

        $this->init($bootstrap::$plugin);
    }

    /**
     * @param string $pluginFile
     *
     * @return void
     */
    private function init($pluginFile)
    {
        $code = \get_site_option($this->option, '');

        if (empty($code)) {
            return;
        }

        $checker = \Puc_v4_Factory::buildUpdateChecker(
            'https://envato.itgalaxy.company/envato/plugin-request',
            $pluginFile,
            'woocommerce-synchronization-1c'
        );

        $checker->addQueryArgFilter(function () {
            return [
                'purchaseCode' => \get_site_option($this->option, ''),
                'itemID' => $this->pluginID,
                'version' => $this->version,
                'action' => 'plugin_update',
                'domain' => !empty(\network_site_url()) ? \network_site_url() : \get_home_url(),
            ];
        });
    }
}
