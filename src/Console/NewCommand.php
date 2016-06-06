<?php
namespace Club\KickoffInstaller\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Club\KickoffInstaller\Installers\KickoffInstaller;

class NewCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('new')
             ->setDescription('Kickoff a new website or application')
             ->addArgument('framework', InputArgument::REQUIRED)
             ->addOption('clean', null, InputOption::VALUE_NONE, 'If set, Kickoff will be omitted from the install');
    }

    /**
     * Available installers.
     *
     * @return array An array of available installer class names.
     */
    protected function installers()
    {
        return [
            'craft' => \Club\KickoffInstaller\Installers\CraftInstaller::class,
        ];
    }

    /**
     * Get the full class name of an installer.
     *
     * @param  string $framework Framework short-name.
     * @return string            Installer class name.
     */
    protected function getInstallerClass($framework)
    {
        $installers = $this->installers();

        if (!array_key_exists($framework, $installers)) {
            throw new InvalidArgumentException("An installer for `$framework` does not exist.");
        }

        return $installers[$framework];
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = getcwd();
        $installer = $this->getInstallerClass($input->getArgument('framework'));

        if (!$input->getOption('clean')) {
            (new KickoffInstaller($input, $output, $cwd))->install();
        }

        (new $installer($input, $output, $cwd))->install();

        $output->writeln('<comment>All done. Happy coding!</comment>');
    }
}
