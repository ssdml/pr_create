<?php
class Project {
	protected $projects_folder = "C:\\Users\\serega\\Documents\\projects";
	protected $work_folder = 'work';
	protected $backup_folder = 'backup';
	protected $project_name = '';
	protected $path_separator = '\\';

	protected $texteditor_path = 'C:\Program Files\Sublime Text 3\sublime_text.exe';

	public function __construct($project_name) {
		if (!$project_name) die('Empty project name!');
		$this->project_name = trim($project_name, $this->path_separator);
		$this->project_path = $this->projects_folder . $this->path_separator . $this->project_name;
	}

	public function create() {
		if (!is_dir($this->projects_folder)) $this->createDir($this->projects_folder);

		if (is_dir($this->project_path)) die("Project '$this->project_name' allready exists!");

		$this->createDir($this->project_path);
		chdir($this->project_path);

		$this->createDir($this->work_folder);
		$this->createDir($this->backup_folder);

		$this->createTxtFile('jira.txt');
		exec("\"$this->texteditor_path\" jira.txt");

		$this->createKeepassEntrie();

	}

	protected function createKeepassEntrie() {
		$keepass_path = 'C:\\Program Files\\KeePass Password Safe 2\\KeePass.exe';
		$keepass_database = 'C:\\Users\\serega\\Documents\\projects\\projects.kdbx';
		exec("\"$keepass_path\" \"$keepass_database\"");
	}

	protected function createDir($dirname, $recuresive=true) {
		$result = mkdir($dirname, '0760', $recuresive);
		echo $result ? "Dirictory '$dirname' created\n" : "Cannot create directory: '$dirname'\n";
		return $result;
	}

	protected function createTxtFile($filename, $content='') {
		if ( ($fp = fopen($filename, 'a')) === false ) echo "Cannot create file: '$filename'\n";
		fwrite($fp, $content);
		fclose($fp);
	}
}
echo 'Enter project name: ';
$project_name = trim(fgets(STDIN));

$proj = new Project($project_name);
$proj->create();

?>