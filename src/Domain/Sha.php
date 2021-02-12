<?php

declare(strict_types=1);

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Domain;

use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
final class Sha
{
    private string $value;

    private function __construct(string $value)
    {
        Assert::stringNotEmpty($value);

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
