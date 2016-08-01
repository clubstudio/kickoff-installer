#Kickoff Installer

Kick start the development of your new website or web application. Hit the ground running with pre-configured Gulp tasks, linter configurations, a sensible directory structure and default meta files.

##Installation
The Kickoff Installer requires the PHP package manager. Visit the [Composer website](https://getcomposer.org/) for instructions on how to install it on your system.

Once you have Composer installed, you will need to require the installer as a global dependency:

`composer global require "club/kickoff-installer"`

Make sure that you've added the composer bin directory (`~/.composer/vendor/bin` or similar) to your PATH, otherwise your system will not be able to find the installer.

A quick Google search should help you find out how to add to your PATH. If you use fish shell it's as easy as running `set --universal fish_user_paths $fish_user_paths ~/.composer/vendor/bin` in your terminal.

##Framework Support

The installer supports the following frameworks:

 * [CraftCMS](http://craftcms.com)

It is built so you can easily add support for additional frameworks/platforms without much fuss.

##Kickoff + CraftCMS Usage Example

Create a new project directory and navigate into it:

```
$ mkdir my-new-site && cd my-new-site
```

then run:

```
$ kickoff new craft
```

This will download and install [Kickoff](https://github.com/clubstudioltd/kickoff), followed by CraftCMS and then configure the default CraftCMS install to work with [Kickoff](https://github.com/clubstudioltd/kickoff) – including updating the default directory structure.

##Clean Installs

If you would rather omit Kickoff and get you a clean install of the framework/platform you can use the `--clean` option.

e.g.
```
$ kickoff new craft --clean
```

##Custom Configuration
Each framework installer has it's own configuration. If you would like to make changes to the default settings you can use the `config` command. This will generate a configuration file (`kickoff.json`) for the specified framework installation script.

An example use case would be configuring the directory structure of a CraftCMS installation. Running `kickoff config craft` would generate the necessary config file, which you can then customise to your individual needs.

Remember, you need to run `kickoff config framework-name` and make your changes before running `kickoff new framework-name`.

##Credits
Inspired by Laravel and the Laravel Installer.

##Roadmap
* Have `kickoff new craft --clean` command restructure directories if a config file is present
* Add support for a skeleton Symfony application
