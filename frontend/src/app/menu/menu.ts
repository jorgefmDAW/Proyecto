import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { Navbar } from '../navbar/navbar';

@Component({
  selector: 'app-menu',
  imports: [Navbar, RouterOutlet],
  templateUrl: './menu.html',
  styleUrl: './menu.css'
})
export class Menu {}
