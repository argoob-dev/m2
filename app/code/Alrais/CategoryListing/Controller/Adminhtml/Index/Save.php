<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alrais\CategoryListing\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;


class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    
    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context, PostDataProcessor $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
        
        
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Alrais_CategoryListing::category');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if( isset($data['stores']) ) {
            $data['store_id'] = join(",", $data['stores']);
            unset($data['stores']);
        }

        // echo "<pre>"; print_r($data); print_r($_FILES); exit;

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {

            $model = $this->_objectManager->create('Alrais\CategoryListing\Model\Category');

            $id = $this->getRequest()->getParam('category_list_id');
            if ($id) {
                $model->load($id);
            }

            

            $this->_eventManager->dispatch(
                'category_prepare_save',
                ['category' => $model, 'request' => $this->getRequest()]
            );

            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['category_list_id' => $model->getId(), '_current' => true]);
            }
            
            
            /* File Uploading Start */
            
            $data['banner_image'] = $this->_processbanner_image($data, $model);
                        
            /* File Uploading End */


            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this record.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['category_list_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the record.'));
            }

            $postData['store_id'] = implode(',',$postData['store_id']);
            $this->_getSession()->setFormData($postData);
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['category_list_id' => $this->getRequest()->getParam('category_list_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    
    
    private function _processbanner_image($data, $model){
                
        try{
            
        
            $media_dir_obj = $this->_objectManager->get('Magento\Framework\Filesystem')
                                                    ->getDirectoryRead(DirectoryList::MEDIA);                                                                        
            $media_dir = $media_dir_obj->getAbsolutePath();


            if(!empty($_FILES['banner_image']['name'])){

                $Uploader = $this->_objectManager->create(
                                               'Magento\MediaStorage\Model\File\Uploader',
                                                ['fileId' => 'banner_image']);

                $Uploader->setAllowCreateFolders(true);
                $Uploader->setAllowRenameFiles(true);

                $banner_dir = $media_dir.'/category/';                                
                $result = $Uploader->save($banner_dir);

                unset($result['tmp_name']);
                unset($result['path']);

                $data['banner_image'] = 'category/'.$Uploader->getUploadedFileName();

            }else{

                if(isset($data['banner_image']['delete'])){

                    $data['banner_image'] = '';

                }else{

                    if($model->getId()) { //edit mode

                        if($model->getbanner_image() != ''){                                        
                            $data['banner_image'] = $model->getbanner_image();
            }

                    }else{
                        $data['banner_image'] = '';
                }
            }
        }
            
            if(isset($data['banner_image']))
                return $data['banner_image'];    
            
        
        } catch (\Exception $e) {
        
                $this->messageManager->addError(
                        __($e->getMessage())
                );                                
    }

}
    
}
