<?php

declare(strict_types=1);

namespace App\Store;

use App\Entity\Module;
use App\Entity\SentVersion;
use App\Entity\Version;
use App\Fetch\ModuleVersions;
use Doctrine\ORM\EntityManagerInterface;

final class Storage
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param ModuleVersions[] $modulesVersions
     */
    public function store(array $modulesVersions): void
    {
        $modules = $this->entityManager->getRepository(Module::class)->findAll();
        $modules = array_combine(
            array_map(fn(Module $module) => $module->getCode(), $modules),
            $modules
        );

        foreach ($modulesVersions as $moduleVersions) {
            if (isset($modules[$moduleVersions->moduleCode])) {
                $module = $modules[$moduleVersions->moduleCode];
            } else {
                $module = new Module();
                $module->setTitle($moduleVersions->moduleTitle);
                $module->setCode($moduleVersions->moduleCode);

                $this->entityManager->persist($module);
            }

            $versions = $module->getVersions()->getValues();
            $versions = array_combine(
                array_map(fn(Version $version) => $version->getNumber(), $versions),
                $versions
            );

            foreach ($moduleVersions->versions as $moduleVersion) {
                if (!isset($versions[$moduleVersion->number])) {
                    $date = \DateTimeImmutable::createFromFormat('Y-m-d', $moduleVersion->date);

                    if ($date === false) {
                        throw new \RuntimeException("Wrong date in $moduleVersion->number version");
                    }

                    $version = new Version();
                    $version->setModule($module);
                    $version->setNumber($moduleVersion->number);
                    $version->setDate($date);
                    $version->setDescription($moduleVersion->description);

                    $this->entityManager->persist($version);
                }
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @return Version[]
     */
    public function getUnsent(): array
    {
        return $this->entityManager->createQuery(
            <<<'DQL'
SELECT version
FROM App\Entity\Version version
WHERE NOT EXISTS (SELECT sentVersion FROM App\Entity\SentVersion sentVersion WHERE sentVersion.version = version.id)
ORDER BY version.id
DQL
        )->getResult();
    }

    public function markAsSent(Version $version): void
    {
        $sentVersion = new SentVersion();
        $sentVersion->setVersion($version);
        $sentVersion->setSent(new \DateTimeImmutable());

        $this->entityManager->persist($sentVersion);
        $this->entityManager->flush();
        $this->entityManager->detach($sentVersion);
    }

    public function markAllAsSent(): int
    {
        $sql = <<<'SQL'
INSERT INTO sent_version (version_id, sent)
SELECT id, ?
FROM version
WHERE id NOT IN (SELECT version_id FROM sent_version)
SQL;

        $statement = $this->entityManager->getConnection()->prepare($sql);
        $statement->bindValue(1, new \DateTimeImmutable(), 'datetime');

        return $statement->executeStatement();
    }
}
