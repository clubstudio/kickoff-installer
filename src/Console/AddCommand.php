<?php

namespace Club\KickoffInstaller\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends InstallationCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('add')
             ->setDescription('Add a framework installation to an existing project')
             ->addArgument('framework', InputArgument::REQUIRED);
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installerClass = $this->getInstallerClass(
            $input->getArgument('framework')
        );

        $installer = new $installerClass($input, $output, getcwd());
        $installer->install();

        $output->writeln("<comment>`{$installer->name()}` added to your project. Happy coding!</comment>");
    }
}
