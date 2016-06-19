<?php
namespace Club\KickoffInstaller\Installers;

class CraftInstaller extends Installer
{
    /**
     * Installable framework name.
     *
     * @var string
     */
    protected $name = 'CraftCMS';

    /**
     * The URL to download the framework .zip file from.
     *
     * @var string
     */
    protected $downloadFrom = 'http://buildwithcraft.com/latest.zip?accept_license=yes';

    /**
     * Where the download will be saved to.
     *
     * @var string
     */
    protected $downloadTo = 'craft.zip';

    /**
     * Commands to run once the framework has been downloaded
     *
     * @return array An array of commands
     */
    protected function commands()
    {
        return [
            'mv tmp/craft/app .',
            'mv tmp/craft/config .',
            'mv tmp/craft/plugins .',
            'mv tmp/craft/storage .',
            'mv tmp/craft/templates resources/.',
            'mv tmp/public/index.php public/.',
            'rm public/index.html',
        ];
    }

    /**
     * Commands to run when Kickoff isn't installed
     *
     * @return array An array of commands
     */
    protected function cleanCommands()
    {
        return [
            'mv tmp/craft .',
            'mv tmp/public .',
        ];
    }

    /**
     * Process the install job.
     *
     * @return void
     */
    public function process()
    {
        $commands = $this->cleanCommands();

        if (!$this->input->getOption('clean')) {
            $commands = $this->commands();

            $this->output->writeln('<info>Updating directory structure...</info>');
        }

        $this->runCommands(
            array_merge($commands, ['rm -rf tmp'])
        );

        if (!$this->input->getOption('clean')) {
            $this->postInstallCommands();
        }
    }

    public function postInstallCommands()
    {
        $this->output->writeln('<info>Linking Craft with Kickoff...</info>');

        $this->copyStub('craft/index', 'index');
        $this->copyStub('craft/postinstall', 'postinstall.sh');

        $this->runCommands([
            // Install Composer dependencies.
            'composer install',

            // Update directory structure and use environment vars.
            'chmod +x postinstall.sh',
            './postinstall.sh',
            'rm postinstall.sh',
        ]);
    }
}
