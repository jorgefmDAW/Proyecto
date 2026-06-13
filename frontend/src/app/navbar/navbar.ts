import { Component, inject, OnInit } from '@angular/core';
import { RouterLink, RouterLinkActive, Router } from '@angular/router';
import { Users } from '../services/users-service';
import { LigasService } from '../services/ligas-service';
import { toSignal } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './navbar.html',
  styleUrl: './navbar.css',
})
export class Navbar implements OnInit {
  private usersService = inject(Users);
  private router = inject(Router);

  ligasService = inject(LigasService);
  usuarioActual = toSignal(this.usersService.currentUser$);

  ngOnInit(): void {
    this.usersService.usuarioActual().subscribe({
      next: (usuario) => {
      },
      error: () => console.log('Error al comprobar la sesión')
    });
  }

  hacerLogout(event: Event): void {
    event.preventDefault();
    this.ligasService.salirDeLiga();
    this.usersService.cerrarSesion().subscribe({
      next: () => this.router.navigate(['/login']),
      error: () => this.router.navigate(['/login'])
    });
  }
}