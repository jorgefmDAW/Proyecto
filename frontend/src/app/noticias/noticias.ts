import { Component, inject, signal, OnInit } from '@angular/core';
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
    this.getAllNoticias();
    this.comprobarRol();
    this.cargarEquipos(); 
  }

  cargarEquipos(): void {
    this.equiposService.obtenerEquipos().subscribe({
      next: (res: any[]) => this.equipos.set(res),
      error: (err) => console.error(err)
    });
  }

  comprobarRol(): void {
    this.users.usuarioActual().subscribe({
      next: (usuario: any) => {
        if (usuario && usuario.roles && usuario.roles.includes('ROLE_ADMIN')) {
          this.isAdmin.set(true);
        } else {
          this.isAdmin.set(false);
        }
      },
      error: (err) => {
        console.error(err);
        this.isAdmin.set(false);
      }
    });
  }

  getAllNoticias(): void {
    this.cargando.set(true);
    this.service.getAllNoticias().subscribe({
      next: (res:any) => {
        this.noticias.set(res);
        this.cargando.set(false);
      },
      error: (err) => {
        console.error(err);
        this.cargando.set(false);
      }
    });
  }

  abrirModalLeer(noticia: any): void {
    this.noticiaLeer.set(noticia);
    this.mostrarModalLeer.set(true);
  }

  cerrarModalLeer(): void {
    this.mostrarModalLeer.set(false);
    this.noticiaLeer.set(null);
  }

  abrirModalCrear(): void {
    this.modoEdicion.set(false);
    this.noticiaActual.set({
      titulo: '',
      categoria: '',
      texto: '',
      fecha: new Date().toISOString().split('T')[0] 
    });
    this.mostrarModal.set(true);
  }

  abrirModalEditar(noticia: any): void {
    this.modoEdicion.set(true);
    this.noticiaActual.set(JSON.parse(JSON.stringify(noticia)));
    
    if (this.noticiaActual().fecha) {
        try {
           const partes = this.noticiaActual().fecha.split('-');
           if (partes.length === 3) {
             const fechaFormateada = `${partes[2]}-${partes[1]}-${partes[0]}`;
             this.noticiaActual().fecha = fechaFormateada;
           }
        } catch(e) {
           console.error(e);
        }
    }
    
    this.mostrarModal.set(true);
  }

  cerrarModal(): void {
    this.mostrarModal.set(false);
  }

  guardarNoticia(): void {
    const noticiaData = this.noticiaActual();
    
    if (!noticiaData.titulo || !noticiaData.categoria || !noticiaData.texto || !noticiaData.fecha) {
      alert('Por favor, rellena todos los campos.');
      return;
    }

    if (this.modoEdicion()) {
      this.service.actualizarNoticia(noticiaData.id, noticiaData).subscribe({
        next: () => {
          this.getAllNoticias();
          this.cerrarModal();
        },
        error: (err) => alert('Error al actualizar la noticia')
      });
    } else {
      this.service.crearNoticia(noticiaData).subscribe({
        next: () => {
          this.getAllNoticias();
          this.cerrarModal();
        },
        error: (err) => alert('Error al crear la noticia')
      });
    }
  }

  eliminarNoticia(id: number, titulo: string, event?: Event): void {
    if (event) event.stopPropagation();
    if (confirm(`¿Estás seguro de que quieres eliminar la noticia "${titulo}"?`)) {
      this.service.deleteNoticia(id).subscribe({
        next: () => this.getAllNoticias(),
        error: (err) => alert('Error al borrar la noticia')
      });
    }
  }
}