<?php
class Project {
	protected $project_name = '';
	protected $projects_folder = '';
	protected $ps = '\\'; // path separator

	protected $work_dir = 'work';
	protected $task_dir = 'task';
	protected $folders_to_create = array();

	protected $texteditor = '';

	protected $keepass = '';
	protected $keepass_database = '';

	public function __construct($project_name) {
		if (!$project_name) die('Empty project name!');
		$this->loadConfig('config.ini');
		$this->project_name = trim($project_name, $this->ps);
		$this->project_path = $this->projects_folder . $this->ps . $this->project_name;
		$this->folders_to_create = array($this->work_dir, $this->task_dir, 'backup', 'backup' . $this->ps . 'real', 'backup' . $this->ps . 'test', 'doc');
	}

	public function create() {
		if (!is_dir($this->projects_folder)) $this->createDir($this->projects_folder);

		if (is_dir($this->project_path)) die("Project '$this->project_name' allready exists!");

		$this->createDir($this->project_path);
		chdir($this->project_path);

		array_map(array($this, 'createDir'), $this->folders_to_create);


		if (strpos($this->texteditor, 'sublime') !== false) {
			exec('"' . $this->texteditor. '" "' . $this->createSublimeProject() .'"');
			$this->createFPTSyncSettings();
		}

		$this->createTxtFile($this->task_dir . $this->ps . 'jira.txt');
		$this->createTxtFile($this->task_dir . $this->ps . 'task.txt');

		$this->createKeepassEntrie();

	}

	protected function createSublimeProject() {
		$sublime_project_file = array(
				'folders' => array(
					array('path' => $this->work_dir),
					array('path' => $this->task_dir),
				)
			);
		$sublime_project_file_path = "$this->project_name.sublime-project";
		$this->createTxtFile($sublime_project_file_path, json_encode($sublime_project_file, JSON_PRETTY_PRINT));
		return $sublime_project_file_path;
	}

	protected function createFPTSyncSettings() {
		chdir($this->work_dir);
		$sublime_ftpsync = array($this->project_name => array(
			'host' => '', 'username' => '', 'password' => null, 'path' => '', 'upload_on_save' => true, 'tls' => true)
		);
		$this->createTxtFile('ftpsync.settings', json_encode($sublime_ftpsync, JSON_PRETTY_PRINT));
		chdir($this->project_path);

	}

	protected function loadConfig($iniFile) {
		$ini = parse_ini_file($iniFile);
		foreach ($ini as $key => $value) {
			$this->$key = $value;
		}
	}
	protected function createKeepassEntrie() {
		if ($this->keepass && $this->keepass_database)
			exec("\"$this->keepass\" \"$this->keepass_database\"");
	}

	protected function createDir($dirname, $recuresive=false) {
		$result = mkdir($dirname, 0760, $recuresive);
		echo $result ? "Dirictory '$dirname' created\n" : "Cannot create directory: '$dirname'\n";
		return $result;
	}

	protected function createTxtFile($filename, $content='') {
		if ( ($fp = fopen($filename, 'a')) === false ) { 
			echo "Cannot create file: '$filename'\n";
			return;
		}
		fwrite($fp, $content);
		fclose($fp);
		echo "File $filename created\n";
	}
}
echo 'Enter project name: ';
$project_name = trim(fgets(STDIN));

$proj = new Project($project_name);
$proj->create();

?>
