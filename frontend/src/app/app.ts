import { Component, signal, inject, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { LigasService } from './services/ligas-service';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet],
  templateUrl: './app.html',
  styleUrl: './app.css'
})
export class App implements OnInit {
  protected readonly title = signal('proyecto');
  
  public appLista = signal<boolean>(false);

  private ligasService = inject(LigasService);

  ngOnInit(): void {
    this.ligasService.getLigaActual().subscribe({
      next: () => {
        this.appLista.set(true);
      },
      error: (err) => {
        console.error('Error obteniendo liga inicial', err);
        this.appLista.set(true); 
      }
    });
  }
}