<!doctype html>
<html lang="en">
<head>
    <?php
    if (SiteConfig::isDevMode()) {
        echo DebuggerPack::$debugbarRenderer->renderHead();
    }
    ?>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title><?= wp_get_document_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="x-rim-auto-match" content="none">

    <base href="<?= SiteConfig::$homeUrl; ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700,800&display=swap">
    <script src="<?= SiteConfig::$templateUri; ?>/js/min/lazysizes.min.js" async></script>

    <meta name="theme-color" content="#ffffff"/>
    <link rel="manifest" href="<?= Utils::getAssetUrlWithTimestamp('manifest.json'); ?>">
	<?php wp_head(); ?>
    <?php //echo '<link href="' . SiteConfig::$$templateUri . '/bug-fix.css" rel="stylesheet">'; ?>
</head>
<?php
$pageId = null !== SiteConfig::getPageId() ? 'id="' . SiteConfig::getPageId() . '"' : '';
$pageClasses = null !== SiteConfig::getPageClasses() ? ' ' . SiteConfig::getPageClasses() : '';
?>
<body
    <?= $pageId ; ?>
    class="lazyload-fade<?= $pageClasses; ?>"
    data-site-uri="<?= site_url(); ?>">
<header class="header">
    <div class="container">Header</div>
</header>
