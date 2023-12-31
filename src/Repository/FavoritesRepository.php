<?php

namespace App\Repository;

use App\Entity\Favorites;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorites>
 *
 * @method Favorites|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favorites|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favorites[]    findAll()
 * @method Favorites[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoritesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorites::class);
    }

    public function deletePostFromFavorites($postId, $userId): int
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'DELETE App\Entity\Favorites f
            WHERE f.post= :postId AND f.user= :userId'
        )->setParameters(['postId' => $postId, 'userId' => $userId]);

        return $query->getResult();
    }

    public function findByUserAndPostId($postId, $userId): int
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(f.id) FROM App\Entity\Favorites f
            WHERE f.post= :postId AND f.user= :userId'
        )->setParameters(['postId' => $postId, 'userId' => $userId]);
        return $query->getSingleScalarResult();
    }

    public function findByUserId($userId): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT p.title, p.id AS postId FROM App\Entity\Favorites f JOIN f.post p
            WHERE f.user= :userId'
        )->setParameters(['userId' => $userId]);
        return $query->getResult();
    }

//    /**
//     * @return Favorites[] Returns an array of Favorites objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Favorites
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
