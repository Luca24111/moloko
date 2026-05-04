<?php
$title = $title ?? 'moloch Bar';
$description = $description ?? 'Cocktail menu e bar experience contemporanea.';
$styles = $styles ?? [];
$scripts = $scripts ?? [];
$bodyClass = $bodyClass ?? '';
$currentRoute = $currentRoute ?? '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($title); ?></title>
    <meta name="description" content="<?= $e($description); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    >
    <link rel="stylesheet" href="<?= $e($asset('css/base.css')); ?>">
    <?php foreach ($styles as $style): ?>
        <link rel="stylesheet" href="<?= $e($asset($style)); ?>">
    <?php endforeach; ?>
</head>
<body class="<?= $e($bodyClass); ?>">
<div class="ambient-shape ambient-shape--one" aria-hidden="true"></div>
<div class="ambient-shape ambient-shape--two" aria-hidden="true"></div>

<div class="site-shell">
    <?php $include('components/site_header.php', [
        'navItems' => $navItems ?? [],
        'currentRoute' => $currentRoute,
    ]); ?>

    <main class="main-content">
        <?php $include($contentTemplate, $contentData ?? []); ?>
    </main>

    <?php $include('components/site_footer.php', [
        'footerLinks' => $footerLinks ?? [],
        'year' => $year ?? (int) date('Y'),
    ]); ?>
</div>
<?php foreach ($scripts as $script): ?>
    <script src="<?= $e($asset($script)); ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
