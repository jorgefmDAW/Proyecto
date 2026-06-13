import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { Observable, tap } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root',
})
export class LigasService {
  private http = inject(HttpClient);
  private router = inject(Router);
  private baseUrl = 'http://localhost:8000/api/ligas';

  ligaActiva = signal<any>(null);

  getLigaActual(): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/seleccionada`).pipe(
      tap({
        next: (res) => {
          this.ligaActiva.set(res.liga_seleccionada);
        },
        error: () => {
          this.ligaActiva.set(null);
        }
      })
    );
  }

  getLigasDisponibles(): Observable<any[]> {
    return this.http.get<any[]>(this.baseUrl);
  }

  getMisLigas(): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/mis-ligas`);
  }

  getLigaById(ligaId: number): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/${ligaId}`);
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

  entrarEnLiga(ligaId: number): Observable<any> {
    return this.http.patch<any>(`${this.baseUrl}/entrar/${ligaId}`, {}).pipe(
      tap(() => {
        this.getLigaById(ligaId).subscribe({
          next: (liga) => this.ligaActiva.set(liga),
          error: () => this.ligaActiva.set(null)
        });
      })
    );
  }

  salirDeLiga(): void {
    this.ligaActiva.set(null);
    this.router.navigate(['/ligas']);
  }
}