<?php
namespace Alrais\MainSlider\Block\Adminhtml\Attachment\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\Object;
use Magento\Store\Model\StoreManagerInterface;

class Slider extends AbstractRenderer
{
    private $_storeManager;
    protected $_sliderFactory;
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context, 
            \Alrais\MainSlider\Model\SliderFactory $sliderFactory,
            StoreManagerInterface $storemanager, array $data = [])
    {
        $this->_storeManager = $storemanager;
        $this->_sliderFactory = $sliderFactory;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
    /**
     * Renders grid column
     *
     * @param Object $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $sliderCollection = $this->_sliderFactory->create()->load($this->_getValue($row));
        
        return $sliderCollection->getCaption();
    }
}