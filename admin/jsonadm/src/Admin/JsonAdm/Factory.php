<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm;


/**
 * Factory which can create all JSON API clients
 *
 * @package Admin
 * @subpackage JsonAdm
 * @deprecated Use JsonAdm class instead
 */
class Factory
	extends \Aimeos\Admin\JsonAdm
	implements \Aimeos\Admin\JsonAdm\Common\Factory\Iface
{
	/**
	 * Creates the required client specified by the given path of client names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by clients
	 * @param \Aimeos\Bootstrap $aimeos Aimeos Bootstrap object
	 * @param string $path Name of the client separated by slashes, e.g "product/property"
	 * @param string|null $name Name of the client implementation ("Standard" if null)
	 * @return \Aimeos\Admin\JsonAdm\Iface JSON admin instance
	 * @throws \Aimeos\Admin\JsonAdm\Exception If the given path is invalid
	 */
	static public function create( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\Bootstrap $aimeos, $path, $name = null )
	{
		return parent::create( $context, $aimeos, $path, $name );
	}


	/**
	 * Enables or disables caching of class instances.
	 *
	 * @param boolean $value True to enable caching, false to disable it.
	 * @return boolean Previous cache setting
	 */
	static public function setCache( $value )
	{
		return self::cache( $value );
	}
}
