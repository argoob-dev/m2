<?php
namespace Alrais\MainMenu\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Alrais\MainMenu\Model\NavigatorFactory;
 
class Index extends Action
{
    /**
     * @var \Tutorial\SimpleNews\Model\NewsFactory
     */
    protected $_modelNavigatorFactory;
 
    /**
     * @param Context $context
     * @param NewsFactory $modelNewsFactory
     */
    public function __construct(
        Context $context,
        NavigatorFactory $modelNavigatorFactory
    ) {
        parent::__construct($context);
        $this->_modelNavigatorFactory = $modelNavigatorFactory;
    }
 
    public function execute()
    {
        /**
         * When Magento get your model, it will generate a Factory class
         * for your model at var/generaton folder and we can get your
         * model by this way
         */
        $navigatorModel = $this->_modelNavigatorFactory->create();
 
        // Load the item with ID is 1
        $item = $navigatorModel->load(1);
        var_dump($item->getData());
 
        // Get news collection
        $navigatorCollection = $navigatorModel->getCollection();
        // Load all data of collection
        var_dump($navigatorCollection->getData());
    }
}
