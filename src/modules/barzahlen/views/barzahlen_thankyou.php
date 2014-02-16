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

class barzahlen_thankyou extends barzahlen_thankyou_parent
{
    protected $_infotextOne;
    protected $_sThisTemplate = 'thankyou.tpl';

    /**
     * Grabs the payment information from the session.
     */
    public function init()
    {
        parent::init();
        $this->_infotextOne = oxSession::getVar('barzahlenInfotextOne');
    }

    /**
     * Executes parent method parent::render() and unsets session variables.
     *
     * @extend render
     */
    public function render()
    {
        oxSession::deleteVar('barzahlenInfotextOne');
        return parent::render();
    }

    /**
     * Returns the infotext 1.
     *
     * @return string with infotext 1
     */
    public function getInfotextOne()
    {
        return $this->_infotextOne;
    }
}
