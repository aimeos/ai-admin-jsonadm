<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Common\Decorator;


/**
 * Decorator interface for JSON API client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
interface Iface
	extends \Aimeos\Admin\JsonAdm\Iface
{
	/**
	 * Initializes a new client decorator object
	 *
	 * @param \Aimeos\Admin\JsonAdm\Iface $client Client object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param string $path Name of the client separated by slashes, e.g "product/stock"
	 */
	public function __construct( \Aimeos\Admin\JsonAdm\Iface $client, \Aimeos\MShop\Context\Item\Iface $context, $path );
}