<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
// @codingStandardsIgnoreFile

namespace Alrais\MainSlider\Block\Adminhtml\Attachment\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Alrais\MainSlider\Model\SliderFactory;

class Main extends Generic implements TabInterface {

    const ENABLED = 1;
    const DISABLED = 0;

    protected $_resource;
    protected $connection;
    protected $sliderlist;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry, 
            \Magento\Framework\Data\FormFactory $formfactory, 
            \Magento\Framework\App\ResourceConnection $resource,
            \Alrais\MainSlider\Model\System\Config\Slider $sliderlist
    ) {
        $this->_resource = $resource;
        $this->scopeConfig = $context->getScopeConfig();
        $this->sliderlist = $sliderlist;
        parent::__construct($context, $registry, $formfactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return __('Attachment Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return __('Attachment Information');
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
        $model = $this->_coreRegistry->registry('current_mainslider_attachment');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slider_attachment_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Attachment Information')]);
        if ($model->getSliderId()) {
            $fieldset->addField('attachment_id', 'hidden', ['name' => 'id']);
        }
        $field =  $fieldset->addField(
            'slider_id',
            'select',
            [
                'name' => 'slider_id',
                'label' => __('Slider'),
                'title' => __('Slider'),
                'values' => $this->sliderlist->toOptionArray(),
                'required' => true
            ]
        );
        $fieldset->addField(
                 'title', 'text', ['name' => 'title', 'label' => __('Title'), 'title' => __('Title'), 'required' => true]
        );
       
        
        $fieldset->addField(
                'link', 'text', ['name' => 'link', 'label' => __('Link'), 'title' => __('Link'), 'required' => true]
        );
        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'class' => '',
                'after_element_html' => '<small>Upload image with size less than 1200 KB</small>',
                'required' => true
            ]
        );
         $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),                
                'class' => ''
            ]
        );
        $fieldset->addField(
                'status', 'select', ['name' => 'status', 'label' => __('Status'), 'title' => __('Status'), 'values' => array(0 => array('label' => 'Enable', 'value' => '1'), 1 => array('label' => 'Disable', 'value' => '0')), 'required' => true]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
