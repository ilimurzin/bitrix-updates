<?php

declare(strict_types=1);

namespace App\Fetch;

final readonly class ModuleVersions
{
    public function __construct(
        public string $moduleCode,
        public string $moduleTitle,
        /** @var Version[] */
        public array $versions,
    ) {
    }
}
