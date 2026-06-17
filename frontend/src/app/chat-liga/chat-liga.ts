import { Component, OnInit, OnDestroy, ViewChild, ElementRef, inject, signal, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { toSignal } from '@angular/core/rxjs-interop';
import { Subscription, interval } from 'rxjs';
import { LigasService } from '../services/ligas-service';
import { ChatLigaService } from '../services/chat-liga-service';
import { Users } from '../services/users-service';

@Component({
  selector: 'app-chat-liga',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './chat-liga.html',
  styleUrls: ['./chat-liga.css']
})
export class ChatLiga implements OnInit, OnDestroy {
  ligaId!: number;
  mensajes = signal<any[]>([]);
  nuevoMensaje = signal('');
  error = signal('');
  cargando = signal<boolean>(true);
  
  private pollingSub?: Subscription;
  @ViewChild('scrollContainer') private scrollContainer!: ElementRef;

  private chatService = inject(ChatLigaService);
  private ligasService = inject(LigasService);
  private usersService = inject(Users);
  private cdr = inject(ChangeDetectorRef);

  usuarioActual = toSignal(this.usersService.currentUser$);

  ngOnInit(): void {
    setTimeout(() => {
      this.ligasService.getLigaActual().subscribe({
        next: (res) => {
          if (res?.liga_seleccionada?.id) {
            this.ligaId = res.liga_seleccionada.id;
            this.iniciarChat();
          } else {
            this.error.set('No estás dentro de ninguna liga activa.');
            this.cargando.set(false);
            this.cdr.detectChanges();
          }
        },
        error: () => {
          this.error.set('No se pudo obtener la información de tu liga actual.');
          this.cargando.set(false);
          this.cdr.detectChanges();
        }
      });
    }, 50);
  }

  ngOnDestroy(): void {
    this.pollingSub?.unsubscribe();
  }

  private iniciarChat(): void {
    this.error.set('');
    this.cargarChat();
    this.pollingSub = interval(5000).subscribe(() => this.cargarChat());
  }

  cargarChat(): void {
    if (!this.ligaId) return;

    this.chatService.getMensajesLiga(this.ligaId).subscribe({
      next: (data) => {
        const esNuevoMensaje = this.mensajes().length !== data.length;
        this.mensajes.set(data);
        this.error.set('');
        this.cargando.set(false);
        this.cdr.detectChanges();
        
        if (esNuevoMensaje) {
          this.hacerScrollAbajo();
        }
      },
      error: (err) => {
        if (err.status === 404) {
          this.mensajes.set([]);
        } else {
          this.error.set(err.error?.error || err.error?.message || 'Error de conexión con el chat.');
        }
        this.cargando.set(false);
        this.cdr.detectChanges();
      }
    });
  }

  enviar(): void {
    const texto = this.nuevoMensaje().trim();
    if (!texto || !this.ligaId) return;

    this.chatService.enviarMensaje(this.ligaId, texto).subscribe({
      next: (res) => {
        if (res.mensaje_creado) {
          this.mensajes.update(msgs => [...msgs, res.mensaje_creado]);
          this.nuevoMensaje.set('');
          this.cdr.detectChanges();
          this.hacerScrollAbajo();
        } else {
          this.nuevoMensaje.set('');
          this.cargarChat();
        }
      },
      error: (err) => {
        this.error.set(err.error?.error || err.error?.message || 'No se pudo enviar el mensaje');
        this.cdr.detectChanges();
      }
    });
  }

  private hacerScrollAbajo(): void {
    setTimeout(() => {
      if (this.scrollContainer?.nativeElement) {
        this.scrollContainer.nativeElement.scrollTop = this.scrollContainer.nativeElement.scrollHeight;
      }
    }, 100);
  }
}