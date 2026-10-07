<?php

declare(strict_types=1);

use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\Session;
use Nette\Http\UrlScript;
use Skaut\SkautisNette\SessionAdapter;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

$session = new Session(new Request(new UrlScript('http://localhost/')), new Response());
$adapter = new SessionAdapter($session);

Assert::false($adapter->has('login'));
Assert::null($adapter->get('login'));

$adapter->set('login', ['ID_Login' => 'token']);

Assert::true($adapter->has('login'));
Assert::same(['ID_Login' => 'token'], $adapter->get('login'));
Assert::same(['ID_Login' => 'token'], (new SessionAdapter($session))->get('login'), 'another adapter over the same session sees the value');
