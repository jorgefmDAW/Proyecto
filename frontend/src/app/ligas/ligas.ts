import { Component } from '@angular/core';
@Component({
  selector: 'app-ligas',
  imports: [],
  templateUrl: './ligas.html',
  styleUrl: './ligas.css',
})
export class Ligas {

  private seccion_actual: string = 'mis-ligas'

  getSeccion_actual() {
    return this.seccion_actual
  }
  
  mostrar_seccion(seccion:string) {
    this.seccion_actual = seccion
  }
  
}

