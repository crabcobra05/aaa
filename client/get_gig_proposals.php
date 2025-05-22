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

      .card {
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.1);
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

      .timeline-container {
        height: 80px;
        position: relative;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
      }
      
      .timeline-event {
        position: absolute;
        height: 24px;
        top: 28px;
        border-radius: 4px;
        color: white;
        font-size: 0.75rem;
        padding: 4px 8px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }

      .form-group label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
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

      .is-invalid {
        border-color: var(--danger);
      }

      .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 0.375rem;
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

      .table {
        margin-bottom: 0;
      }

      .table th {
        border-top: none;
        font-weight: 600;
        color: var(--text-primary);
      }

      .table td {
        vertical-align: middle;
      }

      .text-success {
        color: var(--success) !important;
      }

      .text-danger {
        color: var(--danger) !important;
      }

      .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 2rem;
        text-align: center;
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid py-5">
      <h1 class="section-title">Gig Proposals</h1>
      <div class="row justify-content-center">
        <?php $getGigById = getGigById($pdo, $_GET['gig_id']); ?>
        <div class="col-md-5">
          <div class="card shadow mb-4">
            <div class="card-header">
              <h4><?php echo $getGigById['gig_title']; ?></h4>
            </div>
            <div class="card-body">
              <p class="mb-4"><?php echo $getGigById['gig_description']; ?></p>
              <div class="text-muted">
                <small>Posted: <?php echo date('F j, Y', strtotime($getGigById['date_added'])); ?></small>
                <br>
                <small>By: <?php echo $_SESSION['username']; ?></small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="card shadow">
            <div class="card-header">
              <h4>Scheduled Interviews</h4>
            </div>
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th>Freelancer</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $getAllInterviewsByGig = getAllInterviewsByGig($pdo, $_GET['gig_id']); ?>
                  <?php foreach ($getAllInterviewsByGig as $row) { ?>
                  <tr>
                    <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                    <td><?php echo date('M j, Y g:i A', strtotime($row['time_start'])); ?></td>
                    <td><?php echo date('M j, Y g:i A', strtotime($row['time_end'])); ?></td>
                    <td>
                      <?php 
                        if ($row['status'] == "Accepted") {
                          echo "<span class='text-success'>Accepted</span>";
                        }
                        if ($row['status'] == "Rejected") {
                          echo "<span class='text-danger'>Rejected</span>";
                        } 
                        if ($row['status'] == "Pending") {
                          echo "<span class='text-warning'>Pending</span>";
                        }
                      ?>  
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row justify-content-center px-4 pb-5">
      <?php $getProposalsByGigId = getProposalsByGigId($pdo, $_GET['gig_id']); ?>
      <?php foreach ($getProposalsByGigId as $row) { ?>
      <div class="col-md-4 mb-4">
        <div class="card shadow gigProposalContainer">
          <div class="card-body">
            <h2 class="h4 mb-3"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></h2>
            <p class="mb-3"><?php echo $row['description']; ?></p>
            <p class="text-muted mb-4"><small>Submitted: <?php echo date('F j, Y', strtotime($row['date_added'])); ?></small></p>
            
            <form class="addNewInterviewForm d-none">
              <div class="form-group">
                <label>Interview Start Time</label>
                <input type="hidden" class="freelancer_id" value="<?php echo $row['user_id']; ?>">
                <input type="hidden" class="gig_id" value="<?php echo $_GET['gig_id']; ?>">
                <input type="datetime-local" class="time_start form-control">
                <div class="invalid-feedback time-start-feedback"></div>
              </div>
              <div class="form-group">
                <label>Interview End Time</label>
                <input type="datetime-local" class="time_end form-control">
                <div class="invalid-feedback time-end-feedback"></div>
              </div>
              
              <div class="interview-timeline mt-4 mb-4 d-none">
                <h6 class="text-muted mb-2">Scheduled Interviews</h6>
                <div class="timeline-container"></div>
              </div>
              
              <button type="submit" class="btn btn-primary btn-block">Schedule Interview</button>
            </form>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script>
      var allInterviews = [];
      
      <?php foreach ($getAllInterviewsByGig as $interview) { ?>
        allInterviews.push({
          start: "<?php echo $interview['time_start']; ?>",
          end: "<?php echo $interview['time_end']; ?>",
          name: "<?php echo $interview['first_name'] . ' ' . $interview['last_name']; ?>"
        });
      <?php } ?>
      
      $('.gigProposalContainer').on('dblclick', function(event) {
        var addNewInterviewForm = $(this).find('.addNewInterviewForm');
        var timelineContainer = $(this).find('.interview-timeline');
        
        addNewInterviewForm.toggleClass('d-none');
        
        if (!addNewInterviewForm.hasClass('d-none')) {
          timelineContainer.removeClass('d-none');
          renderTimeline($(this).find('.timeline-container'));
        }
      });
      
      function renderTimeline(container) {
        container.empty();
        
        if (allInterviews.length === 0) {
          container.html('<p class="text-muted small mb-0">No interviews scheduled yet</p>');
          return;
        }
        
        var earliestTime = new Date(allInterviews[0].start);
        var latestTime = new Date(allInterviews[0].end);
        
        allInterviews.forEach(function(interview) {
          var startTime = new Date(interview.start);
          var endTime = new Date(interview.end);
          
          if (startTime < earliestTime) earliestTime = startTime;
          if (endTime > latestTime) latestTime = endTime;
        });
        
        earliestTime.setHours(earliestTime.getHours() - 1);
        latestTime.setHours(latestTime.getHours() + 1);
        
        var timeRange = latestTime - earliestTime;
        var containerWidth = container.width();
        
        allInterviews.forEach(function(interview, index) {
          var startTime = new Date(interview.start);
          var endTime = new Date(interview.end);
          
          var startPosition = ((startTime - earliestTime) / timeRange) * containerWidth;
          var width = ((endTime - startTime) / timeRange) * containerWidth;
          
          var eventElement = $('<div class="timeline-event"></div>')
            .css({
              'left': startPosition + 'px',
              'width': width + 'px',
              'background-color': index % 2 === 0 ? '#3498db' : '#9b59b6'
            })
            .text(interview.name);
            
          container.append(eventElement);
        });
      }
      
      $('.time_start').on('change', function() {
        validateDateTime($(this), 'start');
      });
      
      $('.time_end').on('change', function() {
        validateDateTime($(this), 'end');
      });
      
      function validateDateTime(input, type) {
        var form = input.closest('form');
        var startInput = form.find('.time_start');
        var endInput = form.find('.time_end');
        var feedback = input.siblings('.invalid-feedback');
        
        input.removeClass('is-invalid');
        feedback.hide();
        
        var selectedDate = new Date(input.val());
        var now = new Date();
        
        if (selectedDate < now) {
          input.addClass('is-invalid');
          feedback.text('Cannot schedule an interview in the past').show();
          return false;
        }
        
        if (type === 'end' && startInput.val()) {
          var startDate = new Date(startInput.val());
          if (selectedDate <= startDate) {
            input.addClass('is-invalid');
            feedback.text('End time must be after start time').show();
            return false;
          }
        }
        
        for (var i = 0; i < allInterviews.length; i++) {
          var interviewStart = new Date(allInterviews[i].start);
          var interviewEnd = new Date(allInterviews[i].end);
          
          if ((selectedDate >= interviewStart && selectedDate <= interviewEnd)) {
            input.addClass('is-invalid');
            feedback.text('This time conflicts with ' + allInterviews[i].name + '\'s interview').show();
            return false;
          }
        }
        
        return true;
      }

      $('.addNewInterviewForm').on('submit', function(event) {
        event.preventDefault();
        var form = $(this);
        var startInput = form.find('.time_start');
        var endInput = form.find('.time_end');
        
        if (!startInput.val() || !endInput.val()) {
          if (!startInput.val()) {
            startInput.addClass('is-invalid');
            startInput.siblings('.invalid-feedback').text('Please select a start time').show();
          }
          if (!endInput.val()) {
            endInput.addClass('is-invalid');
            endInput.siblings('.invalid-feedback').text('Please select an end time').show();
          }
          return;
        }
        
        if (!validateDateTime(startInput, 'start') || !validateDateTime(endInput, 'end')) {
          return;
        }
        
        var formData = {
          freelancer_id: form.find('.freelancer_id').val(),
          gig_id: form.find('.gig_id').val(),
          time_start: startInput.val(),
          time_end: endInput.val(),
          insertNewGigInterview: 1
        };

        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function(data) {
            if (data) {
              location.reload();
            } else {
              var errorMsg = $('<div class="alert alert-danger mt-3"></div>')
                .text("Could not schedule interview. Please check the selected times and try again.");
              form.prepend(errorMsg);
            }
          }
        });
      });
    </script>
  </body>
</html>