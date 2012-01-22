<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Logs extends Controller {

    /**
     * @var View
     */
    public $layout;
    private $_logDir;

    private $_year;
    private $_month;
    private $_day;
    private $_level;

    function before()
    {
        $this->layout = new View('logs/layout');
        $this->_logDir = Kohana::$config->load('logviewer.log_path');

        $today = getdate();
        $this->_year = $this->request->param('year', $today['year'] );
        $this->_month = $this->request->param('month', $today['mon']);
        $this->_day = $this->request->param('day', $today['mday']);
        $this->_level = $this->request->param('level', null);
    }

	public function action_index()
	{
        //echo "$this->_year/$this->_month/$this->_day/$this->_level";

		if (!$this->request->query('mode')) 
			$this->request->redirect($this->request->uri().'?mode=raw');		
		
        if($this->_getMonths()){
            $this->_setLayoutVars();
            $this->layout->set('content', $this->_getLogReport($this->_level));

        } else {
            $this->layout->set('content', $this->_createMessage("<b>No accessible log files!</b> Check if you've enabled logging in bootstrap.", 'error' ));
        }

        $this->response->body($this->layout);
	}

    public function action_delete()
    {
        $logfile = "/$this->_year/$this->_month/" . $this->request->param('logfile');

        if(@unlink($this->_logDir .'/'. $logfile)){
            $this->layout->set('content', $this->_createMessage("<b>File deleted successfully!</b>", 'success' ));
        } else {
            $this->layout->set('content', $this->_createMessage("<b>File ({$logfile}) deleting failed!</b> Is this file really exist?", 'error' ));
        }

        $this->_setLayoutVars();
        $this->layout->set_global('active_day', "NO_DAY_SELECTED");
        $this->response->body($this->layout);
    }

    protected function _setLayoutVars()
    {
        $this->layout->set('months', $this->_getMonths());
        $this->layout->set('days', $this->_getDays());

        $this->layout->set_global('active_month', "$this->_year/$this->_month");
        $this->layout->set_global('active_day', $this->_day);
        $this->layout->set_global('active_report', "$this->_day.php");
        $this->layout->set_global('log_level', $this->_level);
    }

    private function _getMonths()
    {
        $years = @scandir($this->_logDir);
        if(empty($years)) return false;

        $years = array_slice($years, 2); // remove . and ..
        $months = array();
        foreach ($years as $year) {
            if ($yearMonths = @scandir($this->_logDir . '/' . $year)) {
                $yearMonths = array_slice($yearMonths, 2);
                array_walk($yearMonths, function(&$m, $k, $y)
                    {
                        $m = (substr($m,0,1) != ".") ? $y . DIRECTORY_SEPARATOR . $m : '';
                    }, $year);

                $months = array_merge($months, $yearMonths);
            }
        }
        return $months;
    }

    private function _getDays()
    {
        $days = @scandir($this->_logDir . "/{$this->_year}/{$this->_month}");
        if(empty($days)) return array();

        return array_slice($days, 2); // remove . and ..
    }

    private function _getLogReport($level = null)
    {
        $filePath = $this->_logDir . "/{$this->_year}/{$this->_month}/{$this->_day}.php";
        if(file_exists($filePath)){
            $Report = new Model_Logreport($filePath);
            $logsEntries = $Report->getLogsEntries();

            if($level) {
                foreach($logsEntries as $k => $entry){
                  if(Arr::get($entry, 'level') != $level) unset($logsEntries[$k]);
                }
            }

            $title  = "Log Report - {$this->_year}/{$this->_month}/{$this->_day}";
            $title .= ' <small>'. (($this->_level)? " {$this->_level}" : ' All'). ' Logs</small>';
            
            return View::factory('logs/report', array(
                      'logs' => $logsEntries,
                      'header' => $title
                    ));
            //return $this->_createMessage("<b>File found!</b> Beautiful report coming soon.", 'success');
        } else {
            return $this->_createMessage("<b>No log file found for {$this->_year}/{$this->_month}/{$this->_day}!</b> Please select a Log file from left sidebar.", 'warning');
        }
    }

    /**
     * Create HTML for alert message
     *
     * @param $message
     * @param $type error|success|info|warning
     * @return string Message HTML
     */
    private function _createMessage($message, $type)
    {
        return "<div class=\"alert-message {$type}\"><p>{$message}</p></i></div>";
    }



} // End Controller_Logs
