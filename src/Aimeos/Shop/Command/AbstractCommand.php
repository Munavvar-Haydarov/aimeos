<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Shop\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


abstract class AbstractCommand extends Command
{
	/**
	 * Returns the enabled site items which may be limited by the input arguments.
	 *
	 * @param \MShop_Context_Item_Interface $context Context item object
	 * @param string|array $sites Unique site codes
	 * @return \MShop_Locale_Item_Site_Interface[] List of site items
	 */
	protected function getSiteItems( \MShop_Context_Item_Interface $context, $sites )
	{
		$manager = \MShop_Factory::createManager( $context, 'locale/site' );
		$search = $manager->createSearch();

		if( is_scalar( $sites ) && $sites != '' ) {
			$sites = explode( ' ', $sites );
		}

		if( !empty( $sites ) ) {
			$search->setConditions( $search->compare( '==', 'locale.site.code', $sites ) );
		}

		return $manager->searchItems( $search );
	}
}