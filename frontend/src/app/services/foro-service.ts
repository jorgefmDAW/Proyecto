import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
@Injectable({
  providedIn: 'root',
})
export class ForoService {
  
  private http = inject(HttpClient);
  private baseUrl = 'http://localhost:8000/api/foro-global/mensajes'

  getAllMensajes(): Observable<any[]> {
    return this.http.get<any[]>(this.baseUrl);
  }

  enviarMensaje(mensaje: string): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/enviar`, { mensaje });
  }

}
