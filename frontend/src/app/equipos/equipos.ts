import { Component, OnInit, inject, signal, computed, ChangeDetectorRef } from '@angular/core';
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
  private cdr = inject(ChangeDetectorRef);
  
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
    setTimeout(() => {
      this.cargarEquipos();
    }, 50);
  }

  cargarEquipos() {
    this.cargando.set(true);
    this.equipoService.obtenerEquipos().subscribe({
      next: (data) => {
        let lista = data || [];
        
        lista.sort((a: any, b: any) => a.nombre.localeCompare(b.nombre));
        
        this.equipos.set(lista);
        this.cargando.set(false);
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.cargando.set(false);
        this.cdr.detectChanges();
      }
    });
  }
}