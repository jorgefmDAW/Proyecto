import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ClasificacionService {
  private http = inject(HttpClient);
  private api = 'http://localhost:8000';

  getClasificacionLiga(ligaId: number): Observable<any> {
    return this.http.get(`${this.api}/api/ligas/clasificacion/${ligaId}`);
  }

  getTop10Jugadores(): Observable<any> {
    return this.http.get(`${this.api}/api/jugadores/top10`);
  }
}