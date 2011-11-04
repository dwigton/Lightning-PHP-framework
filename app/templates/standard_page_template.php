<!doctype html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php echo $this->getCss();?>
        <?php echo $this->getScript();?>
    </head>
    <body>
        <div id="page">
            <header>
                <hgroup>
                    <h1>Lightening Framework</h1>
                    <h2>Lightweight and light hearted</h2>
                </hgroup>
            </header>
            <div id ="content">
                <?php $this->renderChild('content'); ?>
            </div>
            <footer>
                copyright <?php echo date('Y');?> Daniel Wigton
            </footer>
        </div>
    </body>
</html>
