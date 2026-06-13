<?php

namespace App\Repository;

use App\Entity\Jugador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Jugador>
 */
class JugadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jugador::class);
    }

    public function findTop10Jugadores(?int $equipo_id = null, ?string $posicion = null): array
    {
        $qb = $this->createQueryBuilder('j')
            ->select('j.nombre', 'j.posicion', 'SUM(p.puntos) as puntos_totales')
            ->join('j.puntuacions', 'p') 
            ->groupBy('j.id')
            ->orderBy('puntos_totales', 'DESC')
            ->setMaxResults(10);
        
        if ($equipo_id) {
            $qb->andWhere('j.equipo = :equipo_id')
                ->setParameter('equipo_id', $equipo_id);
        }

        if ($posicion) {
            $qb->andWhere('j.posicion = :posicion')
                ->setParameter('posicion', $posicion);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Jugador[] Returns an array of Jugador objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('j.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Jugador
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
