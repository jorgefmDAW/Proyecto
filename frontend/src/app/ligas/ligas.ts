import { Component, inject, signal, OnInit, ChangeDetectorRef } from '@angular/core';
import { LigasService } from '../services/ligas-service';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-ligas',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './ligas.html',
  styleUrl: './ligas.css',
})
export class Ligas implements OnInit {
  private seccion_actual: string = 'mis-ligas';
  private service = inject(LigasService);
  private fb = inject(FormBuilder);
  private cdr = inject(ChangeDetectorRef);

  public ligas = signal<any[]>([]);
  public misLigas = signal<any[]>([]);

  public cargandoMisLigas = signal<boolean>(true);
  public cargandoLigasDisponibles = signal<boolean>(true);

  public mostrarModalSolicitud = signal(false);
  public ligaSeleccionada = signal<any>(null);
  public mensajeSolicitud = signal('');

  public toast = signal<{tipo: 'exito' | 'error', mensaje: string} | null>(null);
  private toastTimeout: any;

  crearLigaForm: FormGroup = this.fb.group({
    nombre: ['', [Validators.required, Validators.minLength(3)]],
    max_miembros: [null, [Validators.required, Validators.min(2)]],
    privada: [false]
  });

  ngOnInit(): void {
    setTimeout(() => {
      this.getLigasDisponibles();
      this.getMisLigas();
    }, 50);
  }

  getSeccion_actual() {
    return this.seccion_actual;
  }

  mostrar_seccion(seccion: string) {
    this.seccion_actual = seccion;
    this.cdr.detectChanges();
  }

  mostrarToast(mensaje: string, tipo: 'exito' | 'error') {
    this.toast.set({ mensaje, tipo });
    if (this.toastTimeout) clearTimeout(this.toastTimeout);
    this.toastTimeout = setTimeout(() => {
      this.toast.set(null);
      this.cdr.detectChanges();
    }, 3500);
    this.cdr.detectChanges();
  }

  cerrarToast() {
    this.toast.set(null);
    if (this.toastTimeout) clearTimeout(this.toastTimeout);
    this.cdr.detectChanges();
  }

  getLigasDisponibles(): void {
    this.cargandoLigasDisponibles.set(true);
    this.service.getLigasDisponibles().subscribe({
      next: (res: any) => {
        this.ligas.set(res.ligas);
        this.cargandoLigasDisponibles.set(false);
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error(err);
        this.cargandoLigasDisponibles.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  getMisLigas(): void {
    this.cargandoMisLigas.set(true);
    this.service.getMisLigas().subscribe({
      next: (res: any) => {
        this.misLigas.set(res.ligas);
        this.cargandoMisLigas.set(false);
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error(err);
        this.cargandoMisLigas.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  entrarEnLiga(liga: any): void {
    this.service.entrarEnLiga(liga.id).subscribe({
      next: () => {
        this.mostrarToast(`Has entrado a la liga ${liga.nombre} correctamente`, 'exito');
        this.cdr.detectChanges();
      },
      error: (err) => {
        if (err.status === 403) {
          this.mostrarToast('No tienes acceso a esta liga.', 'error');
        } else {
          this.mostrarToast('Error al entrar en la liga.', 'error');
        }
        this.cdr.detectChanges();
      }
    });
  }

  crearLiga(): void {
    if (this.crearLigaForm.invalid) {
      this.crearLigaForm.markAllAsTouched();
      this.mostrarToast('Revisa los campos del formulario', 'error');
      return;
    }

    this.service.crearLiga(this.crearLigaForm.value).subscribe({
      next: () => {
        this.mostrarToast('¡Liga creada con éxito!', 'exito');
        this.crearLigaForm.reset({ privada: false });
        this.getMisLigas();
        this.getLigasDisponibles();
        this.mostrar_seccion('mis-ligas'); 
        this.cdr.detectChanges();
      },
      error: (err) => {
        const msg = err?.error?.detail || err?.error?.message || 'Error al crear la liga';
        this.mostrarToast(msg, 'error');
        this.cdr.detectChanges();
      }
    });
  }

  unirseLiga(idLiga: number): void {
    this.service.unirseLiga(idLiga).subscribe({
      next: () => {
        this.mostrarToast('Te has unido a la liga correctamente', 'exito');
        this.getMisLigas();
        this.getLigasDisponibles();
        this.cdr.detectChanges();
      },
      error: (err) => {
        const msg = err?.error?.error || 'Error al unirse a la liga';
        this.mostrarToast(msg, 'error');
        this.cdr.detectChanges();
      }
    });
  }

  abandonarLiga(idLiga: number): void {
    this.service.abandonarLiga(idLiga).subscribe({
      next: () => {
        this.mostrarToast('Has abandonado la liga', 'exito');
        this.getMisLigas(); 
        this.getLigasDisponibles(); 
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.mostrarToast('Error al abandonar la liga', 'error');
        this.cdr.detectChanges();
      }
    });
  }

  abrirModalSolicitud(liga: any): void {
    this.ligaSeleccionada.set(liga);
    this.mostrarModalSolicitud.set(true);
    this.cdr.detectChanges();
  }

  cerrarModalSolicitud(): void {
    this.mostrarModalSolicitud.set(false);
    this.ligaSeleccionada.set(null);
    this.mensajeSolicitud.set('');
    this.cdr.detectChanges();
  }

  enviarSolicitud(): void {
    const liga = this.ligaSeleccionada();
    if (!liga) return;

    this.service.solicitarUnirse(liga.id, this.mensajeSolicitud()).subscribe({
      next: () => {
        this.mostrarToast('Solicitud enviada correctamente', 'exito');
        this.cerrarModalSolicitud();
        this.getLigasDisponibles();
        this.cdr.detectChanges();
      },
      error: (err) => {
        const msg = err?.error?.error || 'Error al enviar solicitud';
        this.mostrarToast(msg, 'error');
        this.cdr.detectChanges();
      }
    });
  }
}