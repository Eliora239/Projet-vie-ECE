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
<?php include 'header.php'; ?>

<body class="container mt-4">
  <div class="card shadow-sm fade-in mb-4">
    <div class="card-body">
      <h5><?= htmlspecialchars($vdece['pseudo']) ?></h5>
      <p><?= nl2br(htmlspecialchars($vdece['content'])) ?></p>
      <small class="text-muted"><?= $vdece['date'] ?></small>
    </div>
  </div>

  <h4>Commentaires</h4>
  <div id="comment-list">
    <?php foreach ($comments as $c): ?>
      <div class="alert custom-comment shadow-sm">
        <strong><?= htmlspecialchars($c['pseudo']) ?></strong><br />
        <small class="text-muted"><?= $c['date'] ?></small>
        <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <form id="comment-form" action="ajouter_commentaire.php" method="POST" class="mt-4">
    <input type="hidden" name="vde_id" value="<?= $vdece['id'] ?>" />
    <div class="mb-3">
      <label for="pseudo" class="form-label">Votre pseudo</label>
      <input id="pseudo" name="pseudo" class="form-control" placeholder="Ex: Julie92" required maxlength="50" value="<?= htmlspecialchars($pseudo) ?>" />
    </div>
    <div class="mb-3">
      <label for="comment" class="form-label">Votre commentaire</label>
      <textarea id="comment" name="comment" class="form-control" placeholder="Écrivez ici votre réaction…" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-success px-4"> Publier</button>
  </form>

  <script>
    document.getElementById("comment-form").addEventListener("submit", async function (e) {
      e.preventDefault();
      const form = this;
      const data = new FormData(form);

      try {
        const response = await fetch("ajouter_commentaire.php", {
          method: "POST",
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: data
        });

        if (!response.ok) {
          alert("Erreur réseau : " + response.statusText);
          return;
        }

        const html = await response.text();
        const commentList = document.getElementById("comment-list");
        commentList.insertAdjacentHTML('afterbegin', html);
        form.reset();
      } catch (err) {
        alert("Erreur lors de l'envoi du commentaire : " + err.message);
      }
    });
  </script>
  <?php include 'footer.php'; ?>
</body>
</html>
