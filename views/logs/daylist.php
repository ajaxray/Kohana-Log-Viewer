<ul class="pills">
    <?php foreach($days as $day): ?>
    <li class="<?php if($active_report == $day) echo "active" ?>">
        <?=HTML::anchor("logs/$active_month/" . substr($day, 0, 2) . "/$log_level?mode=$mode", $day); ?>
    </li>
    <?php endforeach;?>
</ul>
