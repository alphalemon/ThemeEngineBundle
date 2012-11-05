<?php

namespace AlphaLemon\ThemeEngineBundle\Command\Generate;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use AlphaLemon\ThemeEngineBundle\Core\Generator\AlAppThemeGenerator;

class GenerateAppThemeBundleCommand extends BaseGenerateBundle
{
    protected function configure()
    {
        $this
            ->setName('alphalemon:generate:app-theme')
            ->setDescription('Generate a App-Block bundle')
            ->setDefinition(array(
                new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the bundle to create'),
                new InputOption('dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the bundle'),
                new InputOption('bundle-name', '', InputOption::VALUE_REQUIRED, 'The optional bundle name'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Do nothing but mandatory for extend', 'annotation'),
                new InputOption('structure', '', InputOption::VALUE_NONE, 'Whether to generate the whole directory structure'),
                new InputOption('no-strict', '', InputOption::VALUE_NONE, 'Skips the strict control on App-Theme namespace'),
            ));
    }

    protected function checkStrictNamespace($namespace)
    {
        if (preg_match('/^AlphaLemon\\\\Theme\\\\[\w]+ThemeBundle/', $namespace) == false) {
            throw new \RuntimeException('A strict AlphaLemon App-Theme namespace must start with AlphaLemon\Theme and the bundle must be suffixed as ThemeBundle');
        }
    }

    protected function getGeneratorExtraOptions(InputInterface $input)
    {
        return array(
            'no-strict' => $input->getOption('no-strict'),
        );
    }

    protected function getGenerator()
    {
        if (null === $this->generator) {
            $kernel = $this->getContainer()->get('kernel');
            $bundlePath = $kernel->locateResource('@SensioGeneratorBundle');

            $this->generator = new AlAppThemeGenerator($this->getContainer()->get('filesystem'), $bundlePath.'/Resources/skeleton/bundle');
        }

        return $this->generator;
    }
}