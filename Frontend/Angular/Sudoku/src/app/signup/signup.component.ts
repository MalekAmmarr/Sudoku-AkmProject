import { Component, OnInit } from '@angular/core';
import { DataService } from '../service/data.service';
import { Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';


@Component({
  selector: 'app-signup',
  templateUrl: './signup.component.html',
  styleUrls: ['./signup.component.css']
})
export class SignupComponent implements OnInit {

  title = 'Signup';
  users: any = {
    name: '',
    username: '',
    email: '',
    password: '',
    picture: null,
    preferences: '',
    creditCard: '',
    expiryDate: '',
    cvv: ''
  };
  
  constructor(private router: Router, private dataService:DataService) { }

  ngOnInit(){
  }

  submitForm(form: any): void {
    if (form.valid) {
      this.dataService.RegisterUser(this.users).subscribe({
        next: (res) => {
          console.log('User created successfully', res);
           // Redirect to the login page after submission
           this.router.navigate(['/login']);
        },
        error: (err) => {
          console.error('Error occurred:', err);
        }
      });

    } else {
      alert('Form is invalid. Please fill out all required fields correctly.');
      }
  }


  onFileChange(event: any): void {
    const file = event.target.files[0];
    if (file) {
      this.users.picture = file;
    }
  }

  formatCreditCardNumber(event: any): void {
    const input = event.target;
    let value = input.value.replace(/\D/g, ''); // Remove all non-digit characters
    value = value.replace(/(\d{4})(?=\d)/g, '$1-'); // Add hyphen after every 4 digits
    input.value = value; // Update the input value
   // this.signupForm.patchValue({ creditCard: value }); // Sync value to the form control
  }

  formatExpiryDate(event: any): void {
    const input = event.target;
    let value = input.value.replace(/\D/g, ''); // Remove all non-digit characters
    value = value.replace(/(\d{2})(?=\d)/g, '$1/'); // Add hyphen after every 4 digits
    input.value = value; // Update the input value
    //this.signupForm.patchValue({ expiryDate: value }); // Sync value to the form control
  }

  formatCvv(event: any): void {
    const input = event.target;
    let value = input.value.replace(/\D/g, ''); // Remove all non-digit characters
    input.value = value; // Update the input value

  }
  
}
