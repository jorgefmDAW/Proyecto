import { Component, OnInit, inject, signal } from '@angular/core';
import { Equipo } from '../services/equipo';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-jugadores',
  imports: [],
  templateUrl: './jugadores.html',
  styleUrl: './jugadores.css',
})
export class Jugadores implements OnInit{

  private equipoService = inject(Equipo);
  private route = inject(ActivatedRoute);
  
  jugador = signal<any[]>([]);
  nombreEquipo = signal<string>('');
  escudoEquipo = signal<string>('');

  ngOnInit() {
    const id = Number(this.route.snapshot.paramMap.get('equipo_id'));
    
    if (id) {
      this.cargarJugadoresEquipos(id);
    }
  }

  cargarJugadoresEquipos(id: number) {
    this.equipoService.obtenerJugadoresPorEquipo(id).subscribe({
      next: (data) => {
        this.nombreEquipo.set(data.equipo_buscado);
        this.escudoEquipo.set(data.escudo_equipo);
        
        let listaJugadores = data.jugadores || [];

        const ordenPosiciones: { [key: string]: number } = {
          'POR': 1,
          'DEF': 2,
          'MED': 3,
          'DEL': 4
        };

        listaJugadores.sort((a: any, b: any) => {
          const pesoA = ordenPosiciones[a.posicion];
          const pesoB = ordenPosiciones[b.posicion] ;
          
          return pesoA - pesoB;
        });

        this.jugador.set(listaJugadores);
      },
      error: (err) => console.error('Error al cargar los jugadores', err)
    });
  }
}
