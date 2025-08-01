<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    // Example custom query method
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findPaginatedTask(int $page = 1, int $limit = 10): array{
        $offset = ($page - 1) *$limit;
        return $this->createQueryBuilder('t')
        ->orderBy('t.id', 'ASC')
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    }

    public function countAllTask(): int{
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}