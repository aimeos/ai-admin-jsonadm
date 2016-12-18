<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


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
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function delete( ServerRequestInterface $request, ResponseInterface $response )
	{
		$view = $this->getView();

		try
		{
			$response = $this->deleteItems( $view, $request, $response );
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

		$body = $view->render( $view->config( $tplconf, $default ) );

		return $response->withHeader( 'Content-Type', 'application/vnd.api+json; supported-ext="bulk"' )
			->withBody( $view->response()->createStreamFromString( $body ) )
			->withStatus( $status );
	}


	/**
	 * Returns the requested resource or the resource list
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function get( ServerRequestInterface $request, ResponseInterface $response )
	{
		$view = $this->getView();

		try
		{
			$response = $this->getItems( $view, $request, $response );
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

		if( $view->param( 'aggregate' ) !== null )
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

		$body = $view->render( $view->config( $tplconf, $default ) );

		return $response->withHeader( 'Content-Type', 'application/vnd.api+json; supported-ext="bulk"' )
			->withBody( $view->response()->createStreamFromString( $body ) )
			->withStatus( $status );
	}


	/**
	 * Updates the resource or the resource list partitially
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function patch( ServerRequestInterface $request, ResponseInterface $response )
	{
		$view = $this->getView();

		try
		{
			$response = $this->patchItems( $view, $request, $response );
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

		$body = $view->render( $view->config( $tplconf, $default ) );

		return $response->withHeader( 'Content-Type', 'application/vnd.api+json; supported-ext="bulk"' )
			->withBody( $view->response()->createStreamFromString( $body ) )
			->withStatus( $status );
	}


	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function post( ServerRequestInterface $request, ResponseInterface $response )
	{
		$view = $this->getView();

		try
		{
			$response = $this->postItems( $view, $request, $response );
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

		$body = $view->render( $view->config( $tplconf, $default ) );

		return $response->withHeader( 'Content-Type', 'application/vnd.api+json; supported-ext="bulk"' )
			->withBody( $view->response()->createStreamFromString( $body ) )
			->withStatus( $status );
	}


	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function put( ServerRequestInterface $request, ResponseInterface $response )
	{
		$status = 501;
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

		$body = $view->render( $view->config( $tplconf, $default ) );

		return $response->withHeader( 'Content-Type', 'application/vnd.api+json; supported-ext="bulk"' )
			->withBody( $view->response()->createStreamFromString( $body ) )
			->withStatus( $status );
	}


	/**
	 * Returns the available REST verbs and the available resources
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @param string|null $prefix Form parameter prefix when nesting parameters is required
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function options( ServerRequestInterface $request, ResponseInterface $response, $prefix = null )
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

			$view->prefix = $prefix;
			$view->resources = $resources;
			$view->attributes = $attributes;

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

		$body = $view->render( $view->config( $tplconf, $default ) );

		return $response->withHeader( 'Allow', 'DELETE,GET,PATCH,POST,OPTIONS' )
			->withHeader( 'Content-Type', 'application/vnd.api+json; supported-ext="bulk"' )
			->withBody( $view->response()->createStreamFromString( $body ) )
			->withStatus( $status );
	}


	/**
	 * Deletes one or more items
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance with "param" view helper
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 * @throws \Aimeos\Admin\JsonAdm\Exception If the request body is invalid
	 */
	protected function deleteItems( \Aimeos\MW\View\Iface $view, ServerRequestInterface $request, ResponseInterface $response )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );

		if( ( $id = $view->param( 'id' ) ) == null )
		{
			$body = (string) $request->getBody();

			if( ( $payload = json_decode( $body ) ) === null || !isset( $payload->data ) || !is_array( $payload->data ) ) {
				throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
			}

			$ids = $this->getIds( $payload );
			$manager->deleteItems( $ids );
			$view->total = count( $ids );
		}
		else
		{
			$manager->deleteItem( $id );
			$view->total = 1;
		}

		return $response;
	}


	/**
	 * Retrieves the item or items and adds the data to the view
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	protected function getItems( \Aimeos\MW\View\Iface $view, ServerRequestInterface $request, ResponseInterface $response )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );

		if( ( $key = $view->param( 'aggregate' ) ) !== null )
		{
			$search = $this->initCriteria( $manager->createSearch(), $view->param() );
			$view->data = $manager->aggregate( $search, $key );
			return $response;
		}

		$total = 1;
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

		return $response;
	}


	/**
	 * Saves new attributes for one or more items
	 *
	 * @param \Aimeos\MW\View\Iface $view View that will contain the "data" and "total" properties afterwards
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 * @throws \Aimeos\Admin\JsonAdm\Exception If "id" parameter isn't available or the body is invalid
	 */
	protected function patchItems( \Aimeos\MW\View\Iface $view, ServerRequestInterface $request, ResponseInterface $response )
	{
		$body = (string) $request->getBody();

		if( ( $payload = json_decode( $body ) ) === null || !isset( $payload->data ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );

		if( is_array( $payload->data ) )
		{
			$data = $this->saveData( $manager, $payload );

			$view->data = $data;
			$view->total = count( $data );
			$response = $response->withHeader( 'Content-Type', 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"' );
		}
		elseif( ( $id = $view->param( 'id' ) ) != null )
		{
			$payload->data->id = $id;
			$data = $this->saveEntry( $manager, $payload->data );

			$view->data = $data;
			$view->total = 1;
		}
		else
		{
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'No ID given' ), 400 );
		}

		return $response;
	}


	/**
	 * Creates one or more new items
	 *
	 * @param \Aimeos\MW\View\Iface $view View that will contain the "data" and "total" properties afterwards
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	protected function postItems( \Aimeos\MW\View\Iface $view, ServerRequestInterface $request, ResponseInterface $response )
	{
		$body = (string) $request->getBody();

		if( ( $payload = json_decode( $body ) ) === null || !isset( $payload->data ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Invalid JSON in body' ), 400 );
		}

		if( isset( $payload->data->id ) || $view->param( 'id' ) != null ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Client generated IDs are not supported' ), 403 );
		}


		$manager = \Aimeos\MShop\Factory::createManager( $this->getContext(), $this->getPath() );

		if( is_array( $payload->data ) )
		{
			$data = $this->saveData( $manager, $payload );

			$view->data = $data;
			$view->total = count( $data );
			$response = $response->withHeader( 'Content-Type', 'application/vnd.api+json; ext="bulk"; supported-ext="bulk"' );
		}
		else
		{
			$payload->data->id = null;
			$data = $this->saveEntry( $manager, $payload->data );

			$view->data = $data;
			$view->total = 1;
		}

		return $response;
	}
}
