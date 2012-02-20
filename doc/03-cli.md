# Command-line interface

You've already learned how to use the command-line interface to do some
things. This chapter documents all the available commands.

## init

In the [Libraries] chapter we looked at how to create a `composer.json` by
hand. There is also an `init` command available that makes it a bit easier to
do this.

When you run the command it will interactively ask you to fill in the fields,
while using some smart defaults.

    $ php composer.phar init

## install

The `install` command reads the `composer.json` file from the current
directory, resolves the dependencies, and installs them into `vendor`.

    $ php composer.phar install

If there is a `composer.lock` file in the current directory, it will use the
exact versions from there instead of resolving them. This ensures that
everyone using the library will get the same versions of the dependencies.

If there is no `composer.lock` file, composer will create one after dependency
resolution.

### Options

* **--prefer-source:** There are two ways of downloading a package: `source` and `dist`. For stable versions composer will use the `dist` by default. The `source` is a version control repository. If `--prefer-source` is enabled, composer will install from `source` if there is one. This is useful if you want to make a bugfix to a project and get a local git clone of the dependency directly.
* **--dry-run:** If you want to run through an installation without actually installing a package, you can use `--dry-run`. This will simulate the installation and show you what would happen.
* **--no-install-recommends:** By default composer will install all packages that are referenced by `recommend`. By passing this option you can disable that.
* **--install-suggests:** The packages referenced by `suggest` will not be installed by default. By passing this option, you can install them.

## update

In order to get the latest versions of the dependencies and to update the
`composer.lock` file, you should use the `update` command.

    $ php composer.phar update

This will resolve all dependencies of the project and write the exact versions
into `composer.lock`.

### Options

* **--prefer-source:** Install packages from `source` when available.
* **--dry-run:** Simulate the command without actually doing anything.
* **--no-install-recommends:** Do not install packages referenced by `recommend`.
* **--install-suggests:** Install packages referenced by `suggest`.

## search

The search command allows you to search through the current project's package
repositories. Usually this will be just packagist. You simply pass it the
terms you want to search for.

    $ php composer.phar search monolog

You can also search for more than one term by passing multiple arguments.

## show

To list all of the available packages, you can use the `show` command.

    $ php composer.phar show

If you want to see the details of a certain package, you can pass the package
name.

    $ php composer.phar show monolog/monolog

    name     : monolog/monolog
    versions : master-dev, 1.0.2, 1.0.1, 1.0.0, 1.0.0-RC1
    type     : library
    names    : monolog/monolog
    source   : [git] http://github.com/Seldaek/monolog.git 3d4e60d0cbc4b888fe5ad223d77964428b1978da
    dist     : [zip] http://github.com/Seldaek/monolog/zipball/3d4e60d0cbc4b888fe5ad223d77964428b1978da 3d4e60d0cbc4b888fe5ad223d77964428b1978da
    license  : MIT

    autoload
    psr-0
    Monolog : src/

    requires
    php >=5.3.0

You can even pass the package version, which will tell you the details of that
specific version.

    $ php composer.phar show monolog/monolog 1.0.2

### Options

* **--installed:** Will list the packages that are installed.
* **--platform:** Will list only [Platform packages].

## depends

The `depends` command tells you which other packages depend on a certain
package. You can specify which link types (`require`, `recommend`, `suggest`)
should be included in the listing.

    $ php composer.phar depends --link-type=require monolog/monolog

    nrk/monolog-fluent
    poc/poc
    propel/propel
    symfony/monolog-bridge
    symfony/symfony

### Options

* **--link-type:** The link types to match on, can be specified multiple
times.

## validate

You should always run the `validate` command before you commit your
`composer.json` file, and before you tag a release. It will check if your
`composer.json` is valid.

    $ php composer.phar validate

## self-update

To update composer itself to the latest version, just run the `self-update`
command. It will replace your `composer.phar` with the latest version.

    $ php composer.phar self-update

## help

To get more information about a certain command, just use `help`.

    $ php composer.phar help install
