<?php

namespace Ae\FeatureBundle\Tests\Command;

use Ae\FeatureBundle\Command\LegacyLoadFeatureCommand;
use PHPUnit_Framework_TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @covers Ae\FeatureBundle\Command\LegacyLoadFeatureCommand
 */
class LegacyLoadFeatureCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group legacy
     */
    public function testExecute()
    {
        $kernel = $this->getMock(KernelInterface::class);
        $container = $this->getMock(ContainerInterface::class);
        $bundle = $this->getMock(BundleInterface::class);
        $commandMock = $this
            ->getMockBuilder(Command::class)
            ->setConstructorArgs(['adespresso:features:load'])
            ->setMethods(['run'])
            ->getMock();

        $kernel
            ->method('getBundles')
            ->willReturn([]);
        $kernel
            ->method('getBundle')
            ->willReturn($bundle);

        $kernel
            ->method('getContainer')
            ->willReturn($container);

        $application = $this
            ->getMockBuilder(Application::class)
            ->setConstructorArgs([$kernel])
            ->setMethods(['find'])
            ->getMock();

        $command = new LegacyLoadFeatureCommand();
        $command->setApplication($application);

        $commandTester = new CommandTester($command);

        $application
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValueMap([
                ['adespresso:features:load', $commandMock],
            ]));

        $commandMock
            ->expects($this->once())
            ->method('run');

        $commandTester->execute([
            'command' => $command->getName(),
            'bundle' => 'AppBundle',
        ]);
    }
}
