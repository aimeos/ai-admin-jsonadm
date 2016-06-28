<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm;


/**
 * JSON API standard client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Base
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/**
	 * Deletes the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function delete( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$view = $this->getView();

		try
		{
			$view = $this->deleteItems( $view, $body );
			$status = 200;
		}
		catch( \Aimeos\Admin\JsonAdm\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $this->getContext()->getI18n()->dt( 'admin/jsonadm', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $this->getContext()->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** admin/jsonadm/standard/template-delete
		 * Relative path to the JSON API template for DELETE requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the DELETE method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see admin/jsonadm/standard/template-aggregate
		 * @see admin/jsonadm/standard/template-get
		 * @see admin/jsonadm/standard/template-patch
		 * @see admin/jsonadm/standard/template-post
		 * @see admin/jsonadm/standard/template-put
		 * @see admin/jsonadm/standard/template-options
		 */
		$tplconf = 'admin/jsonadm/standard/template-delete';
		$default = 'delete-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the requested resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function get( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );

		$view = $this->getView();
		$aggregate = $view->param( 'aggregate' );

		try
		{
			if( $aggregate !== null ) {
				$view = $this->getAggregate( $view );
			} else {
				$view = $this->getItems( $view );
			}

			$status = 200;
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $this->getContext()->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		if( $aggregate !== null )
		{
			/** admin/jsonadm/standard/template-aggregate
			 * Relative path to the JSON API template for GET aggregate requests
			 *
			 * The template file contains the code and processing instructions
			 * to generate the result shown in the JSON API body. The
			 * configuration string is the path to the template file relative
			 * to the templates directory (usually in admin/jsonadm/templates).
			 *
			 * You can overwrite the template file configuration in extensions and
			 * provide alternative templates. These alternative templates should be
			 * named like the default one but with the string "standard" replaced by
			 * an unique name. You may use the name of your project for this. If
			 * you've implemented an alternative client class as well, "standard"
			 * should be replaced by the name of the new class.
			 *
			 * @param string Relative path to the template creating the body for the GET aggregate request of the JSON API
			 * @since 2016.07
			 * @category Developer
			 * @see admin/jsonadm/standard/template-delete
			 * @see admin/jsonadm/standard/template-get
			 * @see admin/jsonadm/standard/template-patch
			 * @see admin/jsonadm/standard/template-post
			 * @see admin/jsonadm/standard/template-put
			 * @see admin/jsonadm/standard/template-options
			 */
			$tplconf = 'admin/jsonadm/standard/template-aggregate';
			$default = 'aggregate-default.php';
		}
		else
		{
			/** admin/jsonadm/standard/template-get
			 * Relative path to the JSON API template for GET requests
			 *
			 * The template file contains the code and processing instructions
			 * to generate the result shown in the JSON API body. The
			 * configuration string is the path to the template file relative
			 * to the templates directory (usually in admin/jsonadm/templates).
			 *
			 * You can overwrite the template file configuration in extensions and
			 * provide alternative templates. These alternative templates should be
			 * named like the default one but with the string "standard" replaced by
			 * an unique name. You may use the name of your project for this. If
			 * you've implemented an alternative client class as well, "standard"
			 * should be replaced by the name of the new class.
			 *
			 * @param string Relative path to the template creating the body for the GET method of the JSON API
			 * @since 2015.12
			 * @category Developer
			 * @see admin/jsonadm/standard/template-aggregate
			 * @see admin/jsonadm/standard/template-delete
			 * @see admin/jsonadm/standard/template-patch
			 * @see admin/jsonadm/standard/template-post
			 * @see admin/jsonadm/standard/template-put
			 * @see admin/jsonadm/standard/template-options
			 */
			$tplconf = 'admin/jsonadm/standard/template-get';
			$default = 'get-default.php';
		}

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Updates the resource or the resource list partitially
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function patch( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$view = $this->getView();

		try
		{
			$view = $this->patchItems( $view, $body, $header );
			$status = 200;
		}
		catch( \Aimeos\Admin\JsonAdm\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $this->getContext()->getI18n()->dt( 'admin/jsonadm', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $this->getContext()->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** admin/jsonadm/standard/template-patch
		 * Relative path to the JSON API template for PATCH requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the PATCH method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see admin/jsonadm/standard/template-aggregate
		 * @see admin/jsonadm/standard/template-get
		 * @see admin/jsonadm/standard/template-post
		 * @see admin/jsonadm/standard/template-delete
		 * @see admin/jsonadm/standard/template-put
		 * @see admin/jsonadm/standard/template-options
		 */
		$tplconf = 'admin/jsonadm/standard/template-patch';
		$default = 'patch-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function post( $body, array &$header, &$status )
	{
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$view = $this->getView();

		try
		{
			$view = $this->postItems( $view, $body, $header );
			$status = 201;
		}
		catch( \Aimeos\Admin\JsonAdm\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = array( array(
				'title' => $this->getContext()->getI18n()->dt( 'admin/jsonadm', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $this->getContext()->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** admin/jsonadm/standard/template-post
		 * Relative path to the JSON API template for POST requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the POST method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see admin/jsonadm/standard/template-aggregate
		 * @see admin/jsonadm/standard/template-get
		 * @see admin/jsonadm/standard/template-patch
		 * @see admin/jsonadm/standard/template-delete
		 * @see admin/jsonadm/standard/template-put
		 * @see admin/jsonadm/standard/template-options
		 */
		$tplconf = 'admin/jsonadm/standard/template-post';
		$default = 'post-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function put( $body, array &$header, &$status )
	{
		$status = 501;
		$header = array( 'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"' );
		$view = $this->getView();

		$view->errors = array( array(
			'title' => $this->getContext()->getI18n()->dt( 'admin/jsonadm', 'Not implemented, use PATCH instead' ),
		) );

		/** admin/jsonadm/standard/template-put
		 * Relative path to the JSON API template for PUT requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the PUT method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see admin/jsonadm/standard/template-aggregate
		 * @see admin/jsonadm/standard/template-delete
		 * @see admin/jsonadm/standard/template-patch
		 * @see admin/jsonadm/standard/template-post
		 * @see admin/jsonadm/standard/template-get
		 * @see admin/jsonadm/standard/template-options
		 */
		$tplconf = 'admin/jsonadm/standard/template-put';
		$default = 'put-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Returns the available REST verbs and the available resources
	 *
	 * @param string $body Request body
	 * @param array &$header Variable which contains the HTTP headers and the new ones afterwards
	 * @param integer &$status Variable which contains the HTTP status afterwards
	 * @return string Content for response body
	 */
	public function options( $body, array &$header, &$status )
	{
		$context = $this->getContext();
		$view = $this->getView();

		try
		{
			$resources = $attributes = array();

			foreach( $this->getDomains( $view ) as $domain )
			{
				$manager = \Aimeos\MShop\Factory::createManager( $context, $domain );
				$resources = array_merge( $resources, $manager->getResourceType( true ) );
				$attributes = array_merge( $attributes, $manager->getSearchAttributes( true ) );
			}

			$view->resources = $resources;
			$view->attributes = $attributes;

			$header = array(
				'Content-Type' => 'application/vnd.api+json; supported-ext="bulk"',
				'Allow' => 'DELETE,GET,PATCH,POST,OPTIONS'
			);
			$status = 200;
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = array( array(
				'title' => $context->getI18n()->dt( 'mshop', $e->getMessage() ),
				'detail' => $e->getTraceAsString(),
			) );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = array( array(
				'title' => $e->getMessage(),
				'detail' => $e->getTraceAsString(),
			) );
		}

		/** admin/jsonadm/standard/template-options
		 * Relative path to the JSON API template for OPTIONS requests
		 *
		 * The template file contains the code and processing instructions
		 * to generate the result shown in the JSON API body. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jsonadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the body for the OPTIONS method of the JSON API
		 * @since 2015.12
		 * @category Developer
		 * @see admin/jsonadm/standard/template-aggregate
		 * @see admin/jsonadm/standard/template-delete
		 * @see admin/jsonadm/standard/template-patch
		 * @see admin/jsonadm/standard/template-post
		 * @see admin/jsonadm/standard/template-get
		 * @see admin/jsonadm/standard/template-put
		 */
		$tplconf = 'admin/jsonadm/standard/template-options';
		$default = 'options-default.php';

		return $view->render( $view->config( $tplconf, $default ) );
	}


	/**
	 * Deletes one or more items
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with "param" view helper
	 * @param string $body Request body
	 * @return \Aimeos\MW\View\Iface $view View object that will contain the "total" property afterwards
	 * @throws \Aimeos\Admin\JsonAdm\Exception If the request body is invalid
	 */
	protected function deleteItems( \Aimeos\MW\View\Iface $view, $body )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );

		if( ( $id = $view->param( 'id' ) ) == null )
		{
			if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) || !is_array( $request->data ) ) {
				throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
			}

			$ids = $this->getIds( $request );
			$manager->deleteItems( $ids );
			$view->total = count( $ids );
		}
		else
		{
			$manager->deleteItem( $id );
			$view->total = 1;
		}

		return $view;
	}


	/**
	 * Retrieves the aggregation and adds the data to the view
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance
	 * @return \Aimeos\MW\View\Iface View instance with additional data assigned
	 */
	protected function getAggregate( \Aimeos\MW\View\Iface $view )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );

		$key = $view->param( 'aggregate' );

		$search = $this->initCriteria( $manager->createSearch(), $view->param() );
		$view->data = $manager->aggregate( $search, $key );

		return $view;
	}


	/**
	 * Retrieves the item or items and adds the data to the view
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance
	 * @return \Aimeos\MW\View\Iface View instance with additional data assigned
	 */
	protected function getItems( \Aimeos\MW\View\Iface $view )
	{
		$total = 1;
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );
		$include = ( ( $include = $view->param( 'include' ) ) !== null ? explode( ',', $include ) : array() );

		if( ( $id = $view->param( 'id' ) ) == null )
		{
			$search = $this->initCriteria( $manager->createSearch(), $view->param() );
			$view->data = $manager->searchItems( $search, array(), $total );
			$view->childItems = $this->getChildItems( $view->data, $include );
			$view->listItems = $this->getListItems( $view->data, $include );
		}
		else
		{
			$view->data = $manager->getItem( $id, array() );
			$view->childItems = $this->getChildItems( array( $id => $view->data ), $include );
			$view->listItems = $this->getListItems( array( $id => $view->data ), $include );
		}

		$view->refItems = $this->getRefItems( $view->listItems );
		$view->total = $total;

		return $view;
	}


	/**
	 * Retrieves the item or items and adds the data to the view
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance
	 * @return \Aimeos\MW\View\Iface View instance with additional data assigned
	 * @deprecated 2016.06 Use getItems() instead
	 */
	protected function getItem( \Aimeos\MW\View\Iface $view )
	{
		return $this->getItems( $view );
	}

	/**
	 * Saves new attributes for one or more items
	 *
	 * @param \Aimeos\MW\View\Iface $view View that will contain the "data" and "total" properties afterwards
	 * @param string $body Request body
	 * @param array &$header Associative list of HTTP headers as value/result parameter
	 * @throws \Aimeos\Admin\JsonAdm\Exception If "id" parameter isn't available or the body is invalid
	 * @return \Aimeos\MW\View\Iface Updated view instance
	 */
	protected function patchItems( \Aimeos\MW\View\Iface $view, $body, array &$header )
	{
		if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );

		if( is_array( $request->data ) )
		{
			$data = $this->saveData( $manager, $request );

			$view->data = $data;
			$view->total = count( $data );
			$header['Content-Type'] = 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"';
		}
		elseif( ( $id = $view->param( 'id' ) ) != null )
		{
			$request->data->id = $id;
			$data = $this->saveEntry( $manager, $request->data );

			$view->data = $data;
			$view->total = 1;
		}
		else
		{
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'No ID given' ), 400 );
		}

		return $view;
	}


	/**
	 * Creates one or more new items
	 *
	 * @param \Aimeos\MW\View\Iface $view View that will contain the "data" and "total" properties afterwards
	 * @param string $body Request body
	 * @param array &$header Associative list of HTTP headers as value/result parameter
	 * @return \Aimeos\MW\View\Iface Updated view instance
	 */
	protected function postItems( \Aimeos\MW\View\Iface $view, $body, array &$header )
	{
		if( ( $request = json_decode( $body ) ) === null || !isset( $request->data ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
		}

		if( isset( $request->data->id ) || $view->param( 'id' ) != null ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Client generated IDs are not supported' ), 403 );
		}


		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );

		if( is_array( $request->data ) )
		{
			$data = $this->saveData( $manager, $request );

			$view->data = $data;
			$view->total = count( $data );
			$header['Content-Type'] = 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"';
		}
		else
		{
			$request->data->id = null;
			$data = $this->saveEntry( $manager, $request->data );

			$view->data = $data;
			$view->total = 1;
		}

		return $view;
	}
}
