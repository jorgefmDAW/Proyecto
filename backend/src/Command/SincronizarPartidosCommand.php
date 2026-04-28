<?php

namespace App\Command;

use App\Entity\Equipo;
use App\Entity\Jornada;
use App\Entity\Partido; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sincronizar-partidos',
    description: 'Carga y sincroniza los partidos a la base de datos mediante el partidosdata.json',
)]
class SincronizarPartidosCommand extends Command
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
        
        $rutaArchivo = __DIR__ . '/../../partidosdata.json';

        if (!file_exists($rutaArchivo)) {
            $io->error(sprintf('¡ERROR! -> No se encontró el archivo en: %s', $rutaArchivo));
            return Command::FAILURE;
        }

        $contenido = file_get_contents($rutaArchivo);
        $datosCalendario = json_decode($contenido, true);

        if (!is_array($datosCalendario)) {
            $io->error('¡ERROR! -> El archivo no tiene un formato JSON válido.');
            return Command::FAILURE;
        }

        $io->info('Sincronizando partidos con las entidades Equipo y Jornada...');
        $io->progressStart(count($datosCalendario)); // inicia la barra de progreso teniendo de valor maximo el numero de elementos del json

        $partidoRepo = $this->entityManager->getRepository(Partido::class);
        $jornadaRepo = $this->entityManager->getRepository(Jornada::class);

        foreach ($datosCalendario as $jornadaData) {
            $jornadaNum = (int) $jornadaData['jornada'];
            
            // busca si la jornada existe, si no existe la crea
            $jornadaObj = $jornadaRepo->find($jornadaNum);
            if (!$jornadaObj) {
                $jornadaObj = new Jornada();
                $jornadaObj->setId($jornadaNum); 
                
                // se guarda en memoria y se cargan en la base de datos las jornadas (antes que los partidos)
                $this->entityManager->persist($jornadaObj);
                $this->entityManager->flush(); 
            }

            $partidos = $jornadaData['partidos'];

            foreach ($partidos as $partidoJson) {
                $idLocal = (int) $partidoJson['local'];
                $idVisitante = (int) $partidoJson['visitante'];
                
                // referencia equipos local y visitante
                $refLocal = $this->entityManager->getReference(Equipo::class, $idLocal);
                $refVisitante = $this->entityManager->getReference(Equipo::class, $idVisitante);

                // busca si el partido existe en esa jornada
                $partido = $partidoRepo->findOneBy([
                    'local' => $refLocal,
                    'visitante' => $refVisitante,
                    'jornada' => $jornadaObj
                ]);

                // si no existe el partido lo crea
                if (!$partido) {
                    $partido = new Partido();
                    $partido->setLocalId($refLocal);
                    $partido->setVisitanteId($refVisitante);
                    $partido->setJornada($jornadaObj);
                    
                    $this->entityManager->persist($partido);
                }
                
                // declara las variables de goles de local y visitante (si en el json los goles son "" significa que aún no se ha jugado el partido y se pone un 0 por defecto)
                $golesLocal = is_numeric($partidoJson['goles_local']) ? (int) $partidoJson['goles_local'] : 0;
                $golesVisitante = is_numeric($partidoJson['goles_visitante']) ? (int) $partidoJson['goles_visitante'] : 0;

                // inserta o actualiza los goles
                $partido->setLocalGoles($golesLocal);
                $partido->setVisitanteGoles($golesVisitante);

                // inserta el dia del partido | en el json se llama fecha
                if (!empty($partidoJson['fecha'])) { 
                    $partido->setDia($partidoJson['fecha']); 
                }

                // inserta la hora
                if (!empty($partidoJson['hora'])) { 
                    try {
                        $horaObj = \DateTime::createFromFormat('H:i', trim($partidoJson['hora']));
                        if ($horaObj) {
                            $partido->setHora($horaObj); 
                        }
                    } catch (\Exception $e) {
                        // si el formato no es correcto no aparecerá ningun error
                    }
                }
            }

            // se guardan todos los registros en la base de datos y se limpian de la memoria
            $this->entityManager->flush();
            $this->entityManager->clear();
            
            $io->progressAdvance(); // avanza la barra de progreso en cada vuelta de bucle
        }

        $io->progressFinish(); // finaliza la barra de progreso
        $io->success('¡Todas las jornadas y partidos se han sincronizado perfectamente con sus relaciones!'); // mensaje de exito

        return Command::SUCCESS;
    }
}