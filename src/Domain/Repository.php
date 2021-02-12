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
final class Repository
{
    private string $username;
    private string $name;

    private function __construct(string $username, string $name)
    {
        Assert::stringNotEmpty($username);

        Assert::stringNotEmpty($name);

        $this->username = $username;
        $this->name     = $name;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromUrl(string $url): self
    {
        Assert::contains($url, '/');
        Assert::notEndsWith($url, '/');

        [$name, $username, ] = array_reverse(u($url)->split('/'));

        return new self(
            $username->toString(),
            $name->toString()
        );
    }

    public static function fromString(string $repository): self
    {
        Assert::contains($repository, '/');
        Assert::notEndsWith($repository, '/');

        [$username, $name] = u($repository)->split('/');

        return new self(
            $username->toString(),
            $name->toString()
        );
    }

    public function username(): string
    {
        return $this->username;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return sprintf(
            '%s/%s',
            $this->username,
            $this->name
        );
    }
}
