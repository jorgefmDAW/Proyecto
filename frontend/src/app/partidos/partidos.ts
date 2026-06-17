import { Component, OnInit, inject, ChangeDetectorRef, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PartidosService } from '../services/partidos-service';
import { LigasService } from '../services/ligas-service';
import { Users } from '../services/users-service';

interface Partido {
  id: number;
  equipoLocal: string;
  equipoVisitante: string;
  estado: 'PENDIENTE' | 'EN_CURSO' | 'FINALIZADO';
  golesLocal?: number;
  golesVisitante?: number;
  jornada: number;
  
  // Datos del jugador estrella
  jugadorEstrella?: string;
  jugadorEstrellaFoto?: string;
  jugadorEstrellaPosicion?: string;
  
  prediccionUsuario?: 'local' | 'visitante' | 'empate';
  escudo_local: any;
  escudo_visitante: any;
  id_local: number;
  id_visitante: number;
  aciertoEquipo?: boolean;
  puntosObtenidosTotales?: number;
  puntosJugador?: number;
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
  private ligasService = inject(LigasService);
  private usersService = inject(Users);
  private cdr = inject(ChangeDetectorRef);

  public menuJornadasAbierto = signal(false);
  jornadas = Array.from({ length: 40 }, (_, i) => i + 1);
  jornadaActiva = 1;
  partidos: Partido[] = [];
  cargando = false;
  
  // Variables del modal
  mostrarModalJugadores = false;
  cargandoJugadores = false;
  partidoSeleccionado: Partido | null = null;
  jugadoresEquipoLocal: any[] = [];
  jugadoresEquipoVisitante: any[] = [];
  
  esAdmin = false;

  ngOnInit(): void {
    this.usersService.usuarioActual().subscribe({
      next: () => {
        this.esAdmin = this.usersService.isAdmin();
        this.cdr.detectChanges();
      }
    });

    this.cargarPartidos(this.jornadaActiva);
  }

  private get ligaId(): number | null {
    return this.ligasService.ligaActiva()?.id ?? null;
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
            jornada: p.jornada || this.jornadaActiva,
            equipoLocal: p.local || p.equipoLocal,
            equipoVisitante: p.visitante || p.equipoVisitante,
            escudo_local: p.escudo_local,
            escudo_visitante: p.escudo_visitante,
            golesLocal: p.local_goles,
            golesVisitante: p.visitante_goles,
            id_local: p.id_local,
            id_visitante: p.id_visitante,
            estado: partidoJugado ? 'FINALIZADO' : 'PENDIENTE',
          };
        });

        this.cargarElecciones();
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error al cargar los partidos:', err);
        this.cargando = false;
        this.cdr.detectChanges();
      },
    });
  }

  private cargarElecciones(): void {
    const ligaId = this.ligaId;
    if (!ligaId) return;

    this.partidosService.obtenerEleccionesPorJornada(this.jornadaActiva, ligaId).subscribe({
      next: (response: any) => {
        const elecciones: any[] = response.elecciones ?? [];
        this.partidos = this.partidos.map((partido) => {
          const eleccion = elecciones.find((e: any) => e.partido_id === partido.id);
          if (!eleccion) return partido;

          let prediccion: 'local' | 'visitante' | 'empate' | undefined;
          if (eleccion.equipo_elegido === partido.equipoLocal) {
            prediccion = 'local';
          } else if (eleccion.equipo_elegido === partido.equipoVisitante) {
            prediccion = 'visitante';
          } else if (eleccion.equipo_elegido === 'empate') {
            prediccion = 'empate';
          }

          let aciertoEquipo = false;
          let puntosEquipo = 0;

          if (partido.estado === 'FINALIZADO' && prediccion) {
            const gL = partido.golesLocal ?? 0;
            const gV = partido.golesVisitante ?? 0;
            const ganadorReal = gL > gV ? 'local' : (gV > gL ? 'visitante' : 'empate');

            if (prediccion === ganadorReal) {
              aciertoEquipo = true;
              puntosEquipo = 5;
            }
          }

          const puntosTotales = eleccion.puntos_obtenidos ?? 0;
          const puntosJugador = Math.max(0, puntosTotales - puntosEquipo);

          return {
            ...partido,
            prediccionUsuario: prediccion,
            
            // Recogemos foto y posición del backend también
            jugadorEstrella: eleccion.jugador_elegido ?? partido.jugadorEstrella,
            jugadorEstrellaFoto: eleccion.jugador_foto ?? partido.jugadorEstrellaFoto,
            jugadorEstrellaPosicion: eleccion.jugador_posicion ?? partido.jugadorEstrellaPosicion,
            
            aciertoEquipo: aciertoEquipo,
            puntosObtenidosTotales: puntosTotales,
            puntosJugador: puntosJugador
          };
        });
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error al cargar elecciones:', err);
      },
    });
  }

  puedeInteractuar(partido: Partido): boolean {
    return partido.estado !== 'FINALIZADO' || this.esAdmin;
  }

  predecirGanador(partido: Partido, opcion: 'local' | 'visitante' | 'empate'): void {
    const ligaId = this.ligaId;
    if (!ligaId) return;

    const equipoId = opcion === 'local' ? partido.id_local : opcion === 'visitante' ? partido.id_visitante : null;

    const guardar = equipoId !== null
      ? this.partidosService.elegirEquipo(partido.id, ligaId, equipoId)
      : this.partidosService.elegirEmpate(partido.id, ligaId);

    guardar.subscribe({
      next: () => {
        partido.prediccionUsuario = opcion;

        if (partido.estado === 'FINALIZADO') {
          const gL = partido.golesLocal ?? 0;
          const gV = partido.golesVisitante ?? 0;
          const ganadorReal = gL > gV ? 'local' : (gV > gL ? 'visitante' : 'empate');
          
          partido.aciertoEquipo = (opcion === ganadorReal);
          this.cargarElecciones();
        }

        if (this.esAdmin && partido.estado === 'FINALIZADO') {
          this.partidosService.calcularPuntos(partido.jornada, ligaId).subscribe();
        }
        
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error al guardar prediccion:', err);
      },
    });
  }

  elegirJugadorEstrella(partido: Partido): void {
    this.partidoSeleccionado = partido;
    this.mostrarModalJugadores = true;
    this.cargandoJugadores = true;
    
    this.jugadoresEquipoLocal = [];
    this.jugadoresEquipoVisitante = [];

    this.partidosService.obtenerJugadoresPorEquipos(partido.id_local, partido.id_visitante).subscribe({
      next: (response: any) => {
        this.jugadoresEquipoLocal = response.jugadores_equipo_1 || [];
        this.jugadoresEquipoVisitante = response.jugadores_equipo_2 || [];
        
        this.cargandoJugadores = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error cargando los jugadores:', err);
        this.cargandoJugadores = false;
        this.cdr.detectChanges();
      },
    });
  }

  seleccionarEstrella(jugador: any): void {
    const partido = this.partidoSeleccionado;
    const ligaId = this.ligaId;

    if (!partido || !ligaId) {
      this.cerrarModal();
      return;
    }

    this.partidosService.elegirJugador(partido.id, ligaId, jugador.id).subscribe({
      next: () => {
        // Actualizamos en vivo la foto, el nombre y la posición
        partido.jugadorEstrella = jugador.nombre;
        partido.jugadorEstrellaFoto = jugador.foto;
        partido.jugadorEstrellaPosicion = jugador.posicion;
        
        this.cerrarModal();

        if (partido.estado === 'FINALIZADO') {
          this.cargarElecciones();

          if (this.esAdmin) {
            this.partidosService.calcularPuntos(partido.jornada, ligaId).subscribe();
          }
        }

        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error al guardar jugador estrella:', err);
        this.cerrarModal();
      },
    });
  }

  cerrarModal(): void {
    this.mostrarModalJugadores = false;
    this.partidoSeleccionado = null;
    this.jugadoresEquipoLocal = [];
    this.jugadoresEquipoVisitante = [];
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