import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
@Injectable({
  providedIn: 'root',
})
export class ClasificacionService {
  
  private http = inject(HttpClient);
  private jugadoresTop10Url = 'http://localhost:8000/api/jugadores/top10'

  obtenerJugadoresTop10(): Observable<any> { 
      return this.http.get<any>(this.jugadoresTop10Url);
  }
  
}
