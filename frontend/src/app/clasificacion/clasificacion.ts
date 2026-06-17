import { Component, OnInit, inject, effect, ChangeDetectorRef, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ClasificacionService } from '../services/clasificacion-service';
import { LigasService } from '../services/ligas-service';

@Component({
  selector: 'app-clasificacion',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './clasificacion.html',
  styleUrl: './clasificacion.css'
})
export class Clasificacion implements OnInit {
  private clasificacionService = inject(ClasificacionService);
  private ligasService = inject(LigasService);
  private cdr = inject(ChangeDetectorRef);

  modo = signal<'usuarios' | 'jugadores'>('usuarios');
  clasificacionUsuarios = signal<any[]>([]);
  top10Jugadores = signal<any[]>([]);
  cargandoUsuarios = signal<boolean>(true);
  cargandoJugadores = signal<boolean>(true);
  error = signal<string | null>(null);

  constructor() {
    effect(() => {
      const liga = this.ligasService.ligaActiva();
      if (liga?.id) {
        this.cargarUsuarios(liga.id);
      }
    });
  }

  ngOnInit(): void {
    setTimeout(() => {
      const liga = this.ligasService.ligaActiva();
  
      if (!liga?.id) {
        this.ligasService.getLigaActual().subscribe({
          error: () => {
            this.error.set('No se pudo obtener la liga activa');
            this.cdr.detectChanges();
          }
        });
      }
  
      this.cargarJugadores();
    }, 50);
  }

  setModo(modo: 'usuarios' | 'jugadores'): void {
    this.modo.set(modo);
    this.cdr.detectChanges();
  }

  private cargarUsuarios(ligaId: number): void {
    this.cargandoUsuarios.set(true);
    this.clasificacionService.getClasificacionLiga(ligaId).subscribe({
      next: (res) => {
        this.clasificacionUsuarios.set(res.clasificacion);
        this.cargandoUsuarios.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.error.set('Error al cargar la clasificación');
        this.cargandoUsuarios.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  private cargarJugadores(): void {
    this.cargandoJugadores.set(true);
    this.clasificacionService.getTop10Jugadores().subscribe({
      next: (res) => {
        this.top10Jugadores.set(res);
        this.cargandoJugadores.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.error.set('Error al cargar el top de jugadores');
        this.cargandoJugadores.set(false);
        this.cdr.detectChanges();
      }
    });
  }
}