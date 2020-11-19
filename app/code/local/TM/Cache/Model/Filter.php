<?php

class TM_Cache_Model_Filter extends Varien_Object implements Zend_Filter_Interface
{
    const PATTERN_BLOCK_PLACEHOLDER      = '{{tm_cache %s %s}}';
    const PATTERN_BLOCK_PLACEHOLDER_TYPE = '([\w]*?)';
    const PATTERN_HANDLES                = '{{handles %s}}';
    const PATTERN_UPDATES                = '{{updates %s}}';
    const PATTERN_MESSAGE_STORAGE        = '{{message_storage %s}}';
    const PATTERN_NON_GREEDY             = '.*?';
    const SEPARATOR_SPACE                = ' ';

    /**
     * Rendered placeholders
     *
     * @var array
     */
    protected $_placeholderOutput = array();

    /**
     * Retrive layout object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        return Mage::app()->getLayout();
    }

    /**
     * Replace all tm_cache placeholders with dynamic content
     *
     * Known placeholders:
     *  {{tm_cache block name="blockName" [type="block/type"]}}
     *  {{tm_cache widget type="block/widget_type"}}
     *  {{tm_cache call class="class/name" method="methodName" type="model|single|disabled"}}
     *  {{handles handle1 handle2}}
     *  {{message_storage storage1 storage2}}
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $replaceRules = array(
            'patterns' => array(),
            'content'  => array()
        );

        if (Mage::registry('tmcache_hit')) {
            // emulate standard behaviour of page/html block with SID placeholder
            $this->_beforeCacheUrl();

            $handles = $this->parseHandles($value);
            if ($handles) {
                $replaceRules['patterns'][] = $handles['pattern'];
                $replaceRules['content'][]  = '';
                $this->getLayout()->getUpdate()->addHandle($handles['value']);
            }

            // $this->getController()->loadLayout(null, false);
            $action = $this->getController();
            $action->addActionLayoutHandles()->loadLayoutUpdates();
            $updates = $this->parseLayoutUpdates($value); // custom layout updates are not cached by magento
            if ($updates) {
                $replaceRules['patterns'][] = $updates['pattern'];
                $replaceRules['content'][]  = '';
                $action->getLayout()->getUpdate()->addUpdate($updates['value']);
            }
            $action->generateLayoutXml();

            $cacheLayout = Mage::getSingleton('tmcache/layout');

            // own layout is used to prevent cached blocks from initializing
            $_profilerKey = Mage_Core_Controller_Varien_Action::PROFILER_KEY . '::' . $this->getController()->getFullActionName();
            if (!$this->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
                Mage::dispatchEvent(
                    'controller_action_layout_generate_blocks_before',
                    array('action'=>$this->getController(), 'layout'=>$this->getLayout())
                );
            }
            Varien_Profiler::start("$_profilerKey::tm_layout_generate_blocks");
            $cacheLayout->generateBlocks();
            Varien_Profiler::stop("$_profilerKey::tm_layout_generate_blocks");
            if (!$this->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
                Mage::dispatchEvent(
                    'controller_action_layout_generate_blocks_after',
                    array('action'=>$this->getController(), 'layout'=>$this->getLayout())
                );
            }

            if ('catalog_category_view' === $action->getFullActionName()) {
                Mage::getSingleton('catalog/session')
                    ->setLastVisitedCategoryId($action->getRequest()->getParam('id'));
            }

            // this code should be after the generateBlocks to guarantee that
            // global_messages will be created before getMessageBlock will called.
            // core/session messages fix
            $storages = $this->parseMessageStorages($value);
            if ($storages) {
                $replaceRules['patterns'][] = $storages['pattern'];
                $replaceRules['content'][]  = '';
                $cacheLayout->initMessages($storages['value']);
            }

            // @todo should I replace uenc?
            // $value = $this->replaceUenc($value);
            $value = $this->replaceLoginReferer($value); // update cached referers
        }

        Mage::register('tmcache_render', 1);
        $isJson = (0 === strpos($value, '{"'));
        foreach ($this->parsePlaceholders($value) as $placeholder) {
            if (isset($this->_placeholderOutput[$placeholder['pattern']])) {
                $content = $this->_placeholderOutput[$placeholder['pattern']];
            } else {
                switch ($placeholder['placeholder_type']) {
                    case 'widget':
                    case 'block':
                        $content = $this->renderBlock($placeholder);
                        break;
                    case 'call':
                        $content = $this->renderCall($placeholder);
                        break;
                    case 'sid':
                        $content = $this->renderSid($placeholder);
                        break;
                    case 'form_key_url':
                        $content = $this->renderFormKeyUrl($placeholder);
                        break;
                    case 'form_key_hidden':
                        $content = $this->renderFormKeyHidden($placeholder);
                        break;
                    default:
                        $content = $this->renderCmsFilter($placeholder);
                        break;
                }
            }
            if ($isJson) {
                $content = Mage::helper('core')->jsonEncode($content);
                $content = substr($content, 1, -1); // remove doublequotes added by jsonEncode
            }
            // emulate standard behaviour of page/html block with SID placeholder
            $content = $this->_afterCacheUrl($content);

            $replaceRules['patterns'][] = $placeholder['pattern'];
            $replaceRules['content'][]  = $content;
        }

        return str_replace($replaceRules['patterns'], $replaceRules['content'], $value);
    }

    /**
     * Copy of Mage_Core_Block_Abstract::_beforeCacheUrl
     */
    protected function _beforeCacheUrl()
    {
        if (Mage::app()->useCache(Mage_Core_Block_Abstract::CACHE_GROUP)) {
            Mage::app()->setUseSessionVar(true);
        }
        return $this;
    }

    /**
     * Copy of Mage_Core_Block_Abstract::_afterCacheUrl
     *
     * @param string $html
     * @return string
     */
    protected function _afterCacheUrl($html)
    {
        if (Mage::app()->useCache(Mage_Core_Block_Abstract::CACHE_GROUP)) {
            Mage::app()->setUseSessionVar(false);
            $html = Mage::getSingleton('core/url')->sessionUrlVar($html);
        }
        return $html;
    }

    public function replaceLoginReferer($html)
    {
        if (false === strpos($html, '/' . Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME . '/')) {
            return $html;
        }

        $referer = $this->getController()->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
        if (!$referer
            && !Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)
            && !Mage::getSingleton('customer/session')->getNoReferer()
        ) {
            $referer = Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));
            $referer = Mage::helper('core')->urlEncode($referer);
        }
        $search  = '/\/(' . Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME . ')\/[^\/]*\//';
        $replace = ($referer ? '/$1/' . $referer . '/' : '');
        $html    = preg_replace($search, $replace, $html);
        return $html;
    }

    public function replaceUenc($html)
    {
        $helper  = Mage::helper('core/url');
        $search  = '/\/(' . Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED . ')\/[^\/]*\//';
        $replace = '/$1/' . $helper->getEncodedUrl() . '/';
        $html    = preg_replace($search, $replace, $html);
        return $html;
    }

    /**
     * @see TM_Cache_Model_Processor::processResponse
     * @return string
     */
    public function replaceDynamicPartsWithPlaceholder($html)
    {
        $session = Mage::getSingleton('core/session');

        // sid
        $search = array(
            $session->getSessionIdQueryParam() . '=' . $session->getSessionId()
        );
        $replace = array(
            sprintf(self::PATTERN_BLOCK_PLACEHOLDER, 'sid', 'tm_cache')
        );

        // form_key
        if (defined('Mage_Core_Model_Url::FORM_KEY')) {
            $search[]  = Mage_Core_Model_Url::FORM_KEY . '/' . $session->getFormKey();
            $replace[] = sprintf(self::PATTERN_BLOCK_PLACEHOLDER, 'form_key_url', 'tm_cache');

            $search[]  = 'value="' . $session->getFormKey() . '"';
            $replace[] = sprintf(self::PATTERN_BLOCK_PLACEHOLDER, 'form_key_hidden', 'tm_cache');
        }

        return str_replace($search, $replace, $html);
    }

    public function renderSid($config)
    {
        $session = Mage::getSingleton('core/session');
        return $session->getSessionIdQueryParam() . '=' . $session->getSessionId();
    }

    public function renderFormKeyUrl($config)
    {
        $session = Mage::getSingleton('core/session');
        return Mage_Core_Model_Url::FORM_KEY . '/' . $session->getFormKey();
    }

    public function renderFormKeyHidden($config)
    {
        $session = Mage::getSingleton('core/session');
        return 'value="' . $session->getFormKey() . '"';
    }

    /**
     * Returns content for block and widget types
     *
     * @param array $config
     * @return string
     */
    public function renderBlock($config)
    {
        $block = null;
        if (!empty($config['name'])) {
            $block = $this->getLayout()->getBlock($config['name']);
        } else {
            $config['name'] = '';
        }

        if (!$block && !empty($config['type'])) {
            $exclude = array('type', 'name', 'pattern', 'placeholder_type');
            $options = array_diff_key($config, array_flip($exclude));
            $block   = $this->getLayout()->createBlock(
                $config['type'], $config['name'], $options
            );
        }

        if (!$block) {
            return '';
        }

        $block->setUsePlaceholder(false);
        if (isset($config['template'])) {
            $block->setTemplate($config['template']);
        }

        if (empty($config['method'])) {
            $content = $block->toHtml();
        } else {
            $content = $block->{$config['method']}();
        }
        return $content;
    }

    /**
     * Try to parse all non matched placeholders:
     *
     * {{tm_cache store url=""}}
     *
     * @param array $config
     * @return string
     */
    public function renderCmsFilter($config)
    {
        $filter  = Mage::helper('cms')->getPageTemplateProcessor();
        $content = str_replace('tm_cache ', '', $config['pattern']);
        return $filter->filter($content);
    }

    /**
     * {{tm_cache call class="class/name" method="methodName" type="model|single|disabled"}}
     *
     * @param array $config
     * @return string
     */
    public function renderCall($config)
    {
        if (!isset($config['type'])) {
            $config['type'] = 'single';
        }

        switch ($config['type']) {
            case 'disabled':
                return '';
            case 'object':
            case 'model':
                $object = Mage::getModel($config['class']);
                break;
            default:
                $object = Mage::getSingleton($config['class']);
                break;
        }

        $result = '';
        if (method_exists($object, $config['method'])) {
            if (!empty($config['args'])) {
                $result = call_user_func_array(
                    array($object, $config['method']),
                    explode(',', $config['args'])
                );
            } else {
                $result = $object->$config['method']();
            }
        } elseif (Mage::getIsDeveloperMode()) {
            Mage::throwException('Method "'.$config['method'].'" is not defined in "'.get_class($object).'"');
        }

        if (!isset($config['silent']) || !$config['silent']) {
            return is_string($result) ? $result : '';
        }
        return '';
    }

    /**
     * Finds placeholers information in html content
     * with preg_match_all by PATTERN_BLOCK_PLACEHOLDER
     *
     * @param string $html
     * @return array
     *  pattern
     *  name
     */
    public function parsePlaceholders($html)
    {
        $matches = array();
        $pattern = sprintf(
            self::PATTERN_BLOCK_PLACEHOLDER,
            self::PATTERN_BLOCK_PLACEHOLDER_TYPE,
            '(' . self::PATTERN_NON_GREEDY . ')'
        );
        preg_match_all("/{$pattern}/si", $html, $matches);

        if (empty($matches[2])) {
            return array();
        }

        $result = array();
        foreach ($matches[2] as $i => $params) {
            $result[$i]['placeholder_type'] = $matches[1][$i];
            $result[$i]['pattern'] = sprintf(
                self::PATTERN_BLOCK_PLACEHOLDER,
                $matches[1][$i],
                $params
            );

            $params = trim($params);
            $params = explode('"' . self::SEPARATOR_SPACE, $params);
            foreach ($params as $j => $keyAndValue) {
                if (!strstr($keyAndValue, '=')) {
                    continue;
                }
                list($key, $value) = explode('=', $keyAndValue);
                $result[$i][$key]  = trim($value, '"\\'); // \ is removed to be compatible with json responses
            }
        }
        return $result;
    }

    /**
     * Finds handles information in html content
     * with preg_match_all by PATTERN_HANDLES
     *
     * @param string $html
     * @return array
     */
    public function parseHandles($html)
    {
        $matches = array();
        $pattern = sprintf(self::PATTERN_HANDLES, '(' . self::PATTERN_NON_GREEDY . ')');
        preg_match_all("/{$pattern}/", $html, $matches);

        if (empty($matches[1])) {
            return array();
        }

        return array(
            'pattern' => $matches[0][0],
            'value'   => explode(self::SEPARATOR_SPACE, $matches[1][0])
        );
    }

    public function parseLayoutUpdates($html)
    {
        $matches = array();
        $pattern = sprintf(self::PATTERN_UPDATES, '(' . self::PATTERN_NON_GREEDY . ')');
        preg_match_all("/{$pattern}/si", $html, $matches);

        if (empty($matches[1])) {
            return array();
        }

        return array(
            'pattern' => $matches[0][0],
            'value'   => $matches[1][0]
        );
    }

    public function parseMessageStorages($html)
    {
        $matches = array();
        $pattern = sprintf(self::PATTERN_MESSAGE_STORAGE, '(' . self::PATTERN_NON_GREEDY . ')');
        preg_match_all("/{$pattern}/", $html, $matches);

        if (empty($matches[1])) {
            return array();
        }

        return array(
            'pattern' => $matches[0][0],
            'value'   => explode(self::SEPARATOR_SPACE, $matches[1][0])
        );
    }

    /**
     * Generate handles placeholder
     *
     * @param array $handles
     * @return string
     */
    public function getLayoutHandlesPlaceholder($handles)
    {
        return sprintf(
            self::PATTERN_HANDLES,
            implode(self::SEPARATOR_SPACE, $handles)
        );
    }

    public function getLayoutUpdatesPlaceholder($update)
    {
        return sprintf(self::PATTERN_UPDATES, (string)$update);
    }

    /**
     * Generate message storage placeholder
     *
     * @param TM_Cache_Block_Core_Messsages $messageBlock
     * @return string
     */
    public function getMessageStoragePlaceholder($messageBlock)
    {
        if (method_exists($messageBlock, 'getUsedStorageTypes')) {
            $messageStorages = $messageBlock->getUsedStorageTypes();
        } else {
            // just in case if third-party module overrides messages block too,
            // and getUsedStorageTypes is undefined
            $messageStorages = array(
                'core/session',     'catalog/session',
                'checkout/session', 'customer/session'
            );
        }
        return sprintf(
            self::PATTERN_MESSAGE_STORAGE,
            implode(self::SEPARATOR_SPACE, $messageStorages)
        );
    }

    /**
     * Generate placeholder for block object
     *
     * @param Mage_Core_Block_Abstract $block
     * @return string
     */
    public function getBlockPlaceholder($block)
    {
        $params = array();
        if (0 === strpos($block->getNameInLayout(), 'ANONYMOUS_')) {
            // inline block call {{block type="module/name" ...}}
            foreach ($block->getData() as $key => $value) {
                if (in_array($key, array('block_params', 'module_name'))) {
                    continue;
                }
                if (!is_numeric($value) && !is_string($value) && !is_bool($value)) {
                    continue;
                }
                $params[] = $key.'="' . $value . '"';
            }
        } else {
            $params[] = 'name="' . $block->getNameInLayout() . '"';
        }
        return sprintf(
            self::PATTERN_BLOCK_PLACEHOLDER,
            'block',
            implode(self::SEPARATOR_SPACE, $params)
        );
    }

    /**
     * Fix for bundle and configurable products: ?options=cart
     */
    public function getStaticMessages($request)
    {
        $params   = array();
        $storages = array();

        if ('cart' === $request->getParam('options')) {
            $storages[] = 'catalog/session';
            $notice     = array(
                'class="catalog/session"',
                'method="addNotice"'
            );
            if ($product = Mage::registry('product')) {
                $notice[] = 'args="' . $product->getTypeInstance(true)->getSpecifyOptionMessage() . '"';
            } else {
                $notice[] = 'args="' . Mage::helper('catalog')->__('Please specify the product\'s option(s).') . '"';
            }
            $params[] = $notice;
        }

        $result = '';
        foreach ($params as $message) {
            $result .= sprintf(
                self::PATTERN_BLOCK_PLACEHOLDER,
                'call',
                implode(self::SEPARATOR_SPACE, $message)
            );
        }
        foreach (array_unique($storages) as $storage) {
            $result .= sprintf(
                self::PATTERN_BLOCK_PLACEHOLDER,
                'call',
                'class="tmcache/layout" method="initMessages" args="' . $storage . '"'
            );
        }
        return $result;
    }

    /**
     * Store placeholder output for visitor who triggers cache creation.
     * This method is added to remove double block rendering issues
     *
     * @param string $placeholder
     * @param string $html
     */
    public function addPlaceholderOutput($placeholder, $html)
    {
        if (false === strstr($html, '{{tm_cache')) {
            $this->_placeholderOutput[$placeholder] = $html;
        }
    }
}
