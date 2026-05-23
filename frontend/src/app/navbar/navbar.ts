import { Component, inject, OnInit } from '@angular/core';
import { RouterLink, RouterLinkActive, Router } from '@angular/router';
import { Users } from '../services/users';
import { toSignal } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-navbar',
  standalone: true, // Importante que sea true en Angular moderno
  imports: [RouterLink, RouterLinkActive], 
  templateUrl: './navbar.html',
  styleUrl: './navbar.css',
})
export class Navbar implements OnInit {
  
  private usersService = inject(Users);
  private router = inject(Router);

  // Convertimos la "caja" del servicio en una variable moderna que el HTML puede leer
  usuarioActual = toSignal(this.usersService.currentUser$);

  ngOnInit(): void {
    // 1. Al cargar el navbar, le pedimos al backend los datos del usuario
    this.usersService.usuarioActual().subscribe({
      next: (usuario) => console.log('Usuario conectado:', usuario.email),
      error: () => console.log('Aún no hay sesión iniciada')
    });
  }
  
  hacerLogout(event: Event): void {
    event.preventDefault(); 
    
    this.usersService.cerrarSesion().subscribe({
      next: () => {
        this.router.navigate(['/login']); 
      },
      error: () => {
        this.router.navigate(['/login']); 
      }
    });
  }
}