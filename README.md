Composer - Package Management for PHP
=====================================

Composer is a package manager tracking local dependencies of your projects and libraries.

See the [about page](http://packagist.org/about) on [packagist.org](http://packagist.org/) for more information.

[![Build Status](https://secure.travis-ci.org/composer/composer.png)](http://travis-ci.org/composer/composer)

Installation / Usage
--------------------

1. Download the [`composer.phar`](http://getcomposer.org/composer.phar) executable or use the installer.

    ``` sh
    $ curl -s http://getcomposer.org/installer | php
    ```


2. Create a composer.json defining your dependencies. Note that this example is
a short version for applications that are not meant to be published as packages
themselves. To create libraries/packages please read the [guidelines](http://packagist.org/about).

    ``` json
    {
        "require": {
            "monolog/monolog": ">=1.0.0"
        }
    }
    ```

3. Run Composer: `php composer.phar install`
4. Browse for more packages on [Packagist](http://packagist.org).

Installation from Source
------------------------

To run tests, or develop Composer itself, you must use the sources and not the phar
file as described above.

1. Run `git clone https://github.com/composer/composer.git`
2. Download the [`composer.phar`](http://getcomposer.org/composer.phar) executable
3. Run Composer to get the dependencies: `php composer.phar install`

Global installation of composer (manual)
----------------------------------------

Since composer works with the current working directory it is possible to install it
in a system wide way.

1. Change into a directory in your path like `cd /usr/local/bin`
2. Get composer `curl -s http://getcomposer.org/installer | php`
3. Make the phar executeable `chmod a+x composer.phar`
4. Change into a project directory `cd /path/to/my/project`
5. Use composer as you normally would `composer.phar install`
6. Optionally you can rename the composer.phar to composer to make it easier

Global installation of composer (via homebrew)
----------------------------------------------

Installing via this homebrew formula will always get you the latest composer version.

1. run `brew uninstall composer ; brew install --HEAD https://raw.github.com/gist/1574469/composer.rb`
2. Change into a project directory `cd /path/to/my/project`
3. Use composer as you normally would `composer.phar install`

*You will see a warning "Warning: Cannot verify package integrity"; however,
this is benign and expected.*

Updating composer
-----------------

Running `php composer.phar self-update` or equivalent will update a phar
install with the latest version.

Configuration
-------------

Additional options for composer can be configured in `composer.json` by using the `config` section.

``` json
{
    "config": {
        "vendor-dir": "custom/path/for/vendor"
    }
}
```

* `vendor-dir`: The location to install vendor packages. The location can be supplied as an absolute or relative path but **must** be within the current working directory.

Contributing
------------

All code contributions - including those of people having commit access -
must go through a pull request and approved by a core developer before being
merged. This is to ensure proper review of all the code.

Fork the project, create a feature branch, and send us a pull request.

If you would like to help take a look at the [list of issues](http://github.com/composer/composer/issues).

Community
---------

The developer mailing list is on [google groups](http://groups.google.com/group/composer-dev)
IRC channels are available for discussion as well, on irc.freenode.org [#composer](irc://irc.freenode.org/composer)
for users and [#composer-dev](irc://irc.freenode.org/composer-dev) for development.

Requirements
------------

PHP 5.3+

Authors
-------

Nils Adermann - <naderman@naderman.de> - <http://twitter.com/naderman> - <http://www.naderman.de><br />
Jordi Boggiano - <j.boggiano@seld.be> - <http://twitter.com/seldaek> - <http://seld.be><br />

See also the list of [contributors](https://github.com/composer/composer/contributors) who participated in this project.

License
-------

Composer is licensed under the MIT License - see the LICENSE file for details

Acknowledgments
---------------

This project's Solver started out as a PHP port of openSUSE's [Libzypp satsolver](http://en.opensuse.org/openSUSE:Libzypp_satsolver).
