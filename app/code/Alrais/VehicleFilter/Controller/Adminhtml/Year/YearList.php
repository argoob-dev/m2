<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\VehicleFilter\Controller\Adminhtml\Year;

class YearList extends \Alrais\VehicleFilter\Controller\Adminhtml\Year {

    public function __construct(
    \Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Alrais\VehicleFilter\Model\Resource\Year\Collection $yearCollection
    ) {
        $this->yearCollection = $yearCollection;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
    }

    public function execute() {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $years = $this->yearCollection
                    ->addFieldToSelect(["value" => "id", "label" => "year"])
                    ->addFieldToFilter("year", array('like' => '%' . $this->getRequest()->getParam('q') . '%'));
            $years->setOrder('year', 'ASC');
            return $result->setData($years->getData());
        }
    }

}
