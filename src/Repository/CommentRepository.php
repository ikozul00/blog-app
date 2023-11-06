<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findByPostId($id): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT c.content,c.createdAt, c.id AS commentId, u.email, u.id AS userId
            FROM App\Entity\Comment c JOIN c.user u WHERE c.post= :id ORDER BY c.createdAt DESC'
        )->setParameter(key:'id', value:$id);

        return $query->getResult();
    }

    public function deleteComment($id): int
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'DELETE App\Entity\Comment c
            WHERE c.id= :id'
        )->setParameter('id', $id);

        // returns an array of Product objects
        return $query->execute();
    }

    public function findByUserId($id): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT c.content,c.createdAt, c.id AS commentId, p.title, p.id AS postId
            FROM App\Entity\Comment c JOIN c.post p WHERE c.user= :id ORDER BY c.createdAt DESC'
        )->setParameter(key:'id', value:$id);

        return $query->getResult();
    }

//    /**
//     * @return Comment[] Returns an array of Comment objects
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

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
