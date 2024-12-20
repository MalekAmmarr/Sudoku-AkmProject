import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../service/auth.service';
import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
})
export class LoginComponent implements OnInit {
  title = 'Login';
  credentials: any = {
    email: '',
    password: '',
  };

  loading: boolean = false; // Add a loading spinner while processing login

  constructor(
    private router: Router,
    private authService: AuthService,
    private cookieService: CookieService
  ) {}

  ngOnInit(): void {}

  submitForm(form: any): void {
    if (form.valid) {
      this.loading = true; // Start loading spinner
      // Step 1: Get CSRF token
      this.authService.getCsrfToken().subscribe({
        next: () => {
          // Step 2: Attempt login
          this.authService.login(this.credentials).subscribe({
            next: (res: any) => {
              console.log('Login successful:', res);

              if (res.token) {
                const token = res.token;

                // Step 3: Store token in cookies
                this.cookieService.set('auth_token', token, {
                  expires: 7, // Token expires in 7 days
                  sameSite: 'Lax',
                  secure: false, // Ensure cookies are only sent over HTTPS
                  path: '/',
                });

                // Step 4: Redirect to profile page
                this.router.navigate(['/profile']);
              } else {
                alert('Login failed: Token not provided.');
              }
            },
            error: (err) => {
              console.error('Login failed:', err);
              alert(
                err.error?.message || 'Login failed. Please check your credentials.'
              );
            },
            complete: () => {
              this.loading = false; // Stop loading spinner
            },
          });
        },
        error: (err) => {
          console.error('CSRF Token error:', err);
          alert('An error occurred while fetching the CSRF token.');
          this.loading = false; // Stop loading spinner
        },
      });
    } else {
      alert('Form is invalid. Please fill out all required fields correctly.');
    }
  }

  navigateToGamePage(): void {
    // Redirect to the Vue app
    window.location.href = 'http://localhost:8080/';
  }
}
