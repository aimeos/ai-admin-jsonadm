<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Rule;


/**
 * JSON API rule client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Standard
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/** admin/jsonadm/rule/decorators/excludes
	 * Excludes decorators added by the "common" option from the JSON API clients
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "admin/jsonadm/common/decorators/default" before they are wrapped
	 * around the Jsonadm client.
	 *
	 *  admin/jsonadm/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Admin\JsonAdm\Common\Decorator\*") added via
	 * "admin/jsonadm/common/decorators/default" for the JSON API client.
	 *
	 * @param array List of decorator names
	 * @since 2021.04
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/rule/decorators/global
	 * @see admin/jsonadm/rule/decorators/local
	 */

	/** admin/jsonadm/rule/decorators/global
	 * Adds a list of globally available decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Admin\Jsonadm\Common\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/rule/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Admin\Jsonadm\Common\Decorator\Decorator1" only to the
	 * "rule" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2021.04
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/rule/decorators/excludes
	 * @see admin/jsonadm/rule/decorators/local
	 */

	/** admin/jsonadm/rule/decorators/local
	 * Adds a list of local decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Admin\Jsonadm\Rule\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/rule/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Admin\Jsonadm\Rule\Decorator\Decorator2" only to the
	 * "rule" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2021.04
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/rule/decorators/excludes
	 * @see admin/jsonadm/rule/decorators/global
	 */
}
