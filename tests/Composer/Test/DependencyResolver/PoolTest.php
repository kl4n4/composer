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

namespace Composer\Test\DependencyResolver;

use Composer\DependencyResolver\Pool;
use Composer\Repository\ArrayRepository;
use Composer\Test\TestCase;

class PoolTest extends TestCase
{
    public function testPool()
    {
        $pool = new Pool;
        $repo = new ArrayRepository;
        $package = $this->getPackage('foo', '1');

        $repo->addPackage($package);
        $pool->addRepository($repo);

        $this->assertEquals(array($package), $pool->whatProvides('foo'));
        $this->assertEquals(array($package), $pool->whatProvides('foo'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetPriorityForNotRegisteredRepository()
    {
        $pool = new Pool;
        $repository = new ArrayRepository;

        $pool->getPriority($repository);
    }

    public function testGetPriorityWhenRepositoryIsRegistered()
    {
        $pool = new Pool;
        $firstRepository = new ArrayRepository;
        $pool->addRepository($firstRepository);
        $secondRepository = new ArrayRepository;
        $pool->addRepository($secondRepository);

        $firstPriority = $pool->getPriority($firstRepository);
        $secondPriority = $pool->getPriority($secondRepository);

        $this->assertEquals(0, $firstPriority);
        $this->assertEquals(1, $secondPriority);
    }

    public function testPackageById()
    {
        $pool = new Pool;
        $repository = new ArrayRepository;
        $package = $this->getPackage('foo', '1');

        $repository->addPackage($package);
        $pool->addRepository($repository);

        $this->assertSame($package, $pool->packageById(1));
    }

    public function testWhatProvidesWhenPackageCannotBeFound()
    {
        $pool = new Pool;

        $this->assertEquals(array(), $pool->whatProvides('foo'));
    }

    public function testGetMaxId()
    {
        $pool = new Pool;
        $repository = new ArrayRepository;
        $firstPackage = $this->getPackage('foo', '1');
        $secondPackage = $this->getPackage('foo1', '1');

        $this->assertEquals(0, $pool->getMaxId());

        $repository->addPackage($firstPackage);
        $repository->addPackage($secondPackage);
        $pool->addRepository($repository);

        $this->assertEquals(2, $pool->getMaxId());
    }
}
