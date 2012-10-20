<?php

namespace AlphaLemon\ThemeEngineBundle\Command\Generate;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use AlphaLemon\ThemeEngineBundle\Core\Generator\AlTemplateGenerator;
use Symfony\Component\DependencyInjection\Container;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use AlphaLemon\ThemeEngineBundle\Core\Generator\TemplateParser\AlTemplateParser;
use AlphaLemon\ThemeEngineBundle\Core\Generator\AlSlotsGenerator;
use AlphaLemon\ThemeEngineBundle\Core\Generator\AlExtensionGenerator;

class GenerateTemplatesCommand extends ContainerAwareCommand
{
    protected $templateParser;
    protected $templateGenerator;
    protected $slotsGenerator;
    protected $extensionGenerator;

    public function setTemplateParser(AlTemplateParser $templateParser)
    {
        $this->templateParser = $templateParser;
    }

    public function getTemplateParser()
    {
        return $this->templateParser;
    }

    public function setTemplateGenerator(AlTemplateGenerator $templateGenerator)
    {
        $this->templateGenerator = $templateGenerator;
    }

    public function getTemplateGenerator()
    {
        return $this->templateGenerator;
    }

    public function setSlotsGenerator(AlSlotsGenerator $slotsGenerator)
    {
        $this->slotsGenerator = $slotsGenerator;
    }

    public function getSlotsGenerator()
    {
        return $this->slotsGenerator;
    }
    
    public function setExtensionGenerator(AlExtensionGenerator $extensionGenerator)
    {
        $this->extensionGenerator = $extensionGenerator;
    }

    public function getExtensionGenerator()
    {
        return $this->extensionGenerator;
    }

    protected function configure()
    {
        $this
            ->setName('alphalemon:generate:templates')
            ->setDescription('Generate the templates config files for the given theme')
            ->setDefinition(array(
                new InputArgument('theme', InputArgument::REQUIRED, 'The name of the theme bundle which gets the template'),
            ));
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When namespace doesn't end with Bundle
     * @throws \RuntimeException         When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $themeName = $input->getArgument('theme');
        $kernel = $this->getContainer()->get('kernel');
        $dir = $kernel->locateResource('@' . $themeName);
        $namespace = $kernel
            ->getBundle($themeName)
            ->getNamespace()
        ;
        
        if (null === $this->templateParser) {
            $this->templateParser = new AlTemplateParser($dir . 'Resources/views/Theme');
        }

        if (null === $this->templateGenerator) {
            $this->templateGenerator = new AlTemplateGenerator();
        }

        if (null === $this->slotsGenerator) {
            $this->slotsGenerator = new AlSlotsGenerator();
        }
        
        if (null === $this->extensionGenerator) {
            $this->extensionGenerator = new AlExtensionGenerator();
        }

        $templates = $this->templateParser->parse();
        $this->addOption('template-name', '', InputOption::VALUE_NONE, '');
        foreach ($templates as $templateName => $elements) {
            $templateName = basename($templateName, '.html.twig');
            if ($elements['generate_template']) {
                $message = $this->templateGenerator->generateTemplate($dir . 'Resources/config/templates', $themeName, $templateName, $elements['assets']);
                $output->writeln($message);
            }

            $slots = $elements['slots'];
            if (!empty($slots)) {
                $message = $this->slotsGenerator->generateSlots($dir . 'Resources/config/templates/slots', $themeName, $templateName, $slots);
                $output->writeln($message);
            }
        }
        
        $message = $this->extensionGenerator->generateExtension($namespace, $dir . 'DependencyInjection', $themeName, $templates);
        $output->writeln($message);
    }
}