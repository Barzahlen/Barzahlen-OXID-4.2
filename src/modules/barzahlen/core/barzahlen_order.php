<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/
 *
 * @copyright   Copyright (c) 2012 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/barzahlen/api/loader.php';

/**
 * Extends Order manager
 */
class barzahlen_order extends barzahlen_order_parent
{
    /**
     * Transaction status codes.
     */
    const STATE_PENDING = "pending";
    const STATE_CANCELED = "canceled";

    /**
     * Log file
     */
    const LOGFILE = "barzahlen.log";

    /**
     * Extends the order cancelation to cancel pending Barzahlen payment slips
     * at the same time.
     */
    public function cancelOrder()
    {
        parent::cancelOrder();

        if ($this->oxorder__oxpaymenttype->value == 'oxidbarzahlen' && $this->oxorder__bzstate->value == self::STATE_PENDING) {

            $sTransactionId = $this->oxorder__bztransaction->value;

            $oRequest = new Barzahlen_Request_Cancel($sTransactionId);
            $cancel = $this->_connectBarzahlenApi($oRequest);

            if ($cancel->isValid()) {
                $this->oxorder__bzstate = new oxField(self::STATE_CANCELED);
                $this->save();
            }
        }
    }

    /**
     * Extends the order deletion to cancel pending Barzahlen payment slips
     * at the same time.
     *
     * @param string $sOxId Ordering ID (default null)
     * @return bool
     */
    public function delete($sOxId = null)
    {
        if ($sOxId) {
            if (!$this->load($sOxId)) {
                return false;
            }
        } elseif (!$sOxId) {
            $sOxId = $this->getId();
            $this->load($sOxId);
        }

        if ($this->oxorder__oxpaymenttype->value == 'oxidbarzahlen' && $this->oxorder__bzstate->value == self::STATE_PENDING) {

            $sTransactionId = $this->oxorder__bztransaction->value;

            $oRequest = new Barzahlen_Request_Cancel($sTransactionId);
            $cancel = $this->_connectBarzahlenApi($oRequest);

            if ($cancel->isValid()) {
                $this->oxorder__bzstate = new oxField(self::STATE_CANCELED);
                $this->save();
            }
        }

        return parent::delete($sOxId);
    }

    /**
     * Performs the api request.
     *
     * @param Barzahlen_Request $oRequest request object
     */
    protected function _connectBarzahlenApi($oRequest)
    {
        $oApi = $this->_getBarzahlenApi();

        try {
            $oApi->handleRequest($oRequest);
        } catch (Exception $e) {
            oxUtils::getInstance()->writeToLog(date('c') . " API connection failed: " . $e . "\r\r", self::LOGFILE);
        }

        return $oRequest;
    }

    /**
     * Prepares a Barzahlen API object for the payment request.
     *
     * @return Barzahlen_Api
     */
    protected function _getBarzahlenApi()
    {
        $oxConfig = oxConfig::getInstance();
        $bzConfig = $oxConfig->getShopConfVar('barzahlen_config');

        $api = new Barzahlen_Api($bzConfig['shop_id'], $bzConfig['payment_key'], $bzConfig['sandbox']);
        $api->setDebug($bzConfig['debug'], self::LOGFILE);
        return $api;
    }
}
