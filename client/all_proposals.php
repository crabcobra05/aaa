<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_client'] == 0) {
  header("Location: ../freelancer/index.php");
}

// Function to get all proposals for all gigs posted by this client
function getAllProposalsForClient($pdo, $client_id) {
  $sql = "SELECT 
            gig_proposals.gig_id,
            gigs.gig_title,
            fiverr_users.user_id,
            fiverr_users.first_name,
            fiverr_users.last_name,
            gig_proposals.gig_proposal_description AS description,
            gig_proposals.date_added
          FROM gig_proposals
          JOIN gigs ON gig_proposals.gig_id = gigs.gig_id
          JOIN fiverr_users ON gig_proposals.user_id = fiverr_users.user_id
          WHERE gigs.user_id = ?
          ORDER BY gig_proposals.date_added DESC";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$client_id]);
  return $stmt->fetchAll();
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
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
        font-family: "Arial", sans-serif;
        background-color: var(--light-bg);
      }
      
      .proposal-card {
        transition: all 0.3s ease;
        height: 100%;
        border-radius: 8px;
        border-left: 4px solid var(--secondary-color);
        background-color: white;
      }
      
      .proposal-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      }
      
      .card-footer {
        background-color: white;
        border-top: none;
      }
      
      .card-title {
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
        color: var(--primary-color);
      }
      
      .proposal-date {
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-style: italic;
      }
      
      .gig-title {
        font-size: 1rem;
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.75rem;
      }
      
      .card-text {
        color: var(--text-primary);
        line-height: 1.5;
        margin-bottom: 1rem;
      }
      
      .btn-primary {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
      }
      
      .btn-primary:hover {
        background-color: #d35400;
        border-color: #d35400;
      }
      
      .category-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
      }
      
      .new-tag {
        background-color: #2ecc71;
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 4px;
        position: absolute;
        top: 10px;
        right: 10px;
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-12">
          <h1 class="display-4 text-center mb-4">All Proposals</h1>
          <p class="lead text-center mb-5">Below are all the proposals submitted for your gigs.</p>
        </div>
      </div>

      <div class="row">
        <?php 
        $allProposals = getAllProposalsForClient($pdo, $_SESSION['user_id']);
        if (count($allProposals) > 0) {
          foreach ($allProposals as $proposal) { 
        ?>
          <div class="col-md-4 col-lg-3 mb-4">
            <div class="card shadow proposal-card" data-id="<?php echo $proposal['gig_id']; ?>">
              <?php 
                // Check if proposal is less than 3 days old
                $proposalDate = new DateTime($proposal['date_added']);
                $currentDate = new DateTime();
                $daysDiff = $currentDate->diff($proposalDate)->days;
                
                // Determine category color based on gig title
                $categoryColors = [
                  'Website' => '#3498db', // Blue
                  'Logo' => '#9b59b6',    // Purple
                  'Mobile' => '#2ecc71',  // Green
                  'Content' => '#f1c40f', // Yellow
                  'Digital' => '#e74c3c'  // Red
                ];
                
                $categoryColor = '#3498db'; // Default blue
                foreach ($categoryColors as $keyword => $color) {
                  if (stripos($proposal['gig_title'], $keyword) !== false) {
                    $categoryColor = $color;
                    break;
                  }
                }
                
                // Set border color based on category
                echo '<style>.proposal-card[data-id="'.$proposal['gig_id'].'"] { border-left-color: '.$categoryColor.'; }</style>';
              ?>
              
              <?php if ($daysDiff < 3): ?>
                <span class="new-tag">NEW</span>
              <?php endif; ?>
              
              <div class="card-body">
                <h5 class="card-title"><?php echo $proposal['first_name'] . ' ' . $proposal['last_name']; ?></h5>
                <p class="gig-title mb-2">
                  <span class="category-indicator" style="background-color: <?php echo $categoryColor; ?>"></span>
                  <?php echo $proposal['gig_title']; ?>
                </p>
                <p class="card-text"><?php echo (strlen($proposal['description']) > 100) ? substr($proposal['description'], 0, 100) . '...' : $proposal['description']; ?></p>
                <p class="proposal-date mb-3">Submitted: <?php echo date('F j, Y', strtotime($proposal['date_added'])); ?></p>
              </div>
              <div class="card-footer text-right">
                <a href="get_gig_proposals.php?gig_id=<?php echo $proposal['gig_id']; ?>" class="btn btn-primary">View Details</a>
              </div>
            </div>
          </div>
        <?php 
          }
        } else {
        ?>
          <div class="col-12 text-center">
            <div class="alert alert-info" role="alert">
              No proposals have been submitted for your gigs yet.
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
    <?php include 'includes/footer.php'; ?>
  </body>
</html>
