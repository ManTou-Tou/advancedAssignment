<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 48px;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        
        h1 {
            font-size: 32px;
            margin-bottom: 16px;
            color: #222;
        }
        
        .welcome-text {
            font-size: 18px;
            color: #222;
            margin-bottom: 8px;
        }
        
        .status-text {
            font-size: 16px;
            color: #666;
            margin-bottom: 32px;
        }
        
        .logout-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .logout-btn:hover {
            background: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="dashboard-card">
        <h1>Admin Dashboard</h1>
        <p class="welcome-text">Welcome back, {{ Auth::guard('admin')->user()->name }}!</p>
        <p class="status-text">You are logged in as Administrator.</p>
        
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>