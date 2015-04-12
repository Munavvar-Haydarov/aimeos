<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Shop\Command;

use Symfony\Component\Console\Input\InputArgument;


class CacheCommand extends AbstractCommand
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'aimeos:cache';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clears the content cache';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$context = $this->getLaravel()->make( 'Aimeos\Shop\Base\Context' )->get( array(), false );
		$context->setEditor( 'aimeos:cache' );

		$localeManager = \MShop_Locale_Manager_Factory::createManager( $context );

		foreach( $this->getSiteItems( $context, $this->argument('site') ) as $siteItem )
		{
			$localeItem = $localeManager->bootstrap( $siteItem->getCode(), '', '', false );

			$lcontext = clone $context;
			$lcontext->setLocale( $localeItem );

			$cache = new \MAdmin_Cache_Proxy_Default( $lcontext );
			$lcontext->setCache( $cache );

			$this->info( sprintf( 'Clearing the Aimeos cache for site "%1$s"', $siteItem->getCode() ) );

			\MAdmin_Cache_Manager_Factory::createManager( $lcontext )->getCache()->flush();
		}
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array( 'site', InputArgument::OPTIONAL, 'Site codes to clear the cache like "default unittest" (none for all)' ),
		);
	}


	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}
