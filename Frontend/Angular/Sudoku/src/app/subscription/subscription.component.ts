import { Component } from '@angular/core';

@Component({
  selector: 'app-subscription',
  templateUrl: './subscription.component.html',
  styleUrl: './subscription.component.css'
})
export class SubscriptionComponent {
  cardNumber: string = '';
  expiryDate: string = '';
  cvv: string = '';

  // Method to handle payment form submission
  onPaymentSubmit() {
    console.log('Payment submitted:', {
      cardNumber: this.cardNumber,
      expiryDate: this.expiryDate,
      cvv: this.cvv
    });

    // Store the card information in local storage (for demo purposes only)
    localStorage.setItem('cardDetails', JSON.stringify({
      cardNumber: this.cardNumber,
      expiryDate: this.expiryDate,
      cvv: this.cvv
    }));

    
    alert('Payment simulated! (This is a demo implementation)');
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
