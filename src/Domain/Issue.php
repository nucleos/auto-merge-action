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

use function Symfony\Component\String\u;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
final class Issue
{
    private int $value;

    private function __construct(int $value)
    {
        Assert::greaterThan($value, 0);

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return u('#')->append((string) $this->value)->toString();
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
