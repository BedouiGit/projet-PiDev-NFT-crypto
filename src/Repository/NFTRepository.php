<?php

namespace App\Repository;

use App\Entity\NFT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NFT>
 *
 * @method NFT|null find($id, $lockMode = null, $lockVersion = null)
 * @method NFT|null findOneBy(array $criteria, array $orderBy = null)
 * @method NFT[]    findAll()
 * @method NFT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NFTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NFT::class);
    }


    public function getTopNFTOwners(int $limit = 10): array
{
    $qb = $this->createQueryBuilder('n');
    $qb->select('IDENTITY(n.User) AS userId', 'COUNT(n.id) AS nftCount')
       ->groupBy('n.User')
       ->orderBy('nftCount', 'DESC')
       ->setMaxResults($limit);

    return $qb->getQuery()->getResult();
}

    
    

//    /**
//     * @return NFT[] Returns an array of NFT objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NFT
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
