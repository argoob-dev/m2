<?php

/**
 * Copyright © 2015 Alrais. All rights reserved.
 */
// @codingStandardsIgnoreFile

namespace Alrais\MainSlider\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Layout;
use Magento\Framework\ObjectManagerInterface;

class Main extends Generic implements TabInterface {

    const ENABLED = 1;
    const DISABLED = 0;

    protected $_resource;
    protected $connection;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Store\Model\System\Store $systemStore, \Magento\Framework\App\ResourceConnection $resource, ObjectManagerInterface $objectManagerInterface, Layout $layout, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formfactory
    ) {
        $this->_systemStore = $systemStore;
        $this->_resource = $resource;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_blockFactory = $layout;
        $this->_objectManager = $objectManagerInterface;
        parent::__construct($context, $registry, $formfactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return __('Slider Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return __('Slider Information');
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
        $model = $this->_coreRegistry->registry('current_mainslider_slider');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $objectManager = $this->_objectManager;
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Slider Information')]);
        if ($model->getSliderId()) {
            $fieldset->addField('slider_id', 'hidden', ['name' => 'slider_id']);
        }
        $fieldset->addField(
                'caption', 'text', ['name' => 'caption', 'label' => __('Caption'), 'title' => __('Caption'), 'required' => true]
        );
        $fieldset->addField(
            'image', 'image', [
            'name' => 'image',
            'label' => __('Image'),
            'title' => __('Image'),
            'class' => '',
            'required' => true,
            'after_element_html' => '<small>Upload image with size less than 1200 KB</small>',
                ]
        );
        $fieldset->addField(
            'mobile_image', 'image', [
            'name' => 'mobile_image',
            'label' => __('Mobile Image'),
            'title' => __('Mobile Image'),
            'required' => false,
            'after_element_html' => '<small>Upload image with size less than 1200 KB</small>',
                ]
        );
        $linkType = $fieldset->addField(
                'link_type', 'select', ['name' => 'link_type',
            'label' => __('Link Type'),
            'title' => __('Link Type'),
            'values' => $objectManager->get('Alrais\MainSlider\Model\System\Config\LinkType')->toOptionArray(),
            'onchange' => 'CheckType(this)',
            'required' => false]
        );

        $external = $fieldset->addField(
                'link', 'text', ['name' => 'link', 'label' => __('Link'), 'title' => __('Link'), 'required' => true]
        );


        $brand = $fieldset->addField(
                'brand', 'select', [
            'label' => __('Brand'),
            'title' => __('Brand'),
            'name' => 'brand',
            'required' => false,
            'values' => $this->getBrands()
                ]
        );
        
        $data_type = $fieldset->addField(
                'data_type', 'text', [
            'label' => __('Data Type'),
            'title' => __('Data Type'),
            'class' => 'data_type',
            'name' => 'data_type',
            'note' => ""
                ]
        );
        $addWidget = $this->_blockFactory->createBlock('\Alrais\MainSlider\Block\Adminhtml\Widget\AddField');
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
        if ($model->getSliderId()) {
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
        // $fieldset->addField(
        //         'slider_type', 'select', ['name' => 'slider_type',
        //     'label' => __('Slider Type'),
        //     'title' => __('Slider Type'),
        //     'values' => $objectManager->get('Alrais\MainSlider\Model\System\Config\SliderType')->toOptionArray(),
        //     'required' => true]
        // );
        $fieldset->addField(
                'sort_order', 'text', [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => ''
                ]
        );
        
        $fieldset->addField(
                'status', 'select', ['name' => 'status', 'label' => __('Status'), 'title' => __('Status'), 'values' => array(0 => array('label' => 'Enable', 'value' => '1'), 1 => array('label' => 'Disable', 'value' => '0')), 'required' => true]
        );
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
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
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
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
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
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
        $brands = $this->getConnection()->fetchAll('SELECT ' . $eav_attribute_option . '.option_id as id,' . $eav_attribute_option_value . '.value as name  FROM ' . $eav_attribute_option_value . ' JOIN ' . $eav_attribute_option . ' ON ' . $eav_attribute_option_value . '.option_id = ' . $eav_attribute_option . '.option_id WHERE ' . $eav_attribute_option . '.attribute_id = 83 AND ' . $eav_attribute_option_value . '.store_id =' . $storeId);
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
