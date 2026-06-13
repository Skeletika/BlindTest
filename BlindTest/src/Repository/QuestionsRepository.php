<?php

namespace App\Repository;

use App\Entity\Questions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Questions>
 */
class QuestionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Questions::class);
    }

    //    /**
    //     * @return Questions[] Returns an array of Questions objects
    //     */
    public function findAllQuestions(): array
    {
        return $this->createQueryBuilder('q')
            ->select('q', 'c', 'cl', 'a')
            ->leftJoin('q.categorie', 'c')
            ->leftJoin('q.answer', 'a')
            ->leftJoin('q.clue', 'cl')
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllQuestionWithCategories(array $categories): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.categorie IN (:val)')
            ->setParameter('val', $categories)
            ->orderBy('q.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function takeQuestions(int $id): array
    {
        return $this->createQueryBuilder('q')
            ->select('q', 'c', 'cl', 'a')
            ->leftJoin('q.categorie', 'c')
            ->leftJoin('q.answer', 'a')
            ->leftJoin('q.clue', 'cl')
            ->where('q.id = (:id)')
            ->setParameter('id', $id)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }
    //    public function findOneBySomeField($value): ?Questions
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
