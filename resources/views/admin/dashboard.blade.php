<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <!-- Google Fonts for better typography -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <!-- Icons (FontAwesome or any other) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            display: flex;
            height: 100vh;
            background: #f0f2f5;
        }
        .sidebar {
            width: 250px;
            background: #00d0aa;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar .logo {
            padding: 20px;
            text-align: center;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .sidebar ul li:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar ul li i {
            margin-right: 10px;
        }
        .sidebar .bottom-links {
            border-top: 1px solid rgba(255,255,255,0.3);
        }
        .header {
            position: sticky;
            top: 0;
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
        }
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .content {
            padding: 20px;
        }
        .logout-btn {
            background: #ff4d4d;
            border: none;
            padding: 8px 15px;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <div class="logo">
                <img src="/your-logo.png" alt="Logo" style="max-width:100px;">
            </div>
            <ul>
                <li><i class="fas fa-tachometer-alt"></i> Dashboard</li>
                <li><i class="fas fa-users"></i> User Management</li>
                <li><i class="fas fa-credit-card"></i> Payment Management</li>
                <li><i class="fas fa-coins"></i> Subscription & Credit Management</li>
                <li><i class="fas fa-file-alt"></i> Content Management</li>
                <li><i class="fas fa-cogs"></i> System & Role Management</li>
            </ul>
            <ul class="bottom-links">
                <li><i class="fas fa-bell"></i> Notifications</li>
                <li><i class="fas fa-comment"></i> Feedback</li>
                <li><i class="fas fa-cog"></i> Settings</li>
            </ul>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <li style="list-style:none; padding:15px 20px; cursor:pointer;">
                <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </li>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <h2>Welcome, {{ Auth::user()->name }}</h2>
            <div>
                <small>Admin Panel</small>
            </div>
        </div>
        <div class="content">
            <h3>Dashboard Content</h3>
            <p>This is your dashboard. You can add charts, tables, or reports here.</p>
        </div>
    </div>

</body>
</html>
