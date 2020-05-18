<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Attachment;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Alrais\MainSlider\Controller\Adminhtml\Attachment {

    protected $uploaderFactory;
    protected $imageModel;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory, \Alrais\MainSlider\Model\Attachment\Image $imageModel
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->imageModel = $imageModel;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        if ($this->getRequest()->getPostValue()) {
            
            $model = $this->_objectManager->create('Alrais\MainSlider\Model\Attachment');
            $data = $this->getRequest()->getPostValue();
            
            try {
                
                $inputFilter = new \Zend_Filter_Input(
                        [], [], $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('attachment_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getAttachmentId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }

                $data['image'] = $this->_process_image($data, $model);

                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('mainslider/*/edit', ['attachment_id' => $model->getAttachmentId()]);
                    return;
                }
                $this->_redirect('mainslider/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int) $this->getRequest()->getParam('attachment_id');
                if (!empty($id)) {
                    $this->_redirect('mainslider/*/edit', ['attachment_id' => $id]);
                } else {
                    $this->_redirect('mainslider/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                        __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('mainslider/*/edit', ['id' => $this->getRequest()->getParam('attachment_id')]);
                return;
            }
        }
        $this->_redirect('mainslider/*/');
    }

    private function _process_image($data, $model) {

        try {


            $media_dir_obj = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::MEDIA);
            $media_dir = $media_dir_obj->getAbsolutePath();


            if (!empty($_FILES['image']['name'])) {

                $Uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'image']);

                $Uploader->setAllowCreateFolders(true);
                $Uploader->setAllowRenameFiles(true);

                $banner_dir = $media_dir . '/mainslider/';
                $result = $Uploader->save($banner_dir);

                unset($result['tmp_name']);
                unset($result['path']);

                $data['image'] = 'mainslider/' . $Uploader->getUploadedFileName();
            } else {

                if (isset($data['image']['delete'])) {

                    $data['image'] = '';
                } else {

                    if ($model->getAttachmentId()) { //edit mode
                        if ($model->getImage() != '') {
                            $data['image'] = $model->getImage();
                        }
                    } else {
                        $data['image'] = '';
                    }
                }
            }

            if (isset($data['image']))
                return $data['image'];
        } catch (\Exception $e) {

            $this->messageManager->addError(
                    __($e->getMessage())
            );
        }
    }

}
