<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class UploadFileService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getLoadedEntity(string $entityClass, int $entityId): ?object
    {
        return $this->entityManager->getRepository($entityClass)->find($entityId);
    }
}
