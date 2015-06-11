<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * @copyright   Copyright (c) 2015 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once getShopBasePath() . 'modules/barzahlen/api/version_check.php';

/**
 * Navigation Controller Extension
 * Checks for a new Barzahlen plugin version once a week.
 */
class barzahlen_navigation extends barzahlen_navigation_parent
{
    /**
     * @const Current Plugin Version
     */
    const CURRENTVERSION = "1.2.1";

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

        $sShopsystem = 'OXID 4.2-4.4';
        $sShopsystemVersion = $oxConfig->getVersion();
        $sPluginVersion = self::CURRENTVERSION;

        try {
            $oChecker = new Barzahlen_Version_Check();
            $newAvailable = $oChecker->isNewVersionAvailable($bzConfig['shop_id'], $sShopsystem, $sShopsystemVersion, $sPluginVersion);
        } catch (Exception $e) {
            oxUtils::getInstance()->writeToLog(date('c') . " " . $e . "\r\r", self::LOGFILE);
        }

        if($newAvailable) {
            $aMessage['warning'] .= ((!empty($aMessage['warning'])) ? "<br>" : '') . sprintf(oxRegistry::getLang()->translateString('BZ__NEW_PLUGIN_AVAILABLE'), $oChecker->getNewPluginVersion(), $oChecker->getNewPluginUrl());
        }

        return $aMessage;
    }
}
