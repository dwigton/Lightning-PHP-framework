<!doctype html>
<html>
    <head>
        <meta charset="UTF-8"> 
        <title><?php echo $title;?></title>
        <?php $this->renderItems('css');?>
        <?php $this->renderItems('script');?>
    </head>
    <body>
        <div id ="content">
            <?php $this->renderChild('content'); ?>
        </div>
    </body>
</html>
