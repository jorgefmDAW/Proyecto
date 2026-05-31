import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { FormControl, Validators, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Users } from '../services/users-service';

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.html',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule] 
})
export class ResetPassword implements OnInit {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private users = inject(Users);

  token = signal<string>('');
  nuevaPassword = new FormControl('', [Validators.required, Validators.minLength(8)]);
  
  mensajeExito = signal<string>('');
  mensajeError = signal<string>('');

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      if (params['token']) {
        this.token.set(params['token']);
      } else {
        this.mensajeError.set('Enlace no válido. Falta el token de seguridad.');
      }
    });
  }

  cambiarContrasena() {
    if (this.nuevaPassword.invalid || !this.token()) return;

    this.users.resetearContraseña(this.token(), this.nuevaPassword.value!).subscribe({
      next: () => {
        this.mensajeExito.set('¡Contraseña cambiada con éxito! Redirigiendo al login...');
        setTimeout(() => this.router.navigate(['/login']), 3000);
      },
      error: (err) => {
        this.mensajeError.set('El enlace ha caducado o no es válido. Vuelve a solicitar el cambio.');
      }
    });
  }
}