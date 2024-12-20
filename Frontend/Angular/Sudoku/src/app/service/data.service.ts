import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders} from '@angular/common/http';
import { Observable } from 'rxjs';
import { CookieService } from 'ngx-cookie-service';

@Injectable({
  providedIn: 'root',
})
export class DataService {
  private baseUrl = 'http://127.0.0.1:8000/api'; // Base API URL
  // private gameUrl = 'http://localhost:8080'
  // private token: string | null = null;

  constructor(private httpclient: HttpClient,private cookieService: CookieService) {}

  // Register a new user
  RegisterUser(data: any): Observable<any> {
    return this.httpclient.post(`${this.baseUrl}/users/register`, data);
  }

  // Login user
  loginUser(credentials: any): Observable<any> {
    return this.httpclient.post(`${this.baseUrl}/users/login`, credentials);
  }

  // setToken(token: string): void {
  //   this.token = token;
  // }
  // Get user profile
  getUserProfile(): Observable<any> {
    const token = this.cookieService.get('auth_token'); // Get the token from cookies
    if (!token) {
      throw new Error('Authentication token is missing');
    }
  
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`, // Attach the token
    });
  
    return this.httpclient.get(`${this.baseUrl}/users/retrieve`, { headers });
  }

  updateUserProfile(data: any): Observable<any> {
    const token = this.cookieService.get('auth_token'); // Get the token from localStorage
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
    });
    return this.httpclient.put('http://127.0.0.1:8000/api/users/update-profile', data, { headers });
  }
  
  deleteUserAccount(userId: number): Observable<any> {
    const token = this.cookieService.get('auth_token'); // Get the token from localStorage
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
    });
    return this.httpclient.delete(`http://127.0.0.1:8000/api/users/delete-Profile`, { headers });
  }
  
}
