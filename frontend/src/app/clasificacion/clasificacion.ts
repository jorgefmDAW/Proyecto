import { Component, OnInit, inject, effect, ChangeDetectorRef } from '@angular/core';
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

  modo: 'usuarios' | 'jugadores' = 'usuarios';
  clasificacionUsuarios: any[] = [];
  top10Jugadores: any[] = [];
  cargando = false;
  error: string | null = null;

  constructor() {
    effect(() => {
      const liga = this.ligasService.ligaActiva();
      if (liga?.id) {
        this.cargarUsuarios(liga.id);
      }
    });
  }

  ngOnInit(): void {
    const liga = this.ligasService.ligaActiva();

    if (!liga?.id) {
      this.ligasService.getLigaActual().subscribe({
        error: () => this.error = 'No se pudo obtener la liga activa'
      });
    }

    this.cargarJugadores();
  }

  setModo(modo: 'usuarios' | 'jugadores'): void {
    this.modo = modo;
  }

  private cargarUsuarios(ligaId: number): void {
    this.cargando = true;
    this.clasificacionService.getClasificacionLiga(ligaId).subscribe({
      next: (res) => {
        this.clasificacionUsuarios = res.clasificacion;
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: () => {
        this.error = 'Error al cargar la clasificación';
        this.cargando = false;
        this.cdr.detectChanges();
      }
    });
  }

  private cargarJugadores(): void {
    this.clasificacionService.getTop10Jugadores().subscribe({
      next: (res) => {
        this.top10Jugadores = res;
        this.cdr.detectChanges();
      },
      error: () => {
        this.error = 'Error al cargar el top de jugadores';
        this.cdr.detectChanges();
      }
    });
  }
}