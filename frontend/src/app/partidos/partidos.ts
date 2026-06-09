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
  private cdr = inject(ChangeDetectorRef); // <-- 1. INYECTAMOS EL DETECTOR DE CAMBIOS

  // Generamos un array del 1 al 38 automáticamente
  jornadas = Array.from({ length: 40 }, (_, i) => i + 1);
  jornadaActiva = 1; // He puesto que empiece en la 1 por defecto
  partidos: Partido[] = [];
  cargando = false;
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

        // Mapeamos los datos con la nueva lógica dictatorial
        this.partidos = datos.map((p: any) => {
          
          // Comprobamos estrictamente que los goles no sean ni null ni undefined
          const partidoJugado = p.local_goles !== null && p.local_goles !== undefined;

          return {
            ...p,
            equipoLocal: p.local || p.equipoLocal,
            equipoVisitante: p.visitante || p.equipoVisitante,
            golesLocal: p.local_goles,
            golesVisitante: p.visitante_goles,
            
            // Si hay goles es FINALIZADO, si está vacío (null) es PENDIENTE garantizado
            estado: partidoJugado ? 'FINALIZADO' : 'PENDIENTE'
          };
        });
        
        console.log('4. Array mapeado listo para pintar:', this.partidos);
        this.cargando = false;
        
        this.cdr.detectChanges(); // <-- ¡OBLIGAMOS A ANGULAR A PINTAR LA PANTALLA!
      },
      error: (err) => {
        console.error('ERROR CAPTURADO:', err);
        this.error = 'Error al cargar los partidos';
        this.cargando = false;
        
        this.cdr.detectChanges(); // <-- También lo ponemos por si hay error
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
    const jugador = prompt('Nombre del jugador estrella:');
    if (jugador) {
      partido.jugadorEstrella = jugador;
    }
  }

  getIniciales(nombre: string): string {
    if (!nombre) return '??';
    return nombre
      .split(' ')
      .map((n) => n[0])
      .join('')
      .substring(0, 2)
      .toUpperCase();
  }

  getColorEquipo(nombre: string): string {
    if (!nombre) return '#ccc';
    const colores = [
      '#F5C518', '#4CAF50', '#2196F3', '#E91E63',
      '#FF5722', '#9C27B0', '#00BCD4', '#FF9800',
    ];
    let hash = 0;
    for (let i = 0; i < nombre.length; i++) {
      hash = nombre.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colores[Math.abs(hash) % colores.length];
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