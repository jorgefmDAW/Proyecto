import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, AbstractControl, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Users } from  '../services/users-service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './login.html',
  styleUrls: ['./login.css'],
})
export class Login {    

  private fb = inject(FormBuilder);
  private users = inject(Users);
  private router = inject(Router);

  mensajeExito = signal('');
  mensajeError = signal('');
  formRegistro = signal(false);
  formLogin = signal(true);

  registroSubmitted = signal(false);
  loginSubmitted = signal(false);

  registroForm = this.fb.group({
    username: ['', [Validators.required, Validators.minLength(3)]],
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(8)]]
  });

  loginForm = this.fb.group({
    username: ['', [Validators.required, Validators.minLength(3)]],
    password: ['', [Validators.required, Validators.minLength(8)]]
  });
  
  activarRegistro() {
    this.formRegistro.set(true);
    this.formLogin.set(false);
    this.mensajeExito.set('');
    this.mensajeError.set('');
    this.registroSubmitted.set(false);
    this.loginSubmitted.set(false);
  }

  activarLogin() {
    this.formRegistro.set(false);
    this.formLogin.set(true);
    this.mensajeExito.set('');
    this.mensajeError.set('');
    this.registroSubmitted.set(false);
    this.loginSubmitted.set(false);
  }

  controlInvalido(formType: 'registro' | 'login', controlName: string): boolean {
    const form: FormGroup = formType === 'registro' ? this.registroForm : this.loginForm;
    const control: AbstractControl | null = form.get(controlName);
    const submitted = formType === 'registro' ? this.registroSubmitted() : this.loginSubmitted();
    return !!(control && control.invalid && (control.touched || submitted));
  }

  registrarUsuario() {
    this.registroSubmitted.set(true);

    if (this.registroForm.invalid) {
      this.registroForm.markAllAsTouched();
      return;
    }

    this.users.añadirUsuario(this.registroForm.value as { username: string, email: string, password: string }).subscribe({
      next: () => {
        this.mensajeExito.set('¡Cuenta creada con éxito!');
        this.mensajeError.set('');
        this.registroForm.reset();
        this.registroSubmitted.set(false);
      },
      error: (err) => {
        const serverMsg = err?.error?.detail || err?.error?.message || err?.message;
        this.mensajeError.set(serverMsg || 'Error al crear la cuenta');
        this.mensajeExito.set('');
      }
    });
  }

  iniciarSesion() {
    this.loginSubmitted.set(true);

    if (this.loginForm.invalid) {
      this.loginForm.markAllAsTouched();
      return;
    }
    const payload: any = { password: this.loginForm.get('password')?.value };
    const usernameVal = this.loginForm.get('username')?.value;
    if (usernameVal) payload.username = usernameVal;

    this.users.iniciarSesion(payload).subscribe({
      next: (res) => {
        this.mensajeExito.set('¡Sesión iniciada con éxito!');
        this.mensajeError.set('');
        this.loginForm.reset();
        this.loginSubmitted.set(false);
        this.router.navigate(['/menu']);
      },
      error: (err) => {
        const serverMsg = err?.error?.detail || err?.error?.message || err?.message;
        this.mensajeError.set(serverMsg || 'Usuario o contraseña incorrectos');
        this.mensajeExito.set('');
      }
    });
  }
}