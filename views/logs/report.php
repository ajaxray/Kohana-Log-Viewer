<h2><?=$header ?></h2>

	<p>&nbsp;</p>

	<div class="row filter-form">
		<?php if($allow_delete): ?>
			<form action="<?=URL::site("logs/$active_month/$active_day?mode=$mode") ?>" method="POST" class="pull-right" style="margin-left: 1em;">
				<input type="hidden" name="delete" value="yes"/>
				<input type="submit" class="btn danger" onclick="return confirm('<?=__('Are you sure to delete?') ?>');" value="<?=__('delete this file')?>"/>
			</form>
		<?php endif ?>

		<form name="mapping-filter" action="" class="pull-right">

			<?=__('Level') ?>
			<select class="input-small" name="levels" onchange="location='<?=URL::site("logs/$active_month/$active_day")?>/' + options[selectedIndex].value + '/?mode=<?=$mode ?>'">
				<option value="">--<?=__('All') ?>--</option>
				<?php
				foreach (Model_Logreport::$levels as $level):
					$select = ($log_level == $level) ? 'selected' : '';
					echo "<option $select value=\"". strtoupper($level).'">'.__($level).'</option>';
				endforeach;
				?>
			</select>&nbsp;

			<?php if($mode == 'raw'): ?>
			<a class="btn success" href="?mode=formatted"><?=__('formatted mode') ?></a>
			<?php else: ?>
			<a class="btn info" href="?mode=raw"><?=__('raw mode')?></a>
			<?php endif; ?>
		</form>
	</div>
	<table class="zebra-striped" width="100%">
		<?php if($mode != 'raw'): ?>
		<thead>
			<tr>
				<th width="5%"><?=__('Level') ?></th>
				<th width="10%"><?=__('Time') ?></th>
				<th width="30%"><?=__('Type') ?></th>
				<th width="65%"><?=__('File') ?></th>
			</tr>

		</thead>
	<?php endif; ?>
		<tbody>
			<?php foreach ($logs as $log):?>
			<tr>
				<?php if($mode != 'raw'): ?>
				<td rowspan="2">
					<span class="label <?=Arr::get($log,'style') ?>"> <?=Arr::get($log,'level') ?> </span>
				</td>
				<td><?=date('H:i:s', Arr::get($log,'time')) ?></td>
				<td><?=Arr::get($log,'type') ?></td>
				<td><?=Arr::get($log,'file') ?></td>

			</tr>
			<tr><td colspan="4"><?=Arr::get($log,'message') ?></td></tr>
			<?php else: // Raw mode ?>
			<tr>
				<td>
					<span class="label <?=Arr::get($log,'style') ?>"> <?=Arr::get($log,'level') ?></span> &nbsp;
					<?=Arr::get($log,'raw') ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>

