<?php

namespace App\Repository;

use App\Entity\Bank;
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
                    ->andWhere('f.type = :type')
                    ->andWhere('f.category = :category')
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
                ->andWhere('f.type = :type')
                ->setParameters(new ArrayCollection([
                    new Parameter('property', $property),
                    new Parameter('year', '%'.$year.'%'),
                    new Parameter('type', TransactionEnum::INCOME),
                ]))
                ->getQuery()
                ->getResult()
            ;
            }
        }
        

        public function findMortgageByPropertyAndYear(Property $property, int $year): ?array
        {
            return $this->createQueryBuilder('f')
            ->where('f.property = :property')
            ->andWhere('f.paidAt LIKE :year')
            ->andWhere('f.type = :type')
            ->andWhere('f.category = :category')
            ->setParameters(new ArrayCollection([
                new Parameter('property', $property),
                new Parameter('year', '%'.$year.'%'),
                new Parameter('type', TransactionEnum::EXPENSE),
                new Parameter('category', FinancialCategoryEnum::MORTAGE),
            ]))
            ->getQuery()
            ->getResult()
        ;
        }

        /**
         * Retourne toutes les entrées financières (validée/payée) pour une date de début et une date de fin
         * @param \App\Entity\Bank $bank
         * @param mixed $dateFrom
         * @param mixed $dateEnded
         * @param mixed $type
         * @param \App\Entity\Property|null $property
         * @return mixed
         */
        public function findSumAmountBetweenTwoDates(Bank $bank, $dateFrom, $dateEnded, TransactionEnum $type, Property $property = null): ?float
        {
            $queryBuilder = $this->createQueryBuilder('f');
        
            $queryBuilder
                ->select('SUM(f.amount) as totalAmount')
                ->where('f.paidAt BETWEEN :dateFrom AND :dateEnded')
                ->andWhere('f.type = :type')
                ->andWhere('f.bank = :bank')
                ->andWhere('f.isPaid = :ispaid')
                ->setParameter('dateFrom', $dateFrom)
                ->setParameter('dateEnded', $dateEnded)
                ->setParameter('type', $type)
                ->setParameter('bank', $bank)
                ->setParameter('ispaid', true);
        
            // Si besoin besoin de filtrer par propriété
            if ($property !== null) {
                $queryBuilder->andWhere('f.property = :property')
                    ->setParameter('property', $property);
            }
        
            return $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        }
        

        public function findByPropertiesAndCategoriesAndTypes(?Property $property, array $categories, ?TransactionEnum $type)
        {
            $qb = $this->createQueryBuilder('fe');
        
            // Filtre sur la propriété
            if ($property) {
                $qb->andWhere('fe.property = :property')
                   ->setParameter('property', $property);
            }
        
            // Filtre sur les catégories
            if (!empty($categories)) {
                $qb->andWhere('fe.category IN (:categories)')
                   ->setParameter('categories', $categories);
            }
        
            // Filtre sur le type
            if ($type) {
                $qb->andWhere('fe.type = :type')
                   ->setParameter('type', $type);
            }
        
            return $qb->getQuery();
        }

        
}
