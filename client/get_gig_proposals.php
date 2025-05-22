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
        --success: #2ecc71;
        --danger: #e74c3c;
        --warning: #f1c40f;
      }
      
      body {
        font-family: "Arial", sans-serif;
        background-color: var(--light-bg);
      }
      
      .btn-primary {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
      }
      
      .btn-primary:hover {
        background-color: #d35400;
        border-color: #d35400;
      }
      
      .timeline-container {
        height: 60px;
        position: relative;
        background-color: #f8f9fa;
        overflow: hidden;
      }
      
      .timeline-event {
        position: absolute;
        height: 20px;
        top: 20px;
        border-radius: 4px;
        color: white;
        font-size: 10px;
        padding: 2px 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
      
      .is-invalid {
        border-color: var(--danger);
      }
      
      .invalid-feedback {
        display: none;
        color: var(--danger);
        font-size: 0.875rem;
      }
    </style>
  </head>
  <body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="display-4 text-center">Gig Proposals. Double click to add interview</div>
      <div class="row justify-content-center">
        <?php $getGigById = getGigById($pdo, $_GET['gig_id']); ?>
        <div class="col-md-5">
          <div class="card shadow mt-4 p-4">
            <div class="card-header"><h4><?php echo $getGigById['gig_title']; ?> </h4></div>
            <div class="card-body">
              <p><?php echo $getGigById['gig_description']; ?></p>
              <p><i><?php echo $getGigById['date_added']; ?></i></p>
              <p><i><?php echo $_SESSION['username']; ?></i></p>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="card shadow mt-4 p-4">
            <div class="card-header"><h4>Interviews</h4></div>
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Time Start</th>
                    <th scope="col">Time End</th>
                    <th scope="col">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $getAllInterviewsByGig = getAllInterviewsByGig($pdo, $_GET['gig_id']); ?>
                  <?php foreach ($getAllInterviewsByGig as $row) { ?>
                  <tr>
                    <td><?php echo $row['first_name']; ?></td>
                    <td><?php echo $row['last_name']; ?></td>
                    <td><?php echo $row['time_start']; ?></td>
                    <td><?php echo $row['time_end']; ?></td>
                    <td>
                      <?php 
                        if ($row['status'] == "Accepted") {
                          echo "<span class='text-success'>Accepted</span>";
                        }
                        if ($row['status'] == "Rejected") {
                          echo "<span class='text-danger'>Rejected</span>";
                        } 
                        if ($row['status'] == "Pending") {
                          echo "Pending";
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
    <div class="row justify-content-center p-4">
      <?php $getProposalsByGigId = getProposalsByGigId($pdo, $_GET['gig_id']); ?>
      <?php foreach ($getProposalsByGigId as $row) { ?>
      <div class="col-md-4 mt-4">
        <div class="card shadow gigProposalContainer p-4">
          <div class="card-body">
            <h2><?php echo $row['last_name'] . ", " . $row['first_name']; ?></h2>
            <p><?php echo $row['description']; ?></p>
            <p><i><?php echo $row['date_added']; ?></i></p>
            <form class="addNewInterviewForm d-none">
              <div class="form-group">
                <label for="time_start">Time Start</label>
                <input type="hidden" class="freelancer_id" value="<?php echo $row['user_id']; ?>">
                <input type="hidden" class="gig_id" value="<?php echo $_GET['gig_id']; ?>">
                <input type="datetime-local" class="time_start form-control">
                <div class="invalid-feedback time-start-feedback"></div>
              </div>
              <div class="form-group">
                <label for="time_end">Time End</label>
                <input type="datetime-local" class="time_end form-control">
                <div class="invalid-feedback time-end-feedback"></div>
                <input type="submit" class="btn btn-primary float-right mt-4">
              </div>
              <div class="interview-timeline mt-3 mb-3 d-none">
                <h6 class="text-muted">Scheduled Interviews:</h6>
                <div class="timeline-container p-2 border rounded"></div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
    <script>
      // Store all interviews for timeline visualization
      var allInterviews = [];
      
      <?php foreach ($getAllInterviewsByGig as $interview) { ?>
        allInterviews.push({
          start: "<?php echo $interview['time_start']; ?>",
          end: "<?php echo $interview['time_end']; ?>",
          name: "<?php echo $interview['first_name'] . ' ' . $interview['last_name']; ?>"
        });
      <?php } ?>
      
      // When a proposal is double-clicked
      $('.gigProposalContainer').on('dblclick', function (event) {
        var addNewInterviewForm = $(this).find('.addNewInterviewForm');
        var timelineContainer = $(this).find('.interview-timeline');
        
        addNewInterviewForm.toggleClass('d-none');
        
        // If showing the form, also show the timeline
        if (!addNewInterviewForm.hasClass('d-none')) {
          timelineContainer.removeClass('d-none');
          renderTimeline($(this).find('.timeline-container'));
        }
      });
      
      // Function to render timeline visualization
      function renderTimeline(container) {
        container.empty();
        
        if (allInterviews.length === 0) {
          container.html('<p class="text-muted small">No interviews scheduled yet.</p>');
          return;
        }
        
        // Find earliest and latest times for scale
        var earliestTime = new Date(allInterviews[0].start);
        var latestTime = new Date(allInterviews[0].end);
        
        allInterviews.forEach(function(interview) {
          var startTime = new Date(interview.start);
          var endTime = new Date(interview.end);
          
          if (startTime < earliestTime) earliestTime = startTime;
          if (endTime > latestTime) latestTime = endTime;
        });
        
        // Add buffer
        earliestTime.setHours(earliestTime.getHours() - 1);
        latestTime.setHours(latestTime.getHours() + 1);
        
        var timeRange = latestTime - earliestTime;
        var containerWidth = container.width();
        
        // Render each interview as a block on the timeline
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
      
      // Real-time validation for date inputs
      $('.time_start').on('change', function() {
        var startInput = $(this);
        var endInput = $(this).closest('form').find('.time_end');
        var startFeedback = $(this).siblings('.time-start-feedback');
        var now = new Date();
        var selectedDate = new Date(startInput.val());
        
        // Reset validation state
        startInput.removeClass('is-invalid');
        startFeedback.hide();
        
        // Check if date is in the past
        if (selectedDate < now) {
          startInput.addClass('is-invalid');
          startFeedback.text('Cannot schedule an interview in the past!').show();
          return false;
        }
        
        // Check for conflicts with existing interviews
        for (var i = 0; i < allInterviews.length; i++) {
          var interviewStart = new Date(allInterviews[i].start);
          var interviewEnd = new Date(allInterviews[i].end);
          
          if ((selectedDate >= interviewStart && selectedDate <= interviewEnd)) {
            startInput.addClass('is-invalid');
            startFeedback.text('This time conflicts with ' + allInterviews[i].name + '\'s interview').show();
            return false;
          }
        }
        
        // If end time is already set, validate it against the new start time
        if (endInput.val()) {
          var endDate = new Date(endInput.val());
          if (endDate <= selectedDate) {
            endInput.addClass('is-invalid');
            endInput.siblings('.time-end-feedback').text('End time must be after start time').show();
          } else {
            endInput.removeClass('is-invalid');
            endInput.siblings('.time-end-feedback').hide();
          }
        }
        
        return true;
      });
      
      $('.time_end').on('change', function() {
        var endInput = $(this);
        var startInput = $(this).closest('form').find('.time_start');
        var endFeedback = $(this).siblings('.time-end-feedback');
        var selectedEndDate = new Date(endInput.val());
        var selectedStartDate = new Date(startInput.val());
        
        // Reset validation state
        endInput.removeClass('is-invalid');
        endFeedback.hide();
        
        // Check if end time is after start time
        if (selectedEndDate <= selectedStartDate) {
          endInput.addClass('is-invalid');
          endFeedback.text('End time must be after start time').show();
          return false;
        }
        
        // Check for conflicts with existing interviews
        for (var i = 0; i < allInterviews.length; i++) {
          var interviewStart = new Date(allInterviews[i].start);
          var interviewEnd = new Date(allInterviews[i].end);
          
          if ((selectedEndDate >= interviewStart && selectedEndDate <= interviewEnd)) {
            endInput.addClass('is-invalid');
            endFeedback.text('This time conflicts with ' + allInterviews[i].name + '\'s interview').show();
            return false;
          }
        }
        
        return true;
      });

      // Form submission with inline validation
      $('.addNewInterviewForm').on('submit', function (event) {
        event.preventDefault();
        var form = $(this);
        var startInput = form.find('.time_start');
        var endInput = form.find('.time_end');
        var startFeedback = startInput.siblings('.time-start-feedback');
        var endFeedback = endInput.siblings('.time-end-feedback');
        
        // Reset validation states
        startInput.removeClass('is-invalid');
        endInput.removeClass('is-invalid');
        startFeedback.hide();
        endFeedback.hide();
        
        var formData = {
          freelancer_id: form.find('.freelancer_id').val(),
          gig_id: form.find('.gig_id').val(),
          time_start: startInput.val(),
          time_end: endInput.val(),
          insertNewGigInterview: 1
        };
        
        // Validate required fields
        if (!formData.time_start || !formData.time_end) {
          if (!formData.time_start) {
            startInput.addClass('is-invalid');
            startFeedback.text('Please select a start time').show();
          }
          if (!formData.time_end) {
            endInput.addClass('is-invalid');
            endFeedback.text('Please select an end time').show();
          }
          return;
        }
        
        // Validate date is not in the past
        var selectedStartDate = new Date(formData.time_start);
        var now = new Date();
        
        if (selectedStartDate < now) {
          startInput.addClass('is-invalid');
          startFeedback.text('Cannot schedule an interview in the past!').show();
          return;
        }
        
        // Validate end time is after start time
        var selectedEndDate = new Date(formData.time_end);
        if (selectedEndDate <= selectedStartDate) {
          endInput.addClass('is-invalid');
          endFeedback.text('End time must be after start time').show();
          return;
        }
        
        // Check for conflicts
        var hasConflict = false;
        for (var i = 0; i < allInterviews.length; i++) {
          var interviewStart = new Date(allInterviews[i].start);
          var interviewEnd = new Date(allInterviews[i].end);
          
          if ((selectedStartDate >= interviewStart && selectedStartDate <= interviewEnd) || 
              (selectedEndDate >= interviewStart && selectedEndDate <= interviewEnd) ||
              (selectedStartDate <= interviewStart && selectedEndDate >= interviewEnd)) {
            hasConflict = true;
            startInput.addClass('is-invalid');
            startFeedback.text('Time conflicts with ' + allInterviews[i].name + '\'s interview').show();
            break;
          }
        }
        
        if (hasConflict) return;

        // If validation passes, submit the form
        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function (data) {
            if (data) {
              location.reload();
            } else {
              // More specific error message
              var errorMsg = $('<div class="alert alert-danger mt-3"></div>')
                .text("Could not schedule interview! Possible reasons: \n- You already scheduled this freelancer\n- There's a time conflict with another interview\n- The selected time is invalid");
              form.prepend(errorMsg);
            }
          }
        });
      });
    </script>
    <?php include 'includes/footer.php'; ?>
  </body>
</html>
