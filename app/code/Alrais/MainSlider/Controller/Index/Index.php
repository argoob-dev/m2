<?php
namespace Alrais\MainSlider\Controller\Index;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Alrais\MainSlider\Model\SliderFactory;
 
class Index extends Action
{
    /**
     * @var \Tutorial\SimpleNews\Model\NewsFactory
     */
    protected $_modelSliderFactory;
 
    /**
     * @param Context $context
     * @param NewsFactory $modelNewsFactory
     */
    public function __construct(
        Context $context,
        SliderFactory $modelSliderFactory
    ) {
        parent::__construct($context);
        $this->_modelSliderFactory = $modelSliderFactory;
    }
 
    public function execute()
    {
        /**
         * When Magento get your model, it will generate a Factory class
         * for your model at var/generaton folder and we can get your
         * model by this way
         */
        $sliderModel = $this->_modelSliderFactory->create();
 
        // Load the item with ID is 1
        $item = $sliderModel->load(1);
        var_dump($item->getData());
 
        // Get news collection
        $sliderCollection = $sliderModel->getCollection();
        // Load all data of collection
        var_dump($sliderCollection->getData());
    }
}
