<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Sign-Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 5px 0;
        }
        input, textarea, button {
            padding: 10px;
            margin-bottom: 10px;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
        .text-danger {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>User Sign-Up</h1>

    <!-- Form to Create a New Account -->
    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required minlength="3">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8">

        <label for="picture">Profile Picture</label>
        <input type="file" id="picture" name="picture">

        <label for="preferences">Preferences</label>
        <textarea id="preferences" name="preferences" rows="3"></textarea>

        <label for="creditCard">Credit Card</label>
        <input type="text" id="creditCard" name="creditCard" maxlength="19" placeholder="1234-5678-9101-1121">

        <label for="expiryDate">Expiry Date</label>
        <input type="text" id="expiryDate" name="expiryDate" maxlength="5" placeholder="MM/YY">

        <label for="cvv">CVV</label>
        <input type="text" id="cvv" name="cvv" maxlength="3">

        <button type="submit">Sign Up</button>
    </form>

    <!-- Existing Users Section -->
    <h2>Registered Users</h2>
    @if($users->isEmpty())
        <p>No registered users. Sign up using the form above.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Score</th>
                    <th>Update Score</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->score }}</td>
                        <td>
                            <form action="{{ route('users.addScore', $user->id) }}" method="POST">
                                @csrf
                                <input type="number" name="score" placeholder="Add Score" min="0" required>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
