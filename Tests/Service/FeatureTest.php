<?php

namespace Ae\FeatureBundle\Tests\Service;

use Ae\FeatureBundle\Service\Feature;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;
    protected $security;
    protected $service;

    protected function setUp()
    {
        $this->manager = $this->getMockBuilder('Ae\FeatureBundle\Entity\FeatureManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->security = $this->getMockBuilder('Ae\FeatureBundle\Security\FeatureSecurity')
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new Feature($this->manager, $this->security);
    }

    public function testIsGrantedTrue()
    {
        $featureEnabled = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $this->manager->expects($this->atLeastOnce())
            ->method('find')
            ->will($this->returnValueMap(array(
                array('featureA', 'group', $featureEnabled),
            )));
        $this->security->expects($this->atLeastOnce())
            ->method('isGranted')
            ->will($this->returnValueMap(array(
                array($featureEnabled, true),
            )));

        $this->assertTrue($this->service->isGranted('featureA', 'group'));
    }

    public function testIsGrantedFalse()
    {
        $featureDisabled = $this->getMock('Ae\FeatureBundle\Entity\Feature');
        $this->manager->expects($this->atLeastOnce())
            ->method('find')
            ->will($this->returnValueMap(array(
                array('featureB', 'group', $featureDisabled),
            )));
        $this->security->expects($this->atLeastOnce())
            ->method('isGranted')
            ->will($this->returnValueMap(array(
                array($featureDisabled, false),
            )));

        $this->assertFalse($this->service->isGranted('featureB', 'group'));
    }
}