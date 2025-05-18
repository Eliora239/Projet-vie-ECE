<?php
session_start();
require 'config.php';

$error = '';
$pseudo = $_SESSION['pseudo'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = substr(trim($_POST['pseudo']), 0, 50);
    $content = trim($_POST['content']);

    if (!empty($pseudo) && !empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO vdeces (pseudo, content) VALUES (?, ?)");
        if ($stmt->execute([$pseudo, $content])) {
            $_SESSION['pseudo'] = $pseudo;
            header("Location: 1index.php");
            exit;
        } else {
            $error = "Erreur lors de l'enregistrement.";
        }
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter une VdECE</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h1>Ajouter une VdECE</h1>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="ajout_vdece.php">
    <div class="mb-3">
      <label for="pseudo" class="form-label">Pseudo</label>
      <input id="pseudo" type="text" name="pseudo" maxlength="50" class="form-control" value="<?= htmlspecialchars($pseudo) ?>" required>
    </div>
    <div class="mb-3">
      <label for="content" class="form-label">Contenu</label>
      <textarea id="content" name="content" class="form-control" required><?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Publier</button>
    <a href="1index.php" class="btn btn-secondary">Annuler</a>
  </form>
</body>
</html>
