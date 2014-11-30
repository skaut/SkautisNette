<?php

namespace Test\Skautis\Nette;

use Skautis\Nette\SessionAdapter as NetteAdapter;
use Nette\Http\UrlScript;
use Nette\Http\Response;
use Nette\Http\Request;
use Nette\Http\Session;

class SessionAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     * @return void
     */
    public function testAdapter()
    {
	$netteRequest = new Request(new UrlScript());
	$netteResponse = new Response();
	$netteSession = new Session($netteRequest, $netteResponse);
	$adapter = new NetteAdapter($netteSession);

	$name = "asd";
	$data = new \StdClass();
	$data->data['user_id'] = 123;
	$data->data['token'] = 'asdqwe';
	$this->assertFalse($adapter->has($name));
	$adapter->set($name, $data);
	$this->assertTrue($adapter->has($name));
	$this->assertEquals($data, $adapter->get($name));
	$object = $adapter->get($name);
	$this->assertEquals(123, $object->data['user_id']);
	$this->assertEquals("asdqwe", $object->data['token']);
    }
}
