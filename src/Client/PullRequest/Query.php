<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Client\PullRequest;

use Nucleos\AutoMergeAction\Domain\Label;
use Nucleos\AutoMergeAction\Domain\Repository;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
final class Query
{
    private string $value;

    private function __construct(string $value)
    {
        Assert::stringNotEmpty($value);

        // @see https://docs.github.com/en/free-pro-team@latest/github/searching-for-information-on-github/troubleshooting-search-queries#limitations-on-query-length
        Assert::maxLength($value, 256);

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

    public static function labeled(Repository $repository, Label $label, ?Label $excludeLabel = null): self
    {
        $query = sprintf(
            'repo:%s type:pr is:open label:%s',
            $repository->toString(),
            $label->toString()
        );

        if (null !== $excludeLabel) {
            $query .= sprintf(' -label:%s', $excludeLabel->toString());
        }

        return new self(
            $query
        );
    }

    public function toString(): string
    {
        return $this->value;
    }
}
