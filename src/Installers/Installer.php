<?php
namespace Club\KickoffInstaller\Installers;

use ZipArchive;
use GuzzleHttp\Client;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Installer
{
    /**
     * Console input object.
     *
     * @var Symfony\Component\Console\InputInterface
     */
    protected $input;

    /**
     * Console output object.
     *
     * @var Symfony\Component\Console\OutputInterface
     */
    protected $output;

    /**
     * The directory to run commands in.
     *
     * @var string
     */
    protected $directory;

    /**
     * Installable framework name.
     *
     * @var string
     */
    protected $name;

    /**
     * The URL to download the framework .zip file from.
     *
     * @var string
     */
    protected $downloadFrom;

    /**
     * Where the download will be saved to.
     *
     * @var string
     */
    protected $downloadTo;

    /**
     * Configuration
     *
     * @var mixed
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $directory
     */
    public function __construct(InputInterface $input, OutputInterface $output, $directory)
    {
        $this->input = $input;
        $this->output = $output;
        $this->directory = $directory;

        $this->loadConfig();
    }

    /**
     * Download the temporary zip file.
     *
     * @return $this
     */
    public function download()
    {
        $this->output->writeln("<info>Downloading {$this->name}...</info>");

        $response = (new Client)->get($this->downloadFrom);

        file_put_contents($this->downloadTo, $response->getBody());

        $this->output->writeln("<comment>{$this->name} download complete.</comment>");

        return $this;
    }

    /**
     * Extract the zip file into the configured directory.
     *
     * @return $this
     */
    protected function extract()
    {
        $archive = new ZipArchive;
        $archive->open($this->downloadTo);
        $archive->extractTo(getcwd() . '/tmp');
        $archive->close();

        return $this;
    }

    /**
     * Process the install job.
     *
     * @return void
     */
    protected function process()
    {
        if ($this->input->getOption('clean')) {
            $this->clean();

            return $this;
        }

        $this->kickoff();

        return $this;
    }

    /**
     * Runs when Kickoff isn't installed
     *
     * @return array An array of commands
     */
    abstract protected function clean();

    /**
     * Runs once the Kickoff framework has been downloaded
     *
     * @return array An array of commands
     */
    abstract protected function kickoff();

    /**
     * Runs when the installation process is complete
     *
     * @return void
     */
    abstract protected function complete();

    /**
     * Clean-up the temporary zip file.
     *
     * @return $this
     */
    public function cleanUp()
    {
        $this->output->writeln("<info>Cleaning up {$this->name} installation...</info>");

        @chmod($this->downloadTo, 0777);
        @unlink($this->downloadTo);

        $this->output->writeln("<comment>All clean!</comment>");

        return $this;
    }

    /**
     * Process the install job.
     *
     * @return void
     */
    public function install()
    {
        $this->download()
             ->extract()
             ->cleanUp()
             ->process()
             ->complete();
    }

    /**
     * Execute Commands.
     *
     * @param  array $commands An array of commands to execute.
     * @return void
     */
    protected function runCommands(array $commands)
    {
        $process = new Process(implode(' && ', $commands), $this->directory, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });
    }

    /**
     * Copy a stub file
     *
     * @param  string $from Stub source location/filename
     * @param  string $to   Stub destination location/filename
     * @return void
     */
    protected function copyStub($from, $to)
    {
        copy(
            __DIR__.'/'.$this->directoryName().'/stubs/'.$from,
            $this->directory.'/'.$to
        );
    }

    /**
     * Load Configuration; from either a local file or the installer's default
     *
     * @param  string $file Configuration file name
     * @return mixed        Array if files exists
     */
    protected function loadConfig()
    {
        if ($this->loadConfigFromFile($this->directory.'/kickoff.json')) {
            $this->config->local = true;
            return;
        }

        $this->loadConfigFromFile(__DIR__.'/'.$this->directoryName().'/config.json');
    }

    /**
     * Load Configuration from a JSON file
     *
     * @param  string $path The full path to the configuration file
     * @return mixed        Array if files exists
     */
    protected function loadConfigFromFile($path)
    {
        if (file_exists($path)) {
            return $this->config = json_decode(file_get_contents($path));
        }
    }

    /**
     * Get the installers directory name
     *
     * @return string camelCase directory name
     */
    protected function directoryName()
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $this->name))));
    }
}
