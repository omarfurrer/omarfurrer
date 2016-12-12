<?php

/**
 * Class GridGallery_Installer_Module
 */
class GridGallery_Installer_Module extends GridGallery_Core_Module
{
    const LAST_VERSION = 'grid_gallery_last_version';
    const LAST_PRO_VERSION = 'grid_gallery_last_pro_version';

    /**
     * @var GridGallery_Installer_Model
     */
    protected $model;

    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        $config = $this->getEnvironment()->getConfig();
        $currentVersion = $config->get('plugin_version');
        $lastVersion = get_option(self::LAST_VERSION);

        if ($lastVersion === false) {
            update_option(self::LAST_VERSION, $currentVersion);
        }

        if (version_compare($currentVersion, $lastVersion, '>')) {

            $this->cleanTwigCacheDir($config->get('plugin_cache_twig'));
            $this->cleanHTMLCache($config->get('plugin_cache_galleries'));

            update_option(self::LAST_VERSION, $currentVersion);

            if ($lastVersion !== false) {
                // Skip show welcome page if user updates plugin.
                update_option($config->get('db_prefix') . 'welcome_page_was_showed', 1);
            }


            if (false === $config->get('plugin_db_update')) {
                return;
            }

            $model = self::getModel();
            $queries = self::getQueries();

            $model->update($queries);
        }

        if ($config->get('is_pro')) {
            $lastProVersion = get_option(self::LAST_PRO_VERSION);
            $currentProVersion = $config->get('pro_plugin_version');
            if (version_compare($currentProVersion, $lastProVersion, '>')) {
                $this->cleanTwigCacheDir($config->get('plugin_cache_twig'));
                update_option(self::LAST_PRO_VERSION, $currentProVersion);
            }
        }

    }

    /**
     * {@inhertidoc}
     */
    public function onInstall()
    {
        parent::onInstall();

        $model = self::getModel();
        $queries = self::getQueries();

        if (!$model->install($queries)) {
            wp_die('Failed to update database.');
        }
    }

    public function onDeactivation()
    {
        $response = false;
        //Uncomment to enable deactivation page
        //$response = $this->getController()->askUninstallAction();

        if (!is_bool($response)) {
            exit($response);
        }

        if ($response) {
            $model = self::getModel();
            $queries = self::getQueries();

            $model->drop($queries);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function onUninstall()
    {
        global $grid_gallery_supsystic;
        
        $self = $grid_gallery_supsystic->getResolver()->getModulesList()->get('installer');
        $self->onDeactivation();
    }

    /**
     * Returns the database queries.
     * @return array|null
     */
    protected static function getQueries()
    {
        if (!is_file($file = dirname(__FILE__) . '/Queries.php')) {
            return null;
        }

        return include $file;
    }

    protected static function getModel()
    {
        return new GridGallery_Installer_Model();
    }

    private function cleanHTMLCache($cachePath) {
        if ($cachePath) {
            array_map('unlink', glob("$cachePath/*"));
        }
    }

    private function cleanTwigCacheDir($dir) {
        if (!$dir || !is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), array('.', '..')); 
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->cleanTwigCacheDir("$dir/$file") : @unlink("$dir/$file"); 
        } 
        return @rmdir($dir); 
    }

}
