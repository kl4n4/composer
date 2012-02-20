<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Repository\Vcs;

use Composer\IO\IOInterface;
use Composer\Util\ProcessExecutor;
use Composer\Util\RemoteFilesystem;

/**
 * A driver implementation for driver with authorization interaction.
 *
 * @author François Pluchino <francois.pluchino@opendisplay.com>
 */
abstract class VcsDriver
{
    protected $url;
    protected $io;
    protected $process;

    /**
     * Constructor.
     *
     * @param string      $url The URL
     * @param IOInterface $io  The IO instance
     * @param ProcessExecutor $process  Process instance, injectable for mocking
     */
    public function __construct($url, IOInterface $io, ProcessExecutor $process = null)
    {
        $this->url = $url;
        $this->io = $io;
        $this->process = $process ?: new ProcessExecutor;
    }

	/**
	 * Dirty way to support authorization to be set in vcs config
	 * @author Thomas Klaner
	 * @param string VCS username
	 * @param string VCS password
	 * @return void
	 */
    public function setAuthorization($username, $password)
    {
		$this->io->setAuthorization($this->url, $username, $password);
    }

    /**
     * Get the https or http protocol depending on SSL support.
     *
     * Call this only if you know that the server supports both.
     *
     * @return string The correct type of protocol
     */
    protected function getScheme()
    {
        if (extension_loaded('openssl')) {
            return 'https';
        }
        return 'http';
    }

    /**
     * Get the remote content.
     *
     * @param string $url The URL of content
     *
     * @return mixed The result
     */
    protected function getContents($url)
    {
        $rfs = new RemoteFilesystem($this->io);
        return $rfs->getContents($this->url, $url, false);
    }

    protected static function isLocalUrl($url)
    {
        return (Boolean) preg_match('{^(file://|/|[a-z]:[\\\\/])}i', $url);
    }
}
