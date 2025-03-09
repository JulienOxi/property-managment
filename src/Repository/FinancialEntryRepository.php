<?php

namespace App\Repository;

use App\Entity\Bank;
use App\Entity\User;
use App\Entity\Property;
use App\Enum\AccessRoleEnum;
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
        public function findBetweenTwoDates(Property $property, $dateFrom, $dateEnded, $type, $category): ?array 
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
                ->getResult()
            ;
        }

        /**
         * Retourne toutes les entrée financière pour une propriété et un année donnée (loyer ou charge)
         * Summary of findEntryByPropertyAndYear
         * @param \App\Entity\Property $property
         * @param int $year
         * @param bool $deposit //true pour les charges, false pour les loyers
         * @param mixed $isPaid //null pour toutes les entrées, true pour les entrées payées, false pour les entrées non payées
         * @return mixed
         */
        public function findEntryByPropertyAndYear(Property $property, int $year, bool $deposit = false, $isPaid = null): ?array
        {
            $queryBuilder = $this->createQueryBuilder('f');

            $queryBuilder
                ->where('f.property = :property')
                ->andWhere('f.paidAt LIKE :year')
                ->setParameter('property', $property)
                ->setParameter('year', '%'.$year.'%');

            if ($deposit) {
                $queryBuilder
                    ->andWhere('f.type = :type')
                    ->andWhere('f.category = :category')
                    ->setParameter('type', TransactionEnum::EXPENSE)
                    ->setParameter('category', FinancialCategoryEnum::CHARGES_DEPOSIT);
            }else{
                $queryBuilder
                    ->andWhere('f.type = :type')
                    ->setParameter('type', TransactionEnum::INCOME)
                    ;
            }

            if ($isPaid !== null) {
                $queryBuilder
                    ->andWhere('f.isPaid = :isPaid')
                    ->setParameter('isPaid', $isPaid);
            }

            return $queryBuilder
                ->getQuery()
                ->getResult();
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
                new Parameter('category', FinancialCategoryEnum::MORTGAGE),
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

        public function findByPropertiesAndCategoriesAndTypes(
            User $user,
            AccessRoleEnum $requiredRole,
            array $property = [],
            array $categories = [],
            ?TransactionEnum $type = null,
            int $limit = null
        ) {
            $qb = $this->createQueryBuilder('fe')
                ->join('fe.property', 'p') // Associe la propriété à l'entité financière
                ->join('p.accessControls', 'ac') // Associe les droits d'accès
                ->where('ac.grantedUser = :user')
                ->andWhere('ac.role IN (:roles)')
                ->orderBy('fe.createdAt', 'DESC')
                ->setParameter('user', $user)
                ->setParameter('roles', $this->getRolesHierarchy($requiredRole));
            
            // Ajout du filtre par propriété
            if ($property) {
                $qb->andWhere('fe.property IN (:property)')
                   ->setParameter('property', $property);
            }
            
            // Ajout du filtre par catégories
            if (!empty($categories)) {
                $qb->andWhere('fe.category IN (:categories)')
                   ->setParameter('categories', $categories);
            }
            
            // Ajout du filtre par type
            if ($type) {
                $qb->andWhere('fe.type = :type')
                   ->setParameter('type', $type);
            }

            if ($limit) {
                $qb->setMaxResults($limit);
            }
            
            return $qb->getQuery()->getResult();
        }
        
        private function getRolesHierarchy(AccessRoleEnum $requiredRole): array
        {
            // Si un role GUEST est defini on prétent que un role MEMBER et un ROLE OWNER auront aussi l'accès
            return match ($requiredRole) {
                AccessRoleEnum::OWNER => [AccessRoleEnum::OWNER->value],
                AccessRoleEnum::MEMBER => [AccessRoleEnum::MEMBER->value, AccessRoleEnum::GUEST->value],
                AccessRoleEnum::GUEST => [AccessRoleEnum::OWNER->value, AccessRoleEnum::MEMBER->value, AccessRoleEnum::GUEST->value],
            };
        }

        public function getUnpaidRents(Property $property): ?float
        {
            return $this->createQueryBuilder('f')
                ->select('COALESCE(SUM(f.amount), 0) as totalAmount')
                ->where('f.property = :property')
                ->andWhere('f.type = :type')
                ->andWhere('f.isPaid = false')
                ->setParameter('property', $property)
                ->setParameter('type', TransactionEnum::INCOME)
                ->getQuery()
                ->getSingleScalarResult();
        }
        
}
