<?php
    $intHasDocument = 0;

    if (!is_dir("md"))
    {
        Respond::redirect("/", "AlertWarning", "This application does not have a markdown directory yet");
    }

    $arrPaths = Application::filesIn("md", "#[A-Za-z0-9_\-]*.md#");

    if (count(Request::getPath()) > 1)
    {
        $strDocument        = Request::getPath()[1];
        $strDocumentData    = shell_exec("markdown \"md/$strDocument\"");
        $intHasDocument     = 1;
    }
?>

<?php if ($intHasDocument): ?>
    <div class="row margin-top margin-bottom">
        <div class="col-lg-2 margin-bottom">
            <div id="doc-nav">
                <h6>Documents</h6>
                <ul class="list-unstyled unmargin">
                    <?php foreach ($arrPaths as $p): ?>
                        <?php $strFileName = $p[0]; ?>
                        <li><a href="/doc/<?= $strFileName; ?>"><i class="fa fa-fw fa-file"></i> <?= $p[0]; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="col-lg-8 margin-bottom">
            <?= $strDocumentData; ?>
        </div>
    </div>
<?php else: ?>
    <div class="row margin-top margin-bottom">
        <div class="col-lg-1">
        </div>

        <div class="col-lg-5">
            <h6>Documents</h6>
            <ul class="list-unstyled unmargin">
                <?php foreach ($arrPaths as $p): ?>
                    <?php $strFileName = $p[0]; ?>
                    <li><a href="/doc/<?= $strFileName; ?>"><i class="fa fa-fw fa-file"></i> <?= $p[0]; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>