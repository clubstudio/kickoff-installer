#Kickoff Installer

Kick start the development of a new website or web application. Hit the ground running with pre-configured Gulp tasks, linter configurations, a sensible directory structure and default meta files.

##Installation
The Kickoff Installer requires [Composer](https://getcomposer.org/). Once you have Composer installed, require the installer as a global dependency:

`composer global require "club/kickoff-installer"`

Make sure that you've added the composer bin directory (`~/.composer/vendor/bin` or similar) to your PATH, otherwise your system will not be able to find the installer.

A quick Google search should help you find out how to add to your PATH. If you use fish shell it's as easy as running `set --universal fish_user_paths $fish_user_paths ~/.composer/vendor/bin` in your terminal.

##Framework Support

The installer currently supports [CraftCMS](http://craftcms.com) only, but is built so we can add support for additional frameworks/platforms without much fuss.

##Kickoff + CraftCMS Usage Example

Create a new project directory and navigate into it:

e.g. ```
$ mkdir my-new-site && cd my-new-site
```

then run:

```
$ kickoff new craft
```

This will download and install [Kickoff](https://github.com/clubstudioltd/kickoff), followed by CraftCMS and then configure the default CraftCMS install to work with [Kickoff](https://github.com/clubstudioltd/kickoff) – including updating the default directory structure.

###Clean Installs

If you would rather emit Kickoff and get you a clean install of the framework/platform you can use the `--clean` option.

e.g.
```
$ kickoff new craft --clean
```

##Credits
Inspired by Laravel and the Laravel Installer.

##Roadmap
* Add support for a skeleton Symfony application
