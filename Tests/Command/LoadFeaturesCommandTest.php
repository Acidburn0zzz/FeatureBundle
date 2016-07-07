<?php

namespace Ae\FeatureBundle\Tests\Command;

use Ae\FeatureBundle\Command\LoadFeaturesCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @covers Ae\FeatureBundle\Command\LoadFeaturesCommand
 */
class LoadFeaturesCommandTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $kernel = $this->getMock(KernelInterface::class);
        $container = $this->getMock(ContainerInterface::class);

        $container
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['twig', null],
                ['ae_feature.manager', null],
            ]));

        $kernel
            ->method('getBundles')
            ->willReturn([]);

        $kernel
            ->method('getContainer')
            ->willReturn($container);

        $application = new Application($kernel);
        $application->add(new LoadFeaturesCommand());

        $command = $application->find('adespresso:features:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => [__DIR__],
        ]);

        $this->assertEmpty($commandTester->getDisplay());
    }
}
