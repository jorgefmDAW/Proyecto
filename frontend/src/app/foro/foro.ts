import { Component, inject, signal } from '@angular/core';
import { ForoService } from '../services/foro-service';

@Component({
  selector: 'app-foro',
  imports: [],
  templateUrl: './foro.html',
  styleUrl: './foro.css',
})
export class Foro {
  private service = inject(ForoService)
  public mensajes = signal<any[]>([])

  ngOnInit(): void {
    this.getAllMensajes()
  }

  getAllMensajes(): void {
    this.service.getAllMensajes().subscribe({
      next: (res:any) => {
        this.mensajes.set(res);
      },
      error: (err) => console.error('Error mostrando todos los mensajes', err)
    })
  }
}
