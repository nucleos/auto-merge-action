<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Tests\Domain;

use InvalidArgumentException;
use Nucleos\AutoMergeAction\Domain\Repository;
use PHPUnit\Framework\TestCase;

final class RepositoryTest extends TestCase
{
    public function testThrowsExceptionIfValueIsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Repository::fromString('');
    }

    public function testThrowsExceptionIfValueDoesNotContainSlash(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Repository::fromString('foo');
    }

    public function testThrowsExceptionIfValueContainSlashButAtEnd(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Repository::fromString('foo/');
    }

    public function testThrowsExceptionIfValueContainSlashButAtStart(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Repository::fromString('/foo');
    }

    public function testFromString(): void
    {
        $repository = Repository::fromString('nucleos/NucleosUserBundle');

        static::assertSame('nucleos', $repository->username());
        static::assertSame('NucleosUserBundle', $repository->name());
        static::assertSame('nucleos/NucleosUserBundle', $repository->toString());
    }

    public function testFromUrl(): void
    {
        $repository = Repository::fromUrl('https://api.github.com/repos/nucleos/NucleosShariffBundle');

        static::assertSame('nucleos', $repository->username());
        static::assertSame('NucleosShariffBundle', $repository->name());
        static::assertSame('nucleos/NucleosShariffBundle', $repository->toString());
    }
}
