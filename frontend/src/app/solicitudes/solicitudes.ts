import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SolicitudesService } from '../services/solicitudes-service';
import { LigasService } from '../services/ligas-service';

@Component({
  selector: 'app-solicitudes',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './solicitudes.html',
  styleUrls: ['./solicitudes.css']
})
export class Solicitudes implements OnInit {
  private solicitudesService = inject(SolicitudesService);
  private ligasService = inject(LigasService);

  solicitudes = signal<any[]>([]);
  cargando = signal(true);
  error = signal('');
  mensajeExito = signal('');

  ngOnInit(): void {
    this.cargarSolicitudes();
  }

  cargarSolicitudes(): void {
    const ligaActual = this.ligasService.ligaActiva();
    
    if (!ligaActual || !ligaActual.id) {
      this.error.set('No hay ninguna liga activa en este momento.');
      this.cargando.set(false);
      return;
    }

    this.cargando.set(true);
    
    this.solicitudesService.getSolicitudesLiga(ligaActual.id).subscribe({
      next: (res) => {
        const pendientes = res.solicitudes.filter((s: any) => s.aceptada === false);
        this.solicitudes.set(pendientes);
        this.cargando.set(false);
      },
      error: (err) => {
        this.error.set(err.error?.error || err.error?.message || 'Error al cargar las solicitudes.');
        this.cargando.set(false);
      }
    });
  }

  aceptar(solicitudId: number): void {
    this.solicitudesService.aceptarSolicitud(solicitudId).subscribe({
      next: (res) => {
        this.mensajeExito.set(res.message);
        this.solicitudes.update(lista => lista.filter(s => s.id !== solicitudId));
        
        setTimeout(() => this.mensajeExito.set(''), 4000);
      },
      error: (err) => {
        this.error.set(err.error?.error || 'No se pudo aceptar la solicitud.');
        setTimeout(() => this.error.set(''), 4000);
      }
    });
  }
}