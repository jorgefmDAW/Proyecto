import { Routes } from '@angular/router';
import { Noticias } from './noticias/noticias';
import { ChatLiga } from './chat-liga/chat-liga';
import { Clasificacion } from './clasificacion/clasificacion';
import { Equipos } from './equipos/equipos';
import { Partidos } from './partidos/partidos';
import { Foro } from './foro/foro';
import { Ligas } from './ligas/ligas';

export const routes: Routes = [
    { path: 'noticias', component: Noticias },
    { path: 'chat', component: ChatLiga },
    { path: 'clasificacion', component: Clasificacion },
    { path: 'equipos', component: Equipos },
    { path: 'partidos', component: Partidos},
    { path: 'foro', component: Foro },
    { path: 'ligas', component: Ligas },
    {path: "", redirectTo: "ligas", pathMatch: "full"}
];