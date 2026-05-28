import { Component, inject, signal } from '@angular/core';
import { LigasService } from '../services/ligas-service';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-ligas',
  imports: [ReactiveFormsModule],
  templateUrl: './ligas.html',
  styleUrl: './ligas.css',
})
export class Ligas {
  private seccion_actual: string = 'mis-ligas';
  private service = inject(LigasService);
  private fb = inject(FormBuilder);

  public ligas = signal<any[]>([]);
  public misLigas = signal<any[]>([]);

  public mensajeExito = signal('');
  public mensajeError = signal('');

  public mostrarModalSolicitud = signal(false);
  public ligaSeleccionada = signal<any>(null);
  public mensajeSolicitud = signal('');

  crearLigaForm: FormGroup = this.fb.group({
    nombre: ['', [Validators.required, Validators.minLength(3)]],
    max_miembros: [null, [Validators.required, Validators.min(2)]],
    privada: [false]
  });

  ngOnInit(): void {
    this.getLigasDisponibles();
    this.getMisLigas()
  }

  getSeccion_actual() {
    return this.seccion_actual;
  }

  mostrar_seccion(seccion: string) {
    this.seccion_actual = seccion;
  }

  getLigasDisponibles(): void {
    this.service.getLigasDisponibles().subscribe({
      next: (res: any) => {
        this.ligas.set(res.ligas);
      },
      error: (err) => console.error('Error mostrando todas las ligas', err)
    });
  }

  getMisLigas(): void {
    this.service.getMisLigas().subscribe({
      next: (res: any) => this.misLigas.set(res.ligas),
      error: (err) => console.error('Error cargando mis ligas', err)
    });
  }

  entrarEnLiga(ligaId: number): void {
    this.service.entrarEnLiga(ligaId).subscribe({
      error: (err) => {
        if (err.status === 403) {
          this.mensajeError.set('No tienes acceso a esta liga.');
        } else {
          this.mensajeError.set('Error al entrar en la liga.');
        }
      }
    });
  }

  crearLiga(): void {
    if (this.crearLigaForm.invalid) {
      this.crearLigaForm.markAllAsTouched();
      return;
    }

    this.service.crearLiga(this.crearLigaForm.value).subscribe({
      next: () => {
        this.mensajeExito.set('¡Liga creada con éxito!');
        this.mensajeError.set('');
        this.crearLigaForm.reset({ privada: false });
        this.getMisLigas();
        this.getLigasDisponibles();
      },
      error: (err) => {
        const msg = err?.error?.detail || err?.error?.message || 'Error al crear la liga';
        this.mensajeError.set(msg);
        this.mensajeExito.set('');
      }
    });
  }

  unirseLiga(idLiga: number): void {
    this.service.unirseLiga(idLiga).subscribe({
      next: () => {
        this.getMisLigas();
        this.getLigasDisponibles();
      },
      error: (err) => console.error('Error al unirse a la liga', err)
    });
  }

  abandonarLiga(idLiga: number): void {
    this.service.abandonarLiga(idLiga).subscribe({
      next: () => {
        this.getMisLigas(); // refresca las ligas del usuario automaticamente
        this.getLigasDisponibles() // refresca las ligas actualizando el numero de miembros o eliminando la liga si era el ultimo miembro el que abandono
      },
      error: (err) => console.error('Error al abandonar la liga', err)
    });
  }

  abrirModalSolicitud(liga: any): void {
    this.ligaSeleccionada.set(liga);
    this.mostrarModalSolicitud.set(true);
  }

  cerrarModalSolicitud(): void {
    this.mostrarModalSolicitud.set(false);
    this.ligaSeleccionada.set(null);
    this.mensajeSolicitud.set('');
  }

  enviarSolicitud(): void {
    const liga = this.ligaSeleccionada();
    if (!liga) return;

    this.service.solicitarUnirse(liga.id, this.mensajeSolicitud()).subscribe({
      next: () => {
        this.cerrarModalSolicitud();
        this.getLigasDisponibles();
      },
      error: (err) => console.error('Error al enviar solicitud', err)
    });
  }

}