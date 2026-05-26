<?php
// Test de connexion à la base de données
require_once __DIR__ . '/../backend/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "<h2>Connexion DB: OK</h2>";

    $res = $db->query("SELECT DATABASE() AS db")->fetch();
    echo "Base courante: <strong>" . htmlspecialchars($res['db'] ?? 'n/a') . "</strong><br>";

    $tables = $db->query("SHOW TABLES")->fetchAll();
    echo "Nombre de tables: <strong>" . count($tables) . "</strong><br>";

    // Vérifier présence de la table utilisateurs
    $hasUsers = $db->query("SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'utilisateurs'")->fetch();
    echo "Table `utilisateurs` présente: <strong>" . (($hasUsers['c'] ?? 0) > 0 ? 'oui' : 'non') . "</strong><br>";

    if (($hasUsers['c'] ?? 0) > 0) {
        $count = $db->query("SELECT COUNT(*) AS c FROM utilisateurs")->fetch();
        echo "Nombre d'utilisateurs: <strong>" . ($count['c'] ?? 'n/a') . "</strong><br>";

        echo "<hr><strong>Aperçu (max 5 utilisateurs)</strong><br>";
        $rows = $db->query("SELECT id_utilisateur, email, type_utilisateur, statut FROM utilisateurs LIMIT 5")->fetchAll();
        echo "<pre>" . htmlspecialchars(print_r($rows, true)) . "</pre>";
    }

} catch (Exception $e) {
    echo "<h2>Erreur connexion DB</h2>";
    echo htmlspecialchars($e->getMessage());
}

?>