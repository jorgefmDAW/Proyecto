import { Component } from '@angular/core';

@Component({
  selector: 'app-login',
  imports: [],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {
  private seccion_actual: string = "iniciar-sesion"

  getSeccion_actual() {
    return this.seccion_actual
  }

  mostrarSeccion(seccion:string) {
    this.seccion_actual = seccion
  }
}

