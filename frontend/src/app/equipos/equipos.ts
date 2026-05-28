import { Component, OnInit, inject, signal } from '@angular/core';
import { Equipo } from '../services/equipo';

@Component({
  selector: 'app-equipos',
  templateUrl: './equipos.html',
  styleUrls: ['./equipos.css'],
})
export class Equipos implements OnInit {
  private equipoService = inject(Equipo);
  equipos = signal<any[]>([]);

  ngOnInit() {
    this.cargarEquipos();
  }

  cargarEquipos() {
    this.equipoService.obtenerEquipos().subscribe({
      next: (data) => this.equipos.set(data || []),
      error: (err) => console.error('Error al cargar equipos', err)
    });
  }
}