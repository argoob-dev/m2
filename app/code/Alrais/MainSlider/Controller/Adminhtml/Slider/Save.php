<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\MainSlider\Controller\Adminhtml\Slider;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Alrais\MainSlider\Controller\Adminhtml\Slider {

    protected $uploaderFactory;
    protected $imageModel;

    public function __construct(
    \Magento\Backend\App\Action\Context $context, 
    \Magento\Framework\Registry $coreRegistry, 
    \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory, 
    \Alrais\MainSlider\Model\Slider\Image $imageModel,
    \Magento\Framework\Filesystem $fileSystem
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->imageModel = $imageModel;
        $this->fileSystem = $fileSystem;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        if ($this->getRequest()->getPostValue()) {
            
            $model = $this->_objectManager->create('Alrais\MainSlider\Model\Slider');
            $data = $this->getRequest()->getPostValue();
            if( isset($data['stores']) ) {
                $data['store_id'] = join(",", $data['stores']);
                unset($data['stores']);
            }
            try {
                
                $inputFilter = new \Zend_Filter_Input(
                        [], [], $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('slider_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getSliderId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                $data['image'] = $this->uploadFileAndGetName('image', 'web', $data);
                $data['mobile_image'] = $this->uploadFileAndGetName('mobile_image', 'mobile', $data);
                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('mainslider/*/edit', ['slider_id' => $model->getSliderId()]);
                    return;
                }
                $this->_redirect('mainslider/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int) $this->getRequest()->getParam('slider_id');
                if (!empty($id)) {
                    $this->_redirect('mainslider/*/edit', ['slider_id' => $id]);
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
                $this->_redirect('mainslider/*/edit', ['slider_id' => $this->getRequest()->getParam('slider_id')]);
                return;
            }
        }
        $this->_redirect('mainslider/*/');
    }

    public function getBaseDir($dir)
    {
        return $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)
            ->getAbsolutePath($dir);
    }

    public function uploadFileAndGetName($input, $type, $data)
    {
        if($type == 'mobile'){
            $slider = 'mainslider/mobile';
        }else{
            $slider = 'mainslider/web';
        }
        $destinationFolder = $this->getBaseDir($slider);
        try {
            if (isset($data[$input]['delete'])) {
                return '';
            } else {
                $uploader = $this->uploaderFactory->create(['fileId' => $input]);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($destinationFolder);
                return $slider.$uploader->getUploadedFileName();
            }
        } catch (\Exception $e) {
            if (isset($data[$input]['value'])) {
                return $data[$input]['value'];
            }
        }
    }
}
