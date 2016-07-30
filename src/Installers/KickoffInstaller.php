<?php
namespace Club\KickoffInstaller\Installers;

class KickoffInstaller extends Installer
{
    /**
     * Installable framework name.
     *
     * @var string
     */
    protected $name = 'Kickoff';

    /**
     * The URL to download the framework .zip file from.
     *
     * @var string
     */
    protected $downloadFrom = 'https://github.com/clubstudioltd/kickoff/archive/master.zip';

    /**
     * Where the download will be saved to.
     *
     * @var string
     */
    protected $downloadTo = 'kickoff.zip';

    public function clean()
    {
        // Do nothing...
    }

    public function kickoff()
    {
        $this->output->writeln('<info>Installing Kickoff...</info>');

        $this->runCommands([
            'rsync --ignore-existing -ar --remove-source-files tmp/kickoff-master/ .'
        ]);

        $this->output->writeln('<comment>Kickoff complete.</comment>');
    }
}
