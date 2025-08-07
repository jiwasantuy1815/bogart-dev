<?php
namespace GutenkitPro\Admin\Updater;
use GutenkitPro\Admin\Updater\GutenkitPluginUpdater;
use GutenkitPro\Traits\PluginHelper;

defined( 'ABSPATH' ) || exit;

class Init{
    public function __construct(){
        
        $license_key = explode('-', trim( get_option('__gutenkit_license_key__') ));
        $license_key = !isset($license_key[0]) ? '' : $license_key[0]; 
        
        $plugin_dir_and_filename = \GutenkitPro::plugin_dir() . 'gutenkit-blocks-addon-pro.php';

        $active_plugins = get_option( 'active_plugins' );
        foreach ( $active_plugins as $active_plugin ) {
            if ( false !== strpos( $active_plugin, 'gutenkit-blocks-addon-pro.php' ) ) {
                $plugin_dir_and_filename = $active_plugin;
                break;
            }
        }
        if ( ! isset( $plugin_dir_and_filename ) || empty( $plugin_dir_and_filename ) ) {
            throw new \Exception( 'Plugin not found! Check the name of your plugin file in the if check above' );
        }

        new GutenkitPluginUpdater(
            \GutenkitPro::account_url(),
            $plugin_dir_and_filename,
            [
                'version' => \GutenkitPro::VERSION, // current version number.
                'license' => $license_key, // license key (used get_option above to retrieve from DB).
                'item_id' => \GutenkitPro::product_id(), // id of this product in EDD.
                'author'  => \GutenkitPro::author_name(), // author of this plugin.
                'url'     => home_url(),
            ]
        );
    }
}