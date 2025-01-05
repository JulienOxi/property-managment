<?php

namespace App\Repository;

use App\Entity\Property;
use App\Enum\TransactionEnum;
use App\Entity\FinancialEntry;
use Doctrine\ORM\Query\Parameter;
use App\Enum\FinancialCategoryEnum;
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
        public function findOneBetweenTwoDates(Property $property, $dateFrom, $dateEnded, $type, $category): ?FinancialEntry
        {
            return $this->createQueryBuilder('f')
                ->where('f.paidAt BETWEEN :dateFrom AND :dateEnded')
                ->andWhere('f.property = :property')
                ->andwhere('f.type = :type')
                ->andWhere('f.category = :category')
                ->setParameters(new ArrayCollection([
                    new Parameter('property', $property),   
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
         * Retourne toutes les entrée financière pour une propriété et un année donnée (loyer ou charge)
         * Summary of findEntryByPropertyAndYear
         * @param \App\Entity\Property $property
         * @param int $year
         * @param bool $deposit //true pour les charges, false pour les loyers
         * @return mixed
         */
        public function findEntryByPropertyAndYear(Property $property, int $year, bool $deposit = false): ?array
        {
            if ($deposit) {
                return $this->createQueryBuilder('f')
                    ->where('f.property = :property')
                    ->andWhere('f.paidAt LIKE :year')
                    ->andWhere('f.type LIKE :type')
                    ->andWhere('f.category LIKE :category')
                    ->setParameters(new ArrayCollection([
                        new Parameter('property', $property),
                        new Parameter('year', '%'.$year.'%'),
                        new Parameter('type', TransactionEnum::EXPENSE),
                        new Parameter('category', FinancialCategoryEnum::CHARGES_DEPOSIT),
                    ]))
                    ->getQuery()
                    ->getResult()
                ;
            }else{
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

        
}
