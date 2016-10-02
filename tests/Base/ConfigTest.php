<?php

class ConfigTest extends AimeosTestAbstract
{
	public function testGet()
	{
		$aimeos = $this->app->make('\Aimeos\Shop\Base\Aimeos');

		$configMock = $this->getMockBuilder('\Illuminate\Config\Repository')
			->setMethods( array('get') )->getMock();

		if( function_exists('apc_store') ) {
			$configMock->expects( $this->exactly(4) )->method('get')
				->will( $this->onConsecutiveCalls( true, 'laravel:', array(), array() ) );
		} else {
			$configMock->expects( $this->exactly(2) )->method('get')
				->will( $this->returnValue( array( 'test' => 1 ) ) );
		}

		$object = new \Aimeos\Shop\Base\Config($configMock, $aimeos);

		$this->assertInstanceOf( '\Aimeos\MW\Config\Iface', $object->get() );
	}
}
