<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015
 * @package laravel
 * @subpackage Base
 */

namespace Aimeos\Shop\Base;


/**
 * Service providing the context objects
 *
 * @package laravel
 * @subpackage Base
 */
class Context
{
	/**
	 * @var \Aimeos\MShop\Context\Item\Iface
	 */
	private static $context;

	/**
	 * @var \Illuminate\Contracts\Config\Repository
	 */
	private $config;

	/**
	 * @var \Aimeos\MShop\Locale\Item\Iface
	 */
	private $locale;

	/**
	 * @var \Illuminate\Session\Store
	 */
	private $session;


	/**
	 * Initializes the object
	 *
	 * @param \Illuminate\Contracts\Config\Repository $config Configuration object
	 * @param \Illuminate\Session\Store $session Laravel session object
	 */
	public function __construct( \Illuminate\Contracts\Config\Repository $config, \Illuminate\Session\Store $session )
	{
		$this->config = $config;
		$this->session = $session;
	}


	/**
	 * Returns the current context
	 *
	 * @param boolean $locale True to add locale object to context, false if not
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	public function get( $locale = true )
	{
		if( self::$context === null )
		{
			$context = new \Aimeos\MShop\Context\Item\Standard();

			$config = $this->getConfig();
			$context->setConfig( $config );

			$dbm = new \Aimeos\MW\DB\Manager\PDO( $config );
			$context->setDatabaseManager( $dbm );

			$fs = new \Aimeos\MW\Filesystem\Laravel( app( 'filesystem.disk' ) );
			$context->setFilesystem( $fs );

			$mail = new \Aimeos\MW\Mail\Swift( function() { return app( 'mailer' )->getSwiftMailer(); } );
			$context->setMail( $mail );

			$logger = \Aimeos\MAdmin\Log\Manager\Factory::createManager( $context );
			$context->setLogger( $logger );

			$cache = new \Aimeos\MAdmin\Cache\Proxy\Standard( $context );
			$context->setCache( $cache );

			self::$context = $context;
		}

		$context = self::$context;

		if( $locale === true )
		{
			$localeItem = $this->getLocale( $context );
			$langid = $localeItem->getLanguageId();

			$context->setLocale( $localeItem );
			$context->setI18n( app('\Aimeos\Shop\Base\I18n')->get( array( $langid ) ) );
		}

		$session = new \Aimeos\MW\Session\Laravel4( $this->session );
		$context->setSession( $session );

		$this->addUser( $context );

		return $context;
	}


	/**
	 * Adds the user ID and name if available
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	protected function addUser( \Aimeos\MShop\Context\Item\Iface $context )
	{
		if( ( $userid = \Auth::id() ) !== null )
		{
			$context->setUserId( $userid );
			$context->setGroupIds( function() use ( $context, $userid )
			{
				$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );
				return $manager->getItem( $userid, array( 'customer/group' ) )->getGroups();
			} );
		}

		if( ( $user = \Auth::user() ) !== null ) {
			$context->setEditor( $user->name );
		}
	}


	/**
	 * Creates a new configuration object.
	 *
	 * @return \Aimeos\MW\Config\Iface Configuration object
	 */
	protected function getConfig()
	{
		$conf = $this->config->get( 'shop' );

		$configPaths = app( '\Aimeos\Shop\Base\Aimeos' )->get()->getConfigPaths( 'mysql' );
		$config = new \Aimeos\MW\Config\PHPArray( $conf, $configPaths );

		if( function_exists( 'apc_store' ) === true && $this->config->get( 'shop.apc_enabled', false ) == true ) {
			$config = new \Aimeos\MW\Config\Decorator\APC( $config, $this->config->get( 'shop.apc_prefix', 'laravel:' ) );
		}

		return $config;
	}


	/**
	 * Returns the locale item for the current request
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @return \Aimeos\MShop\Locale\Item\Iface Locale item object
	 */
	protected function getLocale( \Aimeos\MShop\Context\Item\Iface $context )
	{
		if( $this->locale === null )
		{
			$site = \Route::input( 'site', 'default' );
			$lang = \Route::input( 'locale', '' );
			$currency = \Route::input( 'currency', '' );

			$disableSites = $this->config->has( 'shop.disableSites' );

			$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context );
			$this->locale = $localeManager->bootstrap( $site, $lang, $currency, $disableSites );
		}

		return $this->locale;
	}
}
