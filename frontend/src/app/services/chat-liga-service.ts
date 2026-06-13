import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ChatLigaService {
  private baseUrl = 'http://localhost:8000/api/chat-liga';

  constructor(private http: HttpClient) {}

  getMensajesLiga(ligaId: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.baseUrl}/mensajes/${ligaId}`);
  }

  enviarMensaje(ligaId: number, mensaje: string): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/mensajes/enviar/${ligaId}`, { mensaje });
  }
}