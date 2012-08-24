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
    protected static $awsAccessKey = '';
    protected static $awsSecretKey = '';

    /**
     * {@inheritDoc}
     */
    public function download(PackageInterface $package, $path)
    {
        // FileDownloader::download()
        $url = $package->getDistUrl();
        if (!$url) {
            throw new \InvalidArgumentException('The given package is missing url information');
        }

        $this->filesystem->ensureDirectoryExists($path);

        $fileName = $this->getFileName($package, $path);

        $this->io->write("  - Installing <info>" . $package->getName() . "</info> (<comment>" . VersionParser::formatVersion($package) . "</comment>)");

        //$processUrl = $this->processUrl($url);

        try {
            //$this->rfs->copy($package->getSourceUrl(), $processUrl, $fileName);
            $this->io->write("    Downloading: <comment>loading...</comment>", false);
            $s3 = new S3($this->awsAccessKey, $this->awsSecretKey);
            $s3->getObject($bucketName, $uploadName, $fileName);
            $this->io->overwrite("    Downloading: <comment>100%</comment>");

            if (!file_exists($fileName)) {
                throw new \UnexpectedValueException($url.' could not be saved to '.$fileName.', make sure the'
                    .' directory is writable and you have internet connectivity');
            }

            $checksum = $package->getDistSha1Checksum();
            if ($checksum && hash_file('sha1', $fileName) !== $checksum) {
                throw new \UnexpectedValueException('The checksum verification of the file failed (downloaded from '.$url.')');
            }
        } catch (\Exception $e) {
            // clean up
            $this->filesystem->removeDirectory($path);
            throw $e;
        }

        // ArchiveDownloader::download()
        //$fileName = $this->getFileName($package, $path);
        if ($this->io->isVerbose()) {
            $this->io->write('    Unpacking archive');
        }
        try {
            $this->extract($fileName, $path);

            if ($this->io->isVerbose()) {
                $this->io->write('    Cleaning up');
            }
            unlink($fileName);

            // If we have only a one dir inside it suppose to be a package itself
            $contentDir = glob($path . '/*');
            if (1 === count($contentDir)) {
                $contentDir = $contentDir[0];

                if (is_file($contentDir)) {
                    $this->filesystem->rename($contentDir, $path . '/' . basename($contentDir));
                } else {
                    // Rename the content directory to avoid error when moving up
                    // a child folder with the same name
                    $temporaryName = md5(time().rand());
                    $this->filesystem->rename($contentDir, $temporaryName);
                    $contentDir = $temporaryName;

                    foreach (array_merge(glob($contentDir . '/.*'), glob($contentDir . '/*')) as $file) {
                        if (trim(basename($file), '.')) {
                            $this->filesystem->rename($file, $path . '/' . basename($file));
                        }
                    }

                    rmdir($contentDir);
                }
            }
        } catch (\Exception $e) {
            // clean up
            $this->filesystem->removeDirectory($path);
            throw $e;
        }

        $this->io->write('');
    }
}
