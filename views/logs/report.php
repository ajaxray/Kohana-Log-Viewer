<?php
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'raw';
?>

<h2><?php echo $header ?></h2>

    <p>&nbsp;</p>

    <div class="row filter-form">
        <form name="mapping-filter" action="" class="pull-right">

            Level
            <select class="input-small" name="levels" onchange="location='<?php echo URL::site("logs/$active_month/$active_day")?>/' + options[selectedIndex].value + '/?mode=<?php echo $mode ?>'">
                <option value="">--All--</option>
                <?php 
                foreach (Model_Logreport::$levels as $level):
                    $select = ($log_level == $level) ? 'selected' : '';
                    echo "<option $select value=\"". strtoupper($level).'">'.$level.'</option>';
                endforeach;
                ?>
            </select>&nbsp;
            
            <?php if($mode == 'raw'): ?>
            <a class="btn success" href="?mode=formatted">formatted mode</a>
            <?php else: ?>
            <a class="btn info" href="?mode=raw">raw mode</a>
            <?php endif; ?>
            <a class="btn danger" href="<?php echo URL::site("logs/delete/$active_month/$active_report") ?>" onclick="return confirm('Are you sure to delete?')">delete this file</a>
        </form>
    </div>
    <table class="zebra-striped" width="100%">
        <?php if($mode != 'raw'): ?>
        <thead>
            <tr>
                <th width="5%">Level</th>
                <th width="10%">Time</th>
                <th width="30%">Type</th>
                <th width="65%">File</th>
            </tr>

        </thead>
    <?php endif; ?>
        <tbody>
            <?php foreach ($logs as $log):?>
            <tr>
                <?php if($mode != 'raw'): ?>
                <td rowspan="2">
                    <span class="label <?php echo Arr::get($log,'style') ?>"> <?php echo Arr::get($log,'level') ?> </span>
                </td>
                <td><?php echo date('H:i:s', Arr::get($log,'time')) ?></td>
                <td><?php echo Arr::get($log,'type') ?></td>
                <td><?php echo Arr::get($log,'file') ?></td>

            </tr>
            <tr><td colspan="4"><b>Message: </b><?php echo Arr::get($log,'message') ?></td></tr>
            <?php else: // Raw mode ?>
            <tr>
                <td>
                    <span class="label <?php echo Arr::get($log,'style') ?>"> <?php echo Arr::get($log,'level') ?></span> &nbsp;
                    <?php echo Arr::get($log,'raw') ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

