<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}
include('../connect.php');
$incidentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM incidents WHERE id = $incidentId";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<h3>Incident not found.</h3>";
    exit;
}

$incident = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Incident Details - #<?php echo $incident['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="default.css" />
    <style>
    body {

        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 40px 15px;
    }
    header {
        background: linear-gradient(90deg, #5a47ab, #7c5de8);
        padding: 28px 30px;
        color: white;
        font-weight: 700;
        font-size: 1.8rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        letter-spacing: 0.04em;
        box-shadow: inset 0 -5px 10px rgba(0, 0, 0, 0.2);
    }

    header .incident-id {
      font-size: 1.2rem;
      opacity: 0.9;
      font-weight: 400;
    }
    </style>
</head>

<body>
    <div class="container-incident">
        <header>
            Incident Details
            <span class="incident-id">#<?php echo $incident['id']; ?></span>
        </header>
        <main>
            <div>
                <a href="users.php" class="btn-back"><i class="fa fa-arrow-left"></i> Back to Incidents</a>
            </div>

            <div class="field-label mb-4"><b>Title :</b>
                <div class="field-content"><?php echo htmlspecialchars($incident['title']); ?></div>
            </div>

            <div class="field-label mb-4"><b> Category : </b>
                <div class="field-content "><?php echo htmlspecialchars($incident['category']); ?></div>
            </div>

            <div class="field-label mb-4"><b> Description :</b>
                <div class=""><?php echo nl2br(htmlspecialchars($incident['description'])); ?></div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><b> Priority:</b>
                        <?php  $priorityClass = strtolower($incident['priority']);     ?>
                        <div class="badge-priority <?php echo $priorityClass; ?>">
                            <?php echo htmlspecialchars($incident['priority']); ?>
                        </div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label"><b> Status:</b>
                        <div class="info-value"><?php echo htmlspecialchars($incident['status']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label"><b> Incident Date:</b>
                        <div class="info-value"><?php echo htmlspecialchars($incident['incident_date']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label"><b> Reported On: </b>
                        <div class="info-value"><?php echo date('Y-m-d H:i', strtotime($incident['created_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="field-label"><b> Evidence Files:</b>
                <?php
                  $files = json_decode($incident['evidence_files'], true);
                  if ($files && is_array($files) && count($files) > 0) {
                      echo "<ul class='evidence-list'>";
                      foreach ($files as $file) {
                          $fileName = basename($file);
                          echo "<li><a href='$file' target='_blank' rel='noopener noreferrer'><i class='fa fa-file-arrow-up'></i> $fileName</a></li>";
                      }
                      echo "</ul>";
                  } else {
                      echo "<p style='font-style: italic; color: #777;margin:0;'>No evidence files uploaded.</p>";
                  }
                ?>
            </div>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>