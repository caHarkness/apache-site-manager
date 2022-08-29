<?php
    /*
        Alert.php

        Its primary function as a static class library is to supplement the framework with the functionality to render Toastr messages from cookies sent with the response.
    */

    class Alert
    {
        public static $strSuccess   = "";
        public static $strWarning   = "";
        public static $strDanger    = "";
        public static $strInfo      = "";

        // When this function is called by the framework, write a section of JavaScript that sets up the Toastr library and defines the functions that are to be later called again. If we opt to replace Toastr with another library or method of messaging, we do that HERE.
        public static function js()
        {
            ?>
                <script>
                    $(function() {
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

        // A function to be called within what is or would be considered the "controller" portion of the logic. Typically found preceding the view, all this does is set a cookie with a friendly message to display. This message is later read in the same request or even after a redirect and destroyed once read. "Green"
        public static function success($strMessage)
        {
            Alert::$strSuccess = $strMessage;

            Cookies::set(
                "AlertSuccess",
                $strMessage);
        }

        // "Yellow"
        public static function warning($strMessage)
        {
            Alert::$strWarning = $strMessage;

            Cookies::set(
                "AlertWarning",
                $strMessage);
        }

        // "Red"
        public static function danger($strMessage)
        {
            Alert::$strDanger = $strMessage;
            
            Cookies::set(
                "AlertDanger",
                $strMessage);
        }

        // "Blue"
        public static function info($strMessage)
        {
            Alert::$strInfo = $strMessage;
            
            Cookies::set(
                "AlertInfo",
                $strMessage);
        }

        // A function to be called near the end of the framework's page render, before the closing of the body and html tags. This "consumes" the cookie, taking its value and storing it memory.
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

        // A function to be called after "eating" the cookies. It renders JavaScript that calls our previously defined "fncAlertBlah" functions that can act as an interface to showing green, yellow, red, or blue messages. At the time of writing, we  implement Toastr, but that may change. If we decide to gut Toastr, we change it above in the Alert::js() static function.
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