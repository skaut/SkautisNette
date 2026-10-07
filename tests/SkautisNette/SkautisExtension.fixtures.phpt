<?php

declare(strict_types=1);

use Skaut\Skautis\Skautis;
use Skaut\SkautisNette\Fixture\FixtureWebServiceFactory;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

$container = createContainer([
    'applicationId' => 'test',
    'testMode' => true,
    'fixtures' => __DIR__.'/../../resources/fixtures',
], debugMode: false);

Assert::type(FixtureWebServiceFactory::class, $container->getService('skautis.webServiceFactory'));

$skautis = $container->getByType(Skautis::class);
$skautis->setLoginData([
    'skautIS_Token' => '00000000-0000-4000-8000-000000000001',
    'skautIS_IDRole' => '10',
    'skautIS_IDUnit' => '1003',
    'skautIS_DateLogout' => '31. 12. 2099 23:59:59',
]);

Assert::true($skautis->getUser()->isLoggedIn(true));
Assert::same(1003, $skautis->getUser()->getUnitId());

$detail = $skautis->getWebService('user')->call('UserDetail');
Assert::type(stdClass::class, $detail);
Assert::same('jan.novak', $detail->UserName);
