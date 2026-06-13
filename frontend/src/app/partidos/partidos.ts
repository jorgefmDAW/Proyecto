<<<<<<< HEAD
import { Component, OnInit, inject, ChangeDetectorRef } from '@angular/core';
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

  local_id: number;
  visitante_id: number;
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

  jornadas = Array.from({ length: 40 }, (_, i) => i + 1);
  jornadaActiva = 1; 
  partidos: Partido[] = [];
  cargando = false;
  mostrarModalJugadores = false;
  jugadoresModal: any[] = [];
  cargandoJugadores = false;
  partidoSeleccionado: Partido | null = null;
  error: string | null = null;

  ngOnInit(): void {
    this.cargarPartidos(this.jornadaActiva);
  }

  seleccionarJornada(jornada: number): void {
    this.jornadaActiva = jornada;
    this.cargarPartidos(jornada);
  }

  cargarPartidos(jornada: number): void {
    console.log('1. Pidiendo partidos de la jornada:', jornada);
    this.cargando = true;
    this.error = null;

    this.partidosService.obtenerPartidosPorJornada(jornada).subscribe({
      next: (response: any) => {
        console.log('2. ¡Datos recibidos del backend!', response);
        
        const datos = response.partidos ? response.partidos : response;
        console.log('3. Array de partidos extraído:', datos);

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
            local_id: p.local_id,
            visitante_id: p.visitante_id,
            
            estado: partidoJugado ? 'FINALIZADO' : 'PENDIENTE'
          };
        });
        
        console.log('4. Array mapeado listo para pintar:', this.partidos);
        this.cargando = false;
        
        this.cdr.detectChanges(); 
      },
      error: (err) => {
        console.error('ERROR CAPTURADO:', err);
        this.error = 'Error al cargar los partidos';
        this.cargando = false;
        
        this.cdr.detectChanges(); 
      },
      complete: () => {
        console.log('5. La petición ha terminado al 100%');
      }
    });
  }

  predecirGanador(partido: Partido, equipo: 'local' | 'visitante' | 'empate'): void {
    partido.prediccionUsuario = equipo;
  }

elegirJugadorEstrella(partido: Partido): void {
  this.partidoSeleccionado = partido;
  this.mostrarModalJugadores = true;
  this.cargandoJugadores = true;
  this.jugadoresModal = [];

  this.partidosService.obtenerJugadoresPorEquipos(partido.local_id, partido.visitante_id).subscribe({
    next: (jugadores: any) => {
      
      this.jugadoresModal = jugadores; 
      this.cargandoJugadores = false;
      this.cdr.detectChanges();
    },
    error: (err) => {
      console.error('Error cargando los jugadores', err);
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
=======
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
  local_id: number;
  visitante_id: number;
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

  jornadas = Array.from({ length: 38 }, (_, i) => i + 1);
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
            local_id: p.local_id,
            visitante_id: p.visitante_id,
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
    this.partidoSeleccionado = partido;
    this.mostrarModalJugadores = true;
    this.cargandoJugadores = true;
    this.jugadoresModal = [];

    this.partidosService.obtenerJugadoresPorEquipos(partido.local_id, partido.visitante_id).subscribe({
      next: (jugadores: any) => {
        this.jugadoresModal = jugadores; 
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
>>>>>>> main
}