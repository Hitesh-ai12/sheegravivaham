<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .login-container { width: 300px; margin: 100px auto; background: #fff; padding: 20px; border-radius: 8px; }
        input { width: 100%; padding: 10px; margin: 10px 0; }
        button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        @if ($errors->any())
            <div style="color:red;">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
