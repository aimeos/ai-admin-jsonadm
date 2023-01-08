<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


class TestHelper
{
	private static $aimeos;
	private static $context;


	public static function bootstrap()
	{
		self::getAimeos();
	}


	public static function context( $site = 'unittest' )
	{
		if( !isset( self::$context[$site] ) ) {
			self::$context[$site] = self::createContext( $site );
		}

		return clone self::$context[$site];
	}


	public static function getAimeos()
	{
		if( !isset( self::$aimeos ) )
		{
			require_once 'Bootstrap.php';
			spl_autoload_register( 'Aimeos\\Bootstrap::autoload' );

			self::$aimeos = new \Aimeos\Bootstrap();
		}

		return self::$aimeos;
	}


	public static function getJsonadmPaths()
	{
		return self::getAimeos()->getTemplatePaths( 'admin/jsonadm/templates' );
	}


	private static function createContext( $site )
	{
		$ctx = new \Aimeos\MShop\Context();
		$aimeos = self::getAimeos();


		$paths = $aimeos->getConfigPaths();
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';

		$conf = new \Aimeos\Base\Config\PHPArray( [], $paths );
		$conf = new \Aimeos\Base\Config\Decorator\Memory( $conf );
		$conf = new \Aimeos\Base\Config\Decorator\Documentor( $conf, $file );
		$ctx->setConfig( $conf );


		$dbm = new \Aimeos\Base\DB\Manager\Standard( $conf->get( 'resource', [] ), 'PDO' );
		$ctx->setDatabaseManager( $dbm );


		$logger = new \Aimeos\Base\Logger\File( $site . '.log', \Aimeos\Base\Logger\Iface::DEBUG );
		$ctx->setLogger( $logger );


		$session = new \Aimeos\Base\Session\None();
		$ctx->setSession( $session );


		$i18n = new \Aimeos\Base\Translation\None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );


		$localeManager = \Aimeos\MShop::create( $ctx, 'locale' );
		$locale = $localeManager->bootstrap( $site, 'de', '', false );
		$ctx->setLocale( $locale );


		$view = self::createView( $conf );
		$ctx->setView( $view );


		$ctx->setEditor( 'ai-admin-jsonadm' );

		return $ctx;
	}


	protected static function createView( \Aimeos\Base\Config\Iface $config )
	{
		$tmplpaths = self::getAimeos()->getTemplatePaths( 'admin/jsonadm/templates' );

		$view = new \Aimeos\Base\View\Standard( $tmplpaths );

		$helper = new \Aimeos\Base\View\Helper\Access\All( $view );
		$view->addHelper( 'access', $helper );

		$trans = new \Aimeos\Base\Translation\None( 'de_DE' );
		$helper = new \Aimeos\Base\View\Helper\Translate\Standard( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new \Aimeos\Base\View\Helper\Url\Standard( $view, 'http://baseurl' );
		$view->addHelper( 'url', $helper );

		$helper = new \Aimeos\Base\View\Helper\Number\Standard( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\Base\View\Helper\Date\Standard( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$config = new \Aimeos\Base\Config\Decorator\Protect( $config, array( 'admin/jsonadm' ) );
		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
		$helper = new \Aimeos\Base\View\Helper\Request\Standard( $view, $psr17Factory->createServerRequest( 'GET', 'https://aimeos.org' ) );
		$view->addHelper( 'request', $helper );

		$helper = new \Aimeos\Base\View\Helper\Response\Standard( $view, $psr17Factory->createResponse() );
		$view->addHelper( 'response', $helper );

		return $view;
	}
}
