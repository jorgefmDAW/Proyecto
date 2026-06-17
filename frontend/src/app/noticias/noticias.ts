import { Component, inject, signal, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormsModule } from '@angular/forms'; 
import { CommonModule } from '@angular/common'; 
import { NoticiasService } from '../services/noticias-service';
import { Users } from '../services/users-service';
import { EquiposService } from '../services/equipos-service';

@Component({
  selector: 'app-noticias',
  imports: [FormsModule, CommonModule],
  templateUrl: './noticias.html',
  styleUrl: './noticias.css',
})
export class Noticias implements OnInit {
  private service = inject(NoticiasService);
  private users = inject(Users);
  private equiposService = inject(EquiposService);  
  private cdr = inject(ChangeDetectorRef);

  public noticias = signal<any[]>([]);
  public isAdmin = signal<boolean>(false);
  public equipos = signal<any[]>([]);
  public cargando = signal<boolean>(true);

  public mostrarModal = signal<boolean>(false);
  public modoEdicion = signal<boolean>(false);
  public noticiaActual = signal<any>({
    titulo: '',
    categoria: '',
    texto: '',
    fecha: ''
  });

  public mostrarModalLeer = signal<boolean>(false);
  public noticiaLeer = signal<any>(null);

  ngOnInit(): void {
    setTimeout(() => {
      this.getAllNoticias();
      this.comprobarRol();
      this.cargarEquipos(); 
    }, 50);
  }

  cargarEquipos = (): void => {
    this.equiposService.obtenerEquipos().subscribe({
      next: (res: any[]) => {
        this.equipos.set(res);
        this.cdr.detectChanges();
      },
      error: () => {
        this.cdr.detectChanges();
      }
    });
  }

  comprobarRol = (): void => {
    this.users.usuarioActual().subscribe({
      next: (usuario: any) => {
        if (usuario && usuario.roles && usuario.roles.includes('ROLE_ADMIN')) {
          this.isAdmin.set(true);
        } else {
          this.isAdmin.set(false);
        }
        this.cdr.detectChanges();
      },
      error: () => {
        this.isAdmin.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  getAllNoticias = (): void => {
    this.cargando.set(true);
    this.service.getAllNoticias().subscribe({
      next: (res:any) => {
        this.noticias.set(res);
        this.cargando.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.cargando.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  abrirModalLeer = (noticia: any): void => {
    this.noticiaLeer.set(noticia);
    this.mostrarModalLeer.set(true);
    this.cdr.detectChanges();
  }

  cerrarModalLeer = (): void => {
    this.mostrarModalLeer.set(false);
    this.noticiaLeer.set(null);
    this.cdr.detectChanges();
  }

  abrirModalCrear = (): void => {
    this.modoEdicion.set(false);
    this.noticiaActual.set({
      titulo: '',
      categoria: '',
      texto: '',
      fecha: new Date().toISOString().split('T')[0] 
    });
    this.mostrarModal.set(true);
    this.cdr.detectChanges();
  }

  abrirModalEditar = (noticia: any, event: Event): void => {
    event.stopPropagation();
    event.preventDefault();
    this.modoEdicion.set(true);
    
    const copia = JSON.parse(JSON.stringify(noticia));
    
    if (copia.fecha) {
        try {
           const partes = copia.fecha.split('-');
           if (partes.length === 3) {
             copia.fecha = `${partes[2]}-${partes[1]}-${partes[0]}`;
           }
        } catch(e) {}
    }

    this.noticiaActual.set(copia);
    this.mostrarModal.set(true);
    this.cdr.detectChanges();
  }

  cerrarModal = (): void => {
    this.mostrarModal.set(false);
    this.cdr.detectChanges();
  }

  actualizarCampo = (campo: string, valor: any): void => {
    this.noticiaActual.update(n => ({ ...n, [campo]: valor }));
  }

  guardarNoticia = (): void => {
    const noticiaData = this.noticiaActual();
    
    if (!noticiaData.titulo || !noticiaData.categoria || !noticiaData.texto || !noticiaData.fecha) {
      return;
    }

    if (this.modoEdicion()) {
      this.service.actualizarNoticia(noticiaData.id, noticiaData).subscribe({
        next: () => {
          this.getAllNoticias();
          this.cerrarModal();
        },
        error: () => {
          this.cdr.detectChanges();
        }
      });
    } else {
      this.service.crearNoticia(noticiaData).subscribe({
        next: () => {
          this.getAllNoticias();
          this.cerrarModal();
        },
        error: () => {
          this.cdr.detectChanges();
        }
      });
    }
  }

  eliminarNoticia = (id: number, titulo: string, event: Event): void => {
    event.stopPropagation();
    event.preventDefault();
    if (confirm(`¿Estás seguro de que quieres eliminar la noticia "${titulo}"?`)) {
      this.service.deleteNoticia(id).subscribe({
        next: () => this.getAllNoticias(),
        error: () => {
          this.cdr.detectChanges();
        }
      });
    }
  }
}