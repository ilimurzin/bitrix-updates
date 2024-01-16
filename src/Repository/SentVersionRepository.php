<?php

namespace App\Repository;

use App\Entity\SentVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SentVersion>
 *
 * @method SentVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method SentVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method SentVersion[]    findAll()
 * @method SentVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SentVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SentVersion::class);
    }

//    /**
//     * @return SentVersion[] Returns an array of SentVersion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SentVersion
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
