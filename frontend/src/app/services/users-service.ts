import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject, of, throwError } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class Users {
  private http = inject(HttpClient);
  private apiUrl = `${environment.apiUrl}`;

  private currentUserSubject = new BehaviorSubject<any>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  getAccessToken(): string | null {
    return localStorage.getItem('jwt_token');
  }

  getRefreshToken(): string | null {
    return localStorage.getItem('refresh_token');
  }

  private saveTokens(access: string, refresh: string): void {
    localStorage.setItem('jwt_token', access);
    localStorage.setItem('refresh_token', refresh);
  }

  private clearTokens(): void {
    localStorage.removeItem('jwt_token');
    localStorage.removeItem('refresh_token');
  }

  buildAuthHeaders(): HttpHeaders {
    const token = this.getAccessToken();
    return new HttpHeaders().set('Authorization', `Bearer ${token}`);
  }

  añadirUsuario(datosUsuario: {
    username: string;
    email: string;
    password: string;
  }): Observable<any> {
    return this.http.post(`${this.apiUrl}/usuario/registro`, datosUsuario);
  }

  iniciarSesion(datosSesion: {
    username?: string;
    email?: string;
    password: string;
  }): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, datosSesion).pipe(
      tap((respuesta: any) => {
        if (respuesta.token && respuesta.refresh_token) {
          this.saveTokens(respuesta.token, respuesta.refresh_token);
          this.usuarioActual().subscribe({
            next: (usuario) => this.currentUserSubject.next(usuario),
            error: () => this.currentUserSubject.next(null),
          });
        }
      })
    );
  }

  usuarioActual(): Observable<any> {
    if (!this.getAccessToken()) return of(null);

    return this.http
      .get(`${this.apiUrl}/usuario/perfil`)
      .pipe(tap((usuario) => this.currentUserSubject.next(usuario)));
  }

  refrescarToken(): Observable<any> {
    const refresh = this.getRefreshToken();
    if (!refresh) return throwError(() => new Error('No refresh token'));

    // Cabeceras limpias SIN Authorization para que Symfony no rechace la petición
    const headers = new HttpHeaders({ 'Content-Type': 'application/json' });

    return this.http
      .post(`${this.apiUrl}/token/refresh`, { refresh_token: refresh }, { headers })
      .pipe(
        tap((respuesta: any) => {
          if (respuesta.token && respuesta.refresh_token) {
            this.saveTokens(respuesta.token, respuesta.refresh_token);
          }
        }),
        catchError((err) => {
          this.clearTokens();
          this.currentUserSubject.next(null);
          return throwError(() => err);
        })
      );
  }

  cerrarSesion(): Observable<any> {
    return this.http
      .post(`${this.apiUrl}/usuario/logout`, {})
      .pipe(
        tap(() => {
          this.clearTokens();
          this.currentUserSubject.next(null);
        }),
        catchError(() => {
          this.clearTokens();
          this.currentUserSubject.next(null);
          return of(true);
        })
      );
  }

  solicitarCorreo(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/password/forgot`, { email });
  }

  resetearContraseña(token: string, password: string): Observable<any>{
    return this.http.post(`${this.apiUrl}/password/reset`, { token, password});
  }

  estaLogueado(): boolean {
    return !!this.getAccessToken(); // devuelve true si hay un token, y false si es null
  }

    isAdmin(): boolean {
    const usuario = this.currentUserSubject.getValue();
    const roles: string[] = usuario?.roles ?? [];
    return roles.includes('ROLE_ADMIN');
  }

}