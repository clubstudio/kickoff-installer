<?php

namespace Club\KickoffInstaller\Installers\CraftCms;

use Club\KickoffInstaller\Installers\Installer as BaseInstaller;

class Installer extends BaseInstaller
{
    /**
     * Installable framework name.
     *
     * @var string
     */
    protected $name = 'Craft CMS';

    /**
     * The URL to download the framework .zip file from.
     *
     * @var string
     */
    protected $downloadFrom = 'http://craftcms.com/latest.zip?accept_license=yes';

    /**
     * Where the download will be saved to.
     *
     * @var string
     */
    protected $downloadTo = 'craft.zip';

    /**
     * Configurable directories to path constant mappings.
     *
     * @var array
     */
    protected static $pathConstants = [
        'config' => 'CRAFT_CONFIG_PATH',
        'plugins' => 'CRAFT_PLUGINS_PATH',
        'templates' => 'CRAFT_TEMPLATES_PATH',
        'storage' => 'CRAFT_STORAGE_PATH',
        'translations' => 'CRAFT_TRANSLATIONS_PATH',
    ];

    /**
     * Commands to run when Kickoff isn't installed.
     *
     * @return array An array of commands
     */
    public function clean()
    {
        if (isset($this->config->local)) {
            $this->runCommands(['mv tmp/public .']);
            $this->moveCraftDirectories($this->config->mappings);
            $this->updateFrontController($this->config->mappings);
            $this->replacePaths($this->config->mappings);

            return;
        }

        $this->runCommands([
            'mv tmp/craft .',
            'mv tmp/public .',
        ]);
    }

    /**
     * Commands to run once the framework has been downloaded.
     *
     * @return array An array of commands
     */
    protected function kickoff()
    {
        $this->moveCraftDirectories($this->config->mappings);
        $this->swapIndexFile();

        $this->output->writeln('<info>Linking Craft with Kickoff...</info>');
        $this->updateFrontController();
        $this->installPhpDotEnv($this->config->mappings);
        $this->replacePaths($this->config->mappings);
    }

    /**
     * Runs when the installation process is complete.
     */
    protected function complete()
    {
        $this->runCommands(['rm -rf tmp']);
    }

    /**
     * Move Craft Directories in to those defined in the configuration file.
     *
     * @param object $mappings An object of directory mappings
     */
    protected function moveCraftDirectories($mappings)
    {
        $this->output->writeln('<info>Moving Craft directories...</info>');

        $this->runCommands([
            "mkdir -p ./$mappings->app/",
            "rsync -a --ignore-existing tmp/craft/app/ ./$mappings->app/",

            "mkdir -p ./$mappings->config/",
            "rsync -a --ignore-existing tmp/craft/config/ ./$mappings->config/",

            "mkdir -p ./$mappings->plugins/",
            "rsync -a --ignore-existing tmp/craft/plugins/ ./$mappings->plugins/",

            "mkdir -p ./$mappings->storage/",
            "rsync -a --ignore-existing tmp/craft/storage/ ./$mappings->storage/",

            "mkdir -p ./$mappings->templates/",
            "rsync -a --ignore-existing tmp/craft/templates/ ./$mappings->templates/",

            "echo '/$mappings->app' >> .gitignore",
            "echo '/$mappings->storage/backups/**/*' >> .gitignore",
            "echo '/$mappings->storage/runtime/**/*' >> .gitignore",
            "echo '/$mappings->storage/userphotos/**/*' >> .gitignore",
        ]);

        $this->output->writeln('<comment>Craft directories moved!</comment>');
    }

    /**
     * Swap the default Kickoff index file with the Craft index file.
     */
    protected function swapIndexFile()
    {
        $this->output->writeln('<info>Moving Front Controller...</info>');

        $this->runCommands([
            'mv tmp/public/index.php ./public/',
        ]);

        $this->output->writeln('<comment>Front Controller moved!</comment>');
    }

    /**
     * Inject custom directory mappings into the Craft index file.
     */
    protected function updateFrontController()
    {
        $this->copyStub('index', 'index');

        $this->runCommands([
            'tail -n +5 public/index.php >> index',
            'mv index public/index.php',
        ]);
    }

    /**
     * Install PHPDotEnv Library.
     *
     * @param object $mappings An object of directory mappings
     */
    protected function installPhpDotEnv($mappings)
    {
        $this->copyStub('phpdotenv', 'phpdotenv.sh');
        $this->copyStub('index-phpdotenv', 'index-phpdotenv');

        $this->runCommands([
            'chmod +x phpdotenv.sh',
            './phpdotenv.sh',
            'rm phpdotenv.sh',

            "sed -i.bak \"s/.*'server'.*/    'server' => getenv('DB_HOST'),/\" {$mappings->config}/db.php",
            "sed -i.bak \"s/.*'database'.*/    'database' => getenv('DB_NAME'),/\" {$mappings->config}/db.php",
            "sed -i.bak \"s/.*'user'.*/    'user' => getenv('DB_USER'),/\" {$mappings->config}/db.php",
            "sed -i.bak \"s/.*'password'.*/    'password' => getenv('DB_PASS'),/\" {$mappings->config}/db.php",

            "rm {$mappings->config}/db.php.bak",
        ]);
    }

    /**
     * Replace path names in the Craft index file.
     *
     * @param object $mappings An object of directory mappings
     */
    protected function replacePaths($mappings)
    {
        $commands = [];
        foreach (self::$pathConstants as $key => $constant) {
            if (isset($mappings->$key)) {
                $path = str_replace('/', '\/', "../{$mappings->$key}/");
                $commands[] = "sed -i.bak \"s/.*'$constant'.*/define('$constant', '$path');/\" public/index.php";
            }
        }

        $this->runCommands(array_merge($commands, ['rm public/index.php.bak']));
    }
}
