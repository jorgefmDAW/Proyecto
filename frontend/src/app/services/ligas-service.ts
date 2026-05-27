import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
@Injectable({
  providedIn: 'root',
})
export class LigasService {
  
  private http = inject(HttpClient);
  private baseUrl = 'http://localhost:8000/api/ligas'

  getLigasDisponibles(): Observable<any[]> {
    return this.http.get<any[]>(this.baseUrl);
  }

  getMisLigas(): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/mis-ligas`);
  }

  crearLiga(datos: { nombre: string, privada: boolean, max_miembros: number }): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/crear`, datos);
  }

  unirseLiga(idLiga: number): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/unirse/${idLiga}`, {});
  }

  solicitarUnirse(idLiga: number, mensaje: string): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/unirse/solicitar/${idLiga}`, { mensaje });
  }

  abandonarLiga(idLiga: number): Observable<any> {
    return this.http.delete<any>(`${this.baseUrl}/abandonar/${idLiga}`);
  }

}
