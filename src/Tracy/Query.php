<?php

declare(strict_types=1);

namespace Skaut\SkautisNette\Tracy;

/**
 * One finished skautIS call as shown in the Tracy panel.
 */
final readonly class Query
{
    /**
     * @param array<int|string, mixed>         $args
     * @param array<int, array<string, mixed>> $trace
     */
    public function __construct(
        public string $name,
        public array $args,
        public float $duration,
        public mixed $result,
        public ?string $error,
        public array $trace,
    ) {
    }
}
