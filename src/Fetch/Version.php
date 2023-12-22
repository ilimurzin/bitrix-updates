<?php

declare(strict_types=1);

namespace App\Fetch;

final readonly class Version
{
    public function __construct(
        public string $number,
        public string $date,
        public string $description,
    ) {
    }
}
