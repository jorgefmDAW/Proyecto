import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class SolicitudesService {
  private http = inject(HttpClient);
  private baseUrl = `${environment.apiUrl}/solicitudes`;

  getSolicitudesLiga(ligaId: number): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/${ligaId}`);
  }

  aceptarSolicitud(solicitudId: number): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/aceptar/${solicitudId}`, {});
  }
}