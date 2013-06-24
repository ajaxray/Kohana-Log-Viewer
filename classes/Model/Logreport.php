<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Create report from a generic Kohana Log file
 *
 * Author: Anis uddin Ahmad <anisniit@gmail.com>
 * Created On: 11/10/11 8:44 PM
 */
class Model_Logreport{

    protected $_rawContent;
    protected $_logEntries = array();

    // Copy of Kohana_log_file log levels
    public static $levels = array(
		LOG_EMERG   => 'EMERGENCY',
		LOG_CRIT    => 'CRITICAL',
		LOG_ERR     => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE  => 'NOTICE',
		LOG_INFO    => 'INFO',
		LOG_DEBUG   => 'DEBUG',
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
        $pattern = "/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/";
        $last_log = null;
		$message = '';
		$start_trace = false;
		$i = 0;
        foreach($this->_rawContent as $logRaw) {
			$logRaw = trim($logRaw);
			if (empty($logRaw)) continue;
			if ($logRaw != '--' && $logRaw[0] != '#' && stripos($logRaw, 'STRACE') === FALSE) {
				preg_match($pattern, $logRaw, $matches);

				$log = array();
				$log['raw'] = $logRaw;
				if($matches) { 
					$log['time'] = strtotime($matches[1]);
					$log['level'] = $matches[2];    // Notice, Error etc.
					$log['style'] = $this->_getStyle($matches[2]);    // CSS class for styling
					$log['type'] = $matches[3];     // Exception name
					$log['message'] = $matches[4];
					$log['file'] = $matches[5];
				}

				$this->_logEntries[] = $log;
				$last_log = $i;
				$i++;
			}
			
			if (stripos($logRaw, 'STRACE') !== FALSE) {
				$message = Arr::get($this->_logEntries[$last_log], 'message');
				$this->_logEntries[$last_log]['message'] =  $message . '<br/><br/><p>Stack Trace:</p><ol style="font-family:consolas;font-size:8pt">';
			}
			
			if ($logRaw[0] == '#') {
				$logRaw = preg_replace('/#\d /', '', $logRaw);
				$this->_logEntries[$last_log]['message'] .= '<li>'.$logRaw . '</li>';
			}
			
			if (preg_match('/\{main\}/', $logRaw)) {
				$this->_logEntries[$last_log]['message'] .= '</ol>';
			}
        }
    }

    private function _getStyle($level)
    {
        switch($level){
            case self::$levels[LOG_WARNING]:
            case self::$levels[LOG_DEBUG]:
                return 'warning';
                break;
            case self::$levels[LOG_ERR]:
            case self::$levels[LOG_CRIT]:
            case self::$levels[LOG_EMERG]:
                return 'important';
            break;
            case self::$levels[LOG_NOTICE]:
                return 'notice';
            break;
            case self::$levels[LOG_INFO]:
                return 'success';
            break;
            default: '';
        }
    }

}