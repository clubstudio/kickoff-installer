<?php

namespace Club\KickoffInstaller\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;

abstract class InstallationCommand extends Command
{
    /**
     * Available installers.
     *
     * @return array An array of available installer class names
     */
    protected function installers()
    {
        return [
            'CraftCms' => \Club\KickoffInstaller\Installers\CraftCms\Installer::class,
        ];
    }

    /**
     * Get the full class name of an installer.
     *
     * @param string $framework Framework short-name
     *
     * @return string Installer class name
     */
    protected function getInstallerClass($framework)
    {
        $installers = $this->installers();

        if (!array_key_exists($framework, $installers)) {
            throw new InvalidArgumentException("An installer for `$framework` does not exist.");
        }

        return $installers[$framework];
    }
}
