<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Common;


/**
 * JSON API client interface
 *
 * @package Admin
 * @subpackage JsonAdm
 */
interface Iface
	extends \Aimeos\Admin\JsonAdm\Iface
{
	/**
	 * Initializes the client
	 *
	 * @param \Aimeos\MShop\ContextIface $context MShop context object
	 * @param string $path Name of the client separated by slashes, e.g "order/product"
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context, string $path );
}
