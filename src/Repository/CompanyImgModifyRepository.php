<?php

namespace App\Repository;

use App\Entity\CompanyImgModify;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompanyImgModify>
 *
 * @method CompanyImgModify|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyImgModify|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyImgModify[]    findAll()
 * @method CompanyImgModify[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyImgModifyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyImgModify::class);
    }

//    /**
//     * @return CompanyImgModify[] Returns an array of CompanyImgModify objects
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

//    public function findOneBySomeField($value): ?CompanyImgModify
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
