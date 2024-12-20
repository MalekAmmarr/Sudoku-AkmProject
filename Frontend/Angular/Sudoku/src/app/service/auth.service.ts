import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable,BehaviorSubject } from 'rxjs';
import { CookieService } from 'ngx-cookie-service';



@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private baseUrl = 'http://127.0.0.1:8000'; // Laravel backend URL
  private loggedIn = new BehaviorSubject<boolean>(this.hasToken());

  constructor(private http: HttpClient,private cookieService: CookieService) {}

  // Get CSRF token
  // Get CSRF token
  getCsrfToken(): Observable<any> {
    return this.http.get(`${this.baseUrl}/sanctum/csrf-cookie`, { withCredentials: true });
    
  }

   // Check if the token exists in cookies
   private hasToken(): boolean {
    return !!this.cookieService.get('auth_token');
  }

  // Observable for login state
  isLoggedIn() {
    return this.loggedIn.asObservable();
  }
  // Login user
  /*login(credentials: { email: string; password: string }): Observable<any> {
    return this.http.post(`${this.baseUrl}/api/users/login`, credentials);
  }*/

  // Login user
  login(credentials: { email: string; password: string }): Observable<any> {
    this.loggedIn.next(true); // Update login state
    return this.http.post(`${this.baseUrl}/api/users/login`, credentials, { withCredentials: true });
    
  }

  logout(): void {
    this.cookieService.delete('auth_token', '/');
    this.loggedIn.next(false);
  }
  
}
