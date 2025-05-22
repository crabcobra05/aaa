<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 0) {
  header("Location: ../freelancer/index.php");
}
?>

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
        --success: #2ecc71;
        --danger: #e74c3c;
        --warning: #f1c40f;
      }
      
      body {
        font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: var(--light-bg);
        color: var(--text-primary);
      }

      .welcome-section {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        padding: 4rem 0;
        margin-bottom: 3rem;
        color: white;
        text-align: center;
      }

      .welcome-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
      }

      .welcome-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
      }

      .card {
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.1);
        transition: all 0.3s ease;
      }

      .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.1);
      }

      .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        padding: 1.5rem;
      }

      .card-header h4 {
        margin: 0;
        color: var(--primary-color);
        font-weight: 600;
      }

      .card-body {
        padding: 1.5rem;
      }

      .btn-primary {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        border-radius: 6px;
      }

      .btn-primary:hover {
        background-color: #d35400;
        border-color: #d35400;
      }

      .form-control {
        border-radius: 6px;
        border: 1px solid rgba(0,0,0,0.2);
        padding: 0.625rem 1rem;
        font-size: 0.95rem;
      }

      .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
      }

      .meta-info {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-top: 1rem;
      }

      .meta-info i {
        margin-right: 0.5rem;
      }

      .create-gig-form {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 3rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      }

      .form-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
      }

      .no-records {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 12px;
        margin-top: 2rem;
      }

      .no-records h3 {
        color: var(--text-secondary);
        font-weight: 500;
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="welcome-section">
      <div class="container">
        <h1 class="welcome-title">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <p class="welcome-subtitle">Find the perfect talent for your projects</p>
      </div>
    </div>

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <button class="showCreateGigForm btn btn-primary btn-lg mb-4">Create New Gig</button>
          
          <form class="createNewGig create-gig-form d-none">
            <h3 class="form-title">Create a New Gig</h3>
            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" id="title" class="title form-control" placeholder="Enter gig title">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="description form-control" id="description" rows="4" placeholder="Describe your gig requirements"></textarea>
              <button type="submit" class="btn btn-primary mt-4">Create Gig</button>
            </div>
          </form>

          <?php 
          $getAllGigs = getAllGigs($pdo);
          if (!empty($getAllGigs)) {
            foreach ($getAllGigs as $row) { 
          ?>
            <div class="card shadow mb-4">
              <div class="card-header">
                <h4><?php echo $row['title']; ?></h4>
              </div>
              <div class="card-body">
                <p class="mb-4"><?php echo $row['description']; ?></p>
                <div class="meta-info">
                  <i>Posted by: <?php echo $row['username']; ?></i>
                  <br>
                  <i>Date: <?php echo date('F j, Y', strtotime($row['date_added'])); ?></i>
                </div>
              </div>
            </div>
          <?php 
            }
          } else { 
          ?>
            <div class="no-records">
              <h3>No gigs posted yet</h3>
              <p>Create your first gig to start finding talent!</p>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script>
      $('.showCreateGigForm').on('click', function() {
        $('.createNewGig').toggleClass('d-none');
      });

      $('.createNewGig').on('submit', function(event) {
        event.preventDefault();

        var formData = {
          title: $(this).find('.title').val(),
          description: $(this).find('.description').val(),
          createNewGig: 1
        };

        if (formData.title && formData.description) {
          $.ajax({
            type: "POST",
            url: "core/handleForms.php",
            data: formData,
            success: function(data) {
              location.reload();
            }
          });
        } else {
          alert("Please fill in all fields");
        }
      });
    </script>
  </body>
</html>