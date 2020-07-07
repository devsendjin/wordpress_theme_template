<footer class="block footer">
    <div class="content block-content">
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
