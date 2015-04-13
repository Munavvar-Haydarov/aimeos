<?php

class AdminControllerTest extends AimeosTestAbstract
{
	public function setUp()
	{
		parent::setUp();
		require dirname(dirname(__DIR__)).'/src/routes_admin.php';
	}


	public function testIndexAction()
	{
		$response = $this->action('GET', '\Aimeos\Shop\Controller\AdminController@indexAction', ['site' => 'unittest']);

		$this->assertResponseOk();
		$this->assertRegexp('#<script type="text/javascript">.*window.MShop = {#smu', $response->getContent());
	}


	public function testDoAction()
	{
		$response = $this->action('GET', '\Aimeos\Shop\Controller\AdminController@doAction', ['site' => 'unittest']);

		$this->assertResponseOk();
		$this->assertRegexp('#{.*}#smu', $response->getContent());
	}
}