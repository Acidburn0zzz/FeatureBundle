<?php

namespace Ae\FeatureBundle\Command;

use Ae\FeatureBundle\Twig\Node\FeatureNode;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Twig_Node;

/**
 * Load features from some directories.
 */
class LoadFeaturesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('adespresso:features:load')
            ->setDescription('Persist new features found in templates')
            ->addArgument(
                'path',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'The path where to load the features'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Do not persist new features'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $twig = $container->get('twig');
        $found = [];
        $files = Finder::create()
            ->files()
            ->name('*.twig');

        foreach ($input->getArgument('path') as $path) {
            $files = $files->in($path);
        }

        foreach ($files as $file) {
            $tree = $twig->parse(
                $twig->tokenize(file_get_contents($file->getPathname()))
            );

            if (!$tags = $this->findFeatureNodes($tree)) {
                continue;
            }

            $found += $tags;

            foreach ($tags as $tag) {
                $output->writeln(sprintf(
                    'Found <info>%s</info>.<info>%s</info> in <info>%s</info>',
                    $tag['parent'],
                    $tag['name'],
                    $file->getFilename()
                ));
            }
        }

        if ($input->getOption('dry-run')) {
            return;
        }

        $manager = $container->get('ae_feature.manager');
        foreach ($found as $tag) {
            $manager->findOrCreate($tag['name'], $tag['parent']);
        }
    }

    /**
     * Find feature nodes.
     *
     * @param Twig_Node $node
     *
     * @return array
     */
    private function findFeatureNodes(Twig_Node $node)
    {
        $found = [];
        $stack = [$node];
        while ($stack) {
            $node = array_pop($stack);
            if ($node instanceof FeatureNode) {
                $arguments = $node
                    ->getNode('tests')
                    ->getNode(0)
                    ->getNode('arguments')
                    ->getKeyValuePairs();

                $tag = [];
                foreach ($arguments as $argument) {
                    $keyAttr = $argument['key']->getAttribute('value');
                    $valueAttr = $argument['value']->getAttribute('value');

                    $tag[$keyAttr] = $valueAttr;
                }
                $key = md5(serialize($tag));
                $found[$key] = $tag;
            } else {
                foreach ($node as $child) {
                    if (null !== $child) {
                        $stack[] = $child;
                    }
                }
            }
        }

        return $found;
    }
}
