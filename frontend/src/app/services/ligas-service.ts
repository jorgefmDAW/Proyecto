<<<<<<< HEAD
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
=======
import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { Observable, tap, of } from 'rxjs'; // <-- Añadido 'of'
import { Router } from '@angular/router';
import { Users } from './users-service'; // <-- Ajusta la ruta si es necesario

@Injectable({
  providedIn: 'root',
})
export class LigasService {
  private http = inject(HttpClient);
  private router = inject(Router);
  private usersService = inject(Users); // <-- INYECTAMOS EL SERVICIO DE USUARIOS
  private baseUrl = 'http://localhost:8000/api/ligas';

  ligaActiva = signal<any>(null);

  getLigaActual(): Observable<any> {
    // EL PORTERO: Si no hay token, no hacemos la petición al backend
    if (!this.usersService.getAccessToken()) {
      this.ligaActiva.set(null);
      return of(null); // Devolvemos un observable vacío silencioso
    }

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
    if (!this.usersService.getAccessToken()) return of([]); // Bloqueo opcional aquí también
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
        this.getLigaActual().subscribe();
      })
    );
  }

  salirDeLiga(): void {
    this.ligaActiva.set(null);
    this.router.navigate(['/ligas']);
  }
>>>>>>> main
}