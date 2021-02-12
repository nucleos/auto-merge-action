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
use Nucleos\AutoMergeAction\Domain\Head;
use PHPUnit\Framework\TestCase;

final class HeadTest extends TestCase
{
    public function testThrowsExceptionIfResponseIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Head::fromResponse([]);
    }

    public function testThrowsExceptionIfResponseArrayDoesNotContainKeyRef(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Head::fromResponse([
            'foo'  => 'bar',
            'sha'  => 'sha',
        ]);
    }

    public function testThrowsExceptionIfResponseArrayContainKeyRefButEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Head::fromResponse([
            'ref'  => '',
            'sha'  => 'sha',
        ]);
    }

    public function testThrowsExceptionIfResponseArrayDoesNotContainKeySha(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Head::fromResponse([
            'ref'  => 'ref',
            'foo'  => 'bar',
        ]);
    }

    public function testThrowsExceptionIfResponseArrayContainKeyShaButEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Head::fromResponse([
            'ref'  => 'ref',
            'sha'  => '',
        ]);
    }

    public function testValid(): void
    {
        $response = [
            'ref'  => $ref = 'foo',
            'sha'  => $sha = 'sha',
        ];

        $head = Head::fromResponse($response);

        static::assertSame($ref, $head->ref());
        static::assertSame($sha, $head->sha()->toString());
    }
}
