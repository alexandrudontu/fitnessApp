<?php

namespace App\Repository;

use App\Entity\Exercise;
use App\Entity\MuscleGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercise>
 */
class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    public function create(Exercise $exercise)
    {
        $this->getEntityManager()->persist($exercise);
        $this->getEntityManager()->flush();
    }

    public function delete($id){
        $existingExercise = $this->find($id);
        if(!is_null($existingExercise)){
            $this->getEntityManager()->remove($existingExercise);
            $this->getEntityManager()->flush();
        }
    }

    public function update(Exercise $exercise)
    {
        $this->getEntityManager()->flush();
    }

    public function findByName(string $name): ?Exercise
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findByMuscleGroup(string $muscleGroup): array
    {
        return $this->findBy(['muscleGroup' => $muscleGroup]);
    }

    public function findByNameExcludingId(string $name, int $id): ?Exercise
    {
        return $this->createQueryBuilder('e')
            ->where('e.name = :name')
            ->andWhere('e.id != :id')
            ->setParameter('name', $name)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Exercise[] Returns an array of Exercise objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Exercise
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByWorkout($id)
    {
        return $this->findBy(['id' => $id]);
    }
}
