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
final class Label
{
    private string $name;

    private function __construct(string $name)
    {
        Assert::stringNotEmpty($name);

        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromString(string $name): self
    {
        Assert::notEmpty($name);

        return new self(
            $name
        );
    }

    /**
     * @param array{name: string} $response
     */
    public static function fromResponse(array $response): self
    {
        Assert::notEmpty($response);

        Assert::keyExists($response, 'name');

        return new self(
            $response['name']
        );
    }

    public function equals(self $other): bool
    {
        return u($this->name)->ignoreCase()->equalsTo($other->name())
            ;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->name;
    }
}
