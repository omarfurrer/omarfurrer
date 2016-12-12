<?php

/**
 * Class GridGallery_Photos_Controller
 *
 * @package GridGallery\Photos
 */
class GridGallery_Photos_Controller extends GridGallery_Core_BaseController
{

    const STD_VIEW = 'list'; // accepts 'list' or 'block'.

    public function requireNonces() {
        return array(
            'addAction',
            'addFolderAction',
            'deleteAction',
            'moveAction',
            'updateTitleAction',
            'updateAttachmentAction',
            'updatePositionAction'
        );
    }
    /**
     * {@inheritdoc}
     */
    protected function getModelAliases()
    {
        return array(
            'resources' => 'GridGallery_Galleries_Model_Resources',
            'photos' => 'GridGallery_Photos_Model_Photos',
            'folders' => 'GridGallery_Photos_Model_Folders',
            'position' => 'GridGallery_Photos_Model_Position',
        );
    }

    /**
     * Index Action
     * Shows the list of the all photos
     */
    public function indexAction(Rsc_Http_Request $request)
    {
        $stats = $this->getEnvironment()->getModule('stats');
        $stats->save('Images.tab');

        if ('grid-gallery-images' === $request->query->get('page')) {
            $redirectUrl = $this->generateUrl('photos');

            return $this->redirect($redirectUrl);
        }

        $folders = $this->getModel('folders');
        $photos = $this->getModel('photos');
        $position = $this->getModel('position');

        $images = array_map(
            array($position, 'setPosition'),
            $photos->getAllWithoutFolders()
        );

        return $this->response(
            '@photos/index.twig',
            array(
                'entities' => array(
                    'images' => $position->sort($images),
                    'folders' => $folders->getAll()
                ),
                'view_type' => $request->query->get('view', self::STD_VIEW),
                'ajax_url' => admin_url('admin-ajax.php'),
            )
        );
    }

    /**
     * View Action
     * Shows the photos in the selected album
     *
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function viewAction(Rsc_Http_Request $request)
    {
        if (!$request->query->has('folder_id')) {
            $this->redirect(
                $this->getEnvironment()->generateUrl('photos', 'index')
            );
        }

        $stats = $this->getEnvironment()->getModule('stats');
        $stats->save('folders.view');

        $folderId = (int)$request->query->get('folder_id');

        $folders = $this->getModel('folders');

        if (!$folder = $folders->getById($folderId)) {
            $this->redirect(
                $this->getEnvironment()->generateUrl('photos', 'index')
            );
        }

        $position = $this->getModel('position');

        foreach ($folder->photos as $index => $row) {
            $folder->photos[$index] = $position->setPosition(
                $row,
                'folder',
                $folderId
            );
        }

        $folder->photos = $position->sort($folder->photos);

        return $this->response(
            '@photos/view.twig',
            array(
                'folder' => $folder,
                'ajax_url' => admin_url('admin-ajax.php'),
                'view_type' => $request->query->get('view', self::STD_VIEW),
            )
        );
    }

    /**
     * Add Action
     * Adds new photos to the database
     *
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function addAction(Rsc_Http_Request $request)
    {
        $env = $this->getEnvironment();

        $photos = new GridGallery_Photos_Model_Photos();

        if ($env->getConfig()->isEnvironment(
            Rsc_Environment::ENV_DEVELOPMENT
        )
        ) {
            $photos->setDebugEnabled(true);
        }

        $attachment = get_post($request->post->get('attachment_id'));
        $viewType = $request->post->get('view_type');

        $stats = $this->getEnvironment()->getModule('stats');
        $stats->save('photos.add');

        $this->getModule('galleries')->cleanCache($request->post->get('galleryId'));

        if (!$photos->add($attachment->ID, $request->post->get('folder_id', 0))) {
            $response = array(
                'error' => true,
                'photo' => null,
                'message' => sprintf(
                    $env->translate('Unable to save chosen photo %s: %s'),
                    $attachment->post_title,
                    $photos->getLastError()
                ),
            );
        } else {
            // $photo = $env->getTwig()->render(
            //     sprintf('@ui/%s/image.twig', $viewType ? $viewType : 'block'),
            //     array('image' => $photos->getByAttachmentId($attachment->ID))
            // );

            $response = array(
                'error' => false,
                // 'photo' => $photo,
                'message' => sprintf(
                    $env->translate(
                        'Photo %s was successfully imported to the Grid Gallery'
                    ),
                    $attachment->post_title
                ),
				'link' => $this->generateUrl(
					'galleries',
					'view',
					array('gallery_id' => $request->post->get('galleryId'))
				),
            );
        }

        if($request->post->get('attachType') && $request->post->get('galleryId')) {
            $this->getModel('resources')->attach($request->post->get('galleryId'), 'photo', $photos->getByAttachmentId($attachment->ID)->id,true);
        }

        return $this->response(Rsc_Http_Response::AJAX, $response);
    }

    /**
     * Add Folder Action
     * Adds the new folder
     *
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function addFolderAction(Rsc_Http_Request $request)
    {
        $env = $this->getEnvironment();
        $folders = new GridGallery_Photos_Model_Folders();

        $stats = $this->getEnvironment()->getModule('stats');
        $stats->save('folders.add');

        if ($env->getConfig()->isEnvironment(
            Rsc_Environment::ENV_DEVELOPMENT
        )
        ) {
            $folders->setDebugEnabled(true);
        }

        $folderName = $request->post->get('folder_name');
        $viewType = $request->post->get('view_type');

        if (!$folders->add(
            ($folderName) ? $folderName : $env->translate('New Folder')
        )
        ) {
            $response = array(
                'error' => true,
                'folder' => null,
            );
        } else {
            $folder = $env->getTwig()->render(
                sprintf('@ui/%s/folder.twig', $viewType ? $viewType : 'block'),
                array('folder' => $folders->getById($folders->getInsertId()))
            );

            $response = array(
                'error' => false,
                'folder' => $folder,
                'id' => $folders->getInsertId(),
            );
        }

        return $this->response('ajax', $response);
    }

    /**
     * Delete Action
     * Deletes the specified folders and photos
     *
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function deleteAction(Rsc_Http_Request $request)
    {
        $env = $this->getEnvironment();
        $data = $request->post->get('data');
        $debug = $env->getConfig()->isEnvironment(
            Rsc_Environment::ENV_DEVELOPMENT
        );
        $photos = new GridGallery_Photos_Model_Photos($debug);
        $folders = new GridGallery_Photos_Model_Folders($debug);

        $stats = $this->getEnvironment()->getModule('stats');

        if (!$data) {
            return $this->response(
                'ajax',
                array(
                    'error' => true,
                )
            );
        }

        foreach ($data as $type => $identifies) {
            foreach ($identifies as $id) {
                if ($type === 'photo') {
                    $stats->save('photos.delete');
                    $photos->deleteById((int)$id);
                } else {
                    $stats->save('folders.delete');
                    $folders->deleteById((int)$id);
                }
            }
        }

        return $this->response(
            'ajax',
            array(
                'error' => false,
            )
        );
    }

    public function checkPhotoUsageAction(Rsc_Http_Request $request)
    {
        $photoId = $request->post->get('photo_id');

        $photos = $this->getModel('photos');
        $photo = $photos->getById($photoId);

        $resources = $this->getModel('resources');

        if ($photo->folder_id > 0) {
            $galleries = $resources->getGalleriesWithFolder($photo->folder_id);
        } else {
            $galleries = $resources->getGalleriesWithPhoto($photo->id);
        }

        return $this->response(Rsc_Http_Response::AJAX, array(
            'count' => count($galleries),
        ));
    }

    /**
     * Move Action
     * Moves photos to the folders
     *
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function moveAction(Rsc_Http_Request $request)
    {
        $photos = new GridGallery_Photos_Model_Photos();
        $error = true;

        if ($this->getEnvironment()->getConfig()->isEnvironment(
            Rsc_Environment::ENV_DEVELOPMENT
        )
        ) {
            $photos->setDebugEnabled(true);
        }

        $photoId = $request->post->get('photo_id');
        $folderId = $request->post->get('folder_id');

        if ($photos->toFolder($photoId, $folderId)) {
            $error = false;
        }

        return $this->response(
            'ajax',
            array(
                'error' => $error,
            )
        );
    }

    /**
     * Render Action
     * Renders the photos from the folder
     *
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function renderAction(Rsc_Http_Request $request)
    {
        $photos = $request->post->get('photos');

        if (!is_array($photos)) {
            return $this->response(
                'ajax',
                array(
                    'error' => true,
                    'photos' => null,
                )
            );
        }

        $renders = array();

        foreach ($photos as $photo) {
            $renders[] = $this->getEnvironment()->getTwig()->render(
                '@photos/includes/photo.twig', array('photo' => $photo)
            );
        }

        return $this->response(
            'ajax',
            array(
                'error' => false,
                'photos' => $renders,
            )
        );
    }

    /**
     * Update Title Action
     * Updates the title of the folder
     *
     * @param Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function updateTitleAction(Rsc_Http_Request $request)
    {
        $env = $this->getEnvironment();
        $folders = new GridGallery_Photos_Model_Folders();
        $title = trim($request->post->get('folder_name'));
        $folderId = $request->post->get('folder_id');

        if (empty($title)) {
            return $this->response(
                'ajax',
                array(
                    'error' => true,
                    'message' => $env->translate('The title can\'t be empty'),
                )
            );
        }

        if ($folders->updateTitle($folderId, $title)) {
            return $this->response(
                'ajax',
                array(
                    'error' => false,
                    'message' => $env->translate('Title successfully updated'),
                )
            );
        }

        return $this->response(
            'ajax',
            array(
                'error' => true,
                'message' => $env->translate(
                    'Unable to update the title. Try again later'
                ),
            )
        );
    }

    public function isEmptyAction()
    {
        $debugEnabled = $this->getEnvironment()->isDev();

        $isEmpty = true;
        $photos = new GridGallery_Photos_Model_Photos($debugEnabled);

        $list = $photos->getAll();

        if (count($list) > 0) {
            $isEmpty = false;
        }

        return $this->response(
            Rsc_Http_Response::AJAX,
            array(
                'isEmpty' => $isEmpty,
            )
        );
    }

    /**
     * Before update attachemnt
     * if attachment was updated then replace it and after save all information
     * to new attachment
     * @param Rsc_Http_Request $request
     */
    protected function beforeUpdateAttachment(Rsc_Http_Request $request){
        /** @var GridGallery_Photos_Model_Photos $photos */
        $photos = $this->getModel('photos');

        if($replaceAttachmentId = (int)$request->post->get('replace_attachment_id')){
            /**
             * @var GridGallery_Galleries_Module $gallery
             */
            $gallery = $this->getModule('galleries');
            $replacePost = get_post($replaceAttachmentId);
            $newAttachId = $gallery->media_sideload_image($replacePost->guid,0);
            $photos->updateAttachmentId($request->post->get('image_id'),$newAttachId);
            $request->post->set('attachment_id',$newAttachId);
            $request->post->set('replace_attachment_id',null);
        }
    }
    
    public function updateAttachmentAction(Rsc_Http_Request $request)
    {
        $this->beforeUpdateAttachment($request);
        /** @var GridGallery_Photos_Model_Photos $photos */
        $photos = $this->getModel('photos');

        $alt = $request->post->get('alt');
        if(empty($alt)) $alt = " ";
        $attachmentId = $request->post->get('attachment_id');
        $caption = $request->post->get('caption');
        $description = $request->post->get('description');
        $target = $request->post->get('target', '_self');
        $link = $request->post->get('link');
        $captionEffect = $request->post->get('captionEffect');
        $cropPosition = $request->post->get('cropPosition');

        if($link){
            $rel = $request->post->get('rel', '');
        } else {
            $rel = '';
        }
        
        $photos->updateMetadata($attachmentId, array(
            'alt'           => $alt,
            'caption'       => $caption,
            'description'   => $description,
            'link'          => $link,
            'captionEffect' => $captionEffect,
            'target' => $target,
            'rel' => $rel,
            'cropPosition' => $cropPosition
        ));

        $this->getModule('galleries')->cleanCache($request->post->get('gallery_id'));

        return $this->response(Rsc_Http_Response::AJAX);
    }

    /**
     * Updates the position of the photo.
     * @param  Rsc_Http_Request $request
     * @return Rsc_Http_Response
     */
    public function updatePositionAction(Rsc_Http_Request $request)
    {
        $response = $this->getErrorResponseData(
            $this->translate('Failed to update position.')
        );

        $data = $request->post->get('data');

        if ($this->getModel('position')->update($data)) {
            $response = $this->getSuccessResponseData(
                $this->translate('Position updated successfully!')
            );
        }

        $this->getModule('galleries')->cleanCache($data['scope_id']);

        return $this->response(Rsc_Http_Response::AJAX, $response);
    }
}
