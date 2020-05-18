<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
// @codingStandardsIgnoreFile

namespace Alrais\VehicleFilter\Block\Adminhtml\Make\Edit\Tab;

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
        return __('Make Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return __('Make Information');
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
        $model = $this->_coreRegistry->registry('current_alrais_vehiclefilter_make');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Make Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $storeManagerDataList = $this->_storeManager->getStores();
        $options = array();
        
        foreach ($storeManagerDataList as $key => $value) {
                $options[] = ['label' => $value['name'].' - '.$value['code'], 'value' => $key];
                $fieldset->addField(
                    'make_'.$value['code'], 'text', ['name' => 'make_'.$value['code'], 'label' => __('Make'). " (".$value['name'].")", 'title' => __('Make'), 'required' => true]
                );
        }
        
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
