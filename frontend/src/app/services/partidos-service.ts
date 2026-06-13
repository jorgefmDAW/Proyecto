import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
@Injectable({
  providedIn: 'root',
})
export class PartidosService {
  
  private http = inject(HttpClient);
  private partidosUrl = 'http://localhost:8000/api/partidos'
  private JugadoresPartidosUrl = 'http://localhost:8000/api/jugadores'


  obtenerPartidosPorJornada(jornada: number):Observable<any[]> {
    return this.http.get<any[]>(`${this.partidosUrl}/${jornada}`);
  }
  
  obtenerTodosPartidos(): Observable<any[]> {
    return this.http.get<any[]>(this.partidosUrl);
  }

  obtenerJugadoresPorEquipos( equipo1: number, equipo2: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.JugadoresPartidosUrl}/equipos/${equipo1}/${equipo2}`);
  }
}
