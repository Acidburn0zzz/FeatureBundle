<?php

namespace Ae\FeatureBundle\Twig\Extension;

use Ae\FeatureBundle\Service\Feature;
use Ae\FeatureBundle\Twig\TokenParser\FeatureTokenParser;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureExtension extends \Twig_Extension
{
    protected $service;

    /**
     * @param \Ae\FeatureBundle\Service\Feature $service
     */
    public function __construct(Feature $service)
    {
        $this->service  = $service;
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
            new FeatureTokenParser(),
        );
    }

    public function getName()
    {
        return 'feature';
    }

    /**
     * @param string $name
     * @param string $parent
     *
     * @return bool
     */
    public function isGranted($name, $parent)
    {
        return $this->service->isGranted($name, $parent);
    }
}