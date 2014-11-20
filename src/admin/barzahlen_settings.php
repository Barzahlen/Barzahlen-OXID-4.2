<?php
/**
 * Barzahlen Payment Module (OXID eShop)
 *
 * @copyright   Copyright (c) 2014 Cash Payment Solutions GmbH (https://www.barzahlen.de)
 * @author      Alexander Diebler
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

require_once 'shop_config.php';

class barzahlen_settings extends Shop_Config
{
    protected $_sThisTemplate = 'barzahlen_settings.tpl';

    /**
     * Executes parent method parent::render() and gets the current settings.
     *
     * @extend render
     * @return string with template file
     */
    public function render()
    {
        $this->_aViewData['barzahlen_config'] = $this->getConfig()->getShopConfVar('barzahlen_config');
        return $this->_sThisTemplate;
    }

    /**
     * Saves the entered information.
     */
    public function save()
    {
        $oxConfig = $this->getConfig();
        $bzConfig = $oxConfig->getParameter('barzahlen_config');
        $oxConfig->saveShopConfVar('arr', 'barzahlen_config', $bzConfig);
        $this->_aViewData["info"] = array("class" => "messagebox", "message" => "BZ__SETTINGS_SUCCESS");
    }
}
