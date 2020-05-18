<?php
namespace Magento\CatalogSearch\Helper\Data;

/**
 * Interceptor class for @see \Magento\CatalogSearch\Helper\Data
 */
class Interceptor extends \Magento\CatalogSearch\Helper\Data implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\Stdlib\StringUtils $string, \Magento\Framework\Escaper $escaper, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $string, $escaper, $storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvancedSearchUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAdvancedSearchUrl');
        if (!$pluginInfo) {
            return parent::getAdvancedSearchUrl();
        } else {
            return $this->___callPlugins('getAdvancedSearchUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isMinQueryLength()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMinQueryLength');
        if (!$pluginInfo) {
            return parent::isMinQueryLength();
        } else {
            return $this->___callPlugins('isMinQueryLength', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEscapedQueryText()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEscapedQueryText');
        if (!$pluginInfo) {
            return parent::getEscapedQueryText();
        } else {
            return $this->___callPlugins('getEscapedQueryText', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResultUrl($query = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResultUrl');
        if (!$pluginInfo) {
            return parent::getResultUrl($query);
        } else {
            return $this->___callPlugins('getResultUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSuggestUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSuggestUrl');
        if (!$pluginInfo) {
            return parent::getSuggestUrl();
        } else {
            return $this->___callPlugins('getSuggestUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchTermUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSearchTermUrl');
        if (!$pluginInfo) {
            return parent::getSearchTermUrl();
        } else {
            return $this->___callPlugins('getSearchTermUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMinQueryLength($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMinQueryLength');
        if (!$pluginInfo) {
            return parent::getMinQueryLength($store);
        } else {
            return $this->___callPlugins('getMinQueryLength', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxQueryLength($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMaxQueryLength');
        if (!$pluginInfo) {
            return parent::getMaxQueryLength($store);
        } else {
            return $this->___callPlugins('getMaxQueryLength', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addNoteMessage($message)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addNoteMessage');
        if (!$pluginInfo) {
            return parent::addNoteMessage($message);
        } else {
            return $this->___callPlugins('addNoteMessage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setNoteMessages(array $messages)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setNoteMessages');
        if (!$pluginInfo) {
            return parent::setNoteMessages($messages);
        } else {
            return $this->___callPlugins('setNoteMessages', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNoteMessages()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getNoteMessages');
        if (!$pluginInfo) {
            return parent::getNoteMessages();
        } else {
            return $this->___callPlugins('getNoteMessages', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkNotes($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkNotes');
        if (!$pluginInfo) {
            return parent::checkNotes($store);
        } else {
            return $this->___callPlugins('checkNotes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParamName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQueryParamName');
        if (!$pluginInfo) {
            return parent::getQueryParamName();
        } else {
            return $this->___callPlugins('getQueryParamName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isModuleOutputEnabled');
        if (!$pluginInfo) {
            return parent::isModuleOutputEnabled($moduleName);
        } else {
            return $this->___callPlugins('isModuleOutputEnabled', func_get_args(), $pluginInfo);
        }
    }
}
