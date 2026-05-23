import { Component, OnInit, inject } from '@angular/core';
import { Equipo } from '../services/equipo';

@Component({
  selector: 'app-equipos',
  templateUrl: './equipos.html',
  styleUrls: ['./equipos.css'],
})
export class Equipos implements OnInit {

  private equipoService = inject(Equipo);
  equipos: any[] = [];

  ngOnInit() {
    this.cargarEquipos();
  }

  cargarEquipos() {
    this.equipoService.obtenerEquipos().subscribe(data => {
      console.log("Datos que recibe Angular:", data);
      this.equipos = data || [];
    }, err => {
      console.error('Error al cargar equipos', err);
    });
  }

}
