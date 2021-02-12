<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Tests\Client;

use Ergebnis\Test\Util\Helper;
use Generator;
use InvalidArgumentException;
use Nucleos\AutoMergeAction\Client\PullRequest\Query;
use Nucleos\AutoMergeAction\Domain\Label;
use Nucleos\AutoMergeAction\Domain\Repository;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    use Helper;

    public function testThrowsExceptionIfValueIsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Query::fromString('');
    }

    /**
     * @dataProvider lengthGreaterThan256Characters()
     */
    public function testThrowsExceptionIfValueIsGreaterThan256Characters(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        Query::fromString($value);
    }

    public function testFromString(): void
    {
        $query = Query::fromString('abc');

        static::assertSame('abc', $query->toString());
    }

    public function testLabeled(): void
    {
        $query = Query::labeled(
            Repository::fromString('acme/repository'),
            Label::fromString('merge-label')
        );

        static::assertSame('repo:acme/repository type:pr is:open label:merge-label', $query->toString());
    }

    public function testLabeledWithIgnoreLabel(): void
    {
        $query = Query::labeled(
            Repository::fromString('acme/repository'),
            Label::fromString('merge-label'),
            Label::fromString('not-this-label')
        );

        static::assertSame('repo:acme/repository type:pr is:open label:merge-label -label:not-this-label', $query->toString());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function lengthGreaterThan256Characters(): Generator
    {
        yield 'string-longer-than-256-characters' => [self::stringWithLength(257)];
    }

    private static function stringWithLength(int $length): string
    {
        $faker = self::faker();

        return str_pad(
            substr(
                $faker->sentence,
                0,
                $length
            ),
            $length,
            $faker->randomLetter
        );
    }
}
