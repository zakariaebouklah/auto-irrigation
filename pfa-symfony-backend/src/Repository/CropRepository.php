<?php

namespace App\Repository;

use App\Entity\Crop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Crop>
 *
 * @method Crop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Crop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Crop[]    findAll()
 * @method Crop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CropRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Crop::class);
    }

    public function save(Crop $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Crop $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findStandardCrops(): array
    {
        return $this->createQueryBuilder('c')
                    ->where('c.owner = null')
                    ->getQuery()
                    ->getResult();
    }

//    /**
//     * @return Crop[] Returns an array of Crop objects
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

//    public function findOneBySomeField($value): ?Crop
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
