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

<style>
    #md-document h1,
    #md-document h2,
    #md-document h3,
    #md-document h4,
    #md-document h5,
    #md-document h6 {
        margin-top: 32px;
        margin-bottom: 8px;
    }
</style>

<?php if ($intHasDocument): ?>
    <title><?= $strDocument; ?></title>

    <div class="row margin-top margin-bottom">
        <div class="col-lg-2 margin-bottom">
            <div id="doc-nav">
                <div class="margin-bottom">
                    <h6>Documents</h6>
                    <ul class="list-unstyled unmargin">
                        <?php foreach ($arrPaths as $p): ?>
                            <?php $strFileName = $p[0]; ?>
                            <li><a href="/doc/<?= $strFileName; ?>"><?= $p[0]; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="margin-bottom">
                    <h6>Index</h6>
                    <ul class="list-unstyled unmargin" id="md-headers">
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8 margin-bottom" id="md-document">
            <?= $strDocumentData; ?>
        </div>

        <script>
            $(function() {
                $("#md-document")
                    .find("h1, h2, h3, h4, h5, h6")
                    .each(function(i, x) {
                        $(x).attr("is-header", $(x).prop("tagName").toLowerCase().substring(1));
                    });

                $("*[is-header]").each(function(i, x) {
                    var newid = $(x).text();

                    newid = newid.replaceAll(" ", "_");
                    newid = newid.toLowerCase();

                    $(x).attr("id", newid);

                    var li = $("<li><a href=\"#" + newid + "\">" + $(x).text() + "</a></li>");
                    
                    //var h = parseInt($(x).attr("is-header")) - 1;
                    //var p = h * 4;
                    //p = 0;
                    //li.css("padding-left", p + "px");

                    $("#md-headers").append(li);
                });
            });
        </script>
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

<?php if (in_array("@print", Request::getPath())): ?>
    <script>
        $(function() {
            $("#app-header").hide();
            $("#app-footer").hide();
            $("#doc-nav").hide();
        });
    </script>
<?php endif; ?>