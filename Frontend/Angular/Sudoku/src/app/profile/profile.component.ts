// profile.component.ts
import { Component, OnInit } from '@angular/core';
import { AuthService } from '../auth.service';  // Adjust the path based on your folder structure


@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
})
export class ProfileComponent implements OnInit {
  isLoggedIn = false;

  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    // Check login status when component initializes
    this.isLoggedIn = this.authService.isLoggedIn();
  }

  // Mock method to log in (for testing purposes)
  login() {
    this.authService.login();
    this.isLoggedIn = this.authService.isLoggedIn();
  }

  // Mock method to log out (for testing purposes)
  logout() {
    this.authService.logout();
    this.isLoggedIn = this.authService.isLoggedIn();
  }
}
