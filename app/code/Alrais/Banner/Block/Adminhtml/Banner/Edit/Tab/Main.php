<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Alrais\Banner\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Framework\View\Layout;
use Magento\Framework\ObjectManagerInterface;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_wysiwygConfig;
    protected $_blockFactory;
    protected $_objectManager;
    protected $connection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\App\ResourceConnection $resource, ObjectManagerInterface $objectManagerInterface, Layout $layout, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore, \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->scopeConfig = $scopeConfig;
        $this->_blockFactory = $layout;
        $this->_resource = $resource;
        $this->_objectManager = $objectManagerInterface;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm() {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('banner');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->scopeConfig->getValue('banner/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $isNameDisabled = false;
        } else {
            $isNameDisabled = true;
        }

        if ($this->_isAllowedAction('Alrais_Banner::banner')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $objectManager = $this->_objectManager;

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Banner Information')]);

        if ($model->getId()) {
            $fieldset->addField('banner_id', 'hidden', ['name' => 'banner_id']);
        }

        $fieldset->addField(
                'name', 'text', [
            'name' => 'name',
            'label' => __('name'),
            'title' => __('name'),
            'class' => '',
            'required' => false
                ]
        );


        $fieldset->addField(
                'sort_order', 'text', [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => '',
            'disabled' => $isElementDisabled
                ]
        );

        $fieldset->addField(
                'banner_type', 'select', ['name' => 'banner_type',
            'label' => __('Banner Type'),
            'title' => __('Banner Type'),
            'values' => $objectManager->get('Alrais\Banner\Model\Config\Source\BannerType')->toOptionArray(),
            'required' => true]
        );

        $fieldset->addField(
                'status', 'select', [
            'label' => __('Change Status'),
            'title' => __('Change Status'),
            'name' => 'status',
            'required' => true,
            'options' => $model->getAvailableStatuses(),
            'disabled' => $isElementDisabled
                ]
        );

        $linkType = $fieldset->addField(
                'link_type', 'select', ['name' => 'link_type',
            'label' => __('Link Type'),
            'title' => __('Link Type'),
            'values' => $objectManager->get('Alrais\Banner\Model\Config\Source\LinkType')->toOptionArray(),
            'onchange' => 'CheckType(this)',
            'required' => false]
        );

        $brand = $fieldset->addField(
                'brand', 'select', [
            'label' => __('Brand'),
            'title' => __('Brand'),
            'name' => 'brand',
            'required' => false,
            'values' => $this->getBrands(),
            'disabled' => $isElementDisabled
                ]
        );
        
        $external = $fieldset->addField(
                'external_link', 'text', [
            'label' => __('External Link'),
            'title' => __('External Link'),
            'name' => 'external_link',
            'required' => false
                ]
        );

        $data_type = $fieldset->addField(
                'data_type', 'text', [
            'label' => __('Data Type'),
            'title' => __('Data Type'),
            'class' => 'data_type',
            'name' => 'data_type',
            'note' => "With Menu Type: Enternal Link <br /> Http link can is full link \"http://magento.com, ...\" or short link \"magento.com, ...\". <br/>" .
            "Note: With link type \"https://www.google.com.vn\", \"https://...\", you only can use short link \"google.com.vn\", ..."
                ]
        );
        $addWidget = $this->_blockFactory->createBlock('\Alrais\Banner\Block\Adminhtml\Widget\AddField');
        $addWidget->addFieldWidget(
                [
            'data' => [
                '@' => ['type' => 'complex'],
                'id' => 'product_id',
                'sort_order' => '10',
                'label' => 'Product',
                'required' => false,
                'helper_block' => [
                    'data' => [
                        'button' => [
                            'open' => __('Select Product...')
                        ]
                    ],
                    'type' => 'Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser'
                ]
            ]
                ], $fieldset
        );
        $addWidget->addFieldWidget(
                [
            'data' => [
                '@' => ['type' => 'complex'],
                'id' => 'category_id',
                'sort_order' => '11',
                'label' => 'Category',
                'required' => false,
                'helper_block' => [
                    'data' => [
                        'button' => [
                            'open' => __('Select Category...')
                        ]
                    ],
                    'type' => 'Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser'
                ]
            ]
                ], $fieldset
        );
        $js_type = "";
        if ($model->getId()) {
            $type_val = $model[$linkType->getId()];
            $data_type_val = $model[$data_type->getId()];
            $js_type = '
					data_val[' . $type_val . '] = "' . $data_type_val . '";
					CheckType($(\'' . $linkType->getId() . '\'));
			';
        }
        $linkType->setAfterElementHtml('
					<script type="text/javascript">
						// check type
						var data_val = new Array();
						require([
							"jquery",
							"tinymce",
							"Magento_Ui/js/modal/modal",
							"prototype",
							"mage/adminhtml/events"
						], function(jQuery, tinyMCE, modal){
							$$("div[id^=\'' . 'box_\']").each( function(element){
								element.up().up().up().hide();
							});
							/*$$("[id^=\'' . 'content\']").each(function(element){
								element.up().up().up().hide();
							});*/
							$$(".megamenu_content").each(function(element){
								element.up().up().up().hide();
							});
							$$(".data_type").each(function(element){
								element.up().up().hide();
								element.removeClassName("required-entry");
							});
							$(\'' . $linkType->getId() . '\').observe("focus",function(event){
								var element = Event.element(event);
								data_val[element.value] = $$(".data_type")[0].value;
							});
                                                        ' . $js_type . '
						});
						function CheckType(element){
							type = element.value;
							if(typeof(data_val[type]) !="undefined"){
								$$(".data_type")[0].value = data_val[type];
							}
							else{
								$$(".data_type")[0].value ="";
							}
							$$("div[id^=\'' . 'box_\']").each(function(element){
								element.up().up().up().hide();
					   		});
							$$(".data_type").each(function(element){
								element.up().up().hide();
								element.removeClassName("required-entry");
							});
							/*$$("[id^=\'' . 'content\']").each(function(element){
								element.up().up().up().hide();
								element.removeClassName("required-entry");
							});*/
							$$(".megamenu_content").each(function(element){
								element.up().up().up().hide();
								element.removeClassName("required-entry");
							});
							if(type==3){
								$$("div[id^=\'' . 'box_product_id\']")[0].up().up().up().show();
							}
                                                        if(type==4){
								$$("div[id^=\'' . 'box_category_id\']")[0].up().up().up().show();
							}
                                                        
						}
					</script>
        ');
        $fieldset->addField(
            'banner_image', 'image', [
            'name' => 'banner_image',
            'label' => __('Banner Image'),
            'title' => __('Banner Image'),
            'class' => '',
            'required' => true,
            'disabled' => $isElementDisabled
                ]
        );
        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                    'store_id', 'multiselect', [
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
                    'store_id', 'hidden', ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }



        $this->setChild(
                'form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                        ->addFieldMap($linkType->getHtmlId(), $linkType->getName())
                        ->addFieldMap($brand->getHtmlId(), $brand->getName())
                        ->addFieldDependence($brand->getName(), $linkType->getName(), '5')
                        ->addFieldMap($external->getHtmlId(), $external->getName())
                        ->addFieldDependence($external->getName(), $linkType->getName(), '2')
        );

        $this->_eventManager->dispatch('adminhtml_banner_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel() {
        return __('Banner Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle() {
        return __('Banner Information');
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
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId) {
        return $this->_authorization->isAllowed($resourceId);
    }

    protected function _getJs($element) {
        $js = '
            <script type="text/javascript">
            //<![CDATA[
                openEditorPopup = function(url, name, specs, parent) {
                    if ((typeof popups == "undefined") || popups[name] == undefined || popups[name].closed) {
                        if (typeof popups == "undefined") {
                            popups = new Array();
                        }
                        var opener = (parent != undefined ? parent : window);
                        popups[name] = opener.open(url, name, specs);
                    } else {
                        popups[name].focus();
                    }
                    return popups[name];
                }

                closeEditorPopup = function(name) {
                    if ((typeof popups != "undefined") && popups[name] != undefined && !popups[name].closed) {
                        popups[name].close();
                    }
                }
            //]]>
            </script>';
        return $js;
    }

    protected function getConnection() {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }

    public function getBrandsList() {
        $storeId = $this->getCurrentStoreName();
        $eav_attribute_option = $this->_resource->getTableName('eav_attribute_option');
        $eav_attribute_option_value = $this->_resource->getTableName('eav_attribute_option_value');
        $brands = $this->getConnection()->fetchAll('SELECT ' . $eav_attribute_option . '.option_id as id,' . $eav_attribute_option_value . '.value as name  FROM ' . $eav_attribute_option_value . ' JOIN ' . $eav_attribute_option . ' ON ' . $eav_attribute_option_value . '.option_id = ' . $eav_attribute_option . '.option_id WHERE ' . $eav_attribute_option . '.attribute_id = 81 AND ' . $eav_attribute_option_value . '.store_id =' . $storeId);
        return $brands;
    }

    public function getCurrentStoreName() {
        return $this->_storeManager->getStore()->getId();
    }

    public function getBrands() {
        $options = $this->getBrandsList();
        if (sizeof($options) > 0) {
            $optionsArray = array(array('value' => 0, 'label' => 'Please Select'));
            foreach ($options as $optionList) {

                $optionsArray[] = array('value' => $optionList['id'], 'label' => $optionList['name']);
            }
        } else {
            $optionsArray = array(0 => array('label' => 'NIL', 'value' => '0'));
        }
        return $optionsArray;
    }

}
