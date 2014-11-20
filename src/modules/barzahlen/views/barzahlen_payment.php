<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

class barzahlen_payment extends barzahlen_payment_parent
{
    protected $_sThisTemplate = 'payment.tpl';
    private $_supportedCurrencies = array('EUR');

    /**
     * Executes parent method parent::render().
     *
     * @extend render
     */
    public function render()
    {
        return parent::render();
    }

    /**
     * Returns the sandbox setting.
     *
     * @return boolean
     */
    public function getSandbox()
    {
        $oxConfig = oxConfig::getInstance();
        $bzConfig = $oxConfig->getShopConfVar('barzahlen_config');
        return $bzConfig['sandbox'];
    }

    /**
     * Checks if current shop currency is support by Barzahlen.
     *
     * @return boolean
     */
    public function checkCurrency()
    {
        $oxConfig = $this->getConfig();
        $oCurrency = $oxConfig->getActShopCurrencyObject();
        return in_array($oCurrency->name, $this->_supportedCurrencies);
    }
}
