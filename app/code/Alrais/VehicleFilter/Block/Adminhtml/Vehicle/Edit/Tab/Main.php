<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
// @codingStandardsIgnoreFile

namespace Alrais\VehicleFilter\Block\Adminhtml\Vehicle\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface {
    
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry, 
            \Magento\Framework\Data\FormFactory $formfactory
    ) {
        parent::__construct($context, $registry, $formfactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return __('Vehicle Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return __('Vehicle Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm() {
        $model = $this->_coreRegistry->registry('current_alrais_vehiclefilter_vehicle');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Vehicle Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $makeArray = $objectManager->get('Alrais\VehicleFilter\Model\Resource\Make\Collection')->toOptionArray();
        $modelArray = $objectManager->get('Alrais\VehicleFilter\Model\Resource\Model\Collection')->toOptionArray();
        $yearArray = $objectManager->get('Alrais\VehicleFilter\Model\Resource\Year\Collection')->toOptionArray();
        $fieldset->addField(
                'make_id', 'select', ['name' => 'make_id', 'label' => __('Select Make'), 'title' => __('Select Make'), 'values' => $makeArray, 'type' => 'options', 'required' => true]
        );
        $fieldset->addField(
                'model_id', 'select', ['name' => 'model_id', 'label' => __('Select Model'), 'title' => __('Select Model'), 'values' => $modelArray, 'type' => 'options', 'required' => true]
        );
        $fieldset->addField(
                'year_id', 'select', ['name' => 'year_id', 'label' => __('Select Year'), 'title' => __('Select Year'), 'values' => $yearArray, 'type' => 'options', 'required' => true]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
