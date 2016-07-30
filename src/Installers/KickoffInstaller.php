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

    /**
     * Process the install job.
     *
     * @return void
     */
    public function process()
    {
        $this->output->writeln('<info>Installing Kickoff...</info>');

        $this->runCommands([
            'rsync -ar --remove-source-files tmp/kickoff-master/ .'
        ]);

        $this->output->writeln('<comment>Kickoff complete.</comment>');
    }
}
