<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Make;

class MakeList extends \Alrais\VehicleFilter\Controller\Adminhtml\Make {

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Make\Collection $makeCollection
    ) {
        $this->makeCollection = $makeCollection;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
                $makes = $this->makeCollection 
                        ->addFieldToSelect(["value"=>"id","label"=>"make_en"])
                        ->addFieldToFilter("make_en",array('like' => '%'.$this->getRequest()->getParam('q').'%'));
                return $result->setData($makes->getData());
        }
        
    }

}
