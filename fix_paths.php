<?php
// Script pour corriger les chemins dans les fichiers PHP

$files = [
    'dashboard_doyen.php',
    'dashboard_apparitaire.php',
    'dashboard_enseignant.php',
    'dashboard_vicedoyen.php',
    'valve.php'
];

foreach ($files as $file) {
    $filepath = __DIR__ . '/frontend/pages/' . $file;
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        
        // Ajouter l'en-tête PHP pour la session
        $php_header = "<?php\n// Vérifier si l'utilisateur est connecté\nsession_start();\nif (!isset(\$_SESSION['user_id']) || \$_SESSION['role'] !== '" . str_replace(['dashboard_', '.php'], '', $file) . "') {\n    header('Location: login.php');\n    exit();\n}\n?>\n";
        
        // Remplacer les chemins CSS
        $content = str_replace('href="assets/', 'href="../assets/css/dashboard/', $content);
        
        // Remplacer les chemins JS
        $content = str_replace('src="assets/js/', 'src="../assets/js/dashboard/', $content);
        
        // Remplacer les liens .html par .php
        $content = str_replace('.html"', '.php"', $content);
        $content = str_replace('.html\'', '.php\'', $content);
        
        // Ajouter l'en-tête PHP au début
        $content = $php_header . $content;
        
        file_put_contents($filepath, $content);
        echo "Fichier $file corrigé\n";
    }
}

echo "Tous les fichiers ont été corrigés !\n";
?>