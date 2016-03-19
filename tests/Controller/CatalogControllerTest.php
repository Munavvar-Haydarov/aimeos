<?php

class CatalogControllerTest extends AimeosTestAbstract
{
	public function setUp()
	{
		parent::setUp();
		View::addLocation(dirname(__DIR__).'/fixtures/views');
	}


	public function testCountAction()
	{
		$response = $this->action('GET', '\Aimeos\Shop\Controller\CatalogController@countAction', ['site' => 'unittest']);

		$this->assertResponseOk();
		$this->assertContains('.catalog-filter-count', $response->getContent());
		$this->assertContains('.catalog-filter-attribute', $response->getContent());
	}


	public function testDetailAction()
	{
		$response = $this->action('GET', '\Aimeos\Shop\Controller\CatalogController@detailAction', ['site' => 'unittest']);

		$this->assertResponseOk();
		$this->assertContains('<section class="aimeos catalog-stage">', $response->getContent());
		$this->assertContains('<section class="aimeos catalog-detail"', $response->getContent());
		$this->assertContains('<section class="aimeos catalog-session">', $response->getContent());
	}


	public function testListAction()
	{
		$response = $this->action('GET', '\Aimeos\Shop\Controller\CatalogController@listAction', ['site' => 'unittest']);

		$this->assertResponseOk();
		$this->assertContains('<section class="aimeos catalog-filter">', $response->getContent());
		$this->assertContains('<section class="aimeos catalog-stage">', $response->getContent());
		$this->assertContains('<section class="aimeos catalog-list">', $response->getContent());
	}


	public function testStockAction()
	{
		$response = $this->action('GET', '\Aimeos\Shop\Controller\CatalogController@stockAction', ['site' => 'unittest']);

		$this->assertResponseOk();
		$this->assertContains('.aimeos .product .stock', $response->getContent());
	}


	public function testSuggestAction()
	{
		$response = $this->action('GET', '\Aimeos\Shop\Controller\CatalogController@suggestAction', ['site' => 'unittest'], ['f_search' => 'unit']);

		$this->assertResponseOk();
		$this->assertRegexp('/[{.*}]/', $response->getContent());
	}
}