import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({ providedIn: 'root' })
export class ClasificacionService {
  private http = inject(HttpClient);
  private api = `${environment.apiUrl}`;

  getClasificacionLiga(ligaId: number): Observable<any> {
    return this.http.get(`${this.api}/ligas/clasificacion/${ligaId}`);
  }

  getTop10Jugadores(): Observable<any> {
    return this.http.get(`${this.api}/jugadores/top10`);
  }
}
