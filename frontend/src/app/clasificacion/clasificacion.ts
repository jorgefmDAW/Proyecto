import { Component, OnInit, inject, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ClasificacionService } from '../services/clasificacion-service';

@Component({
  selector: 'app-clasificacion',
  imports: [],
  templateUrl: './clasificacion.html',
  styleUrl: './clasificacion.css',
})

export class Clasificacion implements OnInit {
  // Empezamos en 'jugadores' porque es el endpoint que ya funciona
  vistaActual: 'usuarios' | 'jugadores' = 'jugadores'; 
  
  jugadoresTop10: any[] = [];
  cargando = false;
  error: string | null = null;

  private clasificacionService = inject(ClasificacionService);
  private cdr = inject(ChangeDetectorRef);

  ngOnInit(): void {
    // Al cargar la pantalla, pedimos los datos de los jugadores
    this.cargarJugadores();
  }

  cambiarVista(vista: 'usuarios' | 'jugadores'): void {
    this.vistaActual = vista;
    
    // Si vuelve a jugadores y por algún casual está vacío, los pedimos de nuevo
    if (vista === 'jugadores' && this.jugadoresTop10.length === 0) {
      this.cargarJugadores();
    }
  }

  cargarJugadores(): void {
    this.cargando = true;
    this.error = null;

    this.clasificacionService.obtenerJugadoresTop10().subscribe({
      next: (response: any) => {
        // Ojo aquí: Ajusta esto si tu backend devuelve un { jugadores: [...] } o directamente el array [...]
        this.jugadoresTop10 = response.jugadores ? response.jugadores : response;
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error cargando top 10', err);
        this.error = 'No se pudo cargar la clasificación.';
        this.cargando = false;
        this.cdr.detectChanges();
      }
    });
  }
}