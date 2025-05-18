<?php
session_start();
require 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM vdeces WHERE id = ?");
$stmt->execute([$id]);
$vdece = $stmt->fetch();

if (!$vdece) {
    http_response_code(404);
    echo "VdECE introuvable.";
    exit;
}

$comments = $pdo->prepare("SELECT * FROM comments WHERE vde_id = ? ORDER BY date DESC");
$comments->execute([$id]);
$comments = $comments->fetchAll();

$pseudo = $_SESSION['pseudo'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>VdECE</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="style.css" rel="stylesheet" />
</head>
<body class="container mt-4">
  <div class="card shadow-sm fade-in">
    <div class="card-body">
      <h5><?= htmlspecialchars($vdece['pseudo']) ?></h5>
      <p><?= nl2br(htmlspecialchars($vdece['content'])) ?></p>
      <small class="text-muted"><?= $vdece['date'] ?></small>
    </div>
  </div>

  <hr />
  <h4>Commentaires</h4>
  <div id="comment-list">
    <?php foreach ($comments as $c): ?>
      <div class="alert alert-light border fade-in">
        <strong><?= htmlspecialchars($c['pseudo']) ?></strong><br />
        <small class="text-muted"><?= $c['date'] ?></small>
        <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="comment-form" action="http://localhost:8080/vdece-java/ajouter_commentaire" method="POST">
    <input type="hidden" name="vde_id" value="<?= $vdece['id'] ?>" />
    <div class="mb-2">
      <input name="pseudo" class="form-control" placeholder="Votre pseudo" required maxlength="50" value="<?= htmlspecialchars($pseudo) ?>" />
    </div>
    <div class="mb-2">
      <textarea name="comment" class="form-control" placeholder="Votre commentaire" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Commenter</button>
  </form>

  <script>
    document.getElementById("comment-form").addEventListener("submit", async function (e) {
      e.preventDefault();
      console.log("Formulaire soumis, envoi AJAX…");

      const form = this;
      const data = new FormData(form);

      const servletUrl = "http://localhost:8080/vdece-java/ajouter_commentaire";

      try {
        const response = await fetch(servletUrl, {
          method: "POST",
          body: data
        });

        if (!response.ok) {
          console.error("Erreur réseau : ", response.statusText);
          return;
        }

        const html = await response.text();
        document.getElementById("comment-list").innerHTML = html;
        form.reset();
      } catch (err) {
        console.error("Erreur fetch : ", err);
      }
      const response = await fetch(servletUrl, {
  method: "POST",
  mode: "cors",
  body: data
});

    });
  </script>
</body>
</html>
