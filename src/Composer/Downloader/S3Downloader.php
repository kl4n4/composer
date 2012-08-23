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

namespace Composer\Downloader;

/**
 * Downloader for tar files stored in S3 buckets: tar, tar.gz or tar.bz2
 *
 * @author Thomas kl4n4 Klaner
 */
class S3Downloader extends TarDownloader
{

    /**
     * {@inheritDoc}
     */
    public function download(PackageInterface $package, $path)
    {

    }
}
