<?php
session_start();
require 'config.php';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT v.id, v.pseudo, v.content, v.date, COUNT(c.id) AS nb_comments
                       FROM vdeces v
                       LEFT JOIN comments c ON v.id = c.vde_id
                       GROUP BY v.id
                       ORDER BY v.date DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$vdeces = $stmt->fetchAll();

$totalStmt = $pdo->query("SELECT COUNT(*) FROM vdeces");
$totalVdes = $totalStmt->fetchColumn();
$totalPages = ceil($totalVdes / $limit);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Vie d’ECE</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h1>Vie d’ECE</h1>
  <a href="ajout_vdece.php" class="btn btn-primary mb-3">Ajouter une VdECE</a>

  <?php foreach ($vdeces as $v): ?>
    <div class="card mb-3 fade-in">
      <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($v['pseudo']) ?></h5>
        <p class="card-text"><?= nl2br(htmlspecialchars($v['content'])) ?></p>
        <small class="text-muted"><?= $v['date'] ?></small><br>
        <a href="montrer_vdece.php?id=<?= $v['id'] ?>" class="btn btn-link">
          <?= $v['nb_comments'] ?> commentaire(s)
        </a>
      </div>
    </div>
  <?php endforeach; ?>

  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="1index.php?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</body>
</html>
