<?php
/**
 * Copyright © 2017 Alrais, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Alrais\Combo\Block\Combo;

class View extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
	protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $title = __('Combo Products');
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($title);
        }
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'Combo',
                [
                    'label' => __('Combo'),
                    'title' => __('Combo')
                ]
            );
        }
        return $this;
    }
	
	public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }
	public function getIdentities(){
		return [];
	}
	
}