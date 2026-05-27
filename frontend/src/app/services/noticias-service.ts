import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { Observable } from 'rxjs';
@Injectable({
  providedIn: 'root',
})
export class NoticiasService {
  
  private http = inject(HttpClient);
  private baseUrl = 'http://localhost:8000/api/noticias'

  getAllNoticias(): Observable<any[]> {
    return this.http.get<any[]>(this.baseUrl);
  }

}
