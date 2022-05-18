<?php
    // WARNING:
    // DO NOT MAKE CHANGES to this file if it's in any project directory other than 'template.' YOUR CHANGES WILL BE LOST. A call to 'apply-template.sh' refreshes all projects with what's in the template directory!

    // ABOUT:
    // This file, "index.php," is responsible for handing all requests pertinent to the project. For example, let's say a request is made to https://my.site/controller/action/abc/123. This "index.php" breaks apart the request into a several bits. There is a "pages" directory in each project that houses each of the controllers. A controller can be a single .php file itself or a folder containing .php scripts as actions. Refer to the Request.php library in the "lib" directory of the "api" project. Anything after the controller and action in the address bar is stored into a string array that can be accessed via Request::getPath(). The first two elements of this array are "controller" and "action." Strings "abc" and "123" are represented as elements whose indecies are 2 and 3 of this array.

    // Load the configuration of the entire application before anything else! The order implies that the master configuration file is loaded and the project-level configuration offers any overrides.

    require "../../config.php";     // The configuration in "/var/www"
    require "../config.php";        // The configuration in either "/var/www/dev"
    require "config.php";           // The configuration at the project level
    
    Config::finalize();

    class Application
    {
        // Get the relative path of all files matching the regular expression
        public static function filesIn($strDirectory, $strRegex)
        {
            $arrDirectory   = scandir($strDirectory);
            unset($arrDirectory[0]);
            unset($arrDirectory[1]);
            $arrDirectory   = array_values($arrDirectory);
            $arrOutput      = array();

            foreach ($arrDirectory as $strFile)
                if (preg_match($strRegex, $strFile))
                    array_push($arrOutput, array("$strFile", "$strDirectory/$strFile"));

            return
            $arrOutput;
        }

        public static function requireExisting($strFilePath)
        {
            if (file_exists($strFilePath))
                require $strFilePath;
        }

        // WARNING:
        // Be mindful of the PHP scripts placed in the 'lib' folder; all of them are executed.
        static function loadLibraries()
        {
            $arrPaths = self::filesIn("lib", "#[A-Za-z0-9_\-]*.php#");

            foreach ($arrPaths as $arrPath)
                if (!(in_array($arrPath[0], Config::getLoadedLibraries())))
                    require $arrPath[1];
        }

        // Load the contents of the files found in 'var' and set them as a constant. We will only be looking for files named in all capital letters only.
        static function loadFileConstants()
        {
            $arrPaths = self::filesIn("var", "/^[A-Z0-9_]*/");

            foreach ($arrPaths as $arrPath)
                try
                {
                    Config::$value[$arrPath[0]] = trim(file_get_contents($arrPath[1]));
                }
                catch (Exception $x) {}

            // Finalize the configuration again. It's safe to do.
            Config::finalize();
        }

        public static $strInstanceId;
        public static $varStopwatch;

        // Called right before the output buffer begins being written to.
        static function start()
        {
            self::loadLibraries();
            self::loadFileConstants();

            self::$strInstanceId    = Text::generateGuid();
            self::$varStopwatch     = new Stopwatch();

            define("APP_INSTANCE_ID", self::$strInstanceId);

            // Start the output buffer process and clean it.
            ob_start();
            ob_clean();

            // Load cookies into a static field. We can't modify cookies directly until we are ready to send them along with the output buffer, so the solution to this is to modify a static field of the Cookies class.
            Cookies::load();
        }

        // Called after the output buffer is finished being written to and when the application is ready to flush the contents to the client.
        static function end()
        {
            // Finalize the cookies for sending as header information.
            Cookies::finalize();

            // Send the contents of the output buffer to the client requesting the page or resource. Exit after doing so!
            ob_end_flush();
            exit;
        }
    }

    Application::start();
?>

<!DOCTYPE html>

<html>
    <head>
        <!-- Icon -->
        <link rel="icon" href="/res/icon.png">

        <!-- CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        
        <!-- Internal CSS --->
        <?php foreach (Application::filesIn("css", "#[A-Za-z0-9_\-\.]*.css#") as $f): ?>
            <link rel="stylesheet" href="/<?= $f[1]; ?>">
        <?php endforeach; ?>

        <!-- Afar JavaScript libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <!-- Local JavaScript libraries -->
        <?php foreach (Application::filesIn("js", "#[A-Za-z0-9_\-\.]*.js#") as $f): ?>
            <script src="/<?= $f[1]; ?>"></script>
        <?php endforeach; ?>

        <!-- Application-specific additional header -->
        <?php Application::requireExisting("head.php"); ?>
    </head>

    <body>
        <div id="app-header">
            <!-- Navigation -->
            <nav class="navbar navbar-expand-sm navbar-dark bg-dark justify-content-between" id="nav-main">
                <div>
                    <ul class="navbar-nav">
                        <li class="navbar-brand" id="nav-title"><?= APP_NAME; ?></li>
                        <li class="nav-item"><a class="nav-link" href="/">Home</a></li>

                        <!-- App Specific Links -->
                        <?php Application::requireExisting("menu.php"); ?>

                        <?php if (is_dir("md")): ?>
                            <li class="nav-item"><a class="nav-link" href="/doc">Docs</a></li>
                        <?php endif; ?>
                    </ul> 
                </div>

                <ul class="navbar-nav">
                    <?php if (IS_DEV): ?>
                        <li class="navbar-brand">(dev)</li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div id="app-body">
            <?php
                Request::process();
                if (in_array("@partial", Request::getPath()))
                    ob_clean();
            ?>

            <!-- Rendered view -->
            <?php Request::render(); ?>

            <?php
                if (in_array("@partial", Request::getPath()))
                {
                    ob_end_flush();
                    exit;
                }
            ?>
        </div>
        
        <!-- Div containing all footer information (for hiding and printing purposes) -->
        <div id="app-footer">
            <hr />

            <!-- Project footer -->
            <?php Application::requireExisting("footer.php"); ?>

            <!-- Global footer -->
            <div class="row">
                <div class="col-lg-1"></div>

                <div class="col-lg-10">
                    <p><small>Copyright &copy; 2022 Company. This software is proprietary and internal to Company. Do not distribute the information you see on this page.</small></p>
                </div>

                <div class="col-lg-1"></div>
            </div>
        </div>

        <!-- Extra JavaScript -->
        <?php Alert::js(); ?>
        <?php Alert::eat(); ?>
        <?php Alert::render(); ?>

        <!-- Title Script -->
        <script>
            $(function() {
                let title = $("title").text();

                if (title.length > 0)
                    $("#nav-title").html(title);
            });
        </script>

        <?php PageRefresher::js(); ?>
    </body>

    <!-- I <33 sleepy -->
</html>

<?php
    Application::end();
?>