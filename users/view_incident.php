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
<style>
  /* Gradient background */
  body {
   
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 15px;
  }
  .container-incident {
    background: #fff;
    border-radius: 18px;
    max-width: 800px;
    width: 100%;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    overflow: hidden;
    animation: fadeInUp 0.8s ease forwards;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(40px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
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
    box-shadow: inset 0 -5px 10px rgba(0,0,0,0.2);
  }
  header .incident-id {
    font-size: 1.2rem;
    opacity: 0.9;
    font-weight: 400;
  }

  main {
    padding: 30px 35px;
  }

  .field-label {
    font-weight: 600;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.1rem;
    color: #555;
  }
  .field-label i {
    color: #7c5de8;
  }

  .field-content {
    font-size: 1.1rem;
    line-height: 1.5;
    color: #222;
  }

  .description-box {
    background: #f9f9fb;
    padding: 18px 20px;
    border-radius: 12px;
    font-size: 1rem;
    color: #444;
    white-space: pre-wrap;
    box-shadow: inset 0 0 6px rgba(124,93,232,0.15);
    margin-bottom: 25px;
  }

  .badge-priority {
    font-weight: 700;
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    user-select: none;
  }
  .badge-priority.high {
    background: #e53e3e;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.25);
  }
  .badge-priority.medium {
    background: #f6ad55;
    color: #4a3c0b;
    text-shadow: 0 1px 2px rgba(255,255,255,0.3);
  }
  .badge-priority.low {
    background: #48bb78;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.25);
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 28px;
    margin-bottom: 30px;
  }

  .info-item {
    font-size: 1.05rem;
  }

  .info-label {
    font-weight: 600;
    color: #666;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .info-label i {
    color: #7c5de8;
  }
  .info-value {
    color: #222;
  }

  .evidence-list {
    list-style: none;
    padding-left: 0;
    margin:0;
  }
  .evidence-list li {
    /*margin-bottom: 14px;*/
  }
  .evidence-list a {
    color: #7c5de8;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 10px;
    padding: 8px 12px;
    transition: background 0.3s ease, color 0.3s ease;
    box-shadow: 0 3px 6px rgba(124, 93, 232, 0.15);
  }
  .evidence-list a:hover {
    background: #7c5de8;
    color: #fff;
    box-shadow: 0 6px 16px rgba(124, 93, 232, 0.4);
  }
  .evidence-list i.fa-file-arrow-up {
    font-size: 1.3rem;
  }

  .btn-back {
    
    margin-bottom: 28px;
    font-weight: 600;
    color: #fff;
    background: #5a47ab;
    border: none;
    padding: 10px 18px;
    border-radius: 5px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: background 0.3s ease;
    text-decoration: none;
  }
  .btn-back:hover {
    background: #7c5de8;
    text-decoration: none;
    color: #fff;
  }
  .field-label b{
    width: 20%;
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
             <?php 
                $priorityClass = strtolower($incident['priority']);
                ?>
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
            <div class="info-value"><?php echo date('Y-m-d H:i', strtotime($incident['created_at'])); ?></div>
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
