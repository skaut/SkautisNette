<?php

declare(strict_types=1);

use Skaut\Skautis\InvalidArgumentException;
use Skaut\SkautisNette\Fixture\FixtureNotFoundException;
use Skaut\SkautisNette\Fixture\FixtureWebServiceFactory;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

const FIXTURES = __DIR__.'/../../resources/fixtures';

test('plain and variant fixtures', function (): void {
    $service = (new FixtureWebServiceFactory(FIXTURES))
        ->createWebService('https://test-is.skaut.cz/JunakWebservice/OrganizationUnit.asmx?WSDL', []);

    $unit = $service->call('UnitDetail', [['ID' => 1002]]);
    Assert::type(stdClass::class, $unit);
    Assert::same('Středisko Sluneční hodiny', $unit->DisplayName);

    Assert::null($service->call('UnitDetail', [['ID' => 9999]]));

    $persons = $service->call('PersonAll', [['ID_Unit' => 1003, 'OnlyDirectMember' => false]]);
    Assert::type('array', $persons);
    Assert::count(3, $persons);
});

test('magic call and case-insensitive method name', function (): void {
    $service = (new FixtureWebServiceFactory(FIXTURES))
        ->createWebService('https://test-is.skaut.cz/JunakWebservice/UserManagement.asmx?WSDL', []);

    $detail = $service->userDetail();

    Assert::type(stdClass::class, $detail);
    Assert::same(100, $detail->ID_Person);
});

test('missing fixture names the file to create', function (): void {
    $service = (new FixtureWebServiceFactory(FIXTURES))
        ->createWebService('https://test-is.skaut.cz/JunakWebservice/Events.asmx?WSDL', []);

    Assert::exception(
        fn () => $service->call('EventCampUpdate', [['ID' => 1]]),
        FixtureNotFoundException::class,
        '%a%Events/EventCampUpdate.json'
    );
});

test('unknown directory is rejected', function (): void {
    Assert::exception(fn () => new FixtureWebServiceFactory(__DIR__.'/does-not-exist'), InvalidArgumentException::class);
});

test('service name must be in the URL', function (): void {
    $factory = new FixtureWebServiceFactory(FIXTURES);

    Assert::exception(fn () => $factory->createWebService('https://example.com/nonsense', []), InvalidArgumentException::class);
});
