<?php 
class File_upload {

    var $file;
	var $the_file;
	var $the_temp_file;
	var $the_file_type;
	var $the_file_size;
	var $http_error;
    var $upload_dir;
	var $replace;
	var $do_filename_check;
	var $max_length_filename = 100;
	var $max_file_size;
    var $extensions;
	var $ext_string;
	var $language;
	var $rename_file; // if this var is true the file copy get a new name
	var $file_copy; // the new name
	var $message = array();
	var $allowed_types;
	
	function File_upload(){
		$this->language = "en"; // choice of en, nl, es
		$this->rename_file = false;
		$this->ext_string = "";
		$this->max_file_size = 2048;//2 Mb by default
		$this->allowed_types = array("application/rar",
                               "application/x-rar-compressed",
                               "application/arj",
                               "application/excel",
                               "application/gnutar",
                               "application/octet-stream",
                               "application/pdf",
                               "application/powerpoint",
                               "application/postscript",
                               "application/plain",
                               "application/rtf",
                               "application/vocaltec-media-file",
                               "application/wordperfect",
                               "application/x-bzip",
                               "application/x-bzip2",
                               "application/x-compressed",
                               "application/x-excel",
                               "application/x-gzip",
                               "application/x-latex",
                               "application/x-midi",
                               "application/x-msexcel",
                               "application/x-rtf",
                               "application/x-sit",
                               "application/x-stuffit",
                               "application/x-shockwave-flash",
                               "application/x-troff-msvideo",
                               "application/x-zip-compressed",
                               "application/xml",
                               "application/zip",
                               "application/msword",
                               "application/mspowerpoint",
                               "application/vnd.ms-excel",
                               "application/vnd.ms-powerpoint",
                               "application/vnd.ms-word",
                               "application/vnd.ms-word.document.macroEnabled.12",
                               "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                               "application/vnd.ms-word.template.macroEnabled.12",
                               "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
                               "application/vnd.ms-powerpoint.template.macroEnabled.12",
                               "application/vnd.openxmlformats-officedocument.presentationml.template",
                               "application/vnd.ms-powerpoint.addin.macroEnabled.12",
                               "application/vnd.ms-powerpoint.slideshow.macroEnabled.12",
                               "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
                               "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
                               "application/vnd.openxmlformats-officedocument.presentationml.presentation",
                               "application/vnd.ms-excel.addin.macroEnabled.12",
                               "application/vnd.ms-excel.sheet.binary.macroEnabled.12",
                               "application/vnd.ms-excel.sheet.macroEnabled.12",
                               "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                               "application/vnd.ms-excel.template.macroEnabled.12",
                               "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
                               "audio/*",
                               "image/*",
                               "video/*",
                               "multipart/x-zip",
                               "multipart/x-gzip",
                               "text/richtext",
                               "text/plain",
                               "text/xml");
	}
	
	function show_error_string() {
		$msg_string = "";
		foreach ($this->message as $value){
			$msg_string .= $value."<br />\n";
		}
		return $msg_string;
	}
	
	function set_file_name($new_name = ""){ // this "conversion" is used for unique/new filenames 
		if ($this->rename_file) {
			if ($this->the_file == "") return;
			$name = ($new_name == "") ? strtotime("now") : $new_name;
			sleep(3);
			$name = $name.$this->get_extension($this->the_file);
		} else {
			$name = str_replace(" ", "_", $this->the_file); // space will result in problems on linux systems
		}
		return $name;
	}
	
	function upload($to_name = ""){
		$this->the_temp_file = $this->file['tmp_name'];
		$this->the_file = $this->file['name'];
		$this->the_file_type = $this->file['type'];
		$this->the_file_size = $this->file['size'];
		$this->http_error = $this->file['error'];
		//try to upload the file
		$new_name = $this->set_file_name($to_name);
		if ($this->check_file_name($new_name)) {
			if($this->validateSize()){
				//validate the extension
				if ($this->validateExtension()) {
					if (is_uploaded_file($this->the_temp_file)) {
						$this->file_copy = $new_name;
						if ($this->move_upload($this->the_temp_file, $this->file_copy)) {
							$this->message[] = $this->error_text($this->http_error);
							if ($this->rename_file) $this->message[] = $this->error_text(16);
							return true;
						}
					}else{
						$this->message[] = $this->error_text($this->http_error);
						return false;
					}
				}else{
					$this->show_extensions();
					$this->message[] = $this->error_text(11);
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function move_upload($tmp_file, $new_file) {
		if ($this->existing_file($new_file)) {
			$newfile = $this->upload_dir.$new_file;
			if ($this->check_dir($this->upload_dir)) {
				if (move_uploaded_file($tmp_file, $newfile)) {
					umask(0);
					chmod($newfile , 0644);
					return true;
				} else {
					return false;
				}
			} else {
				$this->message[] = $this->error_text(14);
				return false;
			}
		} else {
			$this->message[] = $this->error_text(15);
			return false;
		}
	}
	
	function check_file_name($the_name) {
		if ($the_name != "") {
			if (strlen($the_name) > $this->max_length_filename) {
				$this->message[] = $this->error_text(13);
				return false;
			} else {
				if ($this->do_filename_check == "y") {
					if (preg_match("/^[a-z0-9_]*\.(.){1,5}$/i", $the_name)) {
						return true;
					} else {
						$this->message[] = $this->error_text(12);
						return false;
					}
				} else {
					return true;
				}
			}
		} else {
			$this->message[] = $this->error_text(10);
			return false;
		}
	}
	
	//this method is only used for detailed error reporting
	
	function show_extensions() {
		$this->ext_string = implode(" ", $this->extensions);
	}
	
	function get_extension($from_file) {
		$ext = strtolower(strrchr($from_file,"."));
		return $ext;
	}
	
	function validateExtension() {
		$extension = $this->get_extension($this->the_file);
		$ext_array = $this->extensions;
		if (in_array($extension, $ext_array)) { 
			//validate mime type
			if($this->validateMimeType()){
				return true;
			}else{
				return false;
			}
		} else {
			return false;
		}
	}
	
	function validateMimeType() {
		$type = $this->the_file_type;
		$type_array = $this->allowed_types;
		if(!empty($type) && strpos($type, '/') !== false)
		{
			list($m1, $m2) = explode('/', $type);
			$allowed = false;
			// check wether the mime type is allowed
            foreach($type_array as $k => $v) {
                list($v1, $v2) = explode('/', $v);
                if (($v1 == '*' && $v2 == '*') || ($v1 == $m1 && ($v2 == $m2 || $v2 == '*'))) {
                    $allowed = true;
                    break;
                }
            }
			//
			if ($allowed) {
                return true;
            } else {
            	$this->message[] = $this->error_text(18);
                return false;
            }
		}else{
			$this->message[] = $this->error_text(19);
			return false;
		}
	}
	
	function validateSize(){
		$max_size = $this->max_file_size;
		$actual_size = $this->the_file_size;
		
		if($actual_size > $max_size)
		{
			$this->message[] = $this->error_text(2);
			return false;
		}else{
			return true;
		}
	}
	
	function check_dir($directory) {
		if (!is_dir($directory)) {
			if ($this->create_directory) {
				umask(0);
				mkdir($directory, 0777);
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	function existing_file($file_name) {
		if ($this->replace == "y") {
			return true;
		} else {
			if (file_exists($this->upload_dir.$file_name)) {
				return false;
			} else {
				return true;
			}
		}
	}
	
	function get_uploaded_file_info($file) {
		$str = "File name: ".basename($file)."\n";
		$str .= "File size: ".filesize($file)." bytes\n";
		
		if (function_exists("mime_content_type")) {
			$str .= "Mime type: ".mime_content_type($file)."\n";
		}
		if ($img_dim = getimagesize($file)) {
			$str .= "Image dimensions: x = ".$img_dim[0]."px, y = ".$img_dim[1]."px\n";
		}
		return $str;
	}
	
	function get_uploaded_name($file){
		$base_name = basename($file);
		list($name, $ext) = explode(".", $base_name);
		return $name;
	}
	
	function get_uploaded_mimetype(){
		return $this->the_file_type;
	}
	
	function get_uploaded_extension($file){
		$base_name = basename($file);
		list($name, $ext) = explode(".", $base_name);
		return $ext;
	}
	
	//delete the file
	
	function del_temp_file() {
		$file = $this->upload_dir.$this->file_copy;
		$delete = @unlink($file); 
		clearstatcache();
		if (@file_exists($file)) { 
			$filesys = eregi_replace("/","\\",$file); 
			$delete = @system("del $filesys");
			clearstatcache();
			if (@file_exists($file)) { 
				$delete = @chmod ($file, 0644); 
				$delete = @unlink($file); 
				$delete = @system("del $filesys");
			}
		}
	}
	
	// some error (HTTP)reporting, change the messages or remove options if you like.
	
	function error_text($err_num) {
		
		// start http errors
		$error[0] = "File: <b>".$this->the_file."</b> successfully uploaded!";
		$error[1] = "The uploaded file exceeds the max. upload filesize directive in the server configuration.";
		$error[2] = "The uploaded file exceeds the max file size.";
		$error[3] = "The uploaded file was only partially uploaded.";
		$error[4] = "No file was uploaded.";
		// end  http errors
		$error[10] = "Please select a file for upload.";
		$error[11] = "Only files with the following extensions are allowed: <b>".$this->ext_string."</b>";
		$error[12] = "Sorry, the filename contains invalid characters. Use only alphanumerical chars and separate parts of the name (if needed) with an underscore. <br>A valid filename ends with one dot followed by the extension.";
		$error[13] = "The filename exceeds the maximum length of ".$this->max_length_filename." characters.";
		$error[14] = "Sorry, the upload directory doesn't exist!";
		$error[15] = "Uploading <b>".$this->the_file."...Error!</b> Sorry, a file with this name already exitst.";
		$error[16] = "The uploaded file is renamed to <b>".$this->file_copy."</b>.";
		$error[17] = "The file %s does not exist.";
		$error[18] = "Invalid type of file.";
		$error[19] = "MIME type can't be detected.";
			
		return $error[$err_num];
	}
}
?>