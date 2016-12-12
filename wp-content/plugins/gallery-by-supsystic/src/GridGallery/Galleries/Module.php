<?php

/**
 * Class GridGallery_Galleries_Module
 *
 * @package GridGallery\Galleries
 * @author Artur Kovalevsky
 */
class GridGallery_Galleries_Module extends GridGallery_Core_Module
{

    /**
     * {@inheritdoc}
     */
    public function onInit()
    {
        parent::onInit();
        $dispatcher = $this->getEnvironment()->getDispatcher();
        $dispatcher->on('after_overview_loaded', array($this, 'registerMenu'));
        $this->registerShortcode();

        $resources = new GridGallery_Galleries_Model_Resources();

        $config = $this->getEnvironment()->getConfig();
        $prefix = $config->get('hooks_prefix');

        add_action($prefix . 'after_ui_loaded', array($this, 'registerAssets'));
        add_action($prefix . 'gallery_delete', array($resources, 'deleteByGalleryId'));

        /* Delete attachment */
        add_action('delete_attachment', array($resources, 'deleteAttachmentResources'));
        add_action('grid_gallery_delete_image', array($resources, 'deleteByResourceId'));
        add_action('gg_delete_photo_id', array($resources, 'deletePhotoById'));


        add_image_size('gg_gallery_thumbnail', 450, 250, true);

        // !!!!!! use {} for preg_* functions as start and end of the expresion.
        $pregReplaceFilter = new Twig_SimpleFilter(
            'preg_replace',
            array($this, 'pregReplace')
        );

        $httpFilter = new Twig_SimpleFilter(
            'force_http',
            array($this, 'forceHttpUrl')
        );
		$htmlspecialchars_decode = new Twig_SimpleFilter(
			'htmlspecialchars_decode',
			'htmlspecialchars_decode'
		);

        $function = new Twig_SimpleFunction('translate', array($this->getController(), 'translate'));
		$ceilFunction = new Twig_SimpleFunction('ceil','ceil');
		$all_categories_func =
			new Twig_SimpleFunction(
				'all_categories',
				'get_categories'
			);

        $twig = $this->getEnvironment()->getTwig();
        $twig->enableAutoReload();
        $twig->addFilter($pregReplaceFilter);
        $twig->addFilter($httpFilter);
		$twig->addFilter($htmlspecialchars_decode);
        $twig->addFunction($function);
        $twig->addFunction($ceilFunction);
        $twig->addFunction($all_categories_func);

        // To avoid conflict with other plugins which have older twig version.
        $twig->addFilter(new Twig_SimpleFilter('round', 'round'));


        // Widget
        add_action('widgets_init', array($this, 'registerWidget'));
        // Cache dir
        $this->cacheDirectory = $this->getConfig()->get('plugin_cache_galleries');


    }

	//on shutdown check is footer is printed , if not print scripts for our gallery
	public function shutdown(){
		if(!(defined('DOING_AJAX') && DOING_AJAX) && !did_action('wp_footer')){
			wp_print_footer_scripts();
		}
	}

    public function getFrontendCSS() {
        return array(
            $this->getLocationUrl() . '/assets/css/grid-gallery.galleries.frontend.css',
            $this->getLocationUrl() . '/assets/css/grid-gallery.galleries.effects.css',
            $this->getLocationUrl() . '/assets/css/jquery.flex-images.css',
            $this->getLocationUrl() . '/assets/css/lightSlider.css',
            $this->getLocationUrl() . '/assets/css/prettyPhoto.css',
            $this->getLocationUrl() . '/assets/css/photobox.css',
            // $this->getLocationUrl() . '/assets/css/photobox.ie.css', Need to add check if IE from UA
            $this->getLocationUrl() . '/assets/css/gridgallerypro-embedded.css',
            $this->getLocationUrl() . '/assets/css/icons-effects.css',
            $this->getLocationUrl() . '/assets/css/loaders.css'
        );
    }

    public function getFrontendJS() {
        return array(
            'jquery',
            '//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js',
            $this->getLocationUrl() . '/assets/js/lib/imagesLoaded.min.js',
            $this->getLocationUrl() . '/assets/js/lib/jquery.easing.js',
            $this->getLocationUrl() . '/assets/js/lib/jquery.prettyphoto.js',
            $this->getLocationUrl() . '/assets/js/lib/jquery.quicksand.js',
            $this->getLocationUrl() . '/assets/js/lib/jquery.wookmark.js',
            $this->getLocationUrl() . '/assets/js/lib/hammer.min.js',
            $this->getLocationUrl() . '/assets/js/lib/jquery.history.js',
            array(
                'handle' => 'frontend.jquery.slimscroll.js',
                'source' => $this->getLocationUrl() . '/assets/js/lib/jquery.slimscroll.js',
            ),
            $this->getLocationUrl() . '/assets/js/jquery.photobox.js',
            $this->getLocationUrl() . '/assets/js/jquery.sliphover.js',
            array(
                'source' => $this->getLocationUrl() . '/assets/js/frontend.js',
                'dependencies' => array(
                    'jquery',
                    'frontend.jquery.slimscroll.js',
                    'imagesLoaded.min.js',
                    'jquery.prettyphoto.js',
                    'jquery.easing.js',
                    'jquery.prettyphoto.js',
                    'jquery.quicksand.js',
                    'jquery.wookmark.js',
                    'jquery.photobox.js',
                    'jquery.sliphover.js',
                    'jquery.colorbox.js',
	                'jquery.history.js'
                ),
            ),
        );
    }

    public function getBackendCSS() {
        return array(
            $this->getLocationUrl() . '/assets/css/grid-gallery.galleries.style.css',
            $this->getLocationUrl() . '/assets/css/grid-gallery.galleries.effects.css',
            $this->getLocationUrl() . '/assets/css/ui.jqgrid.css',
            $this->getLocationUrl() . '/assets/css/jquery-ui.theme.min.css',
            $this->getLocationUrl() . '/assets/css/jquery-ui.structure.min.css',
            $this->getLocationUrl() . '/assets/css/jquery.jqplot.min.css',
            $this->getLocationUrl() . '/assets/css/jquery-ui.min.css',
            $this->getLocationUrl() . '/assets/css/gridgallerypro-embedded.css',
            $this->getLocationUrl() . '/assets/css/icons-effects.css',
            $this->getLocationUrl() . '/assets/css/loaders.css',
			$this->getLocationUrl() . '/assets/css/chosen.css',
        );
    }

    public function getBackendJS() {
       return array(
            $this->getLocationUrl() . '/assets/js/settings.js',
            $this->getLocationUrl() . '/assets/js/attrchange.js',
            $this->getLocationUrl() . '/assets/js/addImages.js',
            $this->getLocationUrl() . '/assets/js/position.js',
            $this->getLocationUrl() . '/assets/js/jquery.jqGrid.min.js',
            $this->getLocationUrl() . '/assets/js/grid.locale-en.js',
            $this->getLocationUrl() . '/assets/js/holder.js',
            $this->getLocationUrl() . '/assets/js/grid-gallery.galleries.index.js',
            $this->getLocationUrl() . '/assets/js/grid-gallery.galleries.view.js',
            $this->getLocationUrl() . '/assets/js/grid-gallery.galleries.preview.js',
            $this->getLocationUrl() . '/assets/js/grid-gallery.galleries.thumb.js',
		   	$this->getLocationUrl() . '/assets/js/lib/chosen.jquery.js',
        );
    }

    /**
     * Loads assets
     * @param GridGallery_Ui_Module $ui An instance of the UI module.
     */
    public function registerAssets(GridGallery_Ui_Module $ui)
    {
        $ui->asset->enqueue('styles', $this->getBackendCSS());
        $ui->asset->enqueue('scripts', $this->getBackendJS());
        $ui->asset->register('styles', $this->getFrontendCSS());
        $ui->asset->register('scripts', $this->getFrontendJS());
        // 2.2.9 Backward compatibility for users with old pro
        if ($this->getConfig()->get('is_pro')) {
            if (version_compare($this->getConfig()->get('pro_plugin_version'), '2.2.9', '<')) {
                $ui->asset->enqueue('styles', $this->getFrontendCSS(), 'frontend');
                $ui->asset->enqueue('scripts', $this->getFrontendJS(), 'frontend');
            }
        }
    }

    public function loadFrontendAssets()
    {
        $config = $this->getConfig();
        $prefix = $config->get('plugin_name') . '-';

        $this->getModule('colorbox')->loadColoboxStyles();

        foreach ($this->getFrontendCSS() as $source) {
            $handle = basename($source);
            wp_enqueue_style($handle);
        }

        foreach ($this->getFrontendJS() as $source) {
            if (is_array($source)) {
                if (isset($source['handle'])) {
                    $handle = $source['handle'];
                } else {
                    $handle = basename($source['source']);
                }
            } else {
                $handle = basename($source);
            }
            wp_enqueue_script($handle);
        }

		//on shutdown check is footer is printed , if not print scripts for our gallery
		add_action('shutdown', array($this,'shutdown'));
    }

    public function registerWidget() {
        register_widget('sggWidget');
    }

    public function pregReplace($value, $pattern, $replacement) {
        return preg_replace($pattern, $replacement, $value);
    }

    /**
     * Adds the http:// to the URL's without it.
     * @param  string $url URL.
     * @return string
     */
    public function forceHttpUrl($url)
    {
        if (!preg_match('/^https?:\/\//', $url)) {
            return 'http://' . $url;
        }

        return $url;
    }

	/**
	 * Shortcode callback.
	 * @param  array $attributes An array of the shortcode parameters.
	 * @return string
	 */
	public function getGallery($attributes)
	{
        if (is_feed()) {
            return;
        }

        $this->loadFrontendAssets();
        $id = $attributes['id'];

        $protocol = is_ssl() ? '-ssl' : '';
        $cachePath = $this->cacheDirectory . DIRECTORY_SEPARATOR . $id . $protocol;

		$this->initSocialSharePlugin($id);

		global $wpdb;
		$optValue = get_option($wpdb->prefix . $this->getConfig()->get('db_prefix') . 'rand_sorts');

		if($optValue === false
			|| !isset($optValue['id']) || !isset($optValue['val'])
			|| !($optValue['id'] == $id && $optValue['val'] === true)) {

			if (file_exists($cachePath) && $this->getEnvironment()->isProd()) {
				return file_get_contents($cachePath);
			}
		}

        // Backward compatible with pro version 2.1.5.
        // In case when user have old pro version installed and new free we return old gallery realization.
        if ($this->getConfig()->get('is_pro') && $this->getConfig()->get('pro_plugin_version') === null) {
            return $this->getOldGallery($attributes);
        }

		if ($init = $this->initGallery($attributes)) {
			extract($init);

            $renderData = $this->render('@galleries/shortcode/gallery.twig',
                array(
                    'gallery' => $gallery,
                    'settings' => is_object($settings) ? $settings->data : $settings,
                    'colorbox' => $this->getEnvironment()->getModule('colorbox')
                    ->getLocationUrl(),
                    'isMobile' => $settingsModel->isMobile(true),
                    'mobile' => isset($settings->data['box']['mobile']) ? $settingsModel->isMobile($settings->data['box']['mobile']) :  null,
                )
            );
            if (isset($this->cacheDirectory)) {
                file_put_contents($cachePath, $renderData);
            }
            return $renderData;
		}
	}

	public function initGallery($attributes)
	{
		$galleries = $this->getModel('galleries');
		$cache = $this->getEnvironment()->getCache();
		$gallery = $galleries->getById($attributes['id']);

		if (!$gallery) {
			return;
		}

		$key = sprintf('gallery_settings_%s', $attributes['id']);

		/** @var GridGallery_Settings_Registry $registry */
		$registry = $this->getEnvironment()
			->getModule('settings')
			->getRegistry();

		if (true === (bool)$registry->get('cache_enabled')) {
			$ttl = $registry->get('cache_ttl');
			$cache->setTtl($ttl);

			if (null === $settings = $cache->get($key)) {
				$settings = $this->getGallerySettings($attributes['id']);
				$cache->set($key, $settings, (int)$ttl);
			}
		} else {
			$settings = $this->getGallerySettings($attributes['id']);
		}

		$settings->data['socialSharing'] = $this->initGallerySocialShare($settings->data);

		$posArray = array('left', 'center', 'right');

		$settings->data['area']['position'] =
			(isset($settings->data['area']['position'])
			&&
			isset($posArray[$settings->data['area']['position']])) ?
			$posArray[$settings->data['area']['position']] : 'center';


		if (property_exists($gallery, 'photos') && is_array($gallery->photos)) {
			$position = new GridGallery_Photos_Model_Position();

			/*foreach ($gallery->photos as $index => $row) {
				$gallery->photos[$index] = $position->setPosition(
					$row,
					'gallery',
					$gallery->id
				);
			}*/

			$positions = $position->setPosition(
				$gallery->photos,
				'gallery',
				$gallery->id
				);

			foreach ($gallery->photos as $index => $row) {
				foreach ($positions as $pos) {
					if ($row->id == $pos->photo_id) {
						$gallery->photos[$index]->position = $pos->position;
					}
				}
			}

			//ASC && DESC sort
            if(isset($settings->data['sort'])){
                $gallery->photos = $position->sort($gallery->photos, $settings->data['sort']);
            } else {
                $gallery->photos = $position->sort($gallery->photos);
            }

            foreach($gallery->photos as $photo) {
                $photo->attachment['caption'] = html_entity_decode($photo->attachment['caption']);
                $photo->attachment['description'] = html_entity_decode($photo->attachment['description']);
            }
        }

		$settingsModel = $this->getModel('settings');

		return compact('gallery', 'settings', 'settingsModel');
	}

	/**
	 * init social share for gallery
	 * @param $settings
	 * @return array of social sharing setting and values
	 */
	public function initGallerySocialShare($settingsData){

		$socialSharingModel = $this->getModel('socialSharing');
		if(isset($settingsData['socialSharing'])){
			$socialSharing = $settingsData['socialSharing'];
		}else{
			$socialSharing = array();
		}

		$socialSharing['pluginInstalled'] = $socialSharingModel->isPluginInstalled();
		$socialSharing['projectsList'] = $socialSharingModel->getProjectsList();


		$socialSharing['html'] = "";
		if(
			$socialSharing['pluginInstalled']
		&&
			isset($socialSharing['enabled'])
		&&
			$socialSharing['enabled']
		){
			$socialSharing['html'] = apply_filters('sss_gallery_html',$socialSharing['projectId']);
		}

		return $socialSharing;
	}

	public function initSocialSharePlugin($id){

		$socialSharingModel = $this->getModel('socialSharing');
        $socialSharingModel->getProjectsList();

        $socialSharingModel = $this->getModel('socialSharing');
        $pluginInstalled = $socialSharingModel->isPluginInstalled();
        $projectsList = array_keys($socialSharingModel->getProjectsList());
        if(
            $pluginInstalled
        ){
            $settings = $this->getGallerySettings($id);
			if(isset($settings->data['socialSharing']['projectId'])){
				apply_filters('sss_gallery_html', $settings->data['socialSharing']['projectId']);
			}
		}
	}

    public function getModel($alias)
    {
    	return $this->getController()->getModel($alias);
    }

	public function render($template, $parameters)
	{
		$twig = $this->getEnvironment()->getTwig();
        try {
            return preg_replace('/\s+/', ' ', trim($twig->render($template, $parameters)));
        } catch (Exception $e) {
            if (WP_DEBUG) {
                return $e->getMessage();
            }
        }
		return preg_replace('/\s+/', ' ', trim($twig->render($template, $parameters)));
	}

    public function getOldGallery($attributes)
    {
        $galleries = new GridGallery_Galleries_Model_Galleries();
        $twig = $this->getEnvironment()->getTwig();
        $cache = $this->getEnvironment()->getCache();
        $gallery = $galleries->getById($attributes['id']);

        if (!$gallery) {
            return;
        }

        $key = sprintf('gallery_settings_%s', $attributes['id']);

        /** @var GridGallery_Settings_Registry $registry */
        $registry = $this->getEnvironment()
            ->getModule('settings')
            ->getRegistry();

        if (true === (bool)$registry->get('cache_enabled')) {
            $ttl = $registry->get('cache_ttl');
            $cache->setTtl($ttl);

            if (null === $settings = $cache->get($key)) {
                $settings = $this->getGallerySettings($attributes['id']);
                $cache->set($key, $settings, (int)$ttl);
            }
        } else {
            $settings = $this->getGallerySettings($attributes['id']);
        }

		$settings->data['socialSharing'] = $this->initGallerySocialShare($settings->data);

		$posArray = array('left', 'center', 'right');

		$settings->data['area']['position'] =
			(isset($settings->data['area']['position'])
			&&
			isset($posArray[$settings->data['area']['position']])) ?
			$posArray[$settings->data['area']['position']] : 'center';

        if (property_exists($gallery, 'photos') && is_array($gallery->photos)) {
            $position = new GridGallery_Photos_Model_Position();

            /*foreach ($gallery->photos as $index => $row) {
                $gallery->photos[$index] = $position->setPosition(
                    $row,
                    'gallery',
                    $gallery->id
                );
            }*/

            $positions = $position->setPosition(
                $gallery->photos,
                'gallery',
                $gallery->id
            );

            foreach ($gallery->photos as $index => $row) {
                foreach ($positions as $pos) {
                    if($row->id == $pos->photo_id) {
                        $gallery->photos[$index]->position = $pos->position;
                    }
                }
            }

            $gallery->photos = $position->sort($gallery->photos);

            $cats = array();
            foreach ($gallery->photos as $photo) {
                if (property_exists($photo, 'tags') && is_array($photo->tags) && count($photo->tags) > 0) {
                    foreach ($photo->tags as $tag) {
                        if (!isset($cats[$tag])) {
                            $cats[$tag] = true;
                        }
                    }
                }
            }
        }

        $settingsModel = new GridGallery_Galleries_Model_Settings();
        $postsLenght = sizeof($settingsModel->getPostsToRender($attributes['id'])) + sizeof($settingsModel->getPagesToRender($attributes['id']));

        if(isset($settings->data['posts']) && $settings->data['posts']['enable']) {
            foreach ($settingsModel->getPostsToRender($attributes['id']) as $post) {
                foreach($post['categories'] as $category) {
                    if (!isset($cats[$category['name']])) {
                        $cats[$category['name']] = true;
                    }
                }
            }

            foreach ($settingsModel->getPagesToRender($attributes['id']) as $page) {
                foreach($page['categories'] as $category) {
                    if (!isset($cats[$category['name']])) {
                        $cats[$category['name']] = true;
                    }
                }
            }
        }

        if(is_array($gallery->photos) && $gallery->photos) {
            foreach($gallery->photos as $photo) {
                $photo->attachment['caption'] = html_entity_decode($photo->attachment['caption']);
            }
        }

        $template = $twig->render(
            '@galleries/r314/shortcode/gallery.twig',
            array(
                'gallery' => $gallery,
                'settings' => is_object($settings) ? $settings->data : $settings,
                'colorbox' => $this->getEnvironment()->getModule('colorbox')
                    ->getLocationUrl(),
                'categories' => isset($cats) ? $cats : array(),
                'postsLength' => $postsLenght,
                'posts' => $settingsModel->getPostsToRender($attributes['id']),
                'pages' => $settingsModel->getPagesToRender($attributes['id']),
                'mobile' => isset($settings->data['box']['mobile']) ? $settingsModel->isMobile($settings->data['box']['mobile']) :  null,
            )
        );

        return preg_replace('/\s+/', ' ', trim($template));
    }

    public function addFrontendCss()
    {
        $stylesheets = $this->getFrontendCSS();

        foreach ($stylesheets as $url) {
            echo '<link rel="stylesheet" type="text/css" href="' . $url . '"/>';
        }
    }

    public function addFrontendJs()
    {
        $javascripts = array(
            // $this->getLocationUrl() . '/assets/js/grid-gallery.galleries.frontend.js'
            $this->getLocationUrl() . '/assets/js/frontend.js',
            $this->getLocationUrl() . '/assets/js/jquery.photobox.js',
            $this->getLocationUrl() . '/assets/js/jquery.sliphover.js',
            '//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js',
        );

        wp_enqueue_script('jquery.colorbox.js');

        foreach ($javascripts as $url) {
            echo '<script type="text/javascript" src="' . $url . '"></script>';
        }
    }


    /**
     * Returns the gallery settings from the database.
     * If gallery is not configured, then default settings will be loaded.
     * @param int $galleryId Gallery identifier.
     * @return array
     */
    public function getGallerySettings($galleryId)
    {
        $model = new GridGallery_Galleries_Model_Settings();

        if (null === $settings = $model->get((int)$galleryId)) {
            $config = $this->getEnvironment()->getConfig();
            $config->load('@galleries/settings.php');

            $settings = unserialize($config->get('gallery_settings'));
        }

        return $settings;
    }

    /**
     * Registers the Gallery by Supsystic shortcode in the WordPress.
     */
    public function registerShortcode()
    {
        $attachment = new GridGallery_Galleries_Attachment();
        $handler = array($this, 'getGallery');
        $shortcode = $this->getEnvironment()
            ->getConfig()
            ->get('shortcode_name');

        $this->getEnvironment()
            ->getTwig()
            ->addFunction(
                new Twig_SimpleFunction('get_attachment', array(
                        $attachment,
                        'getAttachment'
                    )
                )
            );

        if (!empty($shortcode) && $shortcode !== null) {
            add_shortcode($shortcode, $handler);
        }

        // for the backward capability =< 0.2.2
        add_shortcode('grid-gallery', $handler);
    }

    /**
     * Adds the submenu item "New gallery".
     */
    public function registerMenu()
    {
        $menu = $this->getMenu();
        $plugin_menu = $this->getConfig()->get('plugin_menu');
        $capability = $plugin_menu['capability'];

        $submenuNewGallery = $menu->createSubmenuItem();
        $submenuGalleries = $menu->createSubmenuItem();

		$submenuNewGallery->setCapability($capability)
			->setMenuSlug('supsystic-gallery&module=galleries&action=showPresets')
			->setMenuTitle($this->translate('New gallery'))
			->setPageTitle($this->translate('New gallery'))
			->setModuleName('galleries');
		// Avoid conflicts with old vendor version
		if(method_exists($submenuNewGallery, 'setSortOrder')) {
			$submenuNewGallery->setSortOrder(20);
		}

		$menu->addSubmenuItem('newGallery', $submenuNewGallery);

        $submenuGalleries->setCapability($capability)
            ->setMenuSlug('supsystic-gallery&module=galleries')
            ->setMenuTitle($this->translate('Galleries'))
            ->setPageTitle($this->translate('Galleries'))
            ->setModuleName('galleries');
		// Avoid conflicts with old vendor version
		if(method_exists($submenuGalleries, 'setSortOrder')) {
			$submenuGalleries->setSortOrder(30);
		}

        $menu->addSubmenuItem('galleries', $submenuGalleries);

    }

    public function cleanCache($galleryId)
    {
        if (empty($galleryId)) {
            return;
        }
        
        $cachePath = $this->getConfig()->get('plugin_cache_galleries') .
        DIRECTORY_SEPARATOR . $galleryId;

        if (file_exists($cachePath)) {
            unlink($cachePath);
        }

        if (file_exists($cachePath . '-ssl')) {
            unlink($cachePath . '-ssl');
        }
    }

	public function media_sideload_image($file, $post_id, $desc = null)
	{
		preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
		$file_array = array();
		$file_array['name'] = basename($matches[0]);

		// Download file to temp location.
		$file_array['tmp_name'] = download_url($file);

		// If error storing temporarily try to download manualy else return the error.
		if (is_wp_error($file_array['tmp_name'])) {

			try {
				$temp = tmpfile();
				fwrite($temp, file_get_contents($file));
				$meta = stream_get_meta_data($temp);
				$file_array['tmp_name'] = $meta['uri'];
				
			} catch (Exception $e) {

				return $file_array['tmp_name'];
			}
		}

		// Do the validation and storage stuff.
		$id = media_handle_sideload($file_array, $post_id, $desc);

		// If error storing permanently, unlink.
		if (is_wp_error($id)) {
			@unlink($file_array['tmp_name']);
			return $id;
		}

		return $id;
	}
}

require_once('Model/widget.php');
