<?php
/**
 *
 * Adyen Payment module (https://www.adyen.com/)
 *
 * Copyright (c) 2015 Adyen BV (https://www.adyen.com/)
 * See LICENSE.txt for license details.
 *
 * Author: Adyen <magento@adyen.com>
 */

namespace Adyen\Payment\Model\Ui;

use Adyen\Payment\Helper\Config;
use Adyen\Payment\Helper\Data;
use Adyen\Payment\Model\Config\Source\RenderMode;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;

class AdyenGenericConfigProvider implements ConfigProviderInterface
{
    const CODE = 'adyen_abstract';

    /**
     * @var Data
     */
    protected $adyenHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Config
     */
    private $adyenConfigHelper;

    /**
     * This data member will be passed to the js frontend. It will be used to map the method code (adyen_ideal) to the
     * corresponding txVariant (ideal). The txVariant will then be used to instantiate the component
     *
     * @var array
     */
    private $txVariants;

    public function __construct(
        Data $adyenHelper,
        Config $adyenConfigHelper,
        StoreManagerInterface $storeManager,
        array $txVariants = []
    ) {
        $this->adyenHelper = $adyenHelper;
        $this->adyenConfigHelper = $adyenConfigHelper;
        $this->storeManager = $storeManager;
        $this->txVariants = $txVariants;
    }

    /**
     * Define foreach payment methods the RedirectUrl
     *
     * @return array
     */
    public function getConfig()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $config = [
            'payment' => []
        ];
        // show logos turned on by default
        if ($this->showLogos()) {
            $config['payment']['adyen']['showLogo'] = true;
        } else {
            $config['payment']['adyen']['showLogo'] = false;
        }

        $config['payment']['adyen']['clientKey'] = $this->adyenHelper->getClientKey();
        $config['payment']['adyen']['checkoutEnvironment'] = $this->adyenHelper->getCheckoutEnvironment($storeId);
        $config['payment']['adyen']['locale'] = $this->adyenHelper->getStoreLocale($storeId);
        $config['payment']['adyen']['chargedCurrency'] = $this->adyenConfigHelper->getChargedCurrency($storeId);
        $config['payment']['adyen']['hasHolderName'] = $this->adyenConfigHelper->getHasHolderName($storeId);
        $config['payment']['adyen']['holderNameRequired'] = $this->adyenConfigHelper->getHolderNameRequired($storeId);
        $config['payment']['adyen']['houseNumberStreetLine'] = $this->adyenConfigHelper
            ->getHouseNumberStreetLine($storeId);
        $config['payment']['customerStreetLinesEnabled'] = $this->adyenHelper->getCustomerStreetLinesEnabled($storeId);
        /* TODO: Do some filtering to only pass the payment methods that are enabled */
        $config['payment']['adyen']['txVariants'] = $this->txVariants;

        return $config;
    }

    /**
     * @return bool
     */
    protected function showLogos()
    {
        $showLogos = $this->adyenHelper->getAdyenAbstractConfigData('title_renderer');
        if ($showLogos == RenderMode::MODE_TITLE_IMAGE) {
            return true;
        }
        return false;
    }
}
