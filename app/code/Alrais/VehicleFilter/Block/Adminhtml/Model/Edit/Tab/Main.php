<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
// @codingStandardsIgnoreFile

namespace Alrais\VehicleFilter\Block\Adminhtml\Model\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface {
    
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry, 
            \Magento\Framework\Data\FormFactory $formfactory,
            \Magento\Store\Model\System\Store $systemStore
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formfactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return __('Model Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return __('Model Information');
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
        $model = $this->_coreRegistry->registry('current_alrais_vehiclefilter_model');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Model Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $storeManagerDataList = $this->_storeManager->getStores();
        $options = array();
        
        foreach ($storeManagerDataList as $key => $value) {
                $options[] = ['label' => $value['name'].' - '.$value['code'], 'value' => $key];
                $fieldset->addField(
                    'model_'.$value['code'], 'text', ['name' => 'model_'.$value['code'], 'label' => __('Model'). " (".$value['name'].")", 'title' => __('Model'), 'required' => true]
                );
        }
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
