import { Component, OnInit, Input } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from '../service/data.service';
// import { UserService } from '../user.service';


@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
})
export class ProfileComponent implements OnInit{

  username: string = '';
  email: string = '';
  highestScore: number = 0;
  userId: number = 1; // Example user ID, replace with the actual ID logic
  showUpdateForm = false; // Toggle for update form
  updatedUser = {
    username: '',
    email: '',
  };

  constructor(private dataService: DataService,private router: Router) { }

  ngOnInit(): void {
    this.loadUserProfile(this.userId);
  }

  loadUserProfile(userId: number) {
    this.dataService.getUserProfile().subscribe({
      next: (data) => {
        this.username = data.username;
        this.email = data.email;
        this.highestScore = data.score; // Assuming the API returns a `score` field
      },
      error: (err) => {
        console.error('Error fetching profile:', err);
      }
    });
  }

  
  @Input() paymentDetails: any; // Property to receive payment details

    showPayment = false;
    newPayment = {
    cardNumber: '',
    expiryDate: '',
    cvv: ''
  };

  togglePayment() {
    this.showPayment = !this.showPayment;
  }

  addNewPayment() {
    // Logic to add a new payment; it could push to an array or directly update properties
    console.log('New payment added:', this.newPayment);
    // Reset new payment fields
    this.newPayment = {
      cardNumber: '',
      expiryDate: '',
      cvv: ''
    };
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

  toggleUpdateForm() {
    this.showUpdateForm = !this.showUpdateForm;
  }

  updateProfile() {
    const updatedData = {
      username: this.updatedUser.username || this.username,
      email: this.updatedUser.email || this.email,
    };

    this.dataService.updateUserProfile(updatedData).subscribe({
      next: (res) => {
        console.log('Profile updated successfully:', res);
        alert('Profile updated successfully!');
        this.loadUserProfile(this.userId); // Refresh profile data
        this.showUpdateForm = false; // Hide the form
      },
      error: (err) => {
        console.error('Error updating profile:', err);
        alert('Failed to update profile. Please try again.');
      },
    });
  }

  deleteAccount() {
    if (confirm('Are you sure you want to delete your account?')) {
      this.dataService.deleteUserAccount(this.userId).subscribe({
        next: () => {
          alert('Account deleted successfully.');
          this.router.navigate(['/login']); // Redirect to login page
        },
        error: (err) => {
          console.error('Error deleting account:', err);
          alert('Failed to delete account. Please try again.');
        },
      });
    }
  }
  

  navigateToGamePage() {
    window.location.href = 'http://localhost:8080/';  // Redirects to the Vue app
}


}
