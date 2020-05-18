<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
// @codingStandardsIgnoreFile

namespace Alrais\MainMenu\Block\Adminhtml\Navigator\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Template;
use Magento\Cms\Model\Wysiwyg\Config;

class Main extends Generic implements TabInterface {

    const ENABLED = 1;
    const DISABLED = 0;

    protected $_resource;
    protected $connection;
    protected $_wysiwygConfig;
    protected $optionFactory;
    protected $_productCollection;
    protected $formkey;


    public function __construct(
    \Magento\Backend\Block\Template\Context $context, 
    \Magento\Framework\Registry $registry,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
    \Magento\Framework\Data\FormFactory $formfactory, 
    \Magento\Framework\App\ResourceConnection $resource, 
    \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, 
    \Magento\Store\Model\System\Store $systemStore,
    array $data = []
    ) {
        $this->_resource = $resource;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_productCollection = $productCollection;
        $this->_registry = $registry;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $registry, $formfactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return __('Menu Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return __('Menu Information');
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
        $model = $this->_coreRegistry->registry('current_alrais_mainmenu_navigator');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('navigator_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Menu Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
                'name', 'text', ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );
        $fieldset->addField(
                'link', 'text', ['name' => 'link', 'label' => __('Link'), 'title' => __('Link'), 'required' => true]
        );
        $fieldset->addField(
               'menu_type', 'select', ['name' => 'menu_type', 'label' => __('Menu Type'), 'title' => __('Menu Type'), 'values' => array(0 => array('label' => 'Top menu', 'value' => '0'), 1 => array('label' => 'Side menu', 'value' => '1')), 'required' => true]
       );
        $fieldset->addField(
                'status', 'select', ['name' => 'status', 'label' => __('Status'), 'title' => __('Status'), 'values' => array(0 => array('label' => 'Enable', 'value' => '1'), 1 => array('label' => 'Disable', 'value' => '0')), 'required' => true]
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
        $fieldset->addField('content', 'editor', [
            'name'      => 'content',
            'label' 	  => 'Content',
            'config'    => $this->_wysiwygConfig->getConfig(),
            'wysiwyg'   => true,
            'required'  => false,
            'after_element_html' => '<small>YOURCOMMENT.</small>',
      ]);
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                    'store_id', 'multiselect', [
                'name' => 'stores[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
                    ]
            );
            $renderer = $this->getLayout()->createBlock(
                    'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                    'store_id', 'hidden', ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
