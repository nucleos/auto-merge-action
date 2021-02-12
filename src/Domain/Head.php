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
final class Head
{
    private string $ref;
    private Sha $sha;

    private function __construct(string $ref, Sha $sha)
    {
        Assert::stringNotEmpty($ref);

        $this->ref  = $ref;
        $this->sha  = $sha;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function fromResponse(array $config): self
    {
        Assert::notEmpty($config);

        Assert::keyExists($config, 'ref');
        Assert::stringNotEmpty($config['ref']);

        Assert::keyExists($config, 'sha');
        Assert::stringNotEmpty($config['sha']);

        return new self(
            $config['ref'],
            Sha::fromString($config['sha'])
        );
    }

    public function ref(): string
    {
        return $this->ref;
    }

    public function sha(): Sha
    {
        return $this->sha;
    }

    public function toString(): string
    {
        return $this->ref;
    }
}
