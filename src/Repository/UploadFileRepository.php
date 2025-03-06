<?php

namespace App\Repository;

use App\Entity\UploadFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UploadFile>
 */
class UploadFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadFile::class);
    }

    //    /**
    //     * @return UploadFile[] Returns an array of UploadFile objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findFilesWithoutEntity($property, $type, $entityClass = 'App\Entity\FinancialEntry'): ?array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.property = :property')
            ->andWhere('u.type = :type')
            ->andWhere('u.entityClass = :entityClass')
            ->setParameter('property', $property)
            ->setParameter('type', $type)
            ->setParameter('entityClass', $entityClass)
            ->getQuery()
            ->getResult()
        ;
    }
}
