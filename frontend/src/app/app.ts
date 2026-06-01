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

  private ligasService = inject(LigasService);

  ngOnInit(): void {
    this.ligasService.getLigaActual().subscribe();
  }

}
