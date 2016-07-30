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
    protected $downloadFrom = 'http://craftcms.com/latest.zip?accept_license=yes';

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
    protected function kickoff()
    {
        $mappings = $this->config->mappings;

        $this->output->writeln('<info>Copying Stubs...</info>');
        $this->copyStub('craft/index', 'index');
        $this->copyStub('craft/phpdotenv', 'phpdotenv.sh');
        $this->output->writeln('<comment>Stubs copied!</comment>');

        $this->output->writeln('<info>Moving Craft directories...</info>');
        $this->runCommands($this->moveCraftDirsCommands($mappings));
        $this->output->writeln('<comment>Craft directories moved!</comment>');

        $this->output->writeln('<info>Linking Craft with Kickoff...</info>');
        $this->runCommands(array_merge(
            $this->updateFrontControllerCommands(),
            $this->phpDotEnvCommands($mappings),
            $this->pathReplacementCommands($mappings)
        ));
    }

    protected function moveCraftDirsCommands($mappings)
    {
        return [
            "mv tmp/craft/app ./$mappings->app",
            "mv tmp/craft/config ./$mappings->config",
            "mv tmp/craft/plugins ./$mappings->plugins",
            "mv tmp/craft/storage ./$mappings->storage",
            "mv tmp/craft/templates ./$mappings->templates",
            'mv tmp/public/index.php public/.',
            'rm public/index.html',
        ];
    }

    protected function updateFrontControllerCommands()
    {
        return [
            'tail -n +5 public/index.php >> index',
            'mv index public/index.php',
        ];
    }

    protected function phpDotEnvCommands($mappings)
    {
        return [
            'chmod +x phpdotenv.sh',
            './phpdotenv.sh',
            'rm phpdotenv.sh',

            "sed -i.bak \"s/.*'server'.*/    'server' => getenv('DB_HOST'),/\" {$mappings->config}/db.php",
            "sed -i.bak \"s/.*'database'.*/    'database' => getenv('DB_NAME'),/\" {$mappings->config}/db.php",
            "sed -i.bak \"s/.*'user'.*/    'user' => getenv('DB_USER'),/\" {$mappings->config}/db.php",
            "sed -i.bak \"s/.*'password'.*/    'password' => getenv('DB_PASS'),/\" {$mappings->config}/db.php",

            "rm {$mappings->config}/db.php.bak"
        ];
    }

    protected function pathConstants()
    {
        return [
            'config' => 'CRAFT_CONFIG_PATH',
            'plugins' => 'CRAFT_PLUGINS_PATH',
            'templates' => 'CRAFT_TEMPLATES_PATH',
            'storage' => 'CRAFT_STORAGE_PATH',
            'translations' => 'CRAFT_TRANSLATIONS_PATH',
        ];
    }

    protected function pathReplacementCommands($mappings)
    {
        $commands  = [];
        foreach ($this->pathConstants() as $key => $constant) {
            if (isset($mappings->$key)) {
                $path = str_replace('/', '\/', "../{$mappings->$key}/");
                $commands[] = "sed -i.bak \"s/.*'$constant'.*/define('$constant', '$path');/\" public/index.php";
            }
        }

        return array_merge($commands, ['rm public/index.php.bak']);
    }

    /**
     * Commands to run when Kickoff isn't installed
     *
     * @return array An array of commands
     */
    public function clean()
    {
        $this->runCommands([
            'mv tmp/craft .',
            'mv tmp/public .',
        ]);
    }

    protected function runCommands(array $commands)
    {
        return parent::runCommands(
            array_merge($commands, ['rm -rf tmp'])
        );
    }
}
