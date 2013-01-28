<?php
	
	/*
		Classes to store and organize project information.
	*/
	class Project
	{
		public $title;	
		public $date;
		public $version;
		public $summary;
		public $files;

		function __construct( $title, $date, $version, $summary = "", $files = array() ) {
			$this->title = (string)$title;
			$this->date = date("D, d M Y H:i", strtotime((string)$date) );
			$this->version = (int)$version;
			$this->summary = (string)$summary;
			$this->files = $files;
		}
	}

	class ProjectFile
	{
		public $type;
		public $path;
		public $size;
		public $versions;

		function __construct( $type, $path, $size, $versions = array() ) {
			$this->type = (string)$type;
			$this->path = (string)$path;
			$this->size = (int)$size;
			$this->versions = $versions;
		}
	}

	class FileVersion
	{
		public $version_number;
		public $author;
		public $message;
		public $date;

		function __construct( $version_number, $author, $message, $date) {
			$this->version_number = (int)$version_number;
			$this->author = (string)$author;
			$this->message = (string)$message;
			$this->date = date("D, d M Y H:i", strtotime((string)$date) );

		}
	}

	// Define file extension as a . followed by at least one alpha numeric character
	$FILE_EXTENSION_REGEXP = "/(\.\w+$)|Doxyfile/";

	// Lookup table for extensions to file types
	$PROJECT_FILE_TYPES = array(
		".rb" => "code",
		".php" => "code",
		".java" => "code",
		".py" => "code", 
		".css" => "code",
		".html" => "code",
		".json" => "resource",
		".xml" => "resource",
		".bmp" => "image",
		".jpeg" => "image",
		".gif" => "image",
		"Doxyfile" => "documentation"
	);
		
	/**
		NOTES:
		- Each project is defined as a directory that's not a sub-directory, which also happens
			to not contain any slashes
		- Each sub-directory is ignored because it doesn't provide any useful or necessary information,
			and each file path will contain the right subdirectory
		- Project summary and individual file messages to be assigned in LOG parsing
	**/


	/*
		Parses an xml list file into previously defined objects.
		@param xml_file - the path to an xml file in svn list format
		@return associative array of projects, where
			key - the title of the project,
			value - the Project object.
	*/
	function parse_svn_list($xml_file) {

		if ( is_null($xml_file) || !file_exists($xml_file) ) {
			throw new Exception( "Illegal argument!" );
		}

		global $PROJECT_FILE_TYPES, $FILE_EXTENSION_REGEXP;

		try {
			$lists = simplexml_load_file($xml_file);
		}
		catch (Exception $e) 
		{
			throw new Exception( "Problem with list file!" );
		}

		// Setup book keeping data structures
		$svn_root_path = $lists->list["path"];
		$projects = array();
		$projects_hash = array();
		$current_project = NULL;

		foreach ($lists->list->entry as $entry) {

			// If entry is a project directory, make a new project based on available data, 
			// store it at the end of projects array and set it as current project.
			// Skip if entry is a sub-directory
			if ( strcmp($entry["kind"], "dir") == 0 ) {

				// Only create a new project if there are no forward slashes in the directory name
				if ( strpos($entry->name, "/") === false ) {
					$current_project = new Project( $entry->name, $entry->commit->date, $entry->commit["revision"] );
					$projects_hash[$current_project->title] = $current_project;
				}
				continue;
			}

			// Else if entry is a file, determine its type and store it in corresponding project
			else if ( strcmp($entry["kind"], "file" ) == 0 ) {

				// Set file type to 'other' unless the extension is defined in our type lookup table
				$file_type = "other";

				$matches = array();
				if ( preg_match_all($FILE_EXTENSION_REGEXP, $entry->name, $matches, PREG_PATTERN_ORDER) === 1 ) {

					// The last match is guaranteed to be the extension
					$extension = $matches[0][ sizeof($matches[0]) - 1 ];

					// If the extension is in the lookup table, change the file type from 'other'
					if ( in_array($extension, array_keys($PROJECT_FILE_TYPES) ) ) {
						$file_type = $PROJECT_FILE_TYPES[$extension];
					}			

				}
				// Make a new Porject File and store in its project
				$project_file = new ProjectFile($file_type, $entry->name, $entry->size);
				$current_project->files[ $project_file->path ] = $project_file;
			}

		}

		// Return the associative array of all created Projects
		return $projects_hash;

	}

	/*
		Updates the given projects_hash with logs from the given svn log file.
		@param xml_file - the path to an xml file in svn log format
	*/ 
	/**
			Only called after parse_svn_list
	**/
	function parse_svn_log($xml_file, &$projects_hash) {

		if ( is_null($xml_file) || is_null($projects_hash) || 
				!is_array($projects_hash) || !file_exists($xml_file) ) {
			throw new Exception( "Illegal argument(s)!" );
		}

		try {
			$log = simplexml_load_file($xml_file);
		}
		catch (Exception $e) {
			throw new Exception( "Problem with log file!" );
		}

		// Regexp to seperate the parts of a file path by slashes
		$PATH_COMPONENTS_REGEXP = "/[\w\d\.]+/";

		foreach ($log->logentry as $logentry) {

			// Make a new FileVersion object which can be used for more than one file
			$file_version = new FileVersion($logentry["revision"], $logentry->author, 
											$logentry->msg, $logentry->date);

			// Loop through all paths, and add the FileVersion if existing file is involved
			foreach ($logentry->paths->path as $path) {

				$matches = array();

				// Store the slash separated elements in $matches
				preg_match_all($PATH_COMPONENTS_REGEXP, $path, $matches, PREG_PATTERN_ORDER);

				// Make a new array without the first matched non-slash element, which is the NetID (reziapo1)
				$slice = array_slice($matches[0], 1);

				// Join all the strings of sliced array to form the file path
				$file_path = implode("/", $slice);

				// Get the project title which is always next after NetID
				$project = $projects_hash[ $matches[0][1] ];

				// If file is in latest project revision, add this Version to its versions list
				if ( !is_null($project->files) && array_key_exists($file_path, $project->files) ) {
					array_push( $project->files[$file_path]->versions, $file_version );
				}

			}

			// If this version is the latest project version, update the project's summary
			if ($file_version->version_number == $project->version) {
				$project->summary = $file_version->message;
			}

		}

	}


?>