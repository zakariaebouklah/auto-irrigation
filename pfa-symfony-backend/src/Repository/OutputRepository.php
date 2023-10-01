<?php

namespace App\Repository;

use App\Entity\Farmer;
use App\Entity\Output;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Output>
 *
 * @method Output|null find($id, $lockMode = null, $lockVersion = null)
 * @method Output|null findOneBy(array $criteria, array $orderBy = null)
 * @method Output[]    findAll()
 * @method Output[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutputRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Output::class);
    }

    public function save(Output $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Output $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllSchedulesOfUser(Farmer $farmer): array
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($entityManager);

        $rsm->addScalarResult('count', 'nb_outputs_for_this_schedule');
        $rsm->addScalarResult('owner_id', 'owner_id');
        $rsm->addScalarResult('crop_id', 'crop_id');
        $rsm->addScalarResult('soil_id', 'soil_id');

        $sql = "SELECT count(*) as count , owner_id, crop_id, soil_id 
                FROM `output` 
                WHERE owner_id = :farmer_id
                GROUP BY owner_id, crop_id, soil_id;";

        $query = $entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('farmer_id', $farmer->getId(), Types::INTEGER);

        return $query->getResult();
    }

    public function getAllStepsOfASchedule(?int $f_id, int $c_id, int $s_id): array
    {
        $em = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata(Output::class, 'o');

        $sql = "SELECT * FROM output o
                WHERE owner_id = :f_id AND crop_id = :c_id AND soil_id = :s_id ";

        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter('f_id', $f_id, Types::INTEGER);
        $query->setParameter('c_id', $c_id, Types::INTEGER);
        $query->setParameter('s_id', $s_id, Types::INTEGER);

        return $query->getResult();

    }

//    /**
//     * @return Output[] Returns an array of Output objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Output
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
