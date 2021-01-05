<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Common\Factory;


/**
 * JSON API client factory interface
 *
 * @package Admin
 * @subpackage JsonAdm
 */
interface Iface
{
	/**
	 * Creates a new client based on the name
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param \Aimeos\Bootstrap $aimeos Aimeos Bootstrap object
	 * @param string $path Name of the client separated by slashes, e.g "product/property"
	 * @param string|null $name Name of the client implementation ("Standard" if null)
	 * @return \Aimeos\Admin\JsonAdm\Iface Client Interface
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos,
		string $path, string $name = null ) : \Aimeos\Admin\JsonAdm\Iface;
}
