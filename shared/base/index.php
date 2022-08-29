<?php
    // WARNING:
    // DO NOT MAKE CHANGES to this file if it's in any directory other than '/var/www/pre/base.' YOUR CHANGES WILL BE LOST. A call to 'source lib.sh project-rebase' refreshes all projects with what's in the /var/www/pre/base directory!

    // ABOUT:
    // This file, "index.php," is responsible for handing all requests pertinent to the project. For example, let's say a request is made to https://my.site/controller/action/abc/123. This "index.php" breaks apart the request into a several bits. There is a "pages" directory in each project that houses each of the controllers. A controller can be a single .php file itself or a folder containing .php scripts as actions. Refer to the Request.php library in the "lib" directory of the "api" project. Anything after the controller and action in the address bar is stored into a string array that can be accessed via Request::getPath(). The first two elements of this array are "controller" and "action." Strings "abc" and "123" are represented as elements whose indecies are 2 and 3 of this array.

    // Load the configuration of the entire application before anything else! The order implies that the master configuration file is loaded and the project-level configuration offers any overrides.

    require "../../config.php";     // The configuration in "/var/www"
    require "../config.php";        // The configuration in either "/var/www/dev" or "/var/www/live"
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
            {
                require $strFilePath;
                return true;
            }

            return false;
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

        static function getDirectoryName()
        {
            return basename(__DIR__);
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

            if (Post::size() > -1)
            {
                $intInputSize = number_format(Post::size(), 0);
                Logger::log("POST request of {$intInputSize} bytes received.");
            }
        }

        // Called after the output buffer is finished being written to and when the application is ready to flush the contents to the client.
        static function end()
        {
            // Finalize the cookies for sending as header information.
            Cookies::finalize();

            $intOutputSize  = number_format(strlen(ob_get_contents()), 0);

            Logger::log(
                Text::join(
                    "View completed in ", Application::$varStopwatch->measure(), " with $intOutputSize bytes."));

            // Send the contents of the output buffer to the client requesting the page or resource. Exit after doing so!
            ob_end_flush();
            exit;
        }
    }

    Application::start();
    Application::requireExisting("init.php");
?>

<!DOCTYPE html>

<html>
    <head>
        <!-- Icon -->
        <link rel="icon" href="/res/icon.png">

        <link rel="stylesheet" href="/files/lib/bootstrap-4.6.2-dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="/files/lib/fontawesome-free-6.1.1-web/css/all.min.css" />
        <link rel="stylesheet" href="/files/lib/toastr-2.1.4/css/toastr.min.css" />
        <link rel="stylesheet" href="/files/lib/bootstrap-datepicker-1.9.0-dist/css/bootstrap-datepicker.min.css" />

        <!-- Internal CSS --->
        <?php foreach (Application::filesIn("css", "#[A-Za-z0-9_\-\.]*.css#") as $f): ?>
            <link rel="stylesheet" href="/<?= $f[1]; ?>?g=<?= rand(); ?>" />
        <?php endforeach; ?>

        <!-- Afar JavaScript libraries -->
        <script src="/files/lib/jquery-3.6.0/js/jquery-3.6.0.min.js"></script>
        <script src="/files/lib/jquery-cookie-1.4.1/js/jquery.cookie.js"></script>
        <script src="/files/lib/bootstrap-4.6.2-dist/js/bootstrap.min.js"></script>
        <script src="/files/lib/fontawesome-free-6.1.1-web/js/all.min.js"></script>
        <script src="/files/lib/toastr-2.1.4/js/toastr.min.js"></script>
        <script src="/files/lib/bootstrap-datepicker-1.9.0-dist/js/bootstrap-datepicker.min.js"></script>

        <!-- Local JavaScript libraries -->
        <?php foreach (Application::filesIn("js", "#[A-Za-z0-9_\-\.]*.js#") as $f): ?>
            <script src="/<?= $f[1]; ?>?g=<?= APP_INSTANCE_ID; ?>"></script>
        <?php endforeach; ?>

        <!-- Application-specific additional header -->
        <?php Application::requireExisting("head.php"); ?>
    </head>

    <body>
        <div id="app-header">

            <!-- Default navigation -->
            <div class="navbar navbar-expand navbar-dark bg-dark justify-content-between">
                <div class="navbar-nav">
                    <span class="navbar-brand">Menu</span>

                    <!-- App Specific Links -->
                    <?php Application::requireExisting("menu.php"); ?>
                </div> 

                <div class="navbar-nav">
                    <div class="navbar-brand">
                        <?php
                            $strTitle = Application::getDirectoryName();
                            if (defined("APP_NAME"))            $strTitle = APP_NAME;
                            if (defined("APP_FRIENDLY_NAME"))   $strTitle = APP_FRIENDLY_NAME;
                            $strSubtitle = Request::getPathString();

                            if (IS_DEV)
                                $strTitle = "{$strTitle} (dev)";
                        ?>

                        <span id="nav-title"><?= $strTitle; ?></span> &middot;
                        <span id="nav-subtitle"><?= $strSubtitle; ?></span>
                        
                        <title><?= $strSubtitle; ?></title>
                    </div>
                </div>

                <div class="navbar-nav">
                </div>
            </div>

            <div id="app-header-extras"></div>
        </div>

        <div id="app-body">
            <!-- Default body -->
            <?php
                Request::process();
                Request::render();
            ?>
        </div>
        
        <div id="app-footer">
            <!-- Default footer -->
            <hr />

            <div class="container">
                <div class="col-lg-1"></div>

                <div class="col-lg-5">
                    <p><small>Copyright &copy; 2022 Conner Harkness.</small></p>
                </div>
            </div>
        </div>

        <!-- Extra JavaScript -->
        <?php Alert::js(); ?>
        <?php Alert::eat(); ?>
        <?php Alert::render(); ?>
    </body>
</html>

<?php
    Application::end();
?>