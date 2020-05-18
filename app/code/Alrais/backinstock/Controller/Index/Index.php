<?php

namespace Alrais\backinstock\Controller\Index;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * Catalog design
     *
     * @var \Magento\Catalog\Model\Design
     */
    protected $_catalogDesign;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator
     */
    protected $categoryUrlPathGenerator;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    protected $_attributeCode;

    protected $_urlManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Model\Design $catalogDesign
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_catalogDesign = $catalogDesign;
        $this->_coreRegistry = $coreRegistry;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layerResolver = $layerResolver;
        $this->categoryRepository = $categoryRepository;
        $this->_urlManager = $context->getUrl();
    }

    /**
     * Initialize requested category object
     *
     * @return \Magento\Catalog\Model\Category
     */

    protected function _initCategory()
    {
        $categoryId = $this->_storeManager->getStore()->getRootCategoryId();
        try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $category->setIsAnchor(true);
        $this->_objectManager->get('Psr\Log\LoggerInterface')->info($category->getName());
        $this->_coreRegistry->register('current_category', $category);
        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after',
                ['category' => $category, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return false;
        }

        return $category;
    }

    /**
     * Category view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if ($this->_request->getParam(\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED)) {
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl());
        }
        $category = $this->_initCategory();
        if ($category) {
            $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
            $datetime = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
            $todayStartOfDayDate = $datetime->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            $todayEndOfDayDate = $datetime->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            $this->layerResolver->get(Resolver::CATALOG_LAYER_CATEGORY)->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('back_in_stock', ['eq' => 1])
            ->addAttributeToFilter('back_in_stock_on_homepage', ['eq' => 1]);
            // ->addStoreFilter()->addAttributeToFilter(
            //     'news_from_date', [
            //     'or' => [
            //         0 => ['date' => true, 'to' => $todayEndOfDayDate],
            //         1 => ['is' => new \Zend_Db_Expr('null')],
            //     ]
            //         ], 'left'
            // )->addAttributeToFilter(
            //         'news_to_date', [
            //     'or' => [
            //         0 => ['date' => true, 'from' => $todayStartOfDayDate],
            //         1 => ['is' => new \Zend_Db_Expr('null')],
            //     ]
            //         ], 'left'
            // )->addAttributeToFilter(
            //         [
            //             ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
            //             ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            //         ]
            // )->addAttributeToSort(
            //         'news_from_date', 'desc'
            // );
            // $collection->addAttributeToFilter('back_in_stock', ['eq' => 1]);
            // $collection->addAttributeToFilter('back_in_stock_on_homepage', ['eq' => 1]);

            $settings = $this->_catalogDesign->getDesignSettings($category);

            $page = $this->resultPageFactory->create();

            if ($settings->getPageLayout()) {
                $page->getConfig()->setPageLayout($settings->getPageLayout());
            }

            $hasChildren = $category->hasChildren();
            $type = $hasChildren ? 'layered' : 'default_without_children';
            $this->_objectManager->get('Psr\Log\LoggerInterface')->info($hasChildren);
            if (!$hasChildren) {
                $parentType = strtok($type, '_');
                $page->addPageLayoutHandles(['type' => $parentType]);
            }
            $page->addPageLayoutHandles(['type' => $type, 'id' => $category->getId()]);
            $page->getConfig()->addBodyClass('page-products')
                ->addBodyClass('categorypath-' . $this->categoryUrlPathGenerator->getUrlPath($category))
                ->addBodyClass('category-' . $category->getUrlKey())
                ->addBodyClass('catalog-category-view');

            return $page;
        } elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
