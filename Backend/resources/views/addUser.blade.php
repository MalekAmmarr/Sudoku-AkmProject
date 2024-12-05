<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Add User</h2>
    <form action="/UserAdd" method="POST">
        @csrf <!-- Laravel CSRF token for form security -->

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
        </div>

        <!-- Score -->
        <div class="mb-3">
            <label for="score" class="form-label">Score</label>
            <input type="number" name="score" id="score" class="form-control" placeholder="Enter score" min="0" value="0">
        </div>

        <!-- Credit Card Details -->
        <h4 class="mt-4">Credit Card Details</h4>

        <!-- Card Number -->
        <div class="mb-3">
            <label for="card_number" class="form-label">Card Number</label>
            <input type="text" name="card_number" id="card_number" class="form-control" placeholder="Enter card number (16 digits)" maxlength="16" pattern="\d{16}" required>
        </div>

        <!-- Card Brand -->
        <div class="mb-3">
            <label for="card_brand" class="form-label">Card Brand</label>
            <input type="text" name="card_brand" id="card_brand" class="form-control" placeholder="Enter card brand (e.g., Visa, MasterCard)" required>
        </div>

        <!-- Card Expiration Date -->
        <div class="mb-3">
            <label for="card_expires_at" class="form-label">Card Expiration Date</label>
            <input type="date" name="card_expires_at" id="card_expires_at" class="form-control" required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Add User</button>
        
        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
