<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Create report from a generic Kohana Log file
 *
 * Author: Anis uddin Ahmad <anisniit@gmail.com>
 * Created On: 11/10/11 8:44 PM
 * Modified: Python <smisoft@rambler.ru>, https://github.com/SmiSoft
 * Modified on: 18 January, 2016
 * - Fixed coding-standarts (Kohana_Class names, allowing simple extension)
 * - Log level PHP Bug #18090 hack
 * - Hard indentation
 * - Internationalization (only russian and english supported, I don't know any
 *   other language, sorry)
 * - Replaced <?php echo ... into <?= - modern PHP understand this construction even short_tags=off
 * - Log level has now not STRICTLY EQUAL, but higher or equal than selected level. This is more appropriate
 * - Select from top-panel month bug (\ instead of /) workaround
 * - Assets located outside of HTML code. You can specify strict location in config file, or use defaults
 * Modified on: 04 Novenber 2016
 * modern Kohana writes exeption into two lines: EMERGENCY line, that describes exception type and INFO line,
 * that describes position. It seems, that used algorithm was worked well for old Kohana versions, but now it
 * can not combine this two lines. Fixed for Kohana 3.3, requires testing for previous Kohana's versions
 * (if you can).
 */
class Kohana_Model_Logreport{

		protected $_rawContent;
		protected $_logEntries = array();

		// Log message levels - Windows users see PHP Bug #18090
		/* Modified by Python - here, I use Kohana_Log constants instead of PHP constants
			Thats's because I override Kohana_Log with:
			<?php defined('SYSPATH') OR die('No direct script access.');
			// File: APPPATH/classes/Log/File.php
			class Log_File extends Kohana_Log_File {
				protected $_log_levels = array(
					Log::EMERGENCY   => 'EMERGENCY',
					Log::ALERT   => 'ALERT',
					Log::CRITICAL => 'CRITICAL',
					Log::ERROR     => 'ERROR',
					Log::WARNING => 'WARNING',
					Log::NOTICE  => 'NOTICE',
					Log::INFO    => 'INFO',
					Log::DEBUG   => 'DEBUG',
				);
			}
			And file:
			<?php defined('SYSPATH') OR die('No direct script access.');
			// File: APPPATH/classes/Log.php
			class Log extends Kohana_Log {
				const EMERGENCY = 0; // LOG_EMERG;    // 0
				const ALERT     = 1; // LOG_ALERT;    // 1
				const CRITICAL  = 2; // LOG_CRIT;     // 2
				const ERROR     = 3; // LOG_ERR;      // 3
				const WARNING   = 4; // LOG_WARNING;  // 4
				const NOTICE    = 5; // LOG_NOTICE;   // 5
				const INFO      = 6; // LOG_INFO;     // 6
				const DEBUG     = 7; // LOG_DEBUG;    // 7
			}
			So, even on Windows I can use full range of log-levels
		 */
		public static $levels = array(
			Log::EMERGENCY   => 'EMERGENCY',
			Log::ALERT   => 'ALERT',
			Log::CRITICAL => 'CRITICAL',
			Log::ERROR     => 'ERROR',
			Log::WARNING => 'WARNING',
			Log::NOTICE  => 'NOTICE',
			Log::INFO    => 'INFO',
			Log::DEBUG   => 'DEBUG',
		);

		function __construct($filepath)
		{
			// Read lines as array. Skip first 2 lines - SYSPATH checking and blank line
			$this->_rawContent = array_slice(file($filepath), 2);
			$this->_createLogEntries();
		}

		public function getLogsEntries($level = null){
			return $this->_logEntries;
		}

		protected function _createLogEntries()
		{
			$pattern = "/(.*?) --- ([A-Z]*): (?:(?:([^:]*): ([^~]*) ~ (.*))|(.*))/";
			$start_trace = false;
			$last_log=-1;
			$startList='<ol style="font-family:consolas;font-size:-2">';
			foreach($this->_rawContent as $logRaw) {
				$logRaw = trim($logRaw);
				if (empty($logRaw)) continue;
				if ($logRaw === '--'){

				}else if (stripos($logRaw, 'STRACE') !== FALSE) {
					$this->_logEntries[$last_log]['message'] .= '<br/><br/><p>Stack Trace:</p>'.$startList;
				} else if (stripos($logRaw,'{main} ~ file [ line ]')!==FALSE or stripos($logRaw,'{main} in file:line')!==FALSE) {
					if (substr($this->_logEntries[$last_log]['message'],-strlen($startList))==$startList)
						$this->_logEntries[$last_log]['message']=substr($this->_logEntries[$last_log]['message'],0,-strlen($startList));
					else
						$this->_logEntries[$last_log]['message'] .= '</ol>';
				}else if ($logRaw[0] == '#') {
					$logRaw = preg_replace('/#\d /', '', $logRaw);
					$this->_logEntries[$last_log]['message'] .= '<li>'.$logRaw . '</li>';
				} else if (($pos=stripos($logRaw,'--- INFO: #0'))!==FALSE){
					$this->_logEntries[$last_log]['message'] .= '<br/><p>'.substr($logRaw,$pos+12).'</p>'.$startList;
					$this->_logEntries[$last_log]['raw'] .= '<br/>'.substr($logRaw,$pos+12);
				} else {
					preg_match($pattern, $logRaw, $matches);

					$log = array();
					$log['raw'] = $logRaw;
					if($matches) {
						$log['time'] = strtotime($matches[1]);
						$log['level'] = $matches[2];    // Notice, Error etc.
						$log['style'] = $this->_getStyle($matches[2]);    // CSS class for styling
						if (isset($matches[6])) {
							$log['type'] = $log['file'] = '';
							$log['message'] = isset($matches[6]) ? $matches[6] : '';
						}
						else {
							$log['type'] = $matches[3];     // Exception name
							$log['message'] = $matches[4];
							$log['file'] = $matches[5];
						}
					}
					$this->_logEntries[++$last_log] = $log;
				}
			}
		}

		private function _getStyle($level)
		{
			switch($level){
				case self::$levels[Log::WARNING]:
				case self::$levels[Log::DEBUG]:
					return 'warning';
					break;
				case self::$levels[Log::ERROR]:
				case self::$levels[Log::CRITICAL]:
				case self::$levels[Log::EMERGENCY]:
					return 'important';
				break;
				case self::$levels[Log::NOTICE]:
					return 'notice';
				break;
				case self::$levels[Log::INFO]:
					return 'success';
				break;
				default: '';
			}
		}

}
