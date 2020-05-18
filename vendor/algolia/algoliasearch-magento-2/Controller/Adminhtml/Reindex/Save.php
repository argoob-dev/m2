<?php

namespace Algolia\AlgoliaSearch\Controller\Adminhtml\Reindex;

use Algolia\AlgoliaSearch\Exception\ProductDeletedException;
use Algolia\AlgoliaSearch\Exception\ProductDisabledException;
use Algolia\AlgoliaSearch\Exception\ProductNotVisibleException;
use Algolia\AlgoliaSearch\Exception\ProductOutOfStockException;
use Algolia\AlgoliaSearch\Exception\UnknownSkuException;
use Algolia\AlgoliaSearch\Helper\Data as DataHelper;
use Algolia\AlgoliaSearch\Helper\Entity\ProductHelper;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;

class Save extends \Magento\Backend\App\Action
{
    const MAX_SKUS = 10;

    /** @var ProductFactory */
    private $productFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var DataHelper */
    private $dataHelper;

    /** @var ProductHelper */
    private $productHelper;

    /**
     * @param Context               $context
     * @param ProductFactory        $productFactory
     * @param StoreManagerInterface $storeManager
     * @param DataHelper            $dataHelper
     * @param ProductHelper         $productHelper
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        DataHelper $dataHelper,
        ProductHelper $productHelper
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * @throws UnknownSkuException
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        $skus = preg_split("/(,|\r\n|\n|\r)/", $this->getRequest()->getParam('skus'));

        $stores = $this->storeManager->getStores();

        foreach ($stores as $storeId => $storeData) {
            if ($this->dataHelper->isIndexingEnabled($storeId) === false) {
                unset($stores[$storeId]);
            }
        }

        if (empty($skus)) {
            $this->messageManager->addErrorMessage(__('Please, enter at least one SKU.'));
        }

        if (count($skus) > self::MAX_SKUS) {
            $this->messageManager->addErrorMessage(
                __(
                    'The maximal number of SKU(s) is %1. Could you please remove some SKU(s) to fit into the limit?',
                    self::MAX_SKUS
                )
            );
        }

        foreach ($skus as $sku) {
            $sku = trim($sku);
            try {
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->productFactory->create();
                $product->load($product->getIdBySku($sku));

                if (! $product->getId()) {
                    throw new UnknownSkuException(__('Product with SKU "%1" was not found.', $sku));
                }

                $this->checkAndReindex($product, $stores);
            } catch (UnknownSkuException $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            } catch (ProductDisabledException $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __(
                        'The product "%1" (%2) is disabled in store "%3".',
                        [$e->getProduct()->getName(), $e->getProduct()->getSku(), $stores[$e->getStoreId()]->getName()]
                    )
                );
            } catch (ProductDeletedException $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __(
                        'The product "%1" (%2) is deleted from store "%3".',
                        [$e->getProduct()->getName(), $e->getProduct()->getSku(), $stores[$e->getStoreId()]->getName()]
                    )
                );
            } catch (ProductNotVisibleException $e) {
                // If it's a simple product that is not visible, try to index its parent if it exists
                if ($e->getProduct()->getTypeId() == 'simple') {
                    $parentId = $this->productHelper->getParentProductIds([$e->getProduct()->getId()]);
                    if (isset($parentId[0])) {
                        $parentId = $parentId[0];
                        /** @var \Magento\Catalog\Model\Product $parentProduct */
                        $parentProduct = $this->productFactory->create();
                        $parentProduct->load($parentId);
                        $this->messageManager->addNoticeMessage(
                            __(
                                'The product "%1" (%2) is not visible but it has a parent product "%3" (%4).',
                                [$e->getProduct()->getName(), $e->getProduct()->getSku(), $parentProduct->getName(), $parentProduct->getSku()]
                            )
                        );

                        $this->checkAndReindex($parentProduct, $stores);
                        continue;
                    }
                }

                $this->messageManager->addExceptionMessage(
                    $e,
                    __(
                        'The product "%1" (%2) is not visible in store "%3".',
                        [$e->getProduct()->getName(), $e->getProduct()->getSku(), $stores[$e->getStoreId()]->getName()]
                    )
                );
            } catch (ProductOutOfStockException $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __(
                        'The product "%1" (%2) is out of stock in store "%3".',
                        [$e->getProduct()->getName(), $e->getProduct()->getSku(), $stores[$e->getStoreId()]->getName()]
                    )
                );
            }
        }

        return $resultRedirect;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array                          $stores
     *
     * @return void
     */
    private function checkAndReindex($product, $stores)
    {
        foreach ($stores as $storeId => $storeData) {
            if (! in_array($storeId, array_values($product->getStoreIds()))) {
                $this->messageManager->addNoticeMessage(
                    __(
                        'The product "%1" (%2) is not associated with store "%3".',
                        [$product->getName(), $product->getSku(), $storeData->getName()]
                    )
                );

                continue;
            }
            $this->dataHelper->canProductBeReindexed($product, $storeId);

            $productIds = [$product->getId()];
            $productIds = array_merge($productIds, $this->productHelper->getParentProductIds($productIds));

            $this->dataHelper->rebuildStoreProductIndex($storeId, $productIds);
            $this->messageManager->addSuccessMessage(
                __(
                    'The Product "%1" (%2) has been reindexed for store "%3".',
                    [$product->getName(), $product->getSku(), $storeData->getName()]
                )
            );
        }
    }
}
