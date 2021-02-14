<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Config;

use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
final class Configuration
{
    public const DEFAULT_LABEL = 'automerge';

    public const DEFAULT_IGNORE_LABEL = 'wip';

    private string $repository;

    private string $label = self::DEFAULT_LABEL;

    private string $ignoreLabel = self::DEFAULT_IGNORE_LABEL;

    private bool $squash = false;

    private bool $dryRun = false;

    /**
     * @param array<string, mixed> $options
     */
    private function __construct(array $options)
    {
        Assert::keyExists($options, 'repository');
        Assert::stringNotEmpty($options['repository']);

        Assert::nullOrString($options['label']);

        Assert::nullOrString($options['ignore-label']);

        Assert::nullOrBoolean($options['squash']);

        Assert::nullOrBoolean($options['dry-run']);

        $this->repository   = $options['repository'];
        $this->label        = $options['label']        ?? $this->label;
        $this->ignoreLabel  = $options['ignore-label'] ?? $this->ignoreLabel;
        $this->squash       = $options['squash']       ?? $this->squash;
        $this->dryRun       = $options['dry-run']      ?? $this->dryRun;
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function fromInput(array $options): self
    {
        return new self($options);
    }

    public function repository(): string
    {
        return $this->repository;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function ignoreLabel(): string
    {
        return $this->ignoreLabel;
    }

    public function isSquash(): bool
    {
        return $this->squash;
    }

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }
}
