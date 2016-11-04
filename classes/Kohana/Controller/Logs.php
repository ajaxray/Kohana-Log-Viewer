<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Display report, created by generic Kohana Log file
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
 * Modified on: 27 July 2016
 * - only last 12 months are displayed, 13's month will jump to the next line, completely destroying layout
 */
class Kohana_Controller_Logs extends Controller {

	/**
	 * @var View
	 */
	public $layout;

	private $_year;
	private $_month;
	private $_day;
	private $_level;
	protected $_config;
	protected $_allow_delete=true;

	function before()
	{
		$this->layout = new View('logs/layout');
		$this->_config=Kohana::$config->load('logviewer');
		$this->layout->bootstrap=$this->_config['bootstrap_css']?$this->_config['bootstrap_css']:URL::site('logs/bootstrap.css');
		$this->layout->style=$this->_config['style_css']?$this->_config['style_css']:URL::site('logs/style.css');
		$this->layout->jquery=$this->_config['jquery_js']?$this->_config['jquery_js']:URL::site('logs/jquery.js');
		$this->_year = $this->request->param('year', date('Y') );
		$this->_month = $this->request->param('month', date('m'));
		$this->_day = $this->request->param('day', date('d'));
		$this->_level = $this->request->param('level', null);
	}

	public function action_index()
	{
		if($this->request->post('delete')==='yes')
			return $this->log_delete();
		$this->_setLayoutVars();
		if($this->_getMonths()){
			$this->layout->set('content', $this->_getLogReport($this->_level));
		} else {
			$this->layout->set('content', $this->_createMessage(
				__('<b>No accessible log files!</b> Check if you\'ve enabled logging in bootstrap.'),
			'error' ));
		}
		$this->response->body($this->layout);
	}

	public function action_asset(){
		$filename=$this->request->param('filename');
		// manually limited file names - prevent hacking attempts
		switch(strtolower($filename)){
			case 'bootstrap.css':
				$filename=Kohana::find_file('assets', 'bootstrap.min', 'css');
				break;
			case 'style.css':
				$filename=Kohana::find_file('assets', 'style', 'css');
				break;
			case 'jquery.js':
				$filename=Kohana::find_file('assets', 'jquery', 'js');
				break;
			default:
				throw new HTTP_Exception_404(__('Element :filename not found'), array(':filename' =>$filename));
		}

		$this->response->headers('Expires',gmdate('D, d M Y H:i:s \G\M\T', (int)KOHANA_START_TIME + (24 * 60 * 60)));
		$this->response->headers('Last-Modified',gmdate('D, d M Y H:i:s \G\M\T', filemtime($filename)));
		$this->response->headers('Cache-Control','max-age=86400, public');
		$this->response->send_file($filename,null,array('inline'=>true));
	}

	protected function log_delete()
	{
		$logfile = "/$this->_year/$this->_month/" . $this->request->param('day').'.php';

		if(!$this->_allow_delete){
			$this->layout->set(
				'content',
				$this->_createMessage(__('<b>Log deletion is prohibited</b>'),'error')
			);
		}else if(@unlink($this->_config['log_path'] .$logfile)){
			$this->layout->set('content', $this->_createMessage(__('<b>File deleted successfully!</b>'), 'success' ));
		} else {
			$this->layout->set(
				'content',
				$this->_createMessage(
					sprintf(
						__('<b>File (%s) deleting failed!</b> Is this file really exist?'),
						HTML::chars($logfile)
					),
				'error')
			);
		}

		$this->_setLayoutVars();
		$this->layout->set_global('active_day', 'NO_DAY_SELECTED');
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

		$this->layout->set_global('allow_delete',$this->_allow_delete);
		$mode=$this->request->query('mode');
		// block hacking attempt
		if(!in_array($mode,array('raw','formatted')))
			$mode='raw';
		$this->layout->set_global('mode',$mode);
	}

	private function _getMonths()
	{
		$years = @scandir($this->_config['log_path']);
		if(empty($years)) return array();

		$years = array_slice($years, 2); // remove . and ..
		$months = array();
		foreach ($years as $year) {
			if ($yearMonths = @scandir($this->_config['log_path'] . '/' . $year)) {
				$yearMonths = array_slice($yearMonths, 2);
				array_walk($yearMonths, function(&$m, $k, $y){
					$m = (substr($m,0,1) != ".") ? $y . '/' . $m : '';
				}, $year);
				// add to result list only folders, that contain files
				foreach($yearMonths as $ym){
					$test=@scandir($this->_config['log_path'].'/'.$ym);
					if(count($test)>2) // not only . and .. in folder
						$months[]=$ym;
				}
			}
		}
		// slice to 12 most recent elements. 13's element will locate to second line, destroying layout
		$cnt=count($months);
		return ($cnt>12)?array_slice($months,$cnt-12):$months;
	}

	private function _getDays()
	{
		$days = @scandir($this->_config['log_path'] . "/{$this->_year}/{$this->_month}");
		if(empty($days)) return array();

		return array_slice($days, 2); // remove . and ..
	}

	private function _getLogReport($level = null)
	{
		$filePath = $this->_config['log_path'] . "/{$this->_year}/{$this->_month}/{$this->_day}.php";
		if(file_exists($filePath)){
			$Report = new Model_Logreport($filePath);
			$logsEntries = $Report->getLogsEntries();

			if($level) {
				$intlevel=array_search($level,Model_Logreport::$levels);
				foreach($logsEntries as $k => $entry){
				  if(array_search(Arr::get($entry, 'level'),Model_Logreport::$levels) > $intlevel)
				  	unset($logsEntries[$k]);
				}
			}

			$title  = sprintf(__('Log Report - %04d/%02d/%02d <small>%s logs</small>'),$this->_year,$this->_month,$this->_day,
				$this->_level? $this->_level : __('All'));

			return View::factory('logs/report', array(
				'logs' => $logsEntries,
				'header' => $title
			));
			//return $this->_createMessage("<b>File found!</b> Beautiful report coming soon.", 'success');
		} else {
			return $this->_createMessage(
				sprintf(__('<b>No log file found for %04d/%02d/%02d!</b> Please select a Log file from left sidebar.'),
					$this->_year, $this->_month, $this->_day
				), 'warning');
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
