import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class PartidosService {
  private http = inject(HttpClient);
  private partidosUrl = 'http://localhost:8000/api/partidos';
  private jugadoresUrl = 'http://localhost:8000/api/jugadores';
  private eleccionesUrl = 'http://localhost:8000/api/elecciones';

  obtenerPartidosPorJornada(jornada: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.partidosUrl}/${jornada}`);
  }

  obtenerTodosPartidos(): Observable<any[]> {
    return this.http.get<any[]>(this.partidosUrl);
  }

  obtenerJugadoresPorEquipos(equipo1: number, equipo2: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.jugadoresUrl}/equipos/${equipo1}/${equipo2}`);
  }

  elegirEquipo(partidoId: number, ligaId: number, equipoId: number): Observable<any> {
    return this.http.patch<any>(
      `${this.eleccionesUrl}/partido/${partidoId}/liga/${ligaId}/equipo`,
      { equipo_id: equipoId }
    );
  }

  elegirEmpate(partidoId: number, ligaId: number): Observable<any> {
    return this.http.patch<any>(
      `${this.eleccionesUrl}/partido/${partidoId}/liga/${ligaId}/equipo`,
      { equipo_id: null, empate: true }
    );
  }

  elegirJugador(partidoId: number, ligaId: number, jugadorId: number): Observable<any> {
    return this.http.patch<any>(
      `${this.eleccionesUrl}/partido/${partidoId}/liga/${ligaId}/jugador`,
      { jugador_id: jugadorId }
    );
  }

  obtenerEleccionesPorJornada(jornadaId: number, ligaId: number): Observable<any> {
    return this.http.get<any>(
      `${this.eleccionesUrl}/jornada/${jornadaId}/liga/${ligaId}`
    );
  }

  calcularPuntos(jornadaId: number, ligaId: number): Observable<any> {
    return this.http.post<any>(
      `${this.eleccionesUrl}/calcular-puntos/jornada/${jornadaId}/liga/${ligaId}`,
      {}
    );
  }
}