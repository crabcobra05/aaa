<?php require_once 'core/dbConfig.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --accent-color: #e67e22;
      --light-bg: #f8f9fa;
      --text-primary: #212529;
      --text-secondary: #6c757d;
    }
    
    body {
      font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    .register-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
      overflow: hidden;
    }

    .register-header {
      background: var(--primary-color);
      color: white;
      padding: 2rem;
      text-align: center;
    }

    .register-header h2 {
      margin: 0;
      font-weight: 600;
      font-size: 1.75rem;
    }

    .register-body {
      padding: 2rem;
    }

    .form-group label {
      font-weight: 500;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }

    .form-control {
      border-radius: 8px;
      border: 2px solid rgba(0,0,0,0.1);
      padding: 0.75rem 1rem;
      font-size: 1rem;
      transition: all 0.2s ease;
    }

    .form-control:focus {
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .btn-primary {
      background-color: var(--accent-color);
      border-color: var(--accent-color);
      padding: 0.75rem 2rem;
      font-weight: 600;
      border-radius: 8px;
      width: 100%;
      margin-top: 1.5rem;
    }

    .btn-primary:hover {
      background-color: #d35400;
      border-color: #d35400;
      transform: translateY(-1px);
    }

    .alert {
      border-radius: 8px;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background-color: #2ecc71;
      border-color: #27ae60;
      color: white;
    }

    .alert-danger {
      background-color: #e74c3c;
      border-color: #c0392b;
      color: white;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="register-card">
          <div class="register-header">
            <h2>Create Your Account</h2>
            <p class="mb-0">Join our community of clients and find the perfect talent</p>
          </div>
          <div class="register-body">
            <?php  
            if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
              $alertClass = $_SESSION['status'] == "200" ? "alert-success" : "alert-danger";
              echo "<div class='alert {$alertClass}'>{$_SESSION['message']}</div>";
            }
            unset($_SESSION['message']);
            unset($_SESSION['status']);
            ?>
            <form action="core/handleForms.php" method="POST">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
              </div>
              <button type="submit" class="btn btn-primary" name="insertNewUserBtn">Create Account</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>