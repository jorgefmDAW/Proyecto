import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';
@Injectable({
  providedIn: 'root',
})
export class ForoService {
  
  private http = inject(HttpClient);
  private baseUrl = `${environment.apiUrl}/foro-global/mensajes`

  getAllMensajes(): Observable<any[]> {
    return this.http.get<any[]>(this.baseUrl);
  }

  enviarMensaje(mensaje: string): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/enviar`, { mensaje });
  }

}
