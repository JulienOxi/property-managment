<?php

namespace App\Repository;

use App\Entity\FinancialEntry;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<FinancialEntry>
 */
class FinancialEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FinancialEntry::class);
    }

       /**
        * @return FinancialEntry Returns an array of FinancialEntry objects
        */
        public function findOneBetweenTwoDates($dateFrom, $dateEnded, $type, $category): ?FinancialEntry
        {
            return $this->createQueryBuilder('f')
                ->where('f.paidAt BETWEEN :dateFrom AND :dateEnded')
                ->andwhere('f.type = :type')
                ->andWhere('f.category = :category')
                ->setParameters(new ArrayCollection([
                    new Parameter('dateFrom', $dateFrom),
                    new Parameter('dateEnded', $dateEnded),
                    new Parameter('type', $type),
                    new Parameter('category', $category),
                ]))
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }

        /**
         * Retourne toutes les entrée financière pour une propriété et un année donnée
         * @param mixed $property
         * @param mixed $year
         * @return mixed
         */
        public function findEntryByPropertyAndYear($property, $year): ?array
        {
            return $this->createQueryBuilder('f')
                ->where('f.property = :property')
                ->andWhere('f.paidAt LIKE :year')
                ->setParameters(new ArrayCollection([
                    new Parameter('property', $property),
                    new Parameter('year', '%'.$year.'%'),
                ]))
                ->getQuery()
                ->getResult()
            ;
        }
        
}
