import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
@Injectable({
  providedIn: 'root',
})
export class Equipo {
  
  private http = inject(HttpClient);

  obtenerEquipos():Observable<any[]> {
    return this.http.get<any[]>(`/api/equipos`);
  }
}
