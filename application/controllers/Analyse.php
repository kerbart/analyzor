<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analyse extends CI_Controller {

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
	 	$result_grep = array();

		exec("grep -nr \"_POST\\[\" " . $this->input->get("directory"), $output_POST);
		exec("grep -nr \"_GET\\[\" " . $this->input->get("directory"), $output_GET);


	 	$result_grep = array_merge($output_POST, $output_GET);

	 	foreach($result_grep as $key => $value) {
			 preg_match("/(\/.*):(.*):.*/", $value, $matches);

			 $last_found_file = substr($value, 0, strpos($value, ":"));
			 $temp = str_replace($last_found_file . ":", "", $value);
			 $last_found_file_line = substr($temp, 0, strpos($temp, ":"));

			 if (sizeof($matches) > 0) {



					 preg_match("/.*POST\[(.*)\].*/", $value, $matches_POST);
					 if (sizeof($matches_POST) > 0) {
							 array_push($result_computed, array(
								 	"file" => $last_found_file,
									 "type" => "POST",
									 "parameter" => $matches_POST[1],
									 "line" => substr($value, strrpos($value, ':')),
									 "num_line" => $last_found_file_line,
									 "critical" => is_critical($value),
									 "isVariable" => is_variable($matches_POST[1])
							 ));
					 }

					 preg_match("/.*GET\[(.*)\].*/", $value, $matches_GET);
					 if (sizeof($matches_GET) > 0) {
							array_push($result_computed, array(
											"file" => $last_found_file,
											"type" => "GET",
											"parameter" => $matches_GET[1],
											"line" => substr($value, strrpos($value, ':')),
											"num_line" => $last_found_file_line,
											"critical" => is_critical($value),
											"isVariable" => is_variable($matches_GET[1]),
											"otherParam" => "something"
									));
					 }
			 } else {
					// maybe should handle the error
			 }


	 }
/*
	 $this->output
		 ->set_content_type('application/json')
		 ->set_output(json_encode($result_computed));
	}
	*/


	foreach ($result_computed as $key => $value) {
if ($value["critical"] ) {
			echo "<div style='border:1px solid black;margin:10px;padding:5px'>" .
			str_replace("/Users/damien/Sites/htdocs/", "", $value["file"]) . "<br />" .
			"ligne n° : " . $value["num_line"] . " : <i>" . $value["line"] . "</i><br />

			type du paramètre : " . $value["type"] . "<br />
			parametre : " . $value["parameter"] . "<br />
		
		</div>
		<hr />
		";
	}
	}
}

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
