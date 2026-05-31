import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { Equipo } from '../services/equipo';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-equipos',
  standalone: true, 
  imports: [
    CommonModule, 
    RouterLink 
  ],
  templateUrl: './equipos.html',
  styleUrls: ['./equipos.css'],
})
export class Equipos implements OnInit {
  private equipoService = inject(Equipo);
  
  equipos = signal<any[]>([]);

  textoBusqueda = signal<string>('');

    equiposFiltrados = computed(() => {
    const texto = this.textoBusqueda().toLowerCase();
    const listaCompleta = this.equipos();

    return listaCompleta.filter(equipo => 
      equipo.nombre.toLowerCase().includes(texto)
    );
  });

  ngOnInit() {
    this.cargarEquipos();
  }

  cargarEquipos() {
    this.equipoService.obtenerEquipos().subscribe({
      next: (data) => {
        let lista = data || [];
        
        lista.sort((a: any, b: any) => a.nombre.localeCompare(b.nombre));
        
        this.equipos.set(lista);
      },
      error: (err) => console.error('Error al cargar equipos', err)
    });
  }
}