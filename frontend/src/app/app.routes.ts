import { Routes } from '@angular/router';
import { Noticias } from './noticias/noticias';
import { ChatLiga } from './chat-liga/chat-liga';
import { Clasificacion } from './clasificacion/clasificacion';
import { Equipos } from './equipos/equipos';
import { Partidos } from './partidos/partidos';
import { Foro } from './foro/foro';
import { Ligas } from './ligas/ligas';
import { Login } from './login/login';
import { Start } from './start/start';
import { Jugadores } from './jugadores/jugadores';
import { Menu } from './menu/menu'; 
import { ResetPassword } from './reset-password/reset-password';
import { Solicitudes } from './solicitudes/solicitudes';

export const routes: Routes = [
    // Rutas públicas
    { path: '', component: Start },
    { path: 'login', component: Login },
    { path: 'reset-password', component: ResetPassword },
    
    // Rutas privadas (Con Navbar)
    {
        path: 'menu', 
        component: Menu, 
        children: [
            // -- SECCIÓN GLOBAL --
            { path: 'ligas', component: Ligas },
            { path: 'equipos', component: Equipos },
            { path: 'equipo/:equipo_id', component: Jugadores }, 
            { path: 'foro', component: Foro },
            { path: 'noticias', component: Noticias },

            // -- SECCIÓN LIGA ACTUAL --
            { path: 'partidos', component: Partidos },
            { path: 'clasificacion', component: Clasificacion },
            { path: 'chat', component: ChatLiga },
            { path: 'solicitudes', component: Solicitudes },

            // Redirección por defecto
            { path: '', redirectTo: 'ligas', pathMatch: 'full' }
        ]
    },

    // Ruta comodín
    { path: '**', redirectTo: '' }
];