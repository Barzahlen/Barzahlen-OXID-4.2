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
 * @copyright   Copyright (c) 2013 Zerebro Internet GmbH (http://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/barzahlen/api/loader.php';

/**
 * Navigation Controller Extension
 * Checks for a new Barzahlen plugin version once a week.
 */
class barzahlen_navigation extends barzahlen_navigation_parent
{
    /**
     * @const Current Plugin Version
     */
    const CURRENTVERSION = "1.1.4";

    /**
     * @const Log file
     */
    const LOGFILE = "barzahlen.log";

    /**
     * Extends the startup checks with Barzahlen plugin version check.
     *
     * @return array
     */
    protected function _doStartUpChecks()
    {
        $aMessage = parent::_doStartUpChecks();

        $oxConfig = oxConfig::getInstance();
        $bzConfig = $oxConfig->getShopConfVar('barzahlen_config');

        // only check once a week
        if ($bzConfig['plugin_check'] != null && $bzConfig['plugin_check'] > strtotime("-1 week")) {
            return $aMessage;
        }

        $bzConfig['plugin_check'] = time();
        $oxConfig->saveShopConfVar('arr', 'barzahlen_config', $bzConfig);

        $oChecker = new Barzahlen_Version_Check($bzConfig['shop_id'], $bzConfig['payment_key']);

        $sShopsystem = 'OXID 4.2-4.4';
        $sShopsystemVersion = $oxConfig->getVersion();
        $sPluginVersion = self::CURRENTVERSION;

        try {
            $currentVersion = $oChecker->checkVersion($sShopsystem, $sShopsystemVersion, $sPluginVersion);
        } catch (Exception $e) {
            oxUtils::getInstance()->writeToLog(date('c') . " " . $e . "\r\r", self::LOGFILE);
        }

        if ($currentVersion != false) {
            $aMessage['warning'] .= ((!empty($aMessage['warning'])) ? "<br>" : '') . oxLang::getInstance()->translateString('BZ__PLUGIN_AVAILABLE') . $currentVersion . '! ' . oxLang::getInstance()->translateString('BZ__GET_NEW_PLUGIN');
        }

        return $aMessage;
    }
}
