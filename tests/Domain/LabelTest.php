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

use Generator;
use InvalidArgumentException;
use Nucleos\AutoMergeAction\Domain\Label;
use PHPUnit\Framework\TestCase;

final class LabelTest extends TestCase
{
    public function testThrowsExceptionIfNameIsEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Label::fromString('');
    }

    public function testValid(): void
    {
        $label = Label::fromString('Test');

        static::assertSame('Test', $label->name());
    }

    /**
     * @dataProvider equalsProvider
     */
    public function testEquals(bool $expected, Label $label, Label $other): void
    {
        static::assertSame(
            $expected,
            $label->equals($other)
        );
    }

    /**
     * @phpstan-return Generator<array{bool, Label, Label}>
     */
    public function equalsProvider(): Generator
    {
        yield 'equal, because of name and case insensitive' => [
            true,
            Label::fromResponse([
                'name' => 'Foo',
            ]),
            Label::fromResponse([
                'name' => 'foo',
            ]),
        ];
    }
}
