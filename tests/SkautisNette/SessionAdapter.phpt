<?php

declare(strict_types=1);

use Tester\Assert;
use Nette\Http;
use Skautis\Nette\SessionAdapter;


require __DIR__ . '/../bootstrap.php';


$httpRequest = new Http\Request(new Http\UrlScript);
$httpResponse = new Http\Response();
$session = new Http\Session($httpRequest, $httpResponse);
$adapter = new SessionAdapter($session);

$name = "asd";
$data = new \StdClass();
$data->data['user_id'] = 123;
$data->data['token'] = 'asdqwe';

Assert::false($adapter->has($name));

$adapter->set($name, $data);
Assert::true($adapter->has($name));
Assert::equal($data, $adapter->get($name));
