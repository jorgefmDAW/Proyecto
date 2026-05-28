import { Component, inject, signal, ElementRef, ViewChild } from '@angular/core';
import { ForoService } from '../services/foro-service';
import { Users } from '../services/users-service';
import { toSignal } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-foro',
  imports: [],
  templateUrl: './foro.html',
  styleUrl: './foro.css',
})
export class Foro {
  private service = inject(ForoService);
  private usersService = inject(Users);

  @ViewChild('chatBody') chatBody!: ElementRef;

  public mensajes = signal<any[]>([]);
  public nuevoMensaje = signal('');
  usuarioActual = toSignal(this.usersService.currentUser$);

  ngOnInit(): void {
    this.getAllMensajes();
  }

  scrollAbajo(): void {
    setTimeout(() => {
      if (this.chatBody) {
        this.chatBody.nativeElement.scrollTop = this.chatBody.nativeElement.scrollHeight;
      }
    }, 0);
  }

  getAllMensajes(): void {
    this.service.getAllMensajes().subscribe({
      next: (res: any) => {
        this.mensajes.set(res);
        this.scrollAbajo();
      },
      error: (err) => console.error('Error mostrando todos los mensajes', err)
    });
  }

  enviarMensaje(): void {
    const mensaje = this.nuevoMensaje().trim();
    if (!mensaje) return;
    this.service.enviarMensaje(mensaje).subscribe({
      next: () => {
        this.nuevoMensaje.set('');
        this.getAllMensajes();
      },
      error: (err) => console.error('Error al enviar el mensaje', err)
    });
  }
}