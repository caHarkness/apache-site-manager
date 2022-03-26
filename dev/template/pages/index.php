<div class="row" style="min-height: 32px;"></div>

<div class="row">
    <div class="col-lg-1"></div>

    <div class="col-lg-10 col-margin">
        <div class="jumbotron">
            <h1 class="display-4"><?= APP_NAME; ?></h1>
            
            <?php if (file_exists("ABOUT.txt")): ?>
                <p class="lead"><?= file_get_contents("ABOUT.txt"); ?></p>
            <?php else: ?>
                <p>This project is missing an ABOUT.txt document.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-1"></div>
</div>