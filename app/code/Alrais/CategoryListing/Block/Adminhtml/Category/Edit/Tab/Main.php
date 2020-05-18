<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alrais\CategoryListing\Block\Adminhtml\Category\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Alrais\CategoryListing\Model\System\Config\Category $categoryList,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_categoryList = $categoryList;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('category');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->scopeConfig->getValue('categorylisting/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $isNameDisabled = false;
        } else {
            $isNameDisabled = true;
        }
        
        if ($this->_isAllowedAction('Alrais_CategoryListing::category')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('category');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Information')]);

        if ($model->getId()) {
            $fieldset->addField('category_list_id', 'hidden', ['name' => 'category_list_id']);
        }

        
        // $fieldset->addField(
        //     'name',
        //     'text',
        //     [
        //         'name' => 'name',
        //         'label' => __('Name'),
        //         'title' => __('Name'),
        //         'class' => '',
        //         'disabled' => $isNameDisabled
        //     ]
        // );
        
        $fieldset->addField(
            'category_id',
            'select',
            [
                'name' => 'category_id',
                'label' => __('Category'),
                'title' => __('Category'),
                'values' => $this->_categoryList->toOptionArray(),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        
        // $fieldset->addField(
        //     'link',
        //     'text',
        //     [
        //         'name' => 'link',
        //         'label' => __('Link'),
        //         'title' => __('Link'),
        //         'class' => ''
        //     ]
        // );
        
        $fieldset->addField(
            'banner_image',
            'image',
            [
                'name' => 'banner_image',
                'label' => __('Category Image'),
                'title' => __('Category Image'),
                'class' => '',
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
                'banner_type', 'select', 
                ['name' => 'banner_type', 
                    'label' => __('Show In'), 
                    'title' => __('Show In'), 
                    'values' => 
                    array(
                        0 => array(
                            'label' => 'Web', 
                            'value' => '0'), 
                        1 => array(
                            'label' => 'Mobile', 
                            'value' => '1')
                        ), 
                    'required' => true]
        );
        
//        $fieldset->addField(
//            'target',
//            'select',
//            [
//                'label' => __('Target'),
//                'name' => 'target',
//                'values' => [
//                    [
//                        'value' => \Alrais\CategoryListing\Model\Category::CATEGORY_TARGET_SELF,
//                        'label' => __('Same Tab or Window without Browser Navigation'),
//                    ],
//                    [
//                        'value' => \Alrais\CategoryListing\Model\Category::CATEGORY_TARGET_BLANK,
//                        'label' => __('New Tab or Window with Browser Navigation'),
//                    ],
//                    [
//                        'value' => \Alrais\CategoryListing\Model\Category::CATEGORY_TARGET_PARENT,
//                        'label' => __('Parent Tab or Window with Browser Navigation'),
//                    ],
//                ],
//            ]
//        );
        
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),                
                'class' => '',
                'disabled' => $isElementDisabled
            ]
        );
        
        
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Change Status'),
                'title' => __('Change Status'),
                'name' => 'status',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        
        
             
        
        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
                );
                $renderer = $this->getLayout()->createBlock(
                    'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
                );
                $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        

        

        $this->_eventManager->dispatch('adminhtml_Category_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Category Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Category Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
