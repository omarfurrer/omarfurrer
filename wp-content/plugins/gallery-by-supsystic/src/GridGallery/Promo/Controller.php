<?php 
/**
* 
*/
class GridGallery_Promo_Controller extends GridGallery_Core_BaseController
{
    public function welcomeAction(Rsc_Http_Request $request)
    {
        return $this->response(
            '@promo/promo.twig',
            array(
                'plugin_name' => $this->getConfig()->get('plugin_title_name'),
                'plugin_version' => $this->getConfig()->get('plugin_version'),
                'start_url' => '?page=supsystic-gallery&module=overview'
            )
        );
    }

    public function showTutorialAction()
    {
        update_user_meta(get_current_user_id(), 'sgg-tutorial_was_showed', false);
        return $this->redirect($this->generateUrl('overview'));
    }
}
?>