#Kickoff Installer

Kick start the development of a new website or web application. Hit the ground running with pre-configured Gulp tasks, linter configurations, a sensible directory structure and default meta files.

##Framework Support

The installer currently supports [CraftCMS](http://craftcms.com) only, but is built so we can add support for additional frameworks/platforms without much fuss.

##Example

```
$ kickoff new craft
```

This will download and install [Kickoff](https://github.com/clubstudioltd/kickoff), followed by CraftCMS and then configure the default CraftCMS install to work with [Kickoff](https://github.com/clubstudioltd/kickoff).

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
