<?php
    class PageRefresher
    {
        public static $intRefreshInterval;

        public static function init()
        {
            self::$intRefreshInterval = -1;
        }

        public static function js()
        {
            ?>
                <?php if (self::$intRefreshInterval > 0): ?>
                    <script>
                        $(function() {
                            setInterval(function() {
                                $.get("<?= Request::getPathString(); ?>/@partial", function(data) {
                                    $("#app-body").html(data);
                                });
                            }, 1000 * <?= self::$intRefreshInterval; ?>);
                        });
                    </script>
                <?php endif; ?>
            <?php
        }

        public static function setInterval($intSeconds)
        {
            self::$intRefreshInterval = $intSeconds;
        }
    }

    PageRefresher::init();
?>