import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';
@Injectable({
  providedIn: 'root',
})
export class EquiposService {
  
  private http = inject(HttpClient);
  private equiposUrl = `${environment.apiUrl}/equipos`
  private jugadoresUrl = `${environment.apiUrl}/jugadores/equipo`


  obtenerEquipos():Observable<any[]> {
    return this.http.get<any[]>(this.equiposUrl);
  }

  obtenerJugadoresPorEquipo(id: number): Observable<any> { 
      return this.http.get<any>(`${this.jugadoresUrl}/${id}`);
  }
  
}
