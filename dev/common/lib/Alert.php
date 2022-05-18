<?php
    class Alert
    {
        public static $strSuccess = "";
        public static $strWarning = "";
        public static $strDanger = "";
        public static $strInfo = "";

        public static function js()
        {
            ?>
                <script>
                    $(function() {
                        $("#alert-container").hide();

                        toastr.options =
                        {
                            "closeButton":          false,
                            "debug":                false,
                            "newestOnTop":          true,
                            "progressBar":          true,
                            "positionClass":        "toast-bottom-left",
                            "preventDuplicates":    false,
                            "onclick":              null,
                            "showDuration":         "300",
                            "hideDuration":         "1000",
                            "timeOut":              "5000",
                            "extendedTimeOut":      "1000",
                            "showEasing":           "swing",
                            "hideEasing":           "linear",
                            "showMethod":           "fadeIn",
                            "hideMethod":           "fadeOut"
                        };
                    });

                    fncAlertPersist = function()
                    {
                        toastr["options"]["timeOut"]            = 1000 * 60 * 5;
                        toastr["options"]["extendedTimeOut"]    = 1000 * 60 * 5;
                    };

                    fncAlertSuccess = function(strMessage)
                    {
                        toastr["success"](strMessage);
                    };

                    fncAlertWarning = function(strMessage)
                    {
                        toastr["warning"](strMessage);
                    };

                    fncAlertDanger = function(strMessage)
                    {
                        toastr["error"](strMessage);
                    };

                    fncAlertInfo = function(strMessage)
                    {
                        fncAlertPersist();
                        toastr["info"](strMessage);
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

        public static function info($strMessage)
        {
            Alert::$strInfo = $strMessage;
            
            Cookies::set(
                "AlertInfo",
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

            if (Cookies::has("AlertInfo"))
                Alert::$strInfo = Cookies::pop("AlertInfo") ?? "";
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

            if (strlen(Alert::$strInfo) > 0)
            {
                ?>
                    <script>
                        $(function() {
                            fncAlertInfo("<?= Alert::$strInfo; ?>");
                        });
                    </script>
                <?php
            }
        }
    }
?>