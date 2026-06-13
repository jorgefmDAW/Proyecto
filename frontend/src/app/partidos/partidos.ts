import { Component, OnInit, inject, ChangeDetectorRef, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PartidosService } from '../services/partidos-service';

interface Partido {
  id: number;
  equipoLocal: string;
  equipoVisitante: string;
  estado: 'PENDIENTE' | 'EN_CURSO' | 'FINALIZADO';
  golesLocal?: number;
  golesVisitante?: number;
  jornada: number;
  jugadorEstrella?: string;
  prediccionUsuario?: 'local' | 'visitante' | 'empate';
  escudo_local: any;
  escudo_visitante: any;
  id_local: number;
  id_visitante: number;
}

@Component({
  selector: 'app-partidos',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './partidos.html',
  styleUrls: ['./partidos.css'],
})
export class Partidos implements OnInit {
  private partidosService = inject(PartidosService);
  private cdr = inject(ChangeDetectorRef); 

  // Variable para el dropdown personalizado de las jornadas
  public menuJornadasAbierto = signal(false);

  jornadas = Array.from({ length: 40 }, (_, i) => i + 1);
  jornadaActiva = 1; 
  partidos: Partido[] = [];
  cargando = false;
  mostrarModalJugadores = false;
  jugadoresModal: any[] = [];
  cargandoJugadores = false;
  partidoSeleccionado: Partido | null = null;

  ngOnInit(): void {
    this.cargarPartidos(this.jornadaActiva);
  }

  seleccionarJornada(jornada: number): void {
    this.jornadaActiva = jornada;
    this.cargarPartidos(jornada);
  }

  cargarPartidos(jornada: number): void {
    this.cargando = true;

    this.partidosService.obtenerPartidosPorJornada(jornada).subscribe({
      next: (response: any) => {
        const datos = response.partidos ? response.partidos : response;

        this.partidos = datos.map((p: any) => {
          const partidoJugado = p.local_goles !== null && p.local_goles !== undefined;

          return {
            ...p,
            equipoLocal: p.local || p.equipoLocal,
            equipoVisitante: p.visitante || p.equipoVisitante,
            escudo_local: p.escudo_local, 
            escudo_visitante: p.escudo_visitante,
            golesLocal: p.local_goles,
            golesVisitante: p.visitante_goles,
            id_local: p.id_local,
            id_visitante: p.id_visitante,
            estado: partidoJugado ? 'FINALIZADO' : 'PENDIENTE'
          };
        });
        
        this.cargando = false;
        this.cdr.detectChanges(); 
      },
      error: (err) => {
        console.error('Error al cargar los partidos:', err);
        this.cargando = false;
        this.cdr.detectChanges(); 
      }
    });
  }

  predecirGanador(partido: Partido, equipo: 'local' | 'visitante' | 'empate'): void {
    partido.prediccionUsuario = equipo;
  }

  elegirJugadorEstrella(partido: Partido): void {
    console.log('Objeto partido:', partido);
    this.partidoSeleccionado = partido;
    this.mostrarModalJugadores = true;
    this.cargandoJugadores = true;
    this.jugadoresModal = [];

    this.partidosService.obtenerJugadoresPorEquipos(partido.id_local, partido.id_visitante).subscribe({
      next: (response: any) => {
        this.jugadoresModal = [
          ...response.jugadores_equipo_1, 
          ...response.jugadores_equipo_2
        ]; 
        
        this.cargandoJugadores = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error cargando los jugadores:', err);
        this.cargandoJugadores = false;
        this.cdr.detectChanges();
      }
    });
  }

  seleccionarEstrella(jugador: any): void {
    if (this.partidoSeleccionado) {
      this.partidoSeleccionado.jugadorEstrella = jugador.nombre; 
    }
    this.cerrarModal();
  }

  cerrarModal(): void {
    this.mostrarModalJugadores = false;
    this.partidoSeleccionado = null;
    this.jugadoresModal = [];
  }

  getEstadoLabel(estado: string): string {
    const labels: Record<string, string> = {
      PENDIENTE: 'PENDIENTE',
      EN_CURSO: 'EN CURSO',
      FINALIZADO: 'FINALIZADO',
    };
    return labels[estado] ?? estado;
  }
}