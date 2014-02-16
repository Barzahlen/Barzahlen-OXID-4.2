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

class barzahlen_payment extends barzahlen_payment_parent {

  protected $_sThisTemplate = 'barzahlen_payment.tpl';

  /**
   * Executes parent method parent::render().
   *
   * @extend render
   */
  public function render() {

    return parent::render();
  }

  /**
   * Returns the sandbox setting.
   *
   * @return boolean
   */
  public function getSandbox() {

    $oxConfig = oxConfig::getInstance();
    $bzConfig = $oxConfig->getShopConfVar('barzahlen_config');
    return $bzConfig['sandbox'];
  }
}