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

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Webmozart\Assert\Assert;

/**
 * @psalm-immutable
 */
final class PullRequest
{
    private Issue $issue;
    private string $title;
    private DateTimeImmutable $updatedAt;
    private Head $head;
    private ?bool $mergeable;
    private string $mergeableState;
    private string $htmlUrl;

    /**
     * @var Label[]
     */
    private array $labels;

    /**
     * @param Label[] $labels
     */
    private function __construct(
        Issue $issue,
        string $title,
        string $updatedAt,
        Head $head,
        ?bool $mergeable,
        string $mergeableState,
        string $htmlUrl,
        array $labels
    ) {
        Assert::stringNotEmpty($title);

        Assert::stringNotEmpty($updatedAt);

        Assert::stringNotEmpty($mergeableState);

        Assert::stringNotEmpty($htmlUrl);

        $this->issue     = $issue;
        $this->title     = $title;
        $this->updatedAt = new DateTimeImmutable(
            $updatedAt,
            new DateTimeZone('UTC')
        );

        $this->head           = $head;
        $this->mergeable      = $mergeable;
        $this->mergeableState = $mergeableState;
        $this->htmlUrl        = $htmlUrl;
        $this->labels         = $labels;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param array<string, mixed> $response
     */
    public static function fromResponse(array $response): self
    {
        Assert::notEmpty($response);

        Assert::keyExists($response, 'number');

        Assert::keyExists($response, 'title');
        Assert::stringNotEmpty($response['title']);

        Assert::keyExists($response, 'updated_at');
        Assert::stringNotEmpty($response['updated_at']);

        Assert::keyExists($response, 'merged_at');
        Assert::nullOrStringNotEmpty($response['merged_at']);

        Assert::keyExists($response, 'base');
        Assert::notEmpty($response['base']);

        Assert::keyExists($response, 'head');
        Assert::notEmpty($response['head']);

        Assert::keyExists($response, 'mergeable');
        Assert::nullOrBoolean($response['mergeable']);

        Assert::keyExists($response, 'mergeable_state');
        Assert::stringNotEmpty($response['mergeable_state']);

        Assert::keyExists($response, 'html_url');
        Assert::stringNotEmpty($response['html_url']);

        Assert::keyExists($response, 'labels');
        $labels = [];
        foreach ($response['labels'] as $label) {
            $labels[] = Label::fromResponse($label);
        }

        return new self(
            Issue::fromInt($response['number']),
            $response['title'],
            $response['updated_at'],
            Head::fromResponse($response['head']),
            $response['mergeable'],
            $response['mergeable_state'],
            $response['html_url'],
            $labels
        );
    }

    public function issue(): Issue
    {
        return $this->issue;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function head(): Head
    {
        return $this->head;
    }

    /**
     * The value of the mergeable attribute can be true, false, or null.
     * If the value is null this means that the mergeability hasn't been computed yet.
     *
     * @see: https://developer.github.com/v3/pulls/#get-a-single-pull-request
     */
    public function isMergeable(): ?bool
    {
        return $this->mergeable;
    }

    public function mergeableState(): string
    {
        return $this->mergeableState;
    }

    public function isCleanBuild(): bool
    {
        return 'clean' === $this->mergeableState;
    }

    public function htmlUrl(): string
    {
        return $this->htmlUrl;
    }

    /**
     * @return Label[]
     */
    public function labels(): array
    {
        return $this->labels;
    }

    public function hasLabels(): bool
    {
        return [] !== $this->labels;
    }

    public function updatedWithinTheLast60Seconds(): bool
    {
        $diff = (new DateTime('now', new DateTimeZone('UTC')))->getTimestamp()
            - $this->updatedAt->getTimestamp();

        return $diff < 60;
    }

    public function toString(): string
    {
        return sprintf(
            '%s: %s',
            $this->issue->toInt(),
            $this->title
        );
    }
}
