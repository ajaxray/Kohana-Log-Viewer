<?php
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'raw';
?>
<ul class="pills">
    <?php foreach($months as $month): ?>
    <li class="<?php if($active_month == $month) echo "active" ?>">
        <a href="<?php echo URL::site("logs/$month") . '?mode='.$mode ?>"><?php echo $month ?></a>
    </li>
    <?php endforeach;?>
</ul>

 
