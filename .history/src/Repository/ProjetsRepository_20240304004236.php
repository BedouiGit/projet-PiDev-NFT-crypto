<?php

namespace App\Repository;

use App\Entity\Projets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Projets>
 *
 * @method Projets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projets[]    findAll()
 * @method Projets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projets::class);
    }

//    /**
//     * @return Projets[] Returns an array of Projets objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Projets
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findByCategoryWithPagination($categoryId, $currentPage = 1, $perPage = 4)
{
    $qb = $this->createQueryBuilder('p');
    $qb->andWhere('p.category = :categoryId')
        ->setParameter('categoryId', $categoryId)
        ->setFirstResult(($currentPage - 1) * $perPage)
        ->setMaxResults($perPage);

    return $qb->getQuery()->getResult();
}


public function search($searchTerm,$currentPage = 1, $perPage = 4)
{
    $qb = $this->createQueryBuilder('u');
    if ($searchTerm) {
        $qb->where('u.firstname LIKE :searchTerm OR u.lastname LIKE :searchTerm OR u.email LIKE :searchTerm')
           ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    $query = $qb->getQuery()
        ->setFirstResult(($currentPage - 1) * $perPage)
        ->setMaxResults($perPage);

    return new Paginator($query, true);
}
}
