<?php

namespace App\Repository;

use App\Entity\CartContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartContent>
 *
 * @method CartContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartContent[]    findAll()
 * @method CartContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartContent::class);
    }

    //    /**
    //     * @return CartContent[] Returns an array of CartContent objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CartContent
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
