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
      
      .proposal-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.1);
        background-color: white;
        position: relative;
        overflow: hidden;
      }
      
      .proposal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.1);
      }
      
      .card-body {
        padding: 1.5rem;
      }
      
      .card-footer {
        background-color: white;
        border-top: 1px solid rgba(0,0,0,0.1);
        padding: 1rem 1.5rem;
      }
      
      .card-title {
        font-weight: 700;
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
        color: var(--primary-color);
        line-height: 1.3;
      }
      
      .proposal-date {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-style: italic;
        margin-bottom: 1rem;
      }
      
      .gig-title {
        font-size: 1rem;
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
      }
      
      .card-text {
        color: var(--text-primary);
        line-height: 1.6;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
      }
      
      .btn-primary {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
      }
      
      .btn-primary:hover {
        background-color: #d35400;
        border-color: #d35400;
        transform: translateY(-1px);
      }
      
      .category-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 8px;
      }
      
      .new-tag {
        background-color: var(--success);
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-weight: 500;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(46, 204, 113, 0.2);
      }

      .proposal-meta {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
      }

      .proposal-meta > * + * {
        margin-left: 1rem;
        padding-left: 1rem;
        border-left: 1px solid rgba(0,0,0,0.1);
      }

      .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        text-align: center;
      }

      .section-subtitle {
        font-size: 1.125rem;
        color: var(--text-secondary);
        text-align: center;
        margin-bottom: 3rem;
      }

      @media (max-width: 768px) {
        .section-title {
          font-size: 2rem;
        }
        
        .section-subtitle {
          font-size: 1rem;
        }
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid py-5">
      <div class="row justify-content-center">
        <div class="col-12">
          <h1 class="section-title">All Proposals</h1>
          <p class="section-subtitle">Review and manage proposals submitted for your gigs</p>
        </div>
      </div>

      <div class="row">
        <?php 
        $allProposals = getAllProposalsForClient($pdo, $_SESSION['user_id']);
        if (count($allProposals) > 0) {
          foreach ($allProposals as $proposal) { 
        ?>
          <div class="col-md-4 col-lg-3 mb-4">
            <div class="card proposal-card" data-id="<?php echo $proposal['gig_id']; ?>">
              <?php 
                $proposalDate = new DateTime($proposal['date_added']);
                $currentDate = new DateTime();
                $daysDiff = $currentDate->diff($proposalDate)->days;
                
                $categoryColors = [
                  'Website' => '#3498db',
                  'Logo' => '#9b59b6',
                  'Mobile' => '#2ecc71',
                  'Content' => '#f1c40f',
                  'Digital' => '#e74c3c'
                ];
                
                $categoryColor = '#3498db';
                foreach ($categoryColors as $keyword => $color) {
                  if (stripos($proposal['gig_title'], $keyword) !== false) {
                    $categoryColor = $color;
                    break;
                  }
                }
              ?>
              
              <?php if ($daysDiff < 3): ?>
                <span class="new-tag">NEW</span>
              <?php endif; ?>
              
              <div class="card-body">
                <h5 class="card-title"><?php echo $proposal['first_name'] . ' ' . $proposal['last_name']; ?></h5>
                
                <div class="proposal-meta">
                  <span><?php echo date('M j, Y', strtotime($proposal['date_added'])); ?></span>
                  <span><?php echo $daysDiff == 0 ? 'Today' : ($daysDiff == 1 ? 'Yesterday' : $daysDiff . ' days ago'); ?></span>
                </div>

                <p class="gig-title">
                  <span class="category-indicator" style="background-color: <?php echo $categoryColor; ?>"></span>
                  <?php echo $proposal['gig_title']; ?>
                </p>
                
                <p class="card-text"><?php echo (strlen($proposal['description']) > 150) ? substr($proposal['description'], 0, 150) . '...' : $proposal['description']; ?></p>
              </div>
              
              <div class="card-footer">
                <a href="get_gig_proposals.php?gig_id=<?php echo $proposal['gig_id']; ?>" class="btn btn-primary btn-block">View Details</a>
              </div>
            </div>
          </div>
        <?php 
          }
        } else {
        ?>
          <div class="col-12 text-center">
            <div class="alert alert-info" role="alert">
              <h4 class="alert-heading mb-3">No Proposals Yet</h4>
              <p class="mb-0">When freelancers submit proposals for your gigs, they'll appear here.</p>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
    <?php include 'includes/footer.php'; ?>
  </body>
</html>