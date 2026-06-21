import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class NoticiasService {
  private http = inject(HttpClient);
  
  private baseUrl = `${environment.apiUrl}/noticias`;
  private adminUrl = `${environment.apiUrl}/admin/noticias`;

  getAllNoticias(): Observable<any[]> {
    return this.http.get<any[]>(this.baseUrl);
  }

  crearNoticia(noticia: any): Observable<any> {
    return this.http.post<any>(`${this.adminUrl}/crear`, noticia);
  }

  actualizarNoticia(id: number, noticia: any): Observable<any> {
    return this.http.patch<any>(`${this.adminUrl}/actualizar/${id}`, noticia);
  }

  deleteNoticia(id: number): Observable<any> {
    return this.http.delete<any>(`${this.adminUrl}/eliminar/${id}`);
  }
}