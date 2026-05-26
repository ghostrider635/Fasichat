<?php
// Test password_verify
require_once __DIR__ . '/../backend/config/Database.php';
$db = null;
try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    // ignore here; tests below will show connection error
}

function h($s){ return htmlspecialchars((string)$s); }

// Si on fournit email GET param, essayer de récupérer le hash
if (isset($_GET['email']) && $db) {
    $email = $_GET['email'];
    $stmt = $db->prepare("SELECT mot_de_passe_hash, email FROM utilisateurs WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "<h3>Hash pour " . h($user['email']) . "</h3>";
        echo "<pre>" . h($user['mot_de_passe_hash']) . "</pre>";
        echo "<p>Collez ce hash dans le formulaire ci‑dessous pour tester.</p>";
    } else {
        echo "<p>Aucun utilisateur trouvé pour l'email: " . h($email) . "</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plain = $_POST['password'] ?? '';
    $hash = $_POST['hash'] ?? '';

    echo "<h3>Test password_verify</h3>";
    echo "Mot de passe fourni: <strong>" . h($plain) . "</strong><br>";
    echo "Hash: <pre>" . h($hash) . "</pre>";
    if ($hash === '') {
        echo "<p style='color:orange'>Aucun hash fourni</p>";
    } else {
        $ok = password_verify($plain, $hash);
        echo "<p>Résultat: <strong>" . ($ok ? 'OK (mots de passe concordent)' : 'NON — mot de passe invalide') . "</strong></p>";
    }
    echo "<hr>";
}

?>

<!doctype html>
<html>
<head><meta charset="utf-8"><title>Test password_verify</title></head>
<body>
<h2>Tester un hash</h2>
<p>Vous pouvez <a href="?email=jean.dupont@etudiant.univ.fr">récupérer le hash par email</a> si la base contient ce compte, ou coller un hash manuellement.</p>
<form method="post">
  <label>Mot de passe à tester: <input name="password" type="text" required></label><br><br>
  <label>Hash (collez ici):<br>
  <textarea name="hash" rows="4" cols="80"></textarea></label><br><br>
  <button type="submit">Vérifier</button>
</form>

</body>
</html>