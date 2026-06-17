import { Component, OnInit, inject, signal, ChangeDetectorRef } from '@angular/core';
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
  private cdr = inject(ChangeDetectorRef);

  solicitudes = signal<any[]>([]);
  cargando = signal(true);
  error = signal('');
  mensajeExito = signal('');

  ngOnInit(): void {
    setTimeout(() => {
      this.cargarSolicitudes();
    }, 50);
  }

  cargarSolicitudes(): void {
    const ligaActual = this.ligasService.ligaActiva();
    
    if (!ligaActual || !ligaActual.id) {
      this.error.set('No hay ninguna liga activa en este momento.');
      this.cargando.set(false);
      this.cdr.detectChanges();
      return;
    }

    this.cargando.set(true);
    this.cdr.detectChanges();
    
    this.solicitudesService.getSolicitudesLiga(ligaActual.id).subscribe({
      next: (res) => {
        const pendientes = res.solicitudes.filter((s: any) => s.aceptada === false);
        this.solicitudes.set(pendientes);
        this.cargando.set(false);
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.error.set(err.error?.error || err.error?.message || 'Error al cargar las solicitudes.');
        this.cargando.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  aceptar(solicitudId: number): void {
    this.solicitudesService.aceptarSolicitud(solicitudId).subscribe({
      next: (res) => {
        this.mensajeExito.set(res.message);
        this.solicitudes.update(lista => lista.filter(s => s.id !== solicitudId));
        this.cdr.detectChanges();
        
        setTimeout(() => {
          this.mensajeExito.set('');
          this.cdr.detectChanges();
        }, 4000);
      },
      error: (err) => {
        this.error.set(err.error?.error || 'No se pudo aceptar la solicitud.');
        this.cdr.detectChanges();
        
        setTimeout(() => {
          this.error.set('');
          this.cdr.detectChanges();
        }, 4000);
      }
    });
  }
}