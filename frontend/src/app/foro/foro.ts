import { Component, inject, signal, ElementRef, ViewChild, OnInit, ChangeDetectorRef } from '@angular/core';
import { ForoService } from '../services/foro-service';
import { Users } from '../services/users-service';
import { toSignal } from '@angular/core/rxjs-interop';

@Component({
  selector: 'app-foro',
  imports: [],
  templateUrl: './foro.html',
  styleUrl: './foro.css',
})
export class Foro implements OnInit {
  private service = inject(ForoService);
  private usersService = inject(Users);
  private cdr = inject(ChangeDetectorRef);

  @ViewChild('chatBody') chatBody!: ElementRef;

  public mensajes = signal<any[]>([]);
  public nuevoMensaje = signal('');
  public cargando = signal<boolean>(true);
  usuarioActual = toSignal(this.usersService.currentUser$);

  ngOnInit(): void {
    setTimeout(() => {
      this.getAllMensajes();
    }, 50);
  }

  scrollAbajo(): void {
    setTimeout(() => {
      if (this.chatBody) {
        this.chatBody.nativeElement.scrollTop = this.chatBody.nativeElement.scrollHeight;
      }
    }, 100);
  }

  getAllMensajes(): void {
    this.cargando.set(true);
    this.service.getAllMensajes().subscribe({
      next: (res: any) => {
        this.mensajes.set(res);
        this.cargando.set(false);
        this.cdr.detectChanges();
        this.scrollAbajo();
      },
      error: (err) => {
        console.error(err);
        this.cargando.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  enviarMensaje(): void {
    const mensaje = this.nuevoMensaje().trim();
    if (!mensaje) return;
    this.service.enviarMensaje(mensaje).subscribe({
      next: () => {
        this.nuevoMensaje.set('');
        this.getAllMensajes();
        this.cdr.detectChanges();
      },
      error: (err) => console.error(err)
    });
  }
}