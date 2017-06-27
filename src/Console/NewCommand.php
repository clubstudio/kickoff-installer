<?php

namespace Club\KickoffInstaller\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Club\KickoffInstaller\Installers\Kickoff\Installer as KickoffInstaller;

class NewCommand extends InstallationCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('new')
             ->setDescription('Kickoff a new website or application')
             ->addArgument('framework', InputArgument::OPTIONAL)
             ->addOption('clean', null, InputOption::VALUE_NONE, 'If set, Kickoff will be omitted from the install');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = getcwd();
        $framework = $input->getArgument('framework');

        if (!$input->getOption('clean')) {
            (new KickoffInstaller($input, $output, $cwd))->install();
        }

        if ($framework and $installer = $this->getInstallerClass($framework)) {
            (new $installer($input, $output, $cwd))->install();
        }

        $output->writeln('<comment>All done. Happy coding!</comment>');
    }
}
