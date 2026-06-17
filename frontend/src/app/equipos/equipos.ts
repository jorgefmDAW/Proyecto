import { Component, OnInit, inject, signal, computed } from '@angular/core';
import { EquiposService } from '../services/equipos-service';
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
  private equipoService = inject(EquiposService);
  
  equipos = signal<any[]>([]);
  textoBusqueda = signal<string>('');
  cargando = signal<boolean>(true);

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
    this.cargando.set(true);
    this.equipoService.obtenerEquipos().subscribe({
      next: (data) => {
        let lista = data || [];
        
        lista.sort((a: any, b: any) => a.nombre.localeCompare(b.nombre));
        
        this.equipos.set(lista);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error(err);
        this.cargando.set(false);
      }
    });
  }
}