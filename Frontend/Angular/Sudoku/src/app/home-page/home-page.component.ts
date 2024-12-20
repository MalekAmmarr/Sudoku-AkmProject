import { Component } from '@angular/core';
import { Router } from '@angular/router';


@Component({
  selector: 'app-home-page',
  templateUrl: './home-page.component.html',
  styleUrl: './home-page.component.css'
})
export class HomePageComponent {
  constructor(private router: Router) {}

  navigateToGamePage() {
    // Navigate to the Vue app URL 
    window.location.href = 'http://localhost:8080/';  // Redirects to the Vue app
}
}