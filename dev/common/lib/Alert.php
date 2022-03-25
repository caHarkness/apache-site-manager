<?php
    class Alert
    {
        public static $strSuccess = "";
        public static $strWarning = "";
        public static $strDanger = "";

        public static function js()
        {
            ?>
                <script>
                    fncAlertSuccess = function(strMessage)
                    {
                        // Replace this with something more clever to show the user a successful message.
                        console.log("Success: " + strMessage);
                    };

                    fncAlertWarning = function(strMessage)
                    {
                        // Replace this with something more clever to show the user a message as a warning.
                        console.log("Warning: " + strMessage);
                    };

                    fncAlertDanger = function(strMessage)
                    {
                        // Replace this with something more clever to show the user an error message.
                        console.log("Danger: " + strMessage);
                    };
                </script>
            <?php
        }

        public static function success($strMessage)
        {
            Alert::$strSuccess = $strMessage;

            Cookies::set(
                "AlertSuccess",
                $strMessage);
        }

        public static function warning($strMessage)
        {
            Alert::$strWarning = $strMessage;

            Cookies::set(
                "AlertWarning",
                $strMessage);
        }

        public static function danger($strMessage)
        {
            Alert::$strDanger = $strMessage;
            
            Cookies::set(
                "AlertDanger",
                $strMessage);
        }

        public static function eat()
        {
            if (Cookies::has("AlertSuccess"))
                Alert::$strSuccess = Cookies::pop("AlertSuccess") ?? "";

            if (Cookies::has("AlertWarning"))
                Alert::$strWarning = Cookies::pop("AlertWarning") ?? "";

            if (Cookies::has("AlertDanger"))
                Alert::$strDanger = Cookies::pop("AlertDanger") ?? "";
        }

        public static function render()
        {
            if (strlen(Alert::$strSuccess) > 0)
            {
                ?>
                    <script>
                        $(function() {
                            fncAlertSuccess("<?= Alert::$strSuccess; ?>");
                        });
                    </script>
                <?php
            }

            if (strlen(Alert::$strWarning) > 0)
            {
                ?>
                    <script>
                        $(function() {
                            fncAlertWarning("<?= Alert::$strWarning; ?>");
                        });
                    </script>
                <?php
            }

            if (strlen(Alert::$strDanger) > 0)
            {
                ?>
                    <script>
                        $(function() {
                            fncAlertDanger("<?= Alert::$strDanger; ?>");
                        });
                    </script>
                <?php
            }
        }
    }
?>