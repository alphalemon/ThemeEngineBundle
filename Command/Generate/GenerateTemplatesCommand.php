<?php

namespace AlphaLemon\ThemeEngineBundle\Command\Generate;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AlphaLemon\ThemeEngineBundle\Core\Generator\AlTemplateGenerator;
use Symfony\Component\DependencyInjection\Container;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use AlphaLemon\ThemeEngineBundle\Core\Generator\TemplateParser\AlTemplateParser;
use AlphaLemon\ThemeEngineBundle\Core\Generator\AlSlotsGenerator;

class GenerateTemplatesCommand extends ContainerAwareCommand
{
    protected $templateParser;
    protected $templateGenerator;
    protected $slotsGenerator;

    public function setTemplateParser(AlTemplateParser $templateParser)
    {
        $this->templateParser = $templateParser;
    }

    public function getTemplateParser($themeName = null)
    {
        return $this->templateParser;
    }

    public function setTemplateGenerator(AlTemplateGenerator $templateGenerator)
    {
        $this->templateGenerator = $templateGenerator;
    }

    public function getTemplateGenerator()
    {
        if (null === $this->templateGenerator) {
            $this->templateGenerator = new AlTemplateGenerator();
        }

        return $this->templateGenerator;
    }

    public function setSlotsGenerator(AlSlotsGenerator $slotsGenerator)
    {
        $this->slotsGenerator = $slotsGenerator;
    }

    public function getSlotsGenerator()
    {
        if (null === $this->slotsGenerator) {
            $this->slotsGenerator = new AlSlotsGenerator();
        }

        return $this->slotsGenerator;
    }

    protected function configure()
    {
        $this
            ->setName('alphalemon:generate:templates')
            ->setDescription('Generate the templates config files for the given theme')
            ->setDefinition(array(
                new InputOption('theme-name', '', InputOption::VALUE_REQUIRED, 'The name of the theme bundle which gets the template'),
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
        $themeName = $input->getOption('theme-name');
        $kernel = $this->getContainer()->get('kernel');
        $dir = $kernel->locateResource('@' . $themeName);

        if (null === $this->templateParser) {
            $this->templateParser = new AlTemplateParser($dir . 'Resources/views');
        }

        $templates = $this->templateParser->parse();
        $this->addOption('template-name', '', InputOption::VALUE_NONE, '');
        foreach ($templates as $templateName => $elements) {
            $templateName = basename($templateName, '.html.twig');
            if ($templateName !== 'base') {
                $generator = $this->getTemplateGenerator();
                $message = $generator->generateTemplate($dir . 'Resources/config/templates', $themeName, $templateName, $elements['assets']);
                $output->writeln($message);
            }

            $slots = $elements['slots'];
            if (!empty($slots)) {
                $slotsGenerator = $this->getSlotsGenerator();
                $message = $slotsGenerator->generateSlots($dir . 'Resources/config/templates/slots', $themeName, $templateName, $slots);

                $output->writeln($message);
            }
        }
    }
}