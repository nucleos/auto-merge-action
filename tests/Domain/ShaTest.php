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

use Ergebnis\Test\Util\Helper;
use InvalidArgumentException;
use Nucleos\AutoMergeAction\Domain\Sha;
use PHPUnit\Framework\TestCase;

final class ShaTest extends TestCase
{
    use Helper;

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function testThrowsExceptionFor(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        Sha::fromString($value);
    }

    public function testValid(): void
    {
        $value = self::faker()->sha256;

        $sha = Sha::fromString($value);

        static::assertSame($value, $sha->toString());
    }
}
