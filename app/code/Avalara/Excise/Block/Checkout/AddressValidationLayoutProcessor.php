<?php

namespace Avalara\Excise\Block\Checkout;

use Avalara\Excise\Helper\Config;

class AddressValidationLayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @const Path to template
     */
    const COMPONENT_PATH = 'Avalara_Excise/js/view/ReviewPayment';

    /**
     * @var Config
     */
    protected $config = null;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AddressValidationLayoutProcessor constructor.
     * @param Config $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(Config $config, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Overrides payment component and adds config variable to be used in the component and template
     *
     * This class takes the place of a layout config change to checkout_index_index.xml. Making the changes to the
     * layout this way is necessary because in the process of merging the layout files, layout overrides are
     * applied over existing nodes in alphabetical order by Namespace_ModuleName. So Magento_Checkout overrides
     * Avalara_Excise because Magento_Checkout layout files are merged after Avalara_Excise layout files. The
     * solution is to set the value of the converted object after the layout files have been merged. Additionally,
     * because the config fields must be accessed from PHP, the most efficient method of setting the config node values
     * is with PHP as the following code does.
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if ($this->config->isModuleEnabled()) {
            if ($this->config->isAddressValidationEnabled($this->storeManager->getStore())) {
                $userHasChoice = $this->config->getAllowUserToChooseAddress($this->storeManager->getStore());
                if ($userHasChoice) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['config']['instructions']
                        = $this->config->getAddressValidationInstructionsWithChoice($this->storeManager->getStore());
                } else {
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                        ['payment']['config']['instructions']
                        = $this->config->getAddressValidationInstructionsWithoutChoice($this->storeManager->getStore());
                }
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['config']['errorInstructions']
                    = $this->config->getAddressValidationErrorInstructions($this->storeManager->getStore());
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['config']['choice'] = $userHasChoice;
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['component'] = self::COMPONENT_PATH;
            }
            $taxIncluded = $this->config->getTaxSummaryConfig($this->storeManager->getStore());
            if ($taxIncluded)
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['children']['tax']['config']['title'] .= __(Config::XML_SUFFIX_AVATAX_TAX_INCLUDED);
        }
        return $jsLayout;
    }
}
