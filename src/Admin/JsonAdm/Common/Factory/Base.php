<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Common\Factory;


/**
 * Common methods for all JSON API factories
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Base
{
	private static $objects = [];


	/**
	 * Injects a client object
	 *
	 * The object is returned via create() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\Admin\JsonAdm\Iface|null $client JSON API client object
	 */
	public static function injectClient( string $classname, \Aimeos\Admin\JsonAdm\Iface $client = null )
	{
		self::$objects[$classname] = $client;
	}


	/**
	 * Adds the decorators to the JSON API client object
	 *
	 * @param \Aimeos\Admin\JsonAdm\Common\Iface $client Client object
	 * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
	 * @param string $path Name of the client separated by slashes, e.g "product/property"
	 * @return \Aimeos\Admin\JsonAdm\Iface Client object
	 */
	protected static function addClientDecorators( \Aimeos\Admin\JsonAdm\Iface $client,
		\Aimeos\MShop\ContextIface $context, string $path ) : \Aimeos\Admin\JsonAdm\Iface
	{
		$config = $context->config();

		/** admin/jsonadm/common/decorators/default
		 * Configures the list of decorators applied to all JSON API clients
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created clients:
		 *
		 *  admin/jsonadm/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all client instances in that order. The decorator classes would be
		 * "\Aimeos\Admin\JsonAdm\Common\Decorator\Decorator1" and
		 * "\Aimeos\Admin\JsonAdm\Common\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2015.12
		 * @category Developer
		 */
		$decorators = $config->get( 'admin/jsonadm/common/decorators/default', [] );

		if( empty( $path ) )
		{
			$classprefix = '\\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
			return self::addDecorators( $client, $decorators, $classprefix, $context, $path );
		}

		$dpath = trim( $path, '/' );
		$dpath = ( $dpath !== '' ? $dpath . '/' : $dpath );

		$excludes = $config->get( 'admin/jsonadm/' . $dpath . 'decorators/excludes', [] );
		$localClass = str_replace( '/', '\\', ucwords( $path, '/' ) );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$client = self::addDecorators( $client, $decorators, $classprefix, $context, $path );

		$classprefix = '\\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$decorators = $config->get( 'admin/jsonadm/' . $dpath . 'decorators/global', [] );
		$client = self::addDecorators( $client, $decorators, $classprefix, $context, $path );

		$classprefix = '\\Aimeos\\Admin\\JsonAdm\\' . ucfirst( $localClass ) . '\\Decorator\\';
		$decorators = $config->get( 'admin/jsonadm/' . $dpath . 'decorators/local', [] );
		return self::addDecorators( $client, $decorators, $classprefix, $context, $path );
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param \Aimeos\Admin\JsonAdm\Iface $client Client object
	 * @param array $decorators List of decorator names
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Admin\JsonAdm\Product\Decorator\"
	 * @param \Aimeos\MShop\ContextIface $context Context instance with necessary objects
	 * @param string $path Name of the client separated by slashes, e.g "product/stock"
	 * @return \Aimeos\Admin\JsonAdm\Iface Client object
	 * @throws \LogicException If class can't be instantiated
	 */
	protected static function addDecorators( \Aimeos\Admin\JsonAdm\Iface $client, array $decorators, string $classprefix,
			\Aimeos\MShop\ContextIface $context, string $path ) : \Aimeos\Admin\JsonAdm\Iface
	{
		$interface = \Aimeos\Admin\JsonAdm\Common\Decorator\Iface::class;

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \LogicException( sprintf( 'Invalid class name "%1$s"', $name ), 400 );
			}

			$client = \Aimeos\Utils::create( $classprefix . $name, [$client, $context, $path], $interface );
		}

		return $client;
	}


	/**
	 * Creates a new client object
	 *
	 * @param string $classname Name of the client class
	 * @param string $interface Name of the client interface
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 * @param string $path Name of the client separated by slashes, e.g "product/property"
	 * @return \Aimeos\Admin\JsonAdm\Common\Iface Client object
	 */
	protected static function createAdmin( string $classname, string $interface,
		\Aimeos\MShop\ContextIface $context, string $path ) : \Aimeos\Admin\JsonAdm\Common\Iface
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		return \Aimeos\Utils::create( $classname, [$context, $path], $interface );
	}
}
