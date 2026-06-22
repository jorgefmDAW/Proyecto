import { Component, OnInit, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormGroup, FormControl, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Users } from '../services/users-service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.html',
  styleUrls: ['./reset-password.css'],
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule] 
})
export class ResetPassword implements OnInit {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private users = inject(Users);

  token = signal<string>('');
  
  nuevaPasswordForm = new FormGroup({
    password: new FormControl('', [Validators.required, Validators.minLength(8)])
  });

  // Alias para acceder fácil en el HTML
  nuevaPassword = this.nuevaPasswordForm.get('password') as FormControl;
  
  mensajeExito = signal<string>('');
  mensajeError = signal<string>('');

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      if (params['token']) {
        this.token.set(params['token']);
      } else {
        this.mensajeError.set('Enlace no válido. Falta el token.');
      }
    });
  }

  cambiarContrasena() {
    if (this.nuevaPasswordForm.invalid) return;

    this.users.resetearContraseña(this.token(), this.nuevaPassword.value!).subscribe({
      next: () => {
        this.mensajeExito.set('¡Contraseña actualizada! Redirigiendo...');
        setTimeout(() => this.router.navigate(['/login']), 3000);
      },
      error: () => {
        this.mensajeError.set('Error: El enlace ha caducado o no es válido.');
      }
    });
  }
}