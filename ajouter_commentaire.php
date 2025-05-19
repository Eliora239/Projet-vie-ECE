<?php
session_start();
require 'config.php';

$pseudo = substr(trim($_POST['pseudo'] ?? ''), 0, 50);
$comment = trim($_POST['comment'] ?? '');
$vde_id = (int)($_POST['vde_id'] ?? 0);

if ($vde_id > 0 && !empty($pseudo) && !empty($comment)) {
    $stmt = $pdo->prepare("INSERT INTO comments (vde_id, pseudo, comment) VALUES (?, ?, ?)");
    $stmt->execute([$vde_id, $pseudo, $comment]);

    $_SESSION['pseudo'] = $pseudo;

    // Requête AJAX ?
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($isAjax) {
        // Affiche le commentaire au format HTML à insérer côté client
        header('Content-Type: text/html; charset=UTF-8');

        echo "
        <div class='alert alert-light border'>
            <strong>" . htmlspecialchars($pseudo) . "</strong><br>
            <small class='text-muted'>" . date('Y-m-d H:i:s') . "</small>
            <p>" . nl2br(htmlspecialchars($comment)) . "</p>
        </div>";
        exit;
    } else {
        // Redirection classique (hors AJAX)
        header("Location: montrer_vdece.php?id=$vde_id");
        exit;
    }
} else {
    http_response_code(400);
    echo "Erreur : tous les champs sont requis.";
}
?>
