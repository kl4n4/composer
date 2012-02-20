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

use Composer\Json\JsonFile;
use Composer\Util\ProcessExecutor;
use Composer\IO\IOInterface;

/**
 * @author Per Bernhardt <plb@webfactory.de>
 */
class HgDriver extends VcsDriver implements VcsDriverInterface
{
    protected $tags;
    protected $branches;
    protected $rootIdentifier;
    protected $infoCache = array();

    public function __construct($url, IOInterface $io, ProcessExecutor $process = null)
    {
        $this->tmpDir = sys_get_temp_dir() . '/composer-' . preg_replace('{[^a-z0-9]}i', '-', $url) . '/';

        parent::__construct($url, $io, $process);
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $url = escapeshellarg($this->url);
        $tmpDir = escapeshellarg($this->tmpDir);
        if (is_dir($this->tmpDir)) {
            $this->process->execute(sprintf('cd %s && hg pull -u', $tmpDir), $output);
        } else {
            $this->process->execute(sprintf('cd %s && hg clone %s %s', escapeshellarg(sys_get_temp_dir()), $url, $tmpDir), $output);
        }

        $this->getTags();
        $this->getBranches();
    }

    /**
     * {@inheritDoc}
     */
    public function getRootIdentifier()
    {
        $tmpDir = escapeshellarg($this->tmpDir);
        if (null === $this->rootIdentifier) {
            $this->process->execute(sprintf('cd %s && hg tip --template "{node}"', $tmpDir), $output);
            $output = $this->process->splitLines($output);
            $this->rootIdentifier = $output[0];
        }

        return $this->rootIdentifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritDoc}
     */
    public function getSource($identifier)
    {
        $label = array_search($identifier, (array)$this->tags) ? : $identifier;

        return array('type' => 'hg', 'url' => $this->getUrl(), 'reference' => $label);
    }

    /**
     * {@inheritDoc}
     */
    public function getDist($identifier)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getComposerInformation($identifier)
    {
        if (!isset($this->infoCache[$identifier])) {
            $this->process->execute(sprintf('cd %s && hg cat -r %s composer.json', escapeshellarg($this->tmpDir), escapeshellarg($identifier)), $composer);

            if (!trim($composer)) {
                throw new \UnexpectedValueException('Failed to retrieve composer information for identifier ' . $identifier . ' in ' . $this->getUrl());
            }

            $composer = JsonFile::parseJson($composer);

            if (!isset($composer['time'])) {
                $this->process->execute(sprintf('cd %s && hg log --template "{date|rfc822date}" -r %s', escapeshellarg($this->tmpDir), escapeshellarg($identifier)), $output);
                $date = new \DateTime(trim($output));
                $composer['time'] = $date->format('Y-m-d H:i:s');
            }
            $this->infoCache[$identifier] = $composer;
        }

        return $this->infoCache[$identifier];
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        if (null === $this->tags) {
            $tags = array();

            $this->process->execute(sprintf('cd %s && hg tags', escapeshellarg($this->tmpDir)), $output);
            foreach ($this->process->splitLines($output) as $tag) {
                if ($tag && preg_match('(^([^\s]+)\s+\d+:(.*)$)', $tag, $match)) {
                    $tags[$match[1]] = $match[2];
                }
            }

            $this->tags = $tags;
        }

        return $this->tags;
    }

    /**
     * {@inheritDoc}
     */
    public function getBranches()
    {
        if (null === $this->branches) {
            $branches = array();

            $this->process->execute(sprintf('cd %s && hg branches', escapeshellarg($this->tmpDir)), $output);
            foreach ($this->process->splitLines($output) as $branch) {
                if ($branch && preg_match('(^([^\s]+)\s+\d+:(.*)$)', $branch, $match)) {
                    $branches[$match[1]] = $match[2];
                }
            }

            $this->branches = $branches;
        }

        return $this->branches;
    }

    /**
     * {@inheritDoc}
     */
    public function hasComposerFile($identifier)
    {
        try {
            $this->getComposerInformation($identifier);
            return true;
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public static function supports($url, $deep = false)
    {
        if (preg_match('#(^(?:https?|ssh)://(?:[^@]@)?bitbucket.org|https://(?:.*?)\.kilnhg.com)#i', $url)) {
            return true;
        }

        if (!$deep) {
            return false;
        }

        $processExecutor = new ProcessExecutor();
        $exit = $processExecutor->execute(sprintf('cd %s && hg identify %s', escapeshellarg(sys_get_temp_dir()), escapeshellarg($url)), $ignored);
        return $exit === 0;
    }
}
