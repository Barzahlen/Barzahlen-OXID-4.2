<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/barzahlen/api/loader.php';

class barzahlen_payment_gateway extends barzahlen_payment_gateway_parent
{
    const LOGFILE = "barzahlen.log";

    /**
     * Executes payment, returns true on success.
     *
     * @param double $dAmount Goods amount
     * @param object &$oOrder User ordering object
     *
     * @return bool
     */
    public function executePayment($dAmount, &$oOrder)
    {
        if ($oOrder->oxorder__oxpaymenttype->value != 'oxidbarzahlen') {
            return parent::executePayment($dAmount, $oOrder);
        }

        $this->_sLastError = "barzahlen";
        $country = oxNew("oxcountry");
        $country->load($oOrder->oxorder__oxbillcountryid->rawValue);

        $api = $this->_getBarzahlenApi($oOrder);

        $customerEmail = $oOrder->oxorder__oxbillemail->rawValue;
        $customerStreetNr = $oOrder->oxorder__oxbillstreet->rawValue . ' ' . $oOrder->oxorder__oxbillstreetnr->rawValue;
        $customerZipcode = $oOrder->oxorder__oxbillzip->rawValue;
        $customerCity = $oOrder->oxorder__oxbillcity->rawValue;
        $customerCountry = $country->oxcountry__oxisoalpha2->rawValue;
        $orderId = $oOrder->oxorder__oxordernr->value;
        $amount = $oOrder->oxorder__oxtotalordersum->value;
        $currency = $oOrder->oxorder__oxcurrency->rawValue;
        $payment = new Barzahlen_Request_Payment($customerEmail, $customerStreetNr, $customerZipcode, $customerCity, $customerCountry, $amount, $currency, $orderId);

        try {
            $api->handleRequest($payment);
        } catch (Exception $e) {
            oxUtils::getInstance()->writeToLog(date('c') . " Transaction/Create failed: " . $e . "\r\r", self::LOGFILE);
        }

        if ($payment->isValid()) {
            oxSession::setVar('barzahlenInfotextOne', (string) $payment->getInfotext1());
            $oOrder->oxorder__bztransaction = new oxField((int) $payment->getTransactionId());
            $oOrder->oxorder__bzstate = new oxField('pending');
            $oOrder->save();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prepares a Barzahlen API object for the payment request.
     *
     * @param object $oOrder User ordering object
     * @return Barzahlen_Api
     */
    protected function _getBarzahlenApi($oOrder)
    {
        $oxConfig = oxConfig::getInstance();
        $bzConfig = $oxConfig->getShopConfVar('barzahlen_config');

        $api = new Barzahlen_Api($bzConfig['shop_id'], $bzConfig['payment_key'], $bzConfig['sandbox']);
        $api->setDebug($bzConfig['debug'], self::LOGFILE);
        $api->setLanguage($this->_getOrderLanguage($oOrder));
        $api->setUserAgent('OXID v' . $oxConfig->getVersion() .  ' / Plugin v1.2.1');
        return $api;
    }

    /**
     * Gets the order language code.
     *
     * @param object $oOrder User ordering object
     * @return string
     */
    protected function _getOrderLanguage($oOrder)
    {
        $oxConfig = oxConfig::getInstance();
        $lgConfig = $oxConfig->getShopConfVar('aLanguageParams');

        return array_search($oOrder->getOrderLanguage(), $lgConfig);
    }
}
