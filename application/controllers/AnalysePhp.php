<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AnalysePhp extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{

		if ($this->input->get("directory") == "") {
			$this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(array("error" => "Choose a folder to analyse...")));
			return ;
		}

		if (!is_dir($this->input->get("directory"))) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array("error" => "Directory does not exists...")));
			return ;
		}


		$result_computed = array();

		/*
		 * Find PHP files
		 */
		$files = array();
        $ite=new RecursiveDirectoryIterator($this->input->get("directory"));

        foreach (new RecursiveIteratorIterator($ite) as $filename=>$cur) {
            $path_parts = pathinfo($filename);
            if ($path_parts['extension'] == "php") {
                $files[] = $filename;
            }
        }

        /*
         * Analysing files
         */
        foreach ($files as $key => $file) {
            $file_lines = file($file);
            $num_line = 1;
            foreach ($file_lines as $key2 => $line) {
               $analysis_result = analyse_line($file, $line, $num_line);
               if (sizeof($analysis_result) > 0) {
                   foreach ($analysis_result as $key3 => $res) {
                       $result_computed[] = $res;
                   }
               }
               $num_line++;
            }

        }

    	 $this->output
		 ->set_content_type('application/json')
		 ->set_output(json_encode($result_computed));
	}

}


function analyse_line($file, $line, $num_line) {
    $result_computed = array();
    $has_comment = false;
    if (strpos($line, "//") > -1) {
        $has_comment = strpos($line, "//") + 1;
    }

    preg_match("/.*POST\[(.*)\].*/", $line, $matches_POST);
    if (sizeof($matches_POST) > 0 ) {
        array_push($result_computed, array(
            "file" => $file,
            "type" => "POST",
            "parameter" => $matches_POST[1],
            "line" => $line,
            "num_line" => $num_line,
            "critical" => is_critical($line),
            "isVariable" => is_variable($matches_POST[1])
        ));
    }

    preg_match("/.*GET\[(.*)\].*/", $line, $matches_GET);
    if (sizeof($matches_GET) > 0 ) {
        array_push($result_computed, array(
            "file" => $file,
            "type" => "POST",
            "parameter" => $matches_GET[1],
            "line" => $line,
            "num_line" => $num_line,
            "critical" => is_critical($line),
            "isVariable" => is_variable($matches_GET[1])
        ));
    }
    return $result_computed;
}


function is_critical($line) {
		if (strpos($line, 'htmlspecialchars') || strpos($line, 'strip_tags') > -1) {
				return false;
		}
		return true;
}

function is_variable($param) {
		if (substr($param, 0, 1) == "$") {
				return true;
		}
		return false;
}
