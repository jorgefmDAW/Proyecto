<?php

namespace App\Command;

use App\Entity\Equipo;
use App\Entity\Jornada;
use App\Entity\Jugador;
use App\Entity\Puntuacion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sincronizar-jugadores',
    description: 'Carga y sincroniza los jugadores y sus puntos a la base de datos mediante el playersdata.json',
)]
class SincronizarJugadoresCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $rutaArchivo = __DIR__ . '/../../playersdata.json';

        if (!file_exists($rutaArchivo)) {
            $io->error(sprintf('¡ERROR! -> No se encontró el archivo en: %s', $rutaArchivo));
            return Command::FAILURE;
        }

        $contenido = file_get_contents($rutaArchivo);
        $jugadores = json_decode($contenido, true);

        if (!is_array($jugadores)) {
            $io->error('¡ERROR! -> El archivo no tiene un formato JSON válido.');
            return Command::FAILURE;
        }

        $io->title('Sincronizando jugadores y puntuaciones de forma inteligente...');
        $io->progressStart(count($jugadores)); // inicia la barra de progreso teniendo de valor maximo el numero de elementos del json

        $jugadorRepo = $this->entityManager->getRepository(Jugador::class);
        $puntuacionRepo = $this->entityManager->getRepository(Puntuacion::class);

        $limite_registros = 50; 
        $i = 0;

        foreach ($jugadores as $datosJugador) {
            $nombreJugador = $datosJugador[0];
            $posicion = $datosJugador[1];
            $equipoId = (int) $datosJugador[2];
            $edad = (int) $datosJugador[3];
            $nacionalidad = $datosJugador[4];
            $arrayPuntos = $datosJugador[5];

            // comprueba si el jugador existe
            $jugador = $jugadorRepo->findOneBy(['nombre' => $nombreJugador]);

            // si no existe se crea con los datos del json
            if (!$jugador) {
                $jugador = new Jugador();
                $jugador->setNombre($nombreJugador);
                
                $referenciaEquipo = $this->entityManager->getReference(Equipo::class, $equipoId);
                $jugador->setEquipo($referenciaEquipo);
                
                $this->entityManager->persist($jugador);
            }
            
            // actualiza la posicion, edad y nacionalidad por si acaso cambian o son nuevos
            $jugador->setPosicion($posicion);
            $jugador->setEdad($edad);
            $jugador->setNacionalidad($nacionalidad);

            // guarda los puntos por cada jornada
            foreach ($arrayPuntos as $indice => $puntos) {
                $jornadaNum = $indice + 1;
                $puntosInt = (int) $puntos; 

                // referencia a la entidad Jornada
                $referenciaJornada = $this->entityManager->getReference(Jornada::class, $jornadaNum);

                // se busca si el jugador tiene puntos en todas las jornadas
                $puntuacion = $puntuacionRepo->findOneBy([
                    'jugador' => $jugador,
                    'jornada' => $referenciaJornada 
                ]);

                // si en una jornada no tiene puntos se le añaden
                if (!$puntuacion) {
                    $puntuacion = new Puntuacion();
                    $puntuacion->setJugador($jugador);
                    $puntuacion->setJornada($referenciaJornada);
                    $puntuacion->setPuntos($puntosInt);
                    
                    $this->entityManager->persist($puntuacion);
                }
                // si ya tiene puntos en la jornada se comprueba si el valor ha cambiado y en ese caso se actualiza 
                else {
                    if ($puntuacion->getPuntos() !== $puntosInt) {
                        $puntuacion->setPuntos($puntosInt);
                    }
                }
            }
            
            $io->progressAdvance(); // avanza la barra de progreso en cada vuelta del bucle
            $i++; // se va incrementando el contador de registros para insertar o actualizar guardados en memoria

            // cada 50 registros se insertan o actualizan y se eliminan de la memoria (rendimiento)
            if (($i % $limite_registros) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear(); 
            }
        }

        // se insertan o actualizan los registros que faltan
        $this->entityManager->flush();
        $this->entityManager->clear();
        
        $io->progressFinish(); // finaliza la barra de progreso
        $io->success(sprintf('¡ÉXITO! -> Se han revisado y sincronizado de forma segura %d jugadores.', count($jugadores)));

        return Command::SUCCESS;
    }
}