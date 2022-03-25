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
        // WARNING:
        // Be mindful of the PHP scripts placed in the 'lib' folder; all of them are executed.
        static function loadLibraries()
        {
            $varDirectory = scandir("lib");
            unset($varDirectory[0]);
            unset($varDirectory[1]);
            $varDirectory = array_values($varDirectory);

            foreach ($varDirectory as $strFile)
                if (preg_match("#[A-Za-z0-9_\-]*.php#", $strFile))
                {
                    if (!(in_array($strFile, Config::getLoadedLibraries())))
                        require "lib/$strFile";
                }
        }

        // Load the contents of the files found in 'var' and set them as a constant. We will only be looking for files named in all capital letters only.
        static function loadFileConstants()
        {
            $varDirectory = scandir("var");
            unset($varDirectory[0]);
            unset($varDirectory[1]);
            $varDirectory = array_values($varDirectory);

            foreach ($varDirectory as $strFile)
                if (preg_match("/^[A-Z0-9_]*/", $strFile))
                {
                    try
                    {
                        //define($strFile, trim(file_get_contents("var/$strFile")));
                        Config::$value["$strFile"] = trim(file_get_contents("var/$strFile"));
                    }
                    catch (Exception $x) {}
                }

            // Finalize the configuration again. It's safe to do.
            Config::finalize();
        }

        public static $strAccessLogGuid = null;
    }

    Application::loadLibraries();
    Application::loadFileConstants();

    // Start the output buffer process and clean it.
    ob_start();
    ob_clean();

    // Load cookies into a static field. We can't modify cookies directly until we are ready to send them along with the output buffer, so the solution to this is to modify a static field of the Cookies class.
    Cookies::load();

    // Log the access to this application and store the $_SERVER blob in the database.
    Application::$strAccessLogGuid =
        Logger::logAccess(
            $_SERVER,
            "Application " . APPLICATION_NAME . " loaded.");

    define("GLOBAL_APPLICATION_ACCESS_LOG_GUID", Application::$strAccessLogGuid);
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
        <link rel="stylesheet" href="/css/layout.css">
        <link rel="stylesheet" href="/css/margin.css">
        <link rel="stylesheet" href="/css/padding.css">
        <link rel="stylesheet" href="/css/styles.css">
        <link rel="stylesheet" href="/css/tooltip.css">

        <!-- Afar JavaScript libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <!-- Local JavaScript libraries -->
        <script src="/js/jquery.tablesorter.js"></script>
    </head>

    <body>
        <!-- Navigation -->
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <span class="navbar-brand" id="nav-title"><?= APPLICATION_NAME; ?></span>

            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="/">Home</a></li>

                    <!-- App Specific Links -->
                    <?php if (file_exists("menu.php")): ?>
                        <?php require "menu.php"; ?>
                    <?php endif;?>
                </ul>
            </div>

            <span class="navbar-brand" id="nav-subtitle">
                <?= IS_DEV? "(dev)": ""; ?>
            </span>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>

        <!-- Rendered view -->
        <?php Request::process(); ?>

        <hr />

        <!-- Project footer -->
        <?php if (file_exists("footer.php")): ?>
            <?php require "footer.php"; ?>
        <?php endif; ?>

        <!-- Global footer -->
        <div class="row">
            <div class="col-lg-1"></div>

            <div class="col-lg-10">
                <p><small>Copyright &copy; 2022 Publication Printers corp. This software is proprietary and internal to Publication Printers Corp. Do not distribute the information you see on this page. Please send an e-mail addressing any concerns of this software to <a href="mailto:Conner Harkness <conner.harkness@publicationprinters.com>">Conner Harkness</a>, Full Stack Software Engineer. For a list of all applications on this server, please click the <a href="<?= APPS_LINK; ?>">APPS.md</a> link above.</small></p>

                <p>
                    <!-- Access Guid -->
                    <small class="fg-opacity-33"><?= GLOBAL_APPLICATION_ACCESS_LOG_GUID; ?></small>
                </p>
            </div>

            <div class="col-lg-1"></div>
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
    </body>
</html>

<?php
    // Finalize the cookies for sending as header information.
    Cookies::finalize();

    // Send the contents of the output buffer to the client requesting the page or resource. Exit after doing so!
    ob_end_flush();
    exit;
?>