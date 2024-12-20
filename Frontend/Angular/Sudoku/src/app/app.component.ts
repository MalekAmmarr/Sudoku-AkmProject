import { Component, OnInit } from '@angular/core';
import { AuthService } from './service/auth.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent implements OnInit {
  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    // Initialize CSRF token
    this.authService.getCsrfToken().subscribe({
      next: () => {
        console.log('CSRF token initialized');
      },
      error: (err) => {
        console.error('Error initializing CSRF token:', err);
      },
    });
  }
}
