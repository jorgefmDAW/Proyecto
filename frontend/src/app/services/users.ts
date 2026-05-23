import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject, of } from 'rxjs';
import { tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class Users {
  
  private http = inject(HttpClient);  
  private apiUrl = 'https://localhost:8000/api';

  // Estado reactivo para guardar al usuario actual
  private currentUserSubject = new BehaviorSubject<any>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  añadirUsuario(datosUsuario: { username: string, email: string, password: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/usuario/registro`, datosUsuario);
  }

  iniciarSesion(datosSesion: { username?: string, email?: string, password: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, datosSesion)
      .pipe(
        tap((respuesta: any) => {
          // 2. Si el backend nos da el token, lo guardamos en el navegador
          if (respuesta.token) {
            localStorage.setItem('jwt_token', respuesta.token);
            
            // 3. Pedimos los datos del perfil ahora que estamos autorizados
            this.usuarioActual().subscribe({ 
              next: (usuario) => this.currentUserSubject.next(usuario),
              error: (err) => {
                this.currentUserSubject.next(null);
              }
            });
          }
        })
      );
  }

  usuarioActual(): Observable<any> {
    // 1. Buscamos el token en el navegador
    const token = localStorage.getItem('jwt_token');
    
    // Si no hay token, no hacemos la petición para no provocar un error 404
    if (!token) {
      return of(null); 
    }

    // 2. Preparamos la cabecera que espera el backend
    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);

    return this.http.get(`${this.apiUrl}/usuario/perfil`, { headers })  
      .pipe(
        tap((usuario) => {
          this.currentUserSubject.next(usuario);
        })
      );
  }

  cerrarSesion(): Observable<any> {
    // Con este sistema, cerrar sesión es simplemente borrar el token y limpiar el usuario
    localStorage.removeItem('jwt_token');
    this.currentUserSubject.next(null);
    
    // Devolvemos un observable para que tu navbar no falle al hacer .subscribe()
    return of(true); 
  }
}