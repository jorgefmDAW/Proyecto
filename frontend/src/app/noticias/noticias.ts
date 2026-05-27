import { Component, inject, signal } from '@angular/core';
import { NoticiasService } from '../services/noticias-service';

@Component({
  selector: 'app-noticias',
  imports: [],
  templateUrl: './noticias.html',
  styleUrl: './noticias.css',
})
export class Noticias {
  private service = inject(NoticiasService)
  public noticias = signal<any[]>([])

  ngOnInit(): void {
    this.getAllNoticias()
  }

  getAllNoticias(): void {
    this.service.getAllNoticias().subscribe({
      next: (res:any) => {
        this.noticias.set(res);
      },
      error: (err) => console.error('Error mostrando todas las noticias', err)
    })
  }
}
