<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Kohana Log Viewer</title>
		<meta name="description" content="A Kohana module for exploring log files">
		<meta name="Author" content="WNeeds - http://wneeds.com" />

		<link type="text/css" href="<?=$bootstrap ?>" rel="stylesheet" media="all" />
		<link type="text/css" href="<?=$style ?>" rel="stylesheet" media="all" />

		<script type="text/javascript"> BASEURL = "<?=URL::base() ?>";</script>
		<script type="text/javascript" src="<?=$jquery ?>"></script>

	</head>

  <body>

    <div class="topbar">
      <div class="fill">
        <div class="container">
          <div class="row">
              <div class="span12">
                <h1>Kohana Log Viewer</h1>
              </div>
              <div class="span4">
								<a href="<?=URL::site('') ?>"><h2 style="color: white;"><?=__('Home') ?></h2></a>
              </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container">

      <div class="content">
        <div class="page-header">
            <?php include(Kohana::find_file('views/logs', 'monthlist')); ?>
        </div>
        <div class="row">
            <div class="span3">
                <?php include(Kohana::find_file('views/logs', 'daylist')); ?>
            </div>
            <div class="span13">
                <?php if(isset($content)) echo $content; ?>
            </div>

        </div>
      </div>

      <footer>
        <p>&copy; WNeeds Ltd.</p>
      </footer>

    </div> <!-- /container -->

  </body>
</html>
