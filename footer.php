<footer class="footer">
    <div class="container">
        <p>Footer</p>
    </div>
</footer>
<?php
wp_footer();

if (SiteConfig::isDevMode()) {
    echo DebuggerPack::$debugbarRenderer->render();
}

?>
</body>
</html>
