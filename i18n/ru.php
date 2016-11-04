<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'Home'=>'Вернуться на сайт',
	'Level'=>'Важность',
	'Time'=>'Время',
	'Type'=>'Тип',
	'File'=>'Файл',
	'All'=>'Все',
	// Если честно, то на английском уровень предупреждений мне более понятен, чем на русском. Кому надо - раскомментируйте
	//'EMERGENCY'=>'ОТКАЗ',
	//'ALERT'=>'ТРЕВОГА',
	//'CRITICAL'=>'ВАЖНО',
	//'ERROR'=>'ОШИБКА',
	//'WARNING'=>'Предупреждение',
	//'NOTICE'=>'Сообщение',
	//'INFO'=>'Для справки',
	//'DEBUG'=>'отладочное',
	'<b>No accessible log files!</b> Check if you\'ve enabled logging in bootstrap.'=>'<strong>Не удалось прочитать файлы отчётов!</strong> Проверьте, что протоколирование включено в файле bootstrap.php.',
	'<b>File deleted successfully!</b>'=>'<strong>Файл успешно удалён</strong>',
	'<b>File (%s) deleting failed!</b> Is this file really exist?'=>'<strong>Файл %s не удалён!</strong> Проверьте права.',
	'Log Report - %04d/%02d/%02d <small>%s logs</small>'=>'Протоколы за %3$02d.%2$02d.%1$04d <small>фильтрация: %4$s</small>',
		// ВНИМАНИЕ! Предыдущий перевод меняет местами параметры, читайте http://php.net/manual/en/function.sprintf.php чтобы понять!
	'<b>No log file found for %04d/%02d/%02d!</b> Please select a Log file from left sidebar.'=>'<strong>Протоколов за %3$02d.%2$02d.%1$04d не найдено!</strong> Обновите страницу (F5) или выберите другой файл из боковой панели',
	'formatted mode'=>'форматирование',
	'raw mode'=>'без форматирования',
	'delete this file'=>'удалить файл',
	'Are you sure to delete?'=>'Вы уверены, что этот файл необходимо удалить?',
	'<b>Log deletion is prohibited</b>'=>'<strong>Удаление протоколов запрещено настроками сайта</strong>',
	'Element :filename not found'=>'Элемент оформления :filename не найден',
);