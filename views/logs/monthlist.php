<?php
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'raw';
?>
<ul class="pills">
    <?php foreach($months as $month): ?>
    <li class="<?php if($active_month == $month) echo "active" ?>">
        <?=HTML::anchor("logs/".str_replace('\\','/',$month)."/01/$log_level?mode=$mode", $month); ?></a>
    </li>
    <?php endforeach;?>
</ul>


