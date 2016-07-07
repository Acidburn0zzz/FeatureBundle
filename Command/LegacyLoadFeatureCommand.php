<?php

namespace Ae\FeatureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 *
 * @deprecated The "features:load" command is deprecated since 1.2 and will be removed in 2.0. Use "adespresso:features:load" instead.
 */
class LegacyLoadFeatureCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('features:load')
            ->setDescription('Persist new features found in templates (deprecated)')
            ->addArgument(
                'bundle',
                InputArgument::REQUIRED,
                'The bundle where to load the features'
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
        $style = new SymfonyStyle($input, $output);
        $style->caution('The "features:load" command is deprecated since 1.2 and will be removed in 2.0. Use "adespresso:features:load" instead.');

        $application = $this->getApplication();
        $path = $application
            ->getKernel()
            ->getBundle($input->getArgument('bundle'))
            ->getPath();

        $path .= '/Resources/views/';

        $commandInput = new ArrayInput([
            'command' => 'adespresso:features:load',
            'path' => [
                $path,
            ],
            '--dry-run' => $input->getOption('dry-run'),
        ]);

        return $application
            ->find('adespresso:features:load')
            ->run($commandInput, $output);
    }
}
