<?php

//   -------------------------------------------------------------------------------
//  | net2ftp: a web based FTP client                                               |
//  | License GNU/GPL - David Gartner - July 2006                                   |
//   -------------------------------------------------------------------------------
//  | PhpConcept Library - Tar Module 1.3                                           |
//  | License GNU/GPL - Vincent Blavet - August 2001                                |
//   -------------------------------------------------------------------------------
//  | PhpConcept Library - Zip Module 2.5                                           |
//  | License GNU/LGPL - Vincent Blavet - March 2006                                |
//   -------------------------------------------------------------------------------
//  | This program is free software; you can redistribute it and/or                 |
//  | modify it under the terms of the GNU (Lesser) General Public License          |
//  | as published by the Free Software Foundation; either version 2                |
//  | of the License, or (at your option) any later version.                        |
//  |                                                                               |
//  | This program is distributed in the hope that it will be useful,               |
//  | but WITHOUT ANY WARRANTY; without even the implied warranty of                |
//  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                 |
//  | GNU General Public License for more details.                                  |
//  |                                                                               |
//  | You should have received a copy of the GNU General Public License             |
//  | along with this program; if not, write to the Free Software                   |
//  | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA     |
//   -------------------------------------------------------------------------------





// --------------------------------------------------------------------------------
// Set PHP parameters
// --------------------------------------------------------------------------------
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set("max_execution_time", "300");
ini_set("memory_limit", "64M");

// --------------------------------------------------------------------------------
// Check the security code
// --------------------------------------------------------------------------------
if ($_GET["security_code"] == "") { 
	echo "You didn't enter the security code at the end of the URL. For security reasons the net2ftp install script only executes when this code is entered.";
	exit();
}
elseif ($_GET["security_code"] != "ctz0ob5t0y5pzn1bo0wh" || $_GET["security_code"] == ("NET2FTP_" . "SECURITY_CODE")) { 
	echo "Incorrect security code. For security reasons the net2ftp install script only executes when the correct code is entered.";
	exit();
}





// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **

function validateDirectory($directory) {

// --------------
// Remove * ? < > |
// --------------

	$directory = preg_replace("/[\\*\\?\\<\\>\\|]/", "", $directory);

	return $directory;

} // end validateDirectory

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************





// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **

function validateGenericInput($input) {

// --------------
// Remove the following characters <>
// --------------

	$input = preg_replace("/\\<\\>]/", "", $input);
	return $input;

} // end validateGenericInput

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************





// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **

function get_filename_extension($filename) {

// --------------
// This function returns the extension of a filename:
// 	name.ext1.ext2.ext3 --> ext3
// 	name --> name
// 	.name --> name
//	.name.ext --> ext
// It also converts the result to lower case:
// 	name.ext1.EXT2 --> ext2
// --------------

	$lastdotposition = strrpos($filename,".");

	if ($lastdotposition === 0)      { $extension = substr($filename, 1); }
	elseif ($lastdotposition == "")  { $extension = $filename; }
	else                             { $extension = substr($filename, $lastdotposition + 1); }

	return strtolower($extension);

} // End get_filename_extension

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************





// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **

function delete_dirorfile($dirorfile, $mode) {

// --------------
// This function deletes a local directory recursively
// Credit goes to itportal at gmail dot com, 17-Jul-2006 05:29
// --------------

	if ($mode != "execute") { $mode = "simulate"; }

	if (is_dir($dirorfile)) { 
		$directory = $dirorfile;
		if(substr($dir, -1, 1) == "/"){
			$directory = substr($directory, 0, strlen($directory) - 1);
		}
		if ($handle = opendir("$directory")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$directory/$item")) { 
						if ($mode == "execute") { echo "Processing directory $directory/$item<br />\n"; }
						delete_dirorfile("$directory/$item", $mode); 
					} else { 
						if ($mode == "execute") { 
							unlink("$directory/$item"); 
							echo "Removed file $directory/$item<br />\n";
						}
						elseif ($mode == "simulate") {
							echo "File $directory/$item<br />\n";
						}
					}
				}
			}
			closedir($handle);
			if ($mode == "execute") { 
				rmdir($directory);
				echo "Removed directory $directory<br />\n";
			}
			elseif ($mode == "simulate") {
				echo "Directory $directory<br />\n";
			}
		}
	}
	elseif (is_file($dirorfile)) {
		$file = $dirorfile;
		if ($mode == "execute") { 
			unlink($file); 
			echo "Removed file $file<br />\n";
		}
		elseif ($mode == "simulate") {
			echo "File $file<br />\n";
		}
	}
	else {
		if ($mode == "execute") { 
			echo "Could not remove $dirorfile<br />\n.";
		}
		elseif ($mode == "simulate") {
			echo "Entry $dirorfile can't be removed.<br />\n";
		}
	}

} // End delete_dirorfile

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************





// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **

function ftpAsciiBinary($filename) {

// --------------
// Checks the first character of a file and its extension to see if it should be 
// transferred in ASCII or Binary mode
// --------------

	$firstcharacter = substr($filename, 0, 1);

	if ($firstcharacter == ".") { 
		$ftpmode = FTP_ASCII; 
		return $ftpmode;
	}

	$last = get_filename_extension($filename);

	if (
		$last == "1st"  		||
		$last == "asp"  		||
		$last == "bas"  		||
		$last == "bat"  		||
		$last == "c"  		||
		$last == "cfg"  		||
		$last == "cfm"  		||
		$last == "cgi"  		||
		$last == "conf"  		||
		$last == "cpp"  		||
		$last == "css"  		||
		$last == "csv"  		||
		$last == "dhtml"		||
		$last == "diz"		||
		$last == "default"	||
		$last == "file"  		||
		$last == "h"  		||
		$last == "hpp"  		||
		$last == "htaccess"	||
		$last == "htpasswd"	||
		$last == "htm"  		||
		$last == "html"  		||
		$last == "inc"  		||
		$last == "ini"  		||
		$last == "js"  		||
		$last == "jsp"  		||
		$last == "log"  		||
		$last == "m3u" 		||
		$last == "mak" 		||
		$last == "msg" 		||
		$last == "nfo" 		||
		$last == "old" 		||
		$last == "pas" 		||
		$last == "patch" 		||
		$last == "perl" 		||
		$last == "php" 		||
		$last == "php3" 		||
		$last == "phps" 		||
		$last == "phtml" 		||
		$last == "pinerc"		||
		$last == "pl" 		||
		$last == "pm" 		||
		$last == "qmail" 		||
		$last == "readme"		||
		$last == "setup" 		||
		$last == "seq" 		||
		$last == "sh" 		|| 
		$last == "sql" 		|| 
		$last == "style" 		|| 
		$last == "tcl" 		|| 
		$last == "tex"		|| 
		$last == "threads"	|| 
		$last == "tmpl"  		||
		$last == "tpl"  		|| 
		$last == "txt"  		|| 
		$last == "ubb"  		||
		$last == "vbs"  		|| 
		$last == "xml"  		||
		strstr($last, "htm")
							)	{ $ftpmode = FTP_ASCII; }
	else 							{ $ftpmode = FTP_BINARY; }

	return $ftpmode;

} // end ftpAsciiBinary

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************






















// --------------------------------------------------------------------------------
// PhpConcept Library - Zip Module 2.5
// --------------------------------------------------------------------------------
// License GNU/LGPL - Vincent Blavet - March 2006
// http://www.phpconcept.net
// --------------------------------------------------------------------------------
//
// Presentation :
//   PclZip is a PHP library that manage ZIP archives.
//   So far tests show that archives generated by PclZip are readable by
//   WinZip application and other tools.
//
// Description :
//   See readme.txt and http://www.phpconcept.net
//
// Warning :
//   This library and the associated files are non commercial, non professional
//   work.
//   It should not have unexpected results. However if any damage is caused by
//   this software the author can not be responsible.
//   The use of this software is at the risk of the user.
//
// --------------------------------------------------------------------------------
// $Id: pclzip.lib.php,v 1.44 2006/03/08 21:23:59 vblavet Exp $
// --------------------------------------------------------------------------------

  // ----- Constants
  define( 'PCLZIP_READ_BLOCK_SIZE', 2048 );
  
  // ----- File list separator
  // In version 1.x of PclZip, the separator for file list is a space
  // (which is not a very smart choice, specifically for windows paths !).
  // A better separator should be a comma (,). This constant gives you the
  // abilty to change that.
  // However notice that changing this value, may have impact on existing
  // scripts, using space separated filenames.
  // Recommanded values for compatibility with older versions :
  //define( 'PCLZIP_SEPARATOR', ' ' );
  // Recommanded values for smart separation of filenames.
  define( 'PCLZIP_SEPARATOR', ',' );

  // ----- Error configuration
  // 0 : PclZip Class integrated error handling
  // 1 : PclError external library error handling. By enabling this
  //     you must ensure that you have included PclError library.
  // [2,...] : reserved for futur use
  define( 'PCLZIP_ERROR_EXTERNAL', 0 );

  // ----- Optional static temporary directory
  //       By default temporary files are generated in the script current
  //       path.
  //       If defined :
  //       - MUST BE terminated by a '/'.
  //       - MUST be a valid, already created directory
  //       Samples :
  // define( 'PCLZIP_TEMPORARY_DIR', '/temp/' );
  // define( 'PCLZIP_TEMPORARY_DIR', 'C:/Temp/' );
  define( 'PCLZIP_TEMPORARY_DIR', '' );

// --------------------------------------------------------------------------------
// ***** UNDER THIS LINE NOTHING NEEDS TO BE MODIFIED *****
// --------------------------------------------------------------------------------

  // ----- Global variables
  $g_pclzip_version = "2.5";

  // ----- Error codes
  //   -1 : Unable to open file in binary write mode
  //   -2 : Unable to open file in binary read mode
  //   -3 : Invalid parameters
  //   -4 : File does not exist
  //   -5 : Filename is too long (max. 255)
  //   -6 : Not a valid zip file
  //   -7 : Invalid extracted file size
  //   -8 : Unable to create directory
  //   -9 : Invalid archive extension
  //  -10 : Invalid archive format
  //  -11 : Unable to delete file (unlink)
  //  -12 : Unable to rename file (rename)
  //  -13 : Invalid header checksum
  //  -14 : Invalid archive size
  define( 'PCLZIP_ERR_USER_ABORTED', 2 );
  define( 'PCLZIP_ERR_NO_ERROR', 0 );
  define( 'PCLZIP_ERR_WRITE_OPEN_FAIL', -1 );
  define( 'PCLZIP_ERR_READ_OPEN_FAIL', -2 );
  define( 'PCLZIP_ERR_INVALID_PARAMETER', -3 );
  define( 'PCLZIP_ERR_MISSING_FILE', -4 );
  define( 'PCLZIP_ERR_FILENAME_TOO_LONG', -5 );
  define( 'PCLZIP_ERR_INVALID_ZIP', -6 );
  define( 'PCLZIP_ERR_BAD_EXTRACTED_FILE', -7 );
  define( 'PCLZIP_ERR_DIR_CREATE_FAIL', -8 );
  define( 'PCLZIP_ERR_BAD_EXTENSION', -9 );
  define( 'PCLZIP_ERR_BAD_FORMAT', -10 );
  define( 'PCLZIP_ERR_DELETE_FILE_FAIL', -11 );
  define( 'PCLZIP_ERR_RENAME_FILE_FAIL', -12 );
  define( 'PCLZIP_ERR_BAD_CHECKSUM', -13 );
  define( 'PCLZIP_ERR_INVALID_ARCHIVE_ZIP', -14 );
  define( 'PCLZIP_ERR_MISSING_OPTION_VALUE', -15 );
  define( 'PCLZIP_ERR_INVALID_OPTION_VALUE', -16 );
  define( 'PCLZIP_ERR_ALREADY_A_DIRECTORY', -17 );
  define( 'PCLZIP_ERR_UNSUPPORTED_COMPRESSION', -18 );
  define( 'PCLZIP_ERR_UNSUPPORTED_ENCRYPTION', -19 );
  define( 'PCLZIP_ERR_INVALID_ATTRIBUTE_VALUE', -20 );
  define( 'PCLZIP_ERR_DIRECTORY_RESTRICTION', -21 );

  // ----- Options values
  define( 'PCLZIP_OPT_PATH', 77001 );
  define( 'PCLZIP_OPT_ADD_PATH', 77002 );
  define( 'PCLZIP_OPT_REMOVE_PATH', 77003 );
  define( 'PCLZIP_OPT_REMOVE_ALL_PATH', 77004 );
  define( 'PCLZIP_OPT_SET_CHMOD', 77005 );
  define( 'PCLZIP_OPT_EXTRACT_AS_STRING', 77006 );
  define( 'PCLZIP_OPT_NO_COMPRESSION', 77007 );
  define( 'PCLZIP_OPT_BY_NAME', 77008 );
  define( 'PCLZIP_OPT_BY_INDEX', 77009 );
  define( 'PCLZIP_OPT_BY_EREG', 77010 );
  define( 'PCLZIP_OPT_BY_PREG', 77011 );
  define( 'PCLZIP_OPT_COMMENT', 77012 );
  define( 'PCLZIP_OPT_ADD_COMMENT', 77013 );
  define( 'PCLZIP_OPT_PREPEND_COMMENT', 77014 );
  define( 'PCLZIP_OPT_EXTRACT_IN_OUTPUT', 77015 );
  define( 'PCLZIP_OPT_REPLACE_NEWER', 77016 );
  define( 'PCLZIP_OPT_STOP_ON_ERROR', 77017 );
  // Having big trouble with crypt. Need to multiply 2 long int
  // which is not correctly supported by PHP ...
  //define( 'PCLZIP_OPT_CRYPT', 77018 );
  define( 'PCLZIP_OPT_EXTRACT_DIR_RESTRICTION', 77019 );
  
  // ----- File description attributes
  define( 'PCLZIP_ATT_FILE_NAME', 79001 );
  define( 'PCLZIP_ATT_FILE_NEW_SHORT_NAME', 79002 );
  define( 'PCLZIP_ATT_FILE_NEW_FULL_NAME', 79003 );

  // ----- Call backs values
  define( 'PCLZIP_CB_PRE_EXTRACT', 78001 );
  define( 'PCLZIP_CB_POST_EXTRACT', 78002 );
  define( 'PCLZIP_CB_PRE_ADD', 78003 );
  define( 'PCLZIP_CB_POST_ADD', 78004 );
  /* For futur use
  define( 'PCLZIP_CB_PRE_LIST', 78005 );
  define( 'PCLZIP_CB_POST_LIST', 78006 );
  define( 'PCLZIP_CB_PRE_DELETE', 78007 );
  define( 'PCLZIP_CB_POST_DELETE', 78008 );
  */

  // --------------------------------------------------------------------------------
  // Class : PclZip
  // Description :
  //   PclZip is the class that represent a Zip archive.
  //   The public methods allow the manipulation of the archive.
  // Attributes :
  //   Attributes must not be accessed directly.
  // Methods :
  //   PclZip() : Object creator
  //   create() : Creates the Zip archive
  //   listContent() : List the content of the Zip archive
  //   extract() : Extract the content of the archive
  //   properties() : List the properties of the archive
  // --------------------------------------------------------------------------------
  class PclZip
  {
    // ----- Filename of the zip file
    var $zipname = '';

    // ----- File descriptor of the zip file
    var $zip_fd = 0;

    // ----- Internal error handling
    var $error_code = 1;
    var $error_string = '';
    
    // ----- Current status of the magic_quotes_runtime
    // This value store the php configuration for magic_quotes
    // The class can then disable the magic_quotes and reset it after
    var $magic_quotes_status;

  // --------------------------------------------------------------------------------
  // Function : PclZip()
  // Description :
  //   Creates a PclZip object and set the name of the associated Zip archive
  //   filename.
  //   Note that no real action is taken, if the archive does not exist it is not
  //   created. Use create() for that.
  // --------------------------------------------------------------------------------
  function PclZip($p_zipname)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclZip::PclZip', "zipname=$p_zipname");

    // ----- Tests the zlib
    if (!function_exists('gzopen'))
    {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 1, "zlib extension seems to be missing");
      die('Abort '.basename(__FILE__).' : Missing zlib extensions');
    }

    // ----- Set the attributes
    $this->zipname = $p_zipname;
    $this->zip_fd = 0;
    $this->magic_quotes_status = -1;

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 1);
    return;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : listContent()
  // Description :
  //   This public method, gives the list of the files and directories, with their
  //   properties.
  //   The properties of each entries in the list are (used also in other functions) :
  //     filename : Name of the file. For a create or add action it is the filename
  //                given by the user. For an extract function it is the filename
  //                of the extracted file.
  //     stored_filename : Name of the file / directory stored in the archive.
  //     size : Size of the stored file.
  //     compressed_size : Size of the file's data compressed in the archive
  //                       (without the headers overhead)
  //     mtime : Last known modification date of the file (UNIX timestamp)
  //     comment : Comment associated with the file
  //     folder : true | false
  //     index : index of the file in the archive
  //     status : status of the action (depending of the action) :
  //              Values are :
  //                ok : OK !
  //                filtered : the file / dir is not extracted (filtered by user)
  //                already_a_directory : the file can not be extracted because a
  //                                      directory with the same name already exists
  //                write_protected : the file can not be extracted because a file
  //                                  with the same name already exists and is
  //                                  write protected
  //                newer_exist : the file was not extracted because a newer file exists
  //                path_creation_fail : the file is not extracted because the folder
  //                                     does not exists and can not be created
  //                write_error : the file was not extracted because there was a
  //                              error while writing the file
  //                read_error : the file was not extracted because there was a error
  //                             while reading the file
  //                invalid_header : the file was not extracted because of an archive
  //                                 format error (bad file header)
  //   Note that each time a method can continue operating when there
  //   is an action error on a file, the error is only logged in the file status.
  // Return Values :
  //   0 on an unrecoverable failure,
  //   The list of the files in the archive.
  // --------------------------------------------------------------------------------
  function listContent()
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclZip::listContent', "");
    $v_result=1;

    // ----- Reset the error handler
    $this->privErrorReset();

    // ----- Check archive
    if (!$this->privCheckFormat()) {
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 0);
      return(0);
    }

    // ----- Call the extracting fct
    $p_list = array();
    if (($v_result = $this->privList($p_list)) != 1)
    {
      unset($p_list);
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 0, PclZip::errorInfo());
      return(0);
    }

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $p_list);
    return $p_list;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function :
  //   extract($p_path="./", $p_remove_path="")
  //   extract([$p_option, $p_option_value, ...])
  // Description :
  //   This method supports two synopsis. The first one is historical.
  //   This method extract all the files / directories from the archive to the
  //   folder indicated in $p_path.
  //   If you want to ignore the 'root' part of path of the memorized files
  //   you can indicate this in the optional $p_remove_path parameter.
  //   By default, if a newer file with the same name already exists, the
  //   file is not extracted.
  //
  //   If both PCLZIP_OPT_PATH and PCLZIP_OPT_ADD_PATH aoptions
  //   are used, the path indicated in PCLZIP_OPT_ADD_PATH is append
  //   at the end of the path value of PCLZIP_OPT_PATH.
  // Parameters :
  //   $p_path : Path where the files and directories are to be extracted
  //   $p_remove_path : First part ('root' part) of the memorized path
  //                    (if any similar) to remove while extracting.
  // Options :
  //   PCLZIP_OPT_PATH :
  //   PCLZIP_OPT_ADD_PATH :
  //   PCLZIP_OPT_REMOVE_PATH :
  //   PCLZIP_OPT_REMOVE_ALL_PATH :
  //   PCLZIP_CB_PRE_EXTRACT :
  //   PCLZIP_CB_POST_EXTRACT :
  // Return Values :
  //   0 or a negative value on failure,
  //   The list of the extracted files, with a status of the action.
  //   (see PclZip::listContent() for list entry format)
  // --------------------------------------------------------------------------------
  function extract()
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::extract", "");
    $v_result=1;

    // ----- Reset the error handler
    $this->privErrorReset();

    // ----- Check archive
    if (!$this->privCheckFormat()) {
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 0);
      return(0);
    }

    // ----- Set default values
    $v_options = array();
//    $v_path = "./";
    $v_path = '';
    $v_remove_path = "";
    $v_remove_all_path = false;

    // ----- Look for variable options arguments
    $v_size = func_num_args();
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "$v_size arguments passed to the method");

    // ----- Default values for option
    $v_options[PCLZIP_OPT_EXTRACT_AS_STRING] = FALSE;

    // ----- Look for arguments
    if ($v_size > 0) {
      // ----- Get the arguments
      $v_arg_list = func_get_args();

      // ----- Look for first arg
      if ((is_integer($v_arg_list[0])) && ($v_arg_list[0] > 77000)) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Variable list of options");

        // ----- Parse the options
        $v_result = $this->privParseOptions($v_arg_list, $v_size, $v_options,
                                            array (PCLZIP_OPT_PATH => 'optional',
                                                   PCLZIP_OPT_REMOVE_PATH => 'optional',
                                                   PCLZIP_OPT_REMOVE_ALL_PATH => 'optional',
                                                   PCLZIP_OPT_ADD_PATH => 'optional',
                                                   PCLZIP_CB_PRE_EXTRACT => 'optional',
                                                   PCLZIP_CB_POST_EXTRACT => 'optional',
                                                   PCLZIP_OPT_SET_CHMOD => 'optional',
                                                   PCLZIP_OPT_BY_NAME => 'optional',
                                                   PCLZIP_OPT_BY_EREG => 'optional',
                                                   PCLZIP_OPT_BY_PREG => 'optional',
                                                   PCLZIP_OPT_BY_INDEX => 'optional',
                                                   PCLZIP_OPT_EXTRACT_AS_STRING => 'optional',
                                                   PCLZIP_OPT_EXTRACT_IN_OUTPUT => 'optional',
                                                   PCLZIP_OPT_REPLACE_NEWER => 'optional'
                                                   ,PCLZIP_OPT_STOP_ON_ERROR => 'optional'
                                                   ,PCLZIP_OPT_EXTRACT_DIR_RESTRICTION => 'optional'
												    ));
        if ($v_result != 1) {
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 0);
          return 0;
        }
        // ----- Set the arguments
        if (isset($v_options[PCLZIP_OPT_PATH])) {
          $v_path = $v_options[PCLZIP_OPT_PATH];
        }
        if (isset($v_options[PCLZIP_OPT_REMOVE_PATH])) {
          $v_remove_path = $v_options[PCLZIP_OPT_REMOVE_PATH];
        }
        if (isset($v_options[PCLZIP_OPT_REMOVE_ALL_PATH])) {
          $v_remove_all_path = $v_options[PCLZIP_OPT_REMOVE_ALL_PATH];
        }
        if (isset($v_options[PCLZIP_OPT_ADD_PATH])) {
          // ----- Check for '/' in last path char
          if ((strlen($v_path) > 0) && (substr($v_path, -1) != '/')) {
            $v_path .= '/';
          }
          $v_path .= $v_options[PCLZIP_OPT_ADD_PATH];
        }
      }

      // ----- Look for 2 args
      // Here we need to support the first historic synopsis of the
      // method.
      else {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Static synopsis");

        // ----- Get the first argument
        $v_path = $v_arg_list[0];

        // ----- Look for the optional second argument
        if ($v_size == 2) {
          $v_remove_path = $v_arg_list[1];
        }
        else if ($v_size > 2) {
          // ----- Error log
          PclZip::privErrorLog(PCLZIP_ERR_INVALID_PARAMETER, "Invalid number / type of arguments");

          // ----- Return
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 0, PclZip::errorInfo());
          return 0;
        }
      }
    }

    // ----- Trace
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "path='$v_path', remove_path='$v_remove_path', remove_all_path='".($v_remove_path?'true':'false')."'");

    // ----- Call the extracting fct
    $p_list = array();
    $v_result = $this->privExtractByRule($p_list, $v_path, $v_remove_path,
	                                     $v_remove_all_path, $v_options);
    if ($v_result < 1) {
      unset($p_list);
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 0, PclZip::errorInfo());
      return(0);
    }

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $p_list);
    return $p_list;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : properties()
  // Description :
  //   This method gives the properties of the archive.
  //   The properties are :
  //     nb : Number of files in the archive
  //     comment : Comment associated with the archive file
  //     status : not_exist, ok
  // Parameters :
  //   None
  // Return Values :
  //   0 on failure,
  //   An array with the archive properties.
  // --------------------------------------------------------------------------------
  function properties()
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::properties", "");

    // ----- Reset the error handler
    $this->privErrorReset();

    // ----- Magic quotes trick
    $this->privDisableMagicQuotes();

    // ----- Check archive
    if (!$this->privCheckFormat()) {
      $this->privSwapBackMagicQuotes();
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 0);
      return(0);
    }

    // ----- Default properties
    $v_prop = array();
    $v_prop['comment'] = '';
    $v_prop['nb'] = 0;
    $v_prop['status'] = 'not_exist';

    // ----- Look if file exists
    if (@is_file($this->zipname))
    {
      // ----- Open the zip file
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Open file in binary read mode");
      if (($this->zip_fd = @fopen($this->zipname, 'rb')) == 0)
      {
        $this->privSwapBackMagicQuotes();
        
        // ----- Error log
        PclZip::privErrorLog(PCLZIP_ERR_READ_OPEN_FAIL, 'Unable to open archive \''.$this->zipname.'\' in binary read mode');

        // ----- Return
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), 0);
        return 0;
      }

      // ----- Read the central directory informations
      $v_central_dir = array();
      if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
      {
        $this->privSwapBackMagicQuotes();
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, 0);
        return 0;
      }

      // ----- Close the zip file
      $this->privCloseFd();

      // ----- Set the user attributes
      $v_prop['comment'] = $v_central_dir['comment'];
      $v_prop['nb'] = $v_central_dir['entries'];
      $v_prop['status'] = 'ok';
    }

    // ----- Magic quotes trick
    $this->privSwapBackMagicQuotes();

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_prop);
    return $v_prop;
  }
  // --------------------------------------------------------------------------------


// --------------------------------------------------------------------------------
// ***** UNDER THIS LINE ARE DEFINED PRIVATE INTERNAL FUNCTIONS *****
// *****                                                        *****
// *****       THESES FUNCTIONS MUST NOT BE USED DIRECTLY       *****
// --------------------------------------------------------------------------------



  // --------------------------------------------------------------------------------
  // Function : privCheckFormat()
  // Description :
  //   This method check that the archive exists and is a valid zip archive.
  //   Several level of check exists. (futur)
  // Parameters :
  //   $p_level : Level of check. Default 0.
  //              0 : Check the first bytes (magic codes) (default value))
  //              1 : 0 + Check the central directory (futur)
  //              2 : 1 + Check each file header (futur)
  // Return Values :
  //   true on success,
  //   false on error, the error code is set.
  // --------------------------------------------------------------------------------
  function privCheckFormat($p_level=0)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privCheckFormat", "");
    $v_result = true;

	// ----- Reset the file system cache
    clearstatcache();

    // ----- Reset the error handler
    $this->privErrorReset();

    // ----- Look if the file exits
    if (!is_file($this->zipname)) {
      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_MISSING_FILE, "Missing archive file '".$this->zipname."'");
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, false, PclZip::errorInfo());
      return(false);
    }

    // ----- Check that the file is readeable
    if (!is_readable($this->zipname)) {
      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_READ_OPEN_FAIL, "Unable to read archive '".$this->zipname."'");
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, false, PclZip::errorInfo());
      return(false);
    }

    // ----- Check the magic code
    // TBC

    // ----- Check the central header
    // TBC

    // ----- Check each file header
    // TBC

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privParseOptions()
  // Description :
  //   This internal methods reads the variable list of arguments ($p_options_list,
  //   $p_size) and generate an array with the options and values ($v_result_list).
  //   $v_requested_options contains the options that can be present and those that
  //   must be present.
  //   $v_requested_options is an array, with the option value as key, and 'optional',
  //   or 'mandatory' as value.
  // Parameters :
  //   See above.
  // Return Values :
  //   1 on success.
  //   0 on failure.
  // --------------------------------------------------------------------------------
  function privParseOptions(&$p_options_list, $p_size, &$v_result_list, $v_requested_options=false)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privParseOptions", "");
    $v_result=1;
    
    // ----- Read the options
    $i=0;
    while ($i<$p_size) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Looking for table index $i, option = '".PclZipUtilOptionText($p_options_list[$i])."(".$p_options_list[$i].")'");

      // ----- Check if the option is supported
      if (!isset($v_requested_options[$p_options_list[$i]])) {
        // ----- Error log
        PclZip::privErrorLog(PCLZIP_ERR_INVALID_PARAMETER, "Invalid optional parameter '".$p_options_list[$i]."' for this method");

        // ----- Return
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
        return PclZip::errorCode();
      }

      // ----- Look for next option
      switch ($p_options_list[$i]) {
        // ----- Look for options that request a path value
        case PCLZIP_OPT_PATH :
        case PCLZIP_OPT_REMOVE_PATH :
        case PCLZIP_OPT_ADD_PATH :
          // ----- Check the number of parameters
          if (($i+1) >= $p_size) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Get the value
          $v_result_list[$p_options_list[$i]] = PclZipUtilTranslateWinPath($p_options_list[$i+1], false);
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "".PclZipUtilOptionText($p_options_list[$i])." = '".$v_result_list[$p_options_list[$i]]."'");
          $i++;
        break;

        case PCLZIP_OPT_EXTRACT_DIR_RESTRICTION :
          // ----- Check the number of parameters
          if (($i+1) >= $p_size) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Get the value
          if (   is_string($p_options_list[$i+1])
              && ($p_options_list[$i+1] != '')) {
            $v_result_list[$p_options_list[$i]] = PclZipUtilTranslateWinPath($p_options_list[$i+1], false);
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "".PclZipUtilOptionText($p_options_list[$i])." = '".$v_result_list[$p_options_list[$i]]."'");
            $i++;
          }
          else {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "".PclZipUtilOptionText($p_options_list[$i])." set with an empty value is ignored.");
          }
        break;

        // ----- Look for options that request an array of string for value
        case PCLZIP_OPT_BY_NAME :
          // ----- Check the number of parameters
          if (($i+1) >= $p_size) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Get the value
          if (is_string($p_options_list[$i+1])) {
              $v_result_list[$p_options_list[$i]][0] = $p_options_list[$i+1];
          }
          else if (is_array($p_options_list[$i+1])) {
              $v_result_list[$p_options_list[$i]] = $p_options_list[$i+1];
          }
          else {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_INVALID_OPTION_VALUE, "Wrong parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }
          ////--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "".PclZipUtilOptionText($p_options_list[$i])." = '".$v_result_list[$p_options_list[$i]]."'");
          $i++;
        break;

        // ----- Look for options that request an EREG or PREG expression
        case PCLZIP_OPT_BY_EREG :
        case PCLZIP_OPT_BY_PREG :
        //case PCLZIP_OPT_CRYPT :
          // ----- Check the number of parameters
          if (($i+1) >= $p_size) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Get the value
          if (is_string($p_options_list[$i+1])) {
              $v_result_list[$p_options_list[$i]] = $p_options_list[$i+1];
          }
          else {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_INVALID_OPTION_VALUE, "Wrong parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "".PclZipUtilOptionText($p_options_list[$i])." = '".$v_result_list[$p_options_list[$i]]."'");
          $i++;
        break;

        // ----- Look for options that takes a string
        case PCLZIP_OPT_COMMENT :
        case PCLZIP_OPT_ADD_COMMENT :
        case PCLZIP_OPT_PREPEND_COMMENT :
          // ----- Check the number of parameters
          if (($i+1) >= $p_size) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_MISSING_OPTION_VALUE,
			                     "Missing parameter value for option '"
								 .PclZipUtilOptionText($p_options_list[$i])
								 ."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Get the value
          if (is_string($p_options_list[$i+1])) {
              $v_result_list[$p_options_list[$i]] = $p_options_list[$i+1];
          }
          else {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_INVALID_OPTION_VALUE,
			                     "Wrong parameter value for option '"
								 .PclZipUtilOptionText($p_options_list[$i])
								 ."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "".PclZipUtilOptionText($p_options_list[$i])." = '".$v_result_list[$p_options_list[$i]]."'");
          $i++;
        break;

        // ----- Look for options that request an array of index
        case PCLZIP_OPT_BY_INDEX :
          // ----- Check the number of parameters
          if (($i+1) >= $p_size) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Get the value
          $v_work_list = array();
          if (is_string($p_options_list[$i+1])) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Index value is a string '".$p_options_list[$i+1]."'");

              // ----- Remove spaces
              $p_options_list[$i+1] = strtr($p_options_list[$i+1], ' ', '');

              // ----- Parse items
              $v_work_list = explode(",", $p_options_list[$i+1]);
          }
          else if (is_integer($p_options_list[$i+1])) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Index value is an integer '".$p_options_list[$i+1]."'");
              $v_work_list[0] = $p_options_list[$i+1].'-'.$p_options_list[$i+1];
          }
          else if (is_array($p_options_list[$i+1])) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Index value is an array");
              $v_work_list = $p_options_list[$i+1];
          }
          else {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_INVALID_OPTION_VALUE, "Value must be integer, string or array for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }
          
          // ----- Reduce the index list
          // each index item in the list must be a couple with a start and
          // an end value : [0,3], [5-5], [8-10], ...
          // ----- Check the format of each item
          $v_sort_flag=false;
          $v_sort_value=0;
          for ($j=0; $j<sizeof($v_work_list); $j++) {
              // ----- Explode the item
              $v_item_list = explode("-", $v_work_list[$j]);
              $v_size_item_list = sizeof($v_item_list);
              
              // ----- TBC : Here we might check that each item is a
              // real integer ...
              
              // ----- Look for single value
              if ($v_size_item_list == 1) {
                  // ----- Set the option value
                  $v_result_list[$p_options_list[$i]][$j]['start'] = $v_item_list[0];
                  $v_result_list[$p_options_list[$i]][$j]['end'] = $v_item_list[0];
              }
              elseif ($v_size_item_list == 2) {
                  // ----- Set the option value
                  $v_result_list[$p_options_list[$i]][$j]['start'] = $v_item_list[0];
                  $v_result_list[$p_options_list[$i]][$j]['end'] = $v_item_list[1];
              }
              else {
                  // ----- Error log
                  PclZip::privErrorLog(PCLZIP_ERR_INVALID_OPTION_VALUE, "Too many values in index range for option '".PclZipUtilOptionText($p_options_list[$i])."'");

                  // ----- Return
                  //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
                  return PclZip::errorCode();
              }

              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extracted index item = [".$v_result_list[$p_options_list[$i]][$j]['start'].",".$v_result_list[$p_options_list[$i]][$j]['end']."]");

              // ----- Look for list sort
              if ($v_result_list[$p_options_list[$i]][$j]['start'] < $v_sort_value) {
                  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "The list should be sorted ...");
                  $v_sort_flag=true;

                  // ----- TBC : An automatic sort should be writen ...
                  // ----- Error log
                  PclZip::privErrorLog(PCLZIP_ERR_INVALID_OPTION_VALUE, "Invalid order of index range for option '".PclZipUtilOptionText($p_options_list[$i])."'");

                  // ----- Return
                  //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
                  return PclZip::errorCode();
              }
              $v_sort_value = $v_result_list[$p_options_list[$i]][$j]['start'];
          }
          
          // ----- Sort the items
          if ($v_sort_flag) {
              // TBC : To Be Completed
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "List sorting is not yet write ...");
          }

          // ----- Next option
          $i++;
        break;

        // ----- Look for options that request no value
        case PCLZIP_OPT_REMOVE_ALL_PATH :
        case PCLZIP_OPT_EXTRACT_AS_STRING :
        case PCLZIP_OPT_NO_COMPRESSION :
        case PCLZIP_OPT_EXTRACT_IN_OUTPUT :
        case PCLZIP_OPT_REPLACE_NEWER :
        case PCLZIP_OPT_STOP_ON_ERROR :
          $v_result_list[$p_options_list[$i]] = true;
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "".PclZipUtilOptionText($p_options_list[$i])." = '".$v_result_list[$p_options_list[$i]]."'");
        break;

        // ----- Look for options that request an octal value
        case PCLZIP_OPT_SET_CHMOD :
          // ----- Check the number of parameters
          if (($i+1) >= $p_size) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Get the value
          $v_result_list[$p_options_list[$i]] = $p_options_list[$i+1];
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "".PclZipUtilOptionText($p_options_list[$i])." = '".$v_result_list[$p_options_list[$i]]."'");
          $i++;
        break;

        // ----- Look for options that request a call-back
        case PCLZIP_CB_PRE_EXTRACT :
        case PCLZIP_CB_POST_EXTRACT :
        case PCLZIP_CB_PRE_ADD :
        case PCLZIP_CB_POST_ADD :
        /* for futur use
        case PCLZIP_CB_PRE_DELETE :
        case PCLZIP_CB_POST_DELETE :
        case PCLZIP_CB_PRE_LIST :
        case PCLZIP_CB_POST_LIST :
        */
          // ----- Check the number of parameters
          if (($i+1) >= $p_size) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_MISSING_OPTION_VALUE, "Missing parameter value for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Get the value
          $v_function_name = $p_options_list[$i+1];
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "call-back ".PclZipUtilOptionText($p_options_list[$i])." = '".$v_function_name."'");

          // ----- Check that the value is a valid existing function
          if (!function_exists($v_function_name)) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_INVALID_OPTION_VALUE, "Function '".$v_function_name."()' is not an existing function for option '".PclZipUtilOptionText($p_options_list[$i])."'");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }

          // ----- Set the attribute
          $v_result_list[$p_options_list[$i]] = $v_function_name;
          $i++;
        break;

        default :
          // ----- Error log
          PclZip::privErrorLog(PCLZIP_ERR_INVALID_PARAMETER,
		                       "Unknown parameter '"
							   .$p_options_list[$i]."'");

          // ----- Return
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
          return PclZip::errorCode();
      }

      // ----- Next options
      $i++;
    }

    // ----- Look for mandatory options
    if ($v_requested_options !== false) {
      for ($key=reset($v_requested_options); $key=key($v_requested_options); $key=next($v_requested_options)) {
        // ----- Look for mandatory option
        if ($v_requested_options[$key] == 'mandatory') {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Detect a mandatory option : ".PclZipUtilOptionText($key)."(".$key.")");
          // ----- Look if present
          if (!isset($v_result_list[$key])) {
            // ----- Error log
            PclZip::privErrorLog(PCLZIP_ERR_INVALID_PARAMETER, "Missing mandatory parameter ".PclZipUtilOptionText($key)."(".$key.")");

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
          }
        }
      }
    }

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privOpenFd()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function privOpenFd($p_mode)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privOpenFd", 'mode='.$p_mode);
    $v_result=1;

    // ----- Look if already open
    if ($this->zip_fd != 0)
    {
      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_READ_OPEN_FAIL, 'Zip file \''.$this->zipname.'\' already open');

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Open the zip file
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Open file in '.$p_mode.' mode');
    if (($this->zip_fd = @fopen($this->zipname, $p_mode)) == 0)
    {
      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_READ_OPEN_FAIL, 'Unable to open archive \''.$this->zipname.'\' in '.$p_mode.' mode');

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privCloseFd()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function privCloseFd()
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privCloseFd", "");
    $v_result=1;

    if ($this->zip_fd != 0)
      @fclose($this->zip_fd);
    $this->zip_fd = 0;

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privList()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privList(&$p_list)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privList", "list");
    $v_result=1;

    // ----- Magic quotes trick
    $this->privDisableMagicQuotes();

    // ----- Open the zip file
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Open file in binary read mode");
    if (($this->zip_fd = @fopen($this->zipname, 'rb')) == 0)
    {
      // ----- Magic quotes trick
      $this->privSwapBackMagicQuotes();
      
      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_READ_OPEN_FAIL, 'Unable to open archive \''.$this->zipname.'\' in binary read mode');

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Read the central directory informations
    $v_central_dir = array();
    if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
    {
      $this->privSwapBackMagicQuotes();
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }

    // ----- Go to beginning of Central Dir
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Offset : ".$v_central_dir['offset']."'");
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Position in file : ".ftell($this->zip_fd)."'");
    @rewind($this->zip_fd);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Position in file : ".ftell($this->zip_fd)."'");
    if (@fseek($this->zip_fd, $v_central_dir['offset']))
    {
      $this->privSwapBackMagicQuotes();

      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_INVALID_ARCHIVE_ZIP, 'Invalid archive size');

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Position in file : ".ftell($this->zip_fd)."'");

    // ----- Read each entry
    for ($i=0; $i<$v_central_dir['entries']; $i++)
    {
      // ----- Read the file header
      if (($v_result = $this->privReadCentralFileHeader($v_header)) != 1)
      {
        $this->privSwapBackMagicQuotes();
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }
      $v_header['index'] = $i;

      // ----- Get the only interesting attributes
      $this->privConvertHeader2FileInfo($v_header, $p_list[$i]);
      unset($v_header);
    }

    // ----- Close the zip file
    $this->privCloseFd();

    // ----- Magic quotes trick
    $this->privSwapBackMagicQuotes();

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privConvertHeader2FileInfo()
  // Description :
  //   This function takes the file informations from the central directory
  //   entries and extract the interesting parameters that will be given back.
  //   The resulting file infos are set in the array $p_info
  //     $p_info['filename'] : Filename with full path. Given by user (add),
  //                           extracted in the filesystem (extract).
  //     $p_info['stored_filename'] : Stored filename in the archive.
  //     $p_info['size'] = Size of the file.
  //     $p_info['compressed_size'] = Compressed size of the file.
  //     $p_info['mtime'] = Last modification date of the file.
  //     $p_info['comment'] = Comment associated with the file.
  //     $p_info['folder'] = true/false : indicates if the entry is a folder or not.
  //     $p_info['status'] = status of the action on the file.
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privConvertHeader2FileInfo($p_header, &$p_info)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privConvertHeader2FileInfo", "Filename='".$p_header['filename']."'");
    $v_result=1;

    // ----- Get the interesting attributes
    $p_info['filename'] = $p_header['filename'];
    $p_info['stored_filename'] = $p_header['stored_filename'];
    $p_info['size'] = $p_header['size'];
    $p_info['compressed_size'] = $p_header['compressed_size'];
    $p_info['mtime'] = $p_header['mtime'];
    $p_info['comment'] = $p_header['comment'];
    $p_info['folder'] = (($p_header['external']&0x00000010)==0x00000010);
    $p_info['index'] = $p_header['index'];
    $p_info['status'] = $p_header['status'];

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privExtractByRule()
  // Description :
  //   Extract a file or directory depending of rules (by index, by name, ...)
  // Parameters :
  //   $p_file_list : An array where will be placed the properties of each
  //                  extracted file
  //   $p_path : Path to add while writing the extracted files
  //   $p_remove_path : Path to remove (from the file memorized path) while writing the
  //                    extracted files. If the path does not match the file path,
  //                    the file is extracted with its memorized path.
  //                    $p_remove_path does not apply to 'list' mode.
  //                    $p_path and $p_remove_path are commulative.
  // Return Values :
  //   1 on success,0 or less on error (see error code list)
  // --------------------------------------------------------------------------------
  function privExtractByRule(&$p_file_list, $p_path, $p_remove_path, $p_remove_all_path, &$p_options)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privExtractByRule", "path='$p_path', remove_path='$p_remove_path', remove_all_path='".($p_remove_all_path?'true':'false')."'");
    $v_result=1;

    // ----- Magic quotes trick
    $this->privDisableMagicQuotes();

    // ----- Check the path
    if (   ($p_path == "")
	    || (   (substr($p_path, 0, 1) != "/")
		    && (substr($p_path, 0, 3) != "../")
			&& (substr($p_path,1,2)!=":/")))
// net2ftp
//      $p_path = "./".$p_path;

    // ----- Reduce the path last (and duplicated) '/'
    if (($p_path != "./") && ($p_path != "/"))
    {
      // ----- Look for the path end '/'
      while (substr($p_path, -1) == "/")
      {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Destination path [$p_path] ends by '/'");
        $p_path = substr($p_path, 0, strlen($p_path)-1);
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Modified to [$p_path]");
      }
    }

    // ----- Look for path to remove format (should end by /)
    if (($p_remove_path != "") && (substr($p_remove_path, -1) != '/'))
    {
      $p_remove_path .= '/';
    }
    $p_remove_path_size = strlen($p_remove_path);

    // ----- Open the zip file
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Open file in binary read mode");
    if (($v_result = $this->privOpenFd('rb')) != 1)
    {
      $this->privSwapBackMagicQuotes();
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }

    // ----- Read the central directory informations
    $v_central_dir = array();
    if (($v_result = $this->privReadEndCentralDir($v_central_dir)) != 1)
    {
      // ----- Close the zip file
      $this->privCloseFd();
      $this->privSwapBackMagicQuotes();

      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }

    // ----- Start at beginning of Central Dir
    $v_pos_entry = $v_central_dir['offset'];

    // ----- Read each entry
    $j_start = 0;
    for ($i=0, $v_nb_extracted=0; $i<$v_central_dir['entries']; $i++)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Read next file header entry : '$i'");

      // ----- Read next Central dir entry
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Position before rewind : ".ftell($this->zip_fd)."'");
      @rewind($this->zip_fd);
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Position after rewind : ".ftell($this->zip_fd)."'");
      if (@fseek($this->zip_fd, $v_pos_entry))
      {
        // ----- Close the zip file
        $this->privCloseFd();
        $this->privSwapBackMagicQuotes();

        // ----- Error log
        PclZip::privErrorLog(PCLZIP_ERR_INVALID_ARCHIVE_ZIP, 'Invalid archive size');

        // ----- Return
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
        return PclZip::errorCode();
      }
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Position after fseek : ".ftell($this->zip_fd)."'");

      // ----- Read the file header
      $v_header = array();
      if (($v_result = $this->privReadCentralFileHeader($v_header)) != 1)
      {
        // ----- Close the zip file
        $this->privCloseFd();
        $this->privSwapBackMagicQuotes();

        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }

      // ----- Store the index
      $v_header['index'] = $i;

      // ----- Store the file position
      $v_pos_entry = ftell($this->zip_fd);

      // ----- Look for the specific extract rules
      $v_extract = false;

      // ----- Look for extract by name rule
      if (   (isset($p_options[PCLZIP_OPT_BY_NAME]))
          && ($p_options[PCLZIP_OPT_BY_NAME] != 0)) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extract with rule 'ByName'");

          // ----- Look if the filename is in the list
          for ($j=0; ($j<sizeof($p_options[PCLZIP_OPT_BY_NAME])) && (!$v_extract); $j++) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Compare with file '".$p_options[PCLZIP_OPT_BY_NAME][$j]."'");

              // ----- Look for a directory
              if (substr($p_options[PCLZIP_OPT_BY_NAME][$j], -1) == "/") {
                  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "The searched item is a directory");

                  // ----- Look if the directory is in the filename path
                  if (   (strlen($v_header['stored_filename']) > strlen($p_options[PCLZIP_OPT_BY_NAME][$j]))
                      && (substr($v_header['stored_filename'], 0, strlen($p_options[PCLZIP_OPT_BY_NAME][$j])) == $p_options[PCLZIP_OPT_BY_NAME][$j])) {
                      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "The directory is in the file path");
                      $v_extract = true;
                  }
              }
              // ----- Look for a filename
              elseif ($v_header['stored_filename'] == $p_options[PCLZIP_OPT_BY_NAME][$j]) {
                  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "The file is the right one.");
                  $v_extract = true;
              }
          }
      }

      // ----- Look for extract by ereg rule
      else if (   (isset($p_options[PCLZIP_OPT_BY_EREG]))
               && ($p_options[PCLZIP_OPT_BY_EREG] != "")) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extract by ereg '".$p_options[PCLZIP_OPT_BY_EREG]."'");

          if (ereg($p_options[PCLZIP_OPT_BY_EREG], $v_header['stored_filename'])) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Filename match the regular expression");
              $v_extract = true;
          }
      }

      // ----- Look for extract by preg rule
      else if (   (isset($p_options[PCLZIP_OPT_BY_PREG]))
               && ($p_options[PCLZIP_OPT_BY_PREG] != "")) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extract with rule 'ByEreg'");

          if (preg_match($p_options[PCLZIP_OPT_BY_PREG], $v_header['stored_filename'])) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Filename match the regular expression");
              $v_extract = true;
          }
      }

      // ----- Look for extract by index rule
      else if (   (isset($p_options[PCLZIP_OPT_BY_INDEX]))
               && ($p_options[PCLZIP_OPT_BY_INDEX] != 0)) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extract with rule 'ByIndex'");
          
          // ----- Look if the index is in the list
          for ($j=$j_start; ($j<sizeof($p_options[PCLZIP_OPT_BY_INDEX])) && (!$v_extract); $j++) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Look if index '$i' is in [".$p_options[PCLZIP_OPT_BY_INDEX][$j]['start'].",".$p_options[PCLZIP_OPT_BY_INDEX][$j]['end']."]");

              if (($i>=$p_options[PCLZIP_OPT_BY_INDEX][$j]['start']) && ($i<=$p_options[PCLZIP_OPT_BY_INDEX][$j]['end'])) {
                  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Found as part of an index range");
                  $v_extract = true;
              }
              if ($i>=$p_options[PCLZIP_OPT_BY_INDEX][$j]['end']) {
                  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Do not look this index range for next loop");
                  $j_start = $j+1;
              }

              if ($p_options[PCLZIP_OPT_BY_INDEX][$j]['start']>$i) {
                  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Index range is greater than index, stop loop");
                  break;
              }
          }
      }

      // ----- Look for no rule, which means extract all the archive
      else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extract with no rule (extract all)");
          $v_extract = true;
      }

	  // ----- Check compression method
	  if (   ($v_extract)
	      && (   ($v_header['compression'] != 8)
		      && ($v_header['compression'] != 0))) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Unsupported compression method (".$v_header['compression'].")");
          $v_header['status'] = 'unsupported_compression';

          // ----- Look for PCLZIP_OPT_STOP_ON_ERROR
          if (   (isset($p_options[PCLZIP_OPT_STOP_ON_ERROR]))
		      && ($p_options[PCLZIP_OPT_STOP_ON_ERROR]===true)) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "PCLZIP_OPT_STOP_ON_ERROR is selected, extraction will be stopped");

              $this->privSwapBackMagicQuotes();
              
              PclZip::privErrorLog(PCLZIP_ERR_UNSUPPORTED_COMPRESSION,
			                       "Filename '".$v_header['stored_filename']."' is "
				  	    	  	   ."compressed by an unsupported compression "
				  	    	  	   ."method (".$v_header['compression'].") ");

              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
              return PclZip::errorCode();
		  }
	  }
	  
	  // ----- Check encrypted files
	  if (($v_extract) && (($v_header['flag'] & 1) == 1)) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Unsupported file encryption");
          $v_header['status'] = 'unsupported_encryption';

          // ----- Look for PCLZIP_OPT_STOP_ON_ERROR
          if (   (isset($p_options[PCLZIP_OPT_STOP_ON_ERROR]))
		      && ($p_options[PCLZIP_OPT_STOP_ON_ERROR]===true)) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "PCLZIP_OPT_STOP_ON_ERROR is selected, extraction will be stopped");

              $this->privSwapBackMagicQuotes();

              PclZip::privErrorLog(PCLZIP_ERR_UNSUPPORTED_ENCRYPTION,
			                       "Unsupported encryption for "
				  	    	  	   ." filename '".$v_header['stored_filename']
								   ."'");

              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
              return PclZip::errorCode();
		  }
    }

      // ----- Look for real extraction
      if (($v_extract) && ($v_header['status'] != 'ok')) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "No need for extract");
          $v_result = $this->privConvertHeader2FileInfo($v_header,
		                                        $p_file_list[$v_nb_extracted++]);
          if ($v_result != 1) {
              $this->privCloseFd();
              $this->privSwapBackMagicQuotes();
              //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
              return $v_result;
          }

          $v_extract = false;
      }
      
      // ----- Look for real extraction
      if ($v_extract)
      {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting file '".$v_header['filename']."', index '$i'");

        // ----- Go to the file position
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Position before rewind : ".ftell($this->zip_fd)."'");
        @rewind($this->zip_fd);
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Position after rewind : ".ftell($this->zip_fd)."'");
        if (@fseek($this->zip_fd, $v_header['offset']))
        {
          // ----- Close the zip file
          $this->privCloseFd();

          $this->privSwapBackMagicQuotes();

          // ----- Error log
          PclZip::privErrorLog(PCLZIP_ERR_INVALID_ARCHIVE_ZIP, 'Invalid archive size');

          // ----- Return
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
          return PclZip::errorCode();
        }
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Position after fseek : ".ftell($this->zip_fd)."'");

        // ----- Look for extraction as string
        if ($p_options[PCLZIP_OPT_EXTRACT_AS_STRING]) {

          // ----- Extracting the file
          $v_result1 = $this->privExtractFileAsString($v_header, $v_string);
          if ($v_result1 < 1) {
            $this->privCloseFd();
            $this->privSwapBackMagicQuotes();
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result1);
            return $v_result1;
          }

          // ----- Get the only interesting attributes
          if (($v_result = $this->privConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted])) != 1)
          {
            // ----- Close the zip file
            $this->privCloseFd();
            $this->privSwapBackMagicQuotes();

            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return $v_result;
          }

          // ----- Set the file content
          $p_file_list[$v_nb_extracted]['content'] = $v_string;

          // ----- Next extracted file
          $v_nb_extracted++;
          
          // ----- Look for user callback abort
          if ($v_result1 == 2) {
          	break;
          }
        }
        // ----- Look for extraction in standard output
        elseif (   (isset($p_options[PCLZIP_OPT_EXTRACT_IN_OUTPUT]))
		        && ($p_options[PCLZIP_OPT_EXTRACT_IN_OUTPUT])) {
          // ----- Extracting the file in standard output
          $v_result1 = $this->privExtractFileInOutput($v_header, $p_options);
          if ($v_result1 < 1) {
            $this->privCloseFd();
            $this->privSwapBackMagicQuotes();
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result1);
            return $v_result1;
          }

          // ----- Get the only interesting attributes
          if (($v_result = $this->privConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted++])) != 1) {
            $this->privCloseFd();
            $this->privSwapBackMagicQuotes();
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return $v_result;
          }

          // ----- Look for user callback abort
          if ($v_result1 == 2) {
          	break;
          }
        }
        // ----- Look for normal extraction
        else {
          // ----- Extracting the file
          $v_result1 = $this->privExtractFile($v_header,
		                                      $p_path, $p_remove_path,
											  $p_remove_all_path,
											  $p_options);
          if ($v_result1 < 1) {
            $this->privCloseFd();
            $this->privSwapBackMagicQuotes();
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result1);
            return $v_result1;
          }

          // ----- Get the only interesting attributes
          if (($v_result = $this->privConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted++])) != 1)
          {
            // ----- Close the zip file
            $this->privCloseFd();
            $this->privSwapBackMagicQuotes();

            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return $v_result;
          }

          // ----- Look for user callback abort
          if ($v_result1 == 2) {
          	break;
          }
        }
      }
    }

    // ----- Close the zip file
    $this->privCloseFd();
    $this->privSwapBackMagicQuotes();

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privExtractFile()
  // Description :
  // Parameters :
  // Return Values :
  //
  // 1 : ... ?
  // PCLZIP_ERR_USER_ABORTED(2) : User ask for extraction stop in callback
  // --------------------------------------------------------------------------------
  function privExtractFile(&$p_entry, $p_path, $p_remove_path, $p_remove_all_path, &$p_options)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclZip::privExtractFile', "path='$p_path', remove_path='$p_remove_path', remove_all_path='".($p_remove_all_path?'true':'false')."'");
    $v_result=1;

    // ----- Read the file header
    if (($v_result = $this->privReadFileHeader($v_header)) != 1)
    {
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }

    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Found file '".$v_header['filename']."', size '".$v_header['size']."'");

    // ----- Check that the file header is coherent with $p_entry info
    if ($this->privCheckFileHeaders($v_header, $p_entry) != 1) {
        // TBC
    }

    // ----- Look for all path to remove

    if ($p_remove_all_path == true) {
        // ----- Look for folder entry that not need to be extracted
        if (($p_entry['external']&0x00000010)==0x00000010) {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "The entry is a folder : need to be filtered");

            $p_entry['status'] = "filtered";

            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return $v_result;
        }

        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "All path is removed");
        // ----- Get the basename of the path
        $p_entry['filename'] = basename($p_entry['filename']);
    }

    // ----- Look for path to remove
    else if ($p_remove_path != "")
    {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Look for some path to remove");
      if (PclZipUtilPathInclusion($p_remove_path, $p_entry['filename']) == 2)
      {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "The folder is the same as the removed path '".$p_entry['filename']."'");

        // ----- Change the file status
        $p_entry['status'] = "filtered";

        // ----- Return
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }

      $p_remove_path_size = strlen($p_remove_path);
      if (substr($p_entry['filename'], 0, $p_remove_path_size) == $p_remove_path)
      {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Found path '$p_remove_path' to remove in file '".$p_entry['filename']."'");

        // ----- Remove the path
        $p_entry['filename'] = substr($p_entry['filename'], $p_remove_path_size);

        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Resulting file is '".$p_entry['filename']."'");
      }
    }

    // ----- Add the path
    if ($p_path != '') {
      $p_entry['filename'] = $p_path."/".$p_entry['filename'];
    }
    
    // ----- Check a base_dir_restriction
    if (isset($p_options[PCLZIP_OPT_EXTRACT_DIR_RESTRICTION])) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Check the extract directory restriction");
      $v_inclusion
      = PclZipUtilPathInclusion($p_options[PCLZIP_OPT_EXTRACT_DIR_RESTRICTION],
                                $p_entry['filename']); 
      if ($v_inclusion == 0) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "PCLZIP_OPT_EXTRACT_DIR_RESTRICTION is selected, file is outside restriction");

        PclZip::privErrorLog(PCLZIP_ERR_DIRECTORY_RESTRICTION,
			                     "Filename '".$p_entry['filename']."' is "
								 ."outside PCLZIP_OPT_EXTRACT_DIR_RESTRICTION");

        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
        return PclZip::errorCode();
      }
    }

    // ----- Look for pre-extract callback
    if (isset($p_options[PCLZIP_CB_PRE_EXTRACT])) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "A pre-callback '".$p_options[PCLZIP_CB_PRE_EXTRACT]."()') is defined for the extraction");

      // ----- Generate a local information
      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_entry, $v_local_header);

      // ----- Call the callback
      // Here I do not use call_user_func() because I need to send a reference to the
      // header.
      eval('$v_result = '.$p_options[PCLZIP_CB_PRE_EXTRACT].'(PCLZIP_CB_PRE_EXTRACT, $v_local_header);');
      if ($v_result == 0) {
        // ----- Change the file status
        $p_entry['status'] = "skipped";
        $v_result = 1;
      }
      
      // ----- Look for abort result
      if ($v_result == 2) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "User callback abort the extraction");
        // ----- This status is internal and will be changed in 'skipped'
        $p_entry['status'] = "aborted";
      	$v_result = PCLZIP_ERR_USER_ABORTED;
      }

      // ----- Update the informations
      // Only some fields can be modified
      $p_entry['filename'] = $v_local_header['filename'];
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "New filename is '".$p_entry['filename']."'");
    }

    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting file (with path) '".$p_entry['filename']."', size '$v_header[size]'");

    // ----- Look if extraction should be done
    if ($p_entry['status'] == 'ok') {

    // ----- Look for specific actions while the file exist
    if (file_exists($p_entry['filename']))
    {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "File '".$p_entry['filename']."' already exists");

      // ----- Look if file is a directory
      if (is_dir($p_entry['filename']))
      {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Existing file '".$p_entry['filename']."' is a directory");

        // ----- Change the file status
        $p_entry['status'] = "already_a_directory";
        
        // ----- Look for PCLZIP_OPT_STOP_ON_ERROR
        // For historical reason first PclZip implementation does not stop
        // when this kind of error occurs.
        if (   (isset($p_options[PCLZIP_OPT_STOP_ON_ERROR]))
		    && ($p_options[PCLZIP_OPT_STOP_ON_ERROR]===true)) {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "PCLZIP_OPT_STOP_ON_ERROR is selected, extraction will be stopped");

            PclZip::privErrorLog(PCLZIP_ERR_ALREADY_A_DIRECTORY,
			                     "Filename '".$p_entry['filename']."' is "
								 ."already used by an existing directory");

            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
		}
      }
      // ----- Look if file is write protected
      else if (!is_writeable($p_entry['filename']))
      {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Existing file '".$p_entry['filename']."' is write protected");

        // ----- Change the file status
        $p_entry['status'] = "write_protected";

        // ----- Look for PCLZIP_OPT_STOP_ON_ERROR
        // For historical reason first PclZip implementation does not stop
        // when this kind of error occurs.
        if (   (isset($p_options[PCLZIP_OPT_STOP_ON_ERROR]))
		    && ($p_options[PCLZIP_OPT_STOP_ON_ERROR]===true)) {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "PCLZIP_OPT_STOP_ON_ERROR is selected, extraction will be stopped");

            PclZip::privErrorLog(PCLZIP_ERR_WRITE_OPEN_FAIL,
			                     "Filename '".$p_entry['filename']."' exists "
								 ."and is write protected");

            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
            return PclZip::errorCode();
		}
      }

      // ----- Look if the extracted file is older
      else if (filemtime($p_entry['filename']) > $p_entry['mtime'])
      {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Existing file '".$p_entry['filename']."' is newer (".date("l dS of F Y h:i:s A", filemtime($p_entry['filename'])).") than the extracted file (".date("l dS of F Y h:i:s A", $p_entry['mtime']).")");
        // ----- Change the file status
        if (   (isset($p_options[PCLZIP_OPT_REPLACE_NEWER]))
		    && ($p_options[PCLZIP_OPT_REPLACE_NEWER]===true)) {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "PCLZIP_OPT_REPLACE_NEWER is selected, file will be replaced");
		}
		else {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "File will not be replaced");
            $p_entry['status'] = "newer_exist";

            // ----- Look for PCLZIP_OPT_STOP_ON_ERROR
            // For historical reason first PclZip implementation does not stop
            // when this kind of error occurs.
            if (   (isset($p_options[PCLZIP_OPT_STOP_ON_ERROR]))
		        && ($p_options[PCLZIP_OPT_STOP_ON_ERROR]===true)) {
                //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "PCLZIP_OPT_STOP_ON_ERROR is selected, extraction will be stopped");

                PclZip::privErrorLog(PCLZIP_ERR_WRITE_OPEN_FAIL,
			             "Newer version of '".$p_entry['filename']."' exists "
					    ."and option PCLZIP_OPT_REPLACE_NEWER is not selected");

                //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
                return PclZip::errorCode();
		    }
		}
      }
      else {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Existing file '".$p_entry['filename']."' is older than the extrated one - will be replaced by the extracted one (".date("l dS of F Y h:i:s A", filemtime($p_entry['filename'])).") than the extracted file (".date("l dS of F Y h:i:s A", $p_entry['mtime']).")");
      }
    }

    // ----- Check the directory availability and create it if necessary
    else {
      if ((($p_entry['external']&0x00000010)==0x00000010) || (substr($p_entry['filename'], -1) == '/'))
        $v_dir_to_check = $p_entry['filename'];
      else if (!strstr($p_entry['filename'], "/"))
        $v_dir_to_check = "";
      else
        $v_dir_to_check = dirname($p_entry['filename']);

      if (($v_result = $this->privDirCheck($v_dir_to_check, (($p_entry['external']&0x00000010)==0x00000010))) != 1) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Unable to create path for '".$p_entry['filename']."'");

        // ----- Change the file status
        $p_entry['status'] = "path_creation_fail";

        // ----- Return
        ////--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
        //return $v_result;
        $v_result = 1;
      }
    }
    }

    // ----- Look if extraction should be done
    if ($p_entry['status'] == 'ok') {

      // ----- Do the extraction (if not a folder)
      if (!(($p_entry['external']&0x00000010)==0x00000010))
      {
        // ----- Look for not compressed file
        if ($p_entry['compression'] == 0) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting an un-compressed file");

		  // ----- Opening destination file
          if (($v_dest_file = @fopen($p_entry['filename'], 'wb')) == 0)
          {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Error while opening '".$p_entry['filename']."' in write binary mode");

            // ----- Change the file status
            $p_entry['status'] = "write_error";

            // ----- Return
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return $v_result;
          }

          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Read '".$p_entry['size']."' bytes");

          // ----- Read the file by PCLZIP_READ_BLOCK_SIZE octets blocks
          $v_size = $p_entry['compressed_size'];
          while ($v_size != 0)
          {
            $v_read_size = ($v_size < PCLZIP_READ_BLOCK_SIZE ? $v_size : PCLZIP_READ_BLOCK_SIZE);
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Read $v_read_size bytes");
            $v_buffer = @fread($this->zip_fd, $v_read_size);
            /* Try to speed up the code
            $v_binary_data = pack('a'.$v_read_size, $v_buffer);
            @fwrite($v_dest_file, $v_binary_data, $v_read_size);
            */
            @fwrite($v_dest_file, $v_buffer, $v_read_size);            
            $v_size -= $v_read_size;
          }

          // ----- Closing the destination file
          fclose($v_dest_file);

          // ----- Change the file mtime
          touch($p_entry['filename'], $p_entry['mtime']);
          

        }
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting a compressed file (Compression method ".$p_entry['compression'].")");
          // ----- TBC
          // Need to be finished
          if (($p_entry['flag'] & 1) == 1) {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "File is encrypted");
            /*
              // ----- Read the encryption header
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Read 12 encryption header bytes");
              $v_encryption_header = @fread($this->zip_fd, 12);
              
              // ----- Read the encrypted & compressed file in a buffer
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Read '".($p_entry['compressed_size']-12)."' compressed & encrypted bytes");
              $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']-12);
              
              // ----- Decrypt the buffer
              $this->privDecrypt($v_encryption_header, $v_buffer,
			                     $p_entry['compressed_size']-12, $p_entry['crc']);
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Buffer is '".$v_buffer."'");
              */
          }
          else {
              //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Read '".$p_entry['compressed_size']."' compressed bytes");
              // ----- Read the compressed file in a buffer (one shot)
              $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);
          }
          
          // ----- Decompress the file
          $v_file_content = @gzinflate($v_buffer);
          unset($v_buffer);
          if ($v_file_content === FALSE) {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Unable to inflate compressed file");

            // ----- Change the file status
            // TBC
            $p_entry['status'] = "error";
            
            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return $v_result;
          }
          
          // ----- Opening destination file
          if (($v_dest_file = @fopen($p_entry['filename'], 'wb')) == 0) {
            //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Error while opening '".$p_entry['filename']."' in write binary mode");

            // ----- Change the file status
            $p_entry['status'] = "write_error";

            //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
            return $v_result;
          }

          // ----- Write the uncompressed data
          @fwrite($v_dest_file, $v_file_content, $p_entry['size']);
          unset($v_file_content);

          // ----- Closing the destination file
          @fclose($v_dest_file);

          // ----- Change the file mtime
          @touch($p_entry['filename'], $p_entry['mtime']);
        }

        // ----- Look for chmod option
        if (isset($p_options[PCLZIP_OPT_SET_CHMOD])) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "chmod option activated '".$p_options[PCLZIP_OPT_SET_CHMOD]."'");

          // ----- Change the mode of the file
          @chmod($p_entry['filename'], $p_options[PCLZIP_OPT_SET_CHMOD]);
        }

        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extraction done");
      }
    }

	// ----- Change abort status
	if ($p_entry['status'] == "aborted") {
      $p_entry['status'] = "skipped";
	}
	
    // ----- Look for post-extract callback
    elseif (isset($p_options[PCLZIP_CB_POST_EXTRACT])) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "A post-callback '".$p_options[PCLZIP_CB_POST_EXTRACT]."()') is defined for the extraction");

      // ----- Generate a local information
      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_entry, $v_local_header);

      // ----- Call the callback
      // Here I do not use call_user_func() because I need to send a reference to the
      // header.
      eval('$v_result = '.$p_options[PCLZIP_CB_POST_EXTRACT].'(PCLZIP_CB_POST_EXTRACT, $v_local_header);');

      // ----- Look for abort result
      if ($v_result == 2) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "User callback abort the extraction");
      	$v_result = PCLZIP_ERR_USER_ABORTED;
      }
    }

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privExtractFileInOutput()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privExtractFileInOutput(&$p_entry, &$p_options)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclZip::privExtractFileInOutput', "");
    $v_result=1;

    // ----- Read the file header
    if (($v_result = $this->privReadFileHeader($v_header)) != 1) {
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }

    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Found file '".$v_header['filename']."', size '".$v_header['size']."'");

    // ----- Check that the file header is coherent with $p_entry info
    if ($this->privCheckFileHeaders($v_header, $p_entry) != 1) {
        // TBC
    }

    // ----- Look for pre-extract callback
    if (isset($p_options[PCLZIP_CB_PRE_EXTRACT])) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "A pre-callback '".$p_options[PCLZIP_CB_PRE_EXTRACT]."()') is defined for the extraction");

      // ----- Generate a local information
      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_entry, $v_local_header);

      // ----- Call the callback
      // Here I do not use call_user_func() because I need to send a reference to the
      // header.
      eval('$v_result = '.$p_options[PCLZIP_CB_PRE_EXTRACT].'(PCLZIP_CB_PRE_EXTRACT, $v_local_header);');
      if ($v_result == 0) {
        // ----- Change the file status
        $p_entry['status'] = "skipped";
        $v_result = 1;
      }

      // ----- Look for abort result
      if ($v_result == 2) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "User callback abort the extraction");
        // ----- This status is internal and will be changed in 'skipped'
        $p_entry['status'] = "aborted";
      	$v_result = PCLZIP_ERR_USER_ABORTED;
      }

      // ----- Update the informations
      // Only some fields can be modified
      $p_entry['filename'] = $v_local_header['filename'];
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "New filename is '".$p_entry['filename']."'");
    }

    // ----- Trace
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting file (with path) '".$p_entry['filename']."', size '$v_header[size]'");

    // ----- Look if extraction should be done
    if ($p_entry['status'] == 'ok') {

      // ----- Do the extraction (if not a folder)
      if (!(($p_entry['external']&0x00000010)==0x00000010)) {
        // ----- Look for not compressed file
        if ($p_entry['compressed_size'] == $p_entry['size']) {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting an un-compressed file");
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Reading '".$p_entry['size']."' bytes");

          // ----- Read the file in a buffer (one shot)
          $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);

          // ----- Send the file to the output
          echo $v_buffer;
          unset($v_buffer);
        }
        else {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting a compressed file");
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Reading '".$p_entry['size']."' bytes");

          // ----- Read the compressed file in a buffer (one shot)
          $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);
          
          // ----- Decompress the file
          $v_file_content = gzinflate($v_buffer);
          unset($v_buffer);

          // ----- Send the file to the output
          echo $v_file_content;
          unset($v_file_content);
        }
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extraction done");
      }
    }

	// ----- Change abort status
	if ($p_entry['status'] == "aborted") {
      $p_entry['status'] = "skipped";
	}

    // ----- Look for post-extract callback
    elseif (isset($p_options[PCLZIP_CB_POST_EXTRACT])) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "A post-callback '".$p_options[PCLZIP_CB_POST_EXTRACT]."()') is defined for the extraction");

      // ----- Generate a local information
      $v_local_header = array();
      $this->privConvertHeader2FileInfo($p_entry, $v_local_header);

      // ----- Call the callback
      // Here I do not use call_user_func() because I need to send a reference to the
      // header.
      eval('$v_result = '.$p_options[PCLZIP_CB_POST_EXTRACT].'(PCLZIP_CB_POST_EXTRACT, $v_local_header);');

      // ----- Look for abort result
      if ($v_result == 2) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "User callback abort the extraction");
      	$v_result = PCLZIP_ERR_USER_ABORTED;
      }
    }

    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privExtractFileAsString()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privExtractFileAsString(&$p_entry, &$p_string)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclZip::privExtractFileAsString', "p_entry['filename']='".$p_entry['filename']."'");
    $v_result=1;

    // ----- Read the file header
    $v_header = array();
    if (($v_result = $this->privReadFileHeader($v_header)) != 1)
    {
      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
    }

    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Found file '".$v_header['filename']."', size '".$v_header['size']."'");

    // ----- Check that the file header is coherent with $p_entry info
    if ($this->privCheckFileHeaders($v_header, $p_entry) != 1) {
        // TBC
    }

    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting file in string (with path) '".$p_entry['filename']."', size '$v_header[size]'");

    // ----- Do the extraction (if not a folder)
    if (!(($p_entry['external']&0x00000010)==0x00000010))
    {
      // ----- Look for not compressed file
//      if ($p_entry['compressed_size'] == $p_entry['size'])
      if ($p_entry['compression'] == 0) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting an un-compressed file");
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Reading '".$p_entry['size']."' bytes");

        // ----- Reading the file
        $p_string = @fread($this->zip_fd, $p_entry['compressed_size']);
      }
      else {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extracting a compressed file (compression method '".$p_entry['compression']."')");

        // ----- Reading the file
        $v_data = @fread($this->zip_fd, $p_entry['compressed_size']);
        
        // ----- Decompress the file
        if (($p_string = @gzinflate($v_data)) === FALSE) {
            // TBC
        }
      }

      // ----- Trace
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Extraction done");
    }
    else {
        // TBC : error : can not extract a folder in a string
    }

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privReadFileHeader()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privReadFileHeader(&$p_header)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privReadFileHeader", "");
    $v_result=1;

    // ----- Read the 4 bytes signature
    $v_binary_data = @fread($this->zip_fd, 4);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Binary data is : '".sprintf("%08x", $v_binary_data)."'");
    $v_data = unpack('Vid', $v_binary_data);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Binary signature is : '".sprintf("0x%08x", $v_data['id'])."'");

    // ----- Check signature
    if ($v_data['id'] != 0x04034b50)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Invalid File header");

      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, 'Invalid archive structure');

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Read the first 42 bytes of the header
    $v_binary_data = fread($this->zip_fd, 26);

    // ----- Look for invalid block size
    if (strlen($v_binary_data) != 26)
    {
      $p_header['filename'] = "";
      $p_header['status'] = "invalid_header";
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Invalid block size : ".strlen($v_binary_data));

      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, "Invalid block size : ".strlen($v_binary_data));

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Extract the values
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Header : '".$v_binary_data."'");
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Header (Hex) : '".bin2hex($v_binary_data)."'");
    $v_data = unpack('vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $v_binary_data);

    // ----- Get filename
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "File name length : ".$v_data['filename_len']);
    $p_header['filename'] = fread($this->zip_fd, $v_data['filename_len']);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Filename : \''.$p_header['filename'].'\'');

    // ----- Get extra_fields
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Extra field length : ".$v_data['extra_len']);
    if ($v_data['extra_len'] != 0) {
      $p_header['extra'] = fread($this->zip_fd, $v_data['extra_len']);
    }
    else {
      $p_header['extra'] = '';
    }
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Extra field : \''.bin2hex($p_header['extra']).'\'');

    // ----- Extract properties
    $p_header['version_extracted'] = $v_data['version'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Version need to extract : ('.$p_header['version_extracted'].') \''.($p_header['version_extracted']/10).'.'.($p_header['version_extracted']%10).'\'');
    $p_header['compression'] = $v_data['compression'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Compression method : \''.$p_header['compression'].'\'');
    $p_header['size'] = $v_data['size'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Size : \''.$p_header['size'].'\'');
    $p_header['compressed_size'] = $v_data['compressed_size'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Compressed Size : \''.$p_header['compressed_size'].'\'');
    $p_header['crc'] = $v_data['crc'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'CRC : \''.sprintf("0x%X", $p_header['crc']).'\'');
    $p_header['flag'] = $v_data['flag'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Flag : \''.$p_header['flag'].'\'');
    $p_header['filename_len'] = $v_data['filename_len'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Filename_len : \''.$p_header['filename_len'].'\'');

    // ----- Recuperate date in UNIX format
    $p_header['mdate'] = $v_data['mdate'];
    $p_header['mtime'] = $v_data['mtime'];
    if ($p_header['mdate'] && $p_header['mtime'])
    {
      // ----- Extract time
      $v_hour = ($p_header['mtime'] & 0xF800) >> 11;
      $v_minute = ($p_header['mtime'] & 0x07E0) >> 5;
      $v_seconde = ($p_header['mtime'] & 0x001F)*2;

      // ----- Extract date
      $v_year = (($p_header['mdate'] & 0xFE00) >> 9) + 1980;
      $v_month = ($p_header['mdate'] & 0x01E0) >> 5;
      $v_day = $p_header['mdate'] & 0x001F;

      // ----- Get UNIX date format
      $p_header['mtime'] = mktime($v_hour, $v_minute, $v_seconde, $v_month, $v_day, $v_year);

      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Date : \''.date("d/m/y H:i:s", $p_header['mtime']).'\'');
    }
    else
    {
      $p_header['mtime'] = time();
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Date is actual : \''.date("d/m/y H:i:s", $p_header['mtime']).'\'');
    }

    // TBC
    //for(reset($v_data); $key = key($v_data); next($v_data)) {
    //  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Attribut[$key] = ".$v_data[$key]);
    //}

    // ----- Set the stored filename
    $p_header['stored_filename'] = $p_header['filename'];

    // ----- Set the status field
    $p_header['status'] = "ok";

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privReadCentralFileHeader()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privReadCentralFileHeader(&$p_header)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privReadCentralFileHeader", "");
    $v_result=1;

    // ----- Read the 4 bytes signature
    $v_binary_data = @fread($this->zip_fd, 4);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Binary data is : '".sprintf("%08x", $v_binary_data)."'");
    $v_data = unpack('Vid', $v_binary_data);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Binary signature is : '".sprintf("0x%08x", $v_data['id'])."'");

    // ----- Check signature
    if ($v_data['id'] != 0x02014b50)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Invalid Central Dir File signature");

      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, 'Invalid archive structure');

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Read the first 42 bytes of the header
    $v_binary_data = fread($this->zip_fd, 42);

    // ----- Look for invalid block size
    if (strlen($v_binary_data) != 42)
    {
      $p_header['filename'] = "";
      $p_header['status'] = "invalid_header";
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Invalid block size : ".strlen($v_binary_data));

      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, "Invalid block size : ".strlen($v_binary_data));

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Extract the values
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Header : '".$v_binary_data."'");
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Header (Hex) : '".bin2hex($v_binary_data)."'");
    $p_header = unpack('vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $v_binary_data);

    // ----- Get filename
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "File name length : ".$p_header['filename_len']);
    if ($p_header['filename_len'] != 0)
      $p_header['filename'] = fread($this->zip_fd, $p_header['filename_len']);
    else
      $p_header['filename'] = '';
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Filename : \''.$p_header['filename'].'\'');

    // ----- Get extra
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Extra length : ".$p_header['extra_len']);
    if ($p_header['extra_len'] != 0)
      $p_header['extra'] = fread($this->zip_fd, $p_header['extra_len']);
    else
      $p_header['extra'] = '';
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Extra : \''.$p_header['extra'].'\'');

    // ----- Get comment
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Comment length : ".$p_header['comment_len']);
    if ($p_header['comment_len'] != 0)
      $p_header['comment'] = fread($this->zip_fd, $p_header['comment_len']);
    else
      $p_header['comment'] = '';
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Comment : \''.$p_header['comment'].'\'');

    // ----- Extract properties
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Version : \''.($p_header['version']/10).'.'.($p_header['version']%10).'\'');
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Version need to extract : \''.($p_header['version_extracted']/10).'.'.($p_header['version_extracted']%10).'\'');
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Size : \''.$p_header['size'].'\'');
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Compressed Size : \''.$p_header['compressed_size'].'\'');
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'CRC : \''.sprintf("0x%X", $p_header['crc']).'\'');
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Flag : \''.$p_header['flag'].'\'');
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Offset : \''.$p_header['offset'].'\'');

    // ----- Recuperate date in UNIX format
    if ($p_header['mdate'] && $p_header['mtime'])
    {
      // ----- Extract time
      $v_hour = ($p_header['mtime'] & 0xF800) >> 11;
      $v_minute = ($p_header['mtime'] & 0x07E0) >> 5;
      $v_seconde = ($p_header['mtime'] & 0x001F)*2;

      // ----- Extract date
      $v_year = (($p_header['mdate'] & 0xFE00) >> 9) + 1980;
      $v_month = ($p_header['mdate'] & 0x01E0) >> 5;
      $v_day = $p_header['mdate'] & 0x001F;

      // ----- Get UNIX date format
      $p_header['mtime'] = mktime($v_hour, $v_minute, $v_seconde, $v_month, $v_day, $v_year);

      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Date : \''.date("d/m/y H:i:s", $p_header['mtime']).'\'');
    }
    else
    {
      $p_header['mtime'] = time();
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Date is actual : \''.date("d/m/y H:i:s", $p_header['mtime']).'\'');
    }

    // ----- Set the stored filename
    $p_header['stored_filename'] = $p_header['filename'];

    // ----- Set default status to ok
    $p_header['status'] = 'ok';

    // ----- Look if it is a directory
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Internal (Hex) : '".sprintf("Ox%04X", $p_header['internal'])."'");
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "External (Hex) : '".sprintf("Ox%04X", $p_header['external'])."' (".(($p_header['external']&0x00000010)==0x00000010?'is a folder':'is a file').')');
    if (substr($p_header['filename'], -1) == '/') {
      //$p_header['external'] = 0x41FF0010;
      $p_header['external'] = 0x00000010;
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Force folder external : \''.sprintf("Ox%04X", $p_header['external']).'\'');
    }

    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Header of filename : \''.$p_header['filename'].'\'');

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privCheckFileHeaders()
  // Description :
  // Parameters :
  // Return Values :
  //   1 on success,
  //   0 on error;
  // --------------------------------------------------------------------------------
  function privCheckFileHeaders(&$p_local_header, &$p_central_header)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privCheckFileHeaders", "");
    $v_result=1;

	// ----- Check the static values
	// TBC
	if ($p_local_header['filename'] != $p_central_header['filename']) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Bad check "filename" : TBC To Be Completed');
	}
	if ($p_local_header['version_extracted'] != $p_central_header['version_extracted']) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Bad check "version_extracted" : TBC To Be Completed');
	}
	if ($p_local_header['flag'] != $p_central_header['flag']) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Bad check "flag" : TBC To Be Completed');
	}
	if ($p_local_header['compression'] != $p_central_header['compression']) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Bad check "compression" : TBC To Be Completed');
	}
	if ($p_local_header['mtime'] != $p_central_header['mtime']) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Bad check "mtime" : TBC To Be Completed');
	}
	if ($p_local_header['filename_len'] != $p_central_header['filename_len']) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Bad check "filename_len" : TBC To Be Completed');
	}

	// ----- Look for flag bit 3
	if (($p_local_header['flag'] & 8) == 8) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Purpose bit flag bit 3 set !');
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'File size, compression size and crc found in central header');
        $p_local_header['size'] = $p_central_header['size'];
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Size : \''.$p_local_header['size'].'\'');
        $p_local_header['compressed_size'] = $p_central_header['compressed_size'];
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Compressed Size : \''.$p_local_header['compressed_size'].'\'');
        $p_local_header['crc'] = $p_central_header['crc'];
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'CRC : \''.sprintf("0x%X", $p_local_header['crc']).'\'');
	}

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privReadEndCentralDir()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privReadEndCentralDir(&$p_central_dir)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privReadEndCentralDir", "");
    $v_result=1;

    // ----- Go to the end of the zip file
    $v_size = filesize($this->zipname);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Size of the file :$v_size");
    @fseek($this->zip_fd, $v_size);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Position at end of zip file : \''.ftell($this->zip_fd).'\'');
    if (@ftell($this->zip_fd) != $v_size)
    {
      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, 'Unable to go to the end of the archive \''.$this->zipname.'\'');

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- First try : look if this is an archive with no commentaries (most of the time)
    // in this case the end of central dir is at 22 bytes of the file end
    $v_found = 0;
    if ($v_size > 26) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Look for central dir with no comment');
      @fseek($this->zip_fd, $v_size-22);
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Position after min central position : \''.ftell($this->zip_fd).'\'');
      if (($v_pos = @ftell($this->zip_fd)) != ($v_size-22))
      {
        // ----- Error log
        PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, 'Unable to seek back to the middle of the archive \''.$this->zipname.'\'');

        // ----- Return
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
        return PclZip::errorCode();
      }

      // ----- Read for bytes
      $v_binary_data = @fread($this->zip_fd, 4);
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Binary data is : '".sprintf("%08x", $v_binary_data)."'");
      $v_data = @unpack('Vid', $v_binary_data);
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Binary signature is : '".sprintf("0x%08x", $v_data['id'])."'");

      // ----- Check signature
      if ($v_data['id'] == 0x06054b50) {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Found central dir at the default position.");
        $v_found = 1;
      }

      $v_pos = ftell($this->zip_fd);
    }

    // ----- Go back to the maximum possible size of the Central Dir End Record
    if (!$v_found) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Start extended search of end central dir');
      $v_maximum_size = 65557; // 0xFFFF + 22;
      if ($v_maximum_size > $v_size)
        $v_maximum_size = $v_size;
      @fseek($this->zip_fd, $v_size-$v_maximum_size);
      if (@ftell($this->zip_fd) != ($v_size-$v_maximum_size))
      {
        // ----- Error log
        PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, 'Unable to seek back to the middle of the archive \''.$this->zipname.'\'');

        // ----- Return
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
        return PclZip::errorCode();
      }
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Position after max central position : \''.ftell($this->zip_fd).'\'');

      // ----- Read byte per byte in order to find the signature
      $v_pos = ftell($this->zip_fd);
      $v_bytes = 0x00000000;
      while ($v_pos < $v_size)
      {
        // ----- Read a byte
        $v_byte = @fread($this->zip_fd, 1);

        // -----  Add the byte
        $v_bytes = ($v_bytes << 8) | Ord($v_byte);

        // ----- Compare the bytes
        if ($v_bytes == 0x504b0506)
        {
          //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, 'Found End Central Dir signature at position : \''.ftell($this->zip_fd).'\'');
          $v_pos++;
          break;
        }

        $v_pos++;
      }

      // ----- Look if not found end of central dir
      if ($v_pos == $v_size)
      {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Unable to find End of Central Dir Record signature");

        // ----- Error log
        PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, "Unable to find End of Central Dir Record signature");

        // ----- Return
        //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
        return PclZip::errorCode();
      }
    }

    // ----- Read the first 18 bytes of the header
    $v_binary_data = fread($this->zip_fd, 18);

    // ----- Look for invalid block size
    if (strlen($v_binary_data) != 18)
    {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "Invalid End of Central Dir Record size : ".strlen($v_binary_data));

      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT, "Invalid End of Central Dir Record size : ".strlen($v_binary_data));

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Extract the values
    ////--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Central Dir Record : '".$v_binary_data."'");
    ////--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 4, "Central Dir Record (Hex) : '".bin2hex($v_binary_data)."'");
    $v_data = unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size', $v_binary_data);

    // ----- Check the global size
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Comment length : ".$v_data['comment_size']);
    if (($v_pos + $v_data['comment_size'] + 18) != $v_size) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 2, "The central dir is not at the end of the archive. Some trailing bytes exists after the archive.");

	  // ----- Removed in release 2.2 see readme file
	  // The check of the file size is a little too strict.
	  // Some bugs where found when a zip is encrypted/decrypted with 'crypt'.
	  // While decrypted, zip has training 0 bytes
	  if (0) {
      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_BAD_FORMAT,
	                       'The central dir is not at the end of the archive.'
						   .' Some trailing bytes exists after the archive.');

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
	  }
    }

    // ----- Get comment
    if ($v_data['comment_size'] != 0)
      $p_central_dir['comment'] = fread($this->zip_fd, $v_data['comment_size']);
    else
      $p_central_dir['comment'] = '';
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Comment : \''.$p_central_dir['comment'].'\'');

    $p_central_dir['entries'] = $v_data['entries'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Nb of entries : \''.$p_central_dir['entries'].'\'');
    $p_central_dir['disk_entries'] = $v_data['disk_entries'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Nb of entries for this disk : \''.$p_central_dir['disk_entries'].'\'');
    $p_central_dir['offset'] = $v_data['offset'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Offset of Central Dir : \''.$p_central_dir['offset'].'\'');
    $p_central_dir['size'] = $v_data['size'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Size of Central Dir : \''.$p_central_dir['size'].'\'');
    $p_central_dir['disk'] = $v_data['disk'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Disk number : \''.$p_central_dir['disk'].'\'');
    $p_central_dir['disk_start'] = $v_data['disk_start'];
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, 'Start disk number : \''.$p_central_dir['disk_start'].'\'');

    // TBC
    //for(reset($p_central_dir); $key = key($p_central_dir); next($p_central_dir)) {
    //  //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "central_dir[$key] = ".$p_central_dir[$key]);
    //}

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privDirCheck()
  // Description :
  //   Check if a directory exists, if not it creates it and all the parents directory
  //   which may be useful.
  // Parameters :
  //   $p_dir : Directory path to check.
  // Return Values :
  //    1 : OK
  //   -1 : Unable to create directory
  // --------------------------------------------------------------------------------
  function privDirCheck($p_dir, $p_is_dir=false)
  {
    $v_result = 1;

    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZip::privDirCheck", "entry='$p_dir', is_dir='".($p_is_dir?"true":"false")."'");

    // ----- Remove the final '/'
    if (($p_is_dir) && (substr($p_dir, -1)=='/'))
    {
      $p_dir = substr($p_dir, 0, strlen($p_dir)-1);
    }
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Looking for entry '$p_dir'");

    // ----- Check the directory availability
    if ((is_dir($p_dir)) || ($p_dir == ""))
    {
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, "'$p_dir' is a directory");
      return 1;
    }

    // ----- Extract parent directory
    $p_parent_dir = dirname($p_dir);
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Parent directory is '$p_parent_dir'");

    // ----- Just a check
    if ($p_parent_dir != $p_dir)
    {
      // ----- Look for parent directory
      if ($p_parent_dir != "")
      {
        if (($v_result = $this->privDirCheck($p_parent_dir)) != 1)
        {
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }
    }

    // ----- Create the directory
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Create directory '$p_dir'");
    if (!@mkdir($p_dir, 0777))
    {
      // ----- Error log
      PclZip::privErrorLog(PCLZIP_ERR_DIR_CREATE_FAIL, "Unable to create directory '$p_dir'");

      // ----- Return
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, PclZip::errorCode(), PclZip::errorInfo());
      return PclZip::errorCode();
    }

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result, "Directory '$p_dir' created");
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privErrorLog()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function privErrorLog($p_error_code=0, $p_error_string='')
  {
    if (PCLZIP_ERROR_EXTERNAL == 1) {
      PclError($p_error_code, $p_error_string);
    }
    else {
      $this->error_code = $p_error_code;
      $this->error_string = $p_error_string;
    }
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privErrorReset()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function privErrorReset()
  {
    if (PCLZIP_ERROR_EXTERNAL == 1) {
      PclErrorReset();
    }
    else {
      $this->error_code = 0;
      $this->error_string = '';
    }
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privDecrypt()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privDecrypt($p_encryption_header, &$p_buffer, $p_size, $p_crc)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclZip::privDecrypt', "size=".$p_size."");
    $v_result=1;
    
    // ----- To Be Modified ;-)
    $v_pwd = "test";
    
    $p_buffer = PclZipUtilZipDecrypt($p_buffer, $p_size, $p_encryption_header,
	                                 $p_crc, $v_pwd);
    
    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privDisableMagicQuotes()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privDisableMagicQuotes()
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclZip::privDisableMagicQuotes', "");
    $v_result=1;

    // ----- Look if function exists
    if (   (!function_exists("get_magic_quotes_runtime"))
	    || (!function_exists("set_magic_quotes_runtime"))) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Functions *et_magic_quotes_runtime are not supported");
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
	}

    // ----- Look if already done
    if ($this->magic_quotes_status != -1) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "magic_quote already disabled");
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
	}

	// ----- Get and memorize the magic_quote value
	$this->magic_quotes_status = @get_magic_quotes_runtime();
    //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Current magic_quotes_runtime status is '".($this->magic_quotes_status==0?'disable':'enable')."'");

	// ----- Disable magic_quotes
	if ($this->magic_quotes_status == 1) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Disable magic_quotes");
	  @set_magic_quotes_runtime(0);
	}

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : privSwapBackMagicQuotes()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function privSwapBackMagicQuotes()
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, 'PclZip::privSwapBackMagicQuotes', "");
    $v_result=1;

    // ----- Look if function exists
    if (   (!function_exists("get_magic_quotes_runtime"))
	    || (!function_exists("set_magic_quotes_runtime"))) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Functions *et_magic_quotes_runtime are not supported");
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
	}

    // ----- Look if something to do
    if ($this->magic_quotes_status != -1) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "magic_quote not modified");
      //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
      return $v_result;
	}

	// ----- Swap back magic_quotes
	if ($this->magic_quotes_status == 1) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 3, "Enable back magic_quotes");
  	  @set_magic_quotes_runtime($this->magic_quotes_status);
	}

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  }
  // End of class
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclZipUtilPathInclusion()
  // Description :
  //   This function indicates if the path $p_path is under the $p_dir tree. Or,
  //   said in an other way, if the file or sub-dir $p_path is inside the dir
  //   $p_dir.
  //   The function indicates also if the path is exactly the same as the dir.
  //   This function supports path with duplicated '/' like '//', but does not
  //   support '.' or '..' statements.
  // Parameters :
  // Return Values :
  //   0 if $p_path is not inside directory $p_dir
  //   1 if $p_path is inside directory $p_dir
  //   2 if $p_path is exactly the same as $p_dir
  // --------------------------------------------------------------------------------
  function PclZipUtilPathInclusion($p_dir, $p_path)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZipUtilPathInclusion", "dir='$p_dir', path='$p_path'");
    $v_result = 1;
    
    // ----- Look for path beginning by ./
    if (   ($p_dir == '.')
        || ((strlen($p_dir) >=2) && (substr($p_dir, 0, 2) == './'))) {
      $p_dir = PclZipUtilTranslateWinPath(getcwd(), FALSE).'/'.substr($p_dir, 1);
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Replacing ./ by full path in p_dir '".$p_dir."'");
    }
    if (   ($p_path == '.')
        || ((strlen($p_path) >=2) && (substr($p_path, 0, 2) == './'))) {
      $p_path = PclZipUtilTranslateWinPath(getcwd(), FALSE).'/'.substr($p_path, 1);
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Replacing ./ by full path in p_path '".$p_path."'");
    }

    // ----- Explode dir and path by directory separator
    $v_list_dir = explode("/", $p_dir);
    $v_list_dir_size = sizeof($v_list_dir);
    $v_list_path = explode("/", $p_path);
    $v_list_path_size = sizeof($v_list_path);

    // ----- Study directories paths
    $i = 0;
    $j = 0;
    while (($i < $v_list_dir_size) && ($j < $v_list_path_size) && ($v_result)) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Working on dir($i)='".$v_list_dir[$i]."' and path($j)='".$v_list_path[$j]."'");

      // ----- Look for empty dir (path reduction)
      if ($v_list_dir[$i] == '') {
        $i++;
        continue;
      }
      if ($v_list_path[$j] == '') {
        $j++;
        continue;
      }

      // ----- Compare the items
      if (($v_list_dir[$i] != $v_list_path[$j]) && ($v_list_dir[$i] != '') && ( $v_list_path[$j] != ''))  {
        //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Items ($i,$j) are different");
        $v_result = 0;
      }

      // ----- Next items
      $i++;
      $j++;
    }

    // ----- Look if everything seems to be the same
    if ($v_result) {
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Look for tie break");
      // ----- Skip all the empty items
      while (($j < $v_list_path_size) && ($v_list_path[$j] == '')) $j++;
      while (($i < $v_list_dir_size) && ($v_list_dir[$i] == '')) $i++;
      //--(MAGIC-PclTrace)--//PclTraceFctMessage(__FILE__, __LINE__, 5, "Looking on dir($i)='".($i < $v_list_dir_size?$v_list_dir[$i]:'')."' and path($j)='".($j < $v_list_path_size?$v_list_path[$j]:'')."'");

      if (($i >= $v_list_dir_size) && ($j >= $v_list_path_size)) {
        // ----- There are exactly the same
        $v_result = 2;
      }
      else if ($i < $v_list_dir_size) {
        // ----- The path is shorter than the dir
        $v_result = 0;
      }
    }

    // ----- Return
    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclZipUtilOptionText()
  // Description :
  //   Translate option value in text. Mainly for debug purpose.
  // Parameters :
  //   $p_option : the option value.
  // Return Values :
  //   The option text value.
  // --------------------------------------------------------------------------------
  function PclZipUtilOptionText($p_option)
  {
    //--(MAGIC-PclTrace)--//PclTraceFctStart(__FILE__, __LINE__, "PclZipUtilOptionText", "option='".$p_option."'");
    
    $v_list = get_defined_constants();
    for (reset($v_list); $v_key = key($v_list); next($v_list)) {
	  $v_prefix = substr($v_key, 0, 10);
	  if ((   ($v_prefix == 'PCLZIP_OPT')
         || ($v_prefix == 'PCLZIP_CB_')
         || ($v_prefix == 'PCLZIP_ATT'))
	      && ($v_list[$v_key] == $p_option)) {
          //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_key);
          return $v_key;
	    }
    }
    
    $v_result = 'Unknown';

    //--(MAGIC-PclTrace)--//PclTraceFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclZipUtilTranslateWinPath()
  // Description :
  //   Translate windows path by replacing '\' by '/' and optionally removing
  //   drive letter.
  // Parameters :
  //   $p_path : path to translate.
  //   $p_remove_disk_letter : true | false
  // Return Values :
  //   The path translated.
  // --------------------------------------------------------------------------------
  function PclZipUtilTranslateWinPath($p_path, $p_remove_disk_letter=true)
  {
    if (stristr(php_uname(), 'windows')) {
      // ----- Look for potential disk letter
      if (($p_remove_disk_letter) && (($v_position = strpos($p_path, ':')) != false)) {
          $p_path = substr($p_path, $v_position+1);
      }
      // ----- Change potential windows directory separator
      if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0,1) == '\\')) {
          $p_path = strtr($p_path, '\\', '/');
      }
    }
    return $p_path;
  }
  // --------------------------------------------------------------------------------







































// --------------------------------------------------------------------------------
// PhpConcept Library - Tar Module 1.3
// --------------------------------------------------------------------------------
// License GNU/GPL - Vincent Blavet - August 2001
// http://www.phpconcept.net
// --------------------------------------------------------------------------------
//
// Presentation :
//   PclTar is a library that allow you to create a GNU TAR + GNU ZIP archive,
//   to add files or directories, to extract all the archive or a part of it.
//   So far tests show that the files generated by PclTar are readable by
//   gzip tools and WinZip application.
//
// Description :
//   See readme.txt (English & Fran�ais) and http://www.phpconcept.net
//
// Warning :
//   This library and the associated files are non commercial, non professional
//   work.
//   It should not have unexpected results. However if any damage is caused by
//   this software the author can not be responsible.
//   The use of this software is at the risk of the user.
//
// --------------------------------------------------------------------------------

// ----- Look for double include
if (!defined("PCL_TAR"))
{
  define( "PCL_TAR", 1 );

  // ----- Configuration variable
  // Theses values may be changed by the user of PclTar library
  if (!isset($g_pcltar_lib_dir))
    $g_pcltar_lib_dir = "lib";

  // ----- Error codes
  //   -1 : Unable to open file in binary write mode
  //   -2 : Unable to open file in binary read mode
  //   -3 : Invalid parameters
  //   -4 : File does not exist
  //   -5 : Filename is too long (max. 99)
  //   -6 : Not a valid tar file
  //   -7 : Invalid extracted file size
  //   -8 : Unable to create directory
  //   -9 : Invalid archive extension
  //  -10 : Invalid archive format
  //  -11 : Unable to delete file (unlink)
  //  -12 : Unable to rename file (rename)
  //  -13 : Invalid header checksum


// --------------------------------------------------------------------------------
// ***** UNDER THIS LINE NOTHING NEEDS TO BE MODIFIED *****
// --------------------------------------------------------------------------------

  // ----- Global variables
  $g_pcltar_version = "1.3";

  // ----- Extract extension type (.php3/.php/...)
  $g_pcltar_extension = substr(strrchr(basename($PATH_TRANSLATED), '.'), 1);

  // ----- Include other libraries
  // This library should be called by each script before the include of PhpZip
  // Library in order to limit the potential 'lib' directory path problem.
// NET2FTP
// Do not include the 2 other libraries again, they are already included below
//  if (!defined("PCLERROR_LIB"))
//  {
//    include($g_pcltar_lib_dir."/pclerror.lib.".$g_pcltar_extension);
//  }
//  if (!defined("PCLTRACE_LIB"))
//  {
//    include($g_pcltar_lib_dir."/pcltrace.lib.".$g_pcltar_extension);
//  }

  // --------------------------------------------------------------------------------
  // Function : PclTarList()
  // Description :
  //   Gives the list of all the files present in the tar archive $p_tarname.
  //   The list is the function result, it will be 0 on error.
  //   Depending on the $p_tarname extension (.tar, .tar.gz or .tgz) the
  //   function will determine the type of the archive.
  // Parameters :
  //   $p_tarname : Name of an existing tar file
  //   $p_mode : 'tar' or 'tgz', if not set, will be determined by $p_tarname extension
  // Return Values :
  //  0 on error (Use PclErrorCode() and PclErrorString() for more info)
  //  or
  //  An array containing file properties. Each file properties is an array of
  //  properties.
  //  The properties (array field names) are :
  //    filename, size, mode, uid, gid, mtime, typeflag, status
  //  Exemple : $v_list = PclTarList("my.tar");
  //            for ($i=0; $i<sizeof($v_list); $i++)
  //              echo "Filename :'".$v_list[$i][filename]."'<br>";
  // --------------------------------------------------------------------------------
  function PclTarList($p_tarname, $p_mode="")
  {
    TrFctStart(__FILE__, __LINE__, "PclTarList", "tar=$p_tarname, mode='$p_mode'");
    $v_result=1;

    // ----- Extract the tar format from the extension
    if (($p_mode == "") || (($p_mode!="tar") && ($p_mode!="tgz")))
    {
      if (($p_mode = PclTarHandleExtension($p_tarname)) == "")
      {
        // ----- Return
        TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
        return 0;
      }
    }

    // ----- Call the extracting fct
    $p_list = array();
    if (($v_result = PclTarHandleExtract($p_tarname, 0, $p_list, "list", "", $p_mode, "")) != 1)
    {
      unset($p_list);
      TrFctEnd(__FILE__, __LINE__, 0, PclErrorString());
      return(0);
    }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $p_list);
    return $p_list;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarExtract()
  // Description :
  //   Extract all the files present in the archive $p_tarname, in the directory
  //   $p_path. The relative path of the archived files are keep and become
  //   relative to $p_path.
  //   If a file with the same name already exists it will be replaced.
  //   If the path to the file does not exist, it will be created.
  //   Depending on the $p_tarname extension (.tar, .tar.gz or .tgz) the
  //   function will determine the type of the archive.
  // Parameters :
  //   $p_tarname : Name of an existing tar file.
  //   $p_path : Path where the files will be extracted. The files will use
  //             their memorized path from $p_path.
  //             If $p_path is "", files will be extracted in "./".
  //   $p_remove_path : Path to remove (from the file memorized path) while writing the
  //                    extracted files. If the path does not match the file path,
  //                    the file is extracted with its memorized path.
  //                    $p_path and $p_remove_path are commulative.
  //   $p_mode : 'tar' or 'tgz', if not set, will be determined by $p_tarname extension
  // Return Values :
  //   Same as PclTarList()
  // --------------------------------------------------------------------------------

  function PclTarExtract($p_tarname, $p_path="./", $p_remove_path="", $p_mode="")
  {
    TrFctStart(__FILE__, __LINE__, "PclTarExtract", "tar='$p_tarname', path='$p_path', remove_path='$p_remove_path', mode='$p_mode'");
    $v_result=1;

    // ----- Extract the tar format from the extension
    if (($p_mode == "") || (($p_mode!="tar") && ($p_mode!="tgz")))
    {
      if (($p_mode = PclTarHandleExtension($p_tarname)) == "")
      {
        // ----- Return
        TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
        return 0;
      }
    }

    // ----- Call the extracting fct
    if (($v_result = PclTarHandleExtract($p_tarname, 0, $p_list, "complete", $p_path, $v_tar_mode, $p_remove_path)) != 1)
    {
      TrFctEnd(__FILE__, __LINE__, 0, PclErrorString());
      return(0);
    }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $p_list);
    return $p_list;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarExtractList()
  // Description :
  //   Extract the files present in the archive $p_tarname and specified in
  //   $p_filelist, in the directory
  //   $p_path. The relative path of the archived files are keep and become
  //   relative to $p_path.
  //   If a directory is sp�cified in the list, all the files from this directory
  //   will be extracted.
  //   If a file with the same name already exists it will be replaced.
  //   If the path to the file does not exist, it will be created.
  //   Depending on the $p_tarname extension (.tar, .tar.gz or .tgz) the
  //   function will determine the type of the archive.
  // Parameters :
  //   $p_tarname : Name of an existing tar file
  //   $p_filelist : An array containing file or directory names, or
  //                 a string containing one filename or directory name, or
  //                 a string containing a list of filenames and/or directory
  //                 names separated by spaces.
  //   $p_path : Path where the files will be extracted. The files will use
  //             their memorized path from $p_path.
  //             If $p_path is "", files will be extracted in "./".
  //   $p_remove_path : Path to remove (from the file memorized path) while writing the
  //                    extracted files. If the path does not match the file path,
  //                    the file is extracted with its memorized path.
  //                    $p_path and $p_remove_path are commulative.
  //   $p_mode : 'tar' or 'tgz', if not set, will be determined by $p_tarname extension
  // Return Values :
  //   Same as PclTarList()
  // --------------------------------------------------------------------------------
  function PclTarExtractList($p_tarname, $p_filelist, $p_path="./", $p_remove_path="", $p_mode="")
  {
    TrFctStart(__FILE__, __LINE__, "PclTarExtractList", "tar=$p_tarname, list, path=$p_path, remove_path='$p_remove_path', mode='$p_mode'");
    $v_result=1;

    // ----- Extract the tar format from the extension
    if (($p_mode == "") || (($p_mode!="tar") && ($p_mode!="tgz")))
    {
      if (($p_mode = PclTarHandleExtension($p_tarname)) == "")
      {
        // ----- Return
        TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
        return 0;
      }
    }

    // ----- Look if the $p_filelist is really an array
    if (is_array($p_filelist))
    {
      // ----- Call the extracting fct
      if (($v_result = PclTarHandleExtract($p_tarname, $p_filelist, $p_list, "partial", $p_path, $v_tar_mode, $p_remove_path)) != 1)
      {
        TrFctEnd(__FILE__, __LINE__, 0, PclErrorString());
        return(0);
      }
    }

    // ----- Look if the $p_filelist is a string
    else if (is_string($p_filelist))
    {
      // ----- Create a list with the elements from the string
      $v_list = explode(" ", $p_filelist);

      // ----- Call the extracting fct
      if (($v_result = PclTarHandleExtract($p_tarname, $v_list, $p_list, "partial", $p_path, $v_tar_mode, $p_remove_path)) != 1)
      {
        TrFctEnd(__FILE__, __LINE__, 0, PclErrorString());
        return(0);
      }
    }

    // ----- Invalid variable
    else
    {
      // ----- Error log
      PclErrorLog(-3, "Invalid variable type p_filelist");

      // ----- Return
      TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
      return 0;
    }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $p_list);
    return $p_list;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarExtractIndex()
  // Description :
  //   Extract the files present in the archive $p_tarname and specified at
  //   the indexes in $p_index, in the directory
  //   $p_path. The relative path of the archived files are keep and become
  //   relative to $p_path.
  //   If a directory is specified in the list, the directory only is created. All
  //   the file stored in this archive for this directory
  //   are not extracted.
  //   If a file with the same name already exists it will be replaced.
  //   If the path to the file does not exist, it will be created.
  //   Depending on the $p_tarname extension (.tar, .tar.gz or .tgz) the
  //   function will determine the type of the archive.
  // Parameters :
  //   $p_tarname : Name of an existing tar file
  //   $p_index : A single index (integer) or a string of indexes of files to
  //              extract. The form of the string is "0,4-6,8-12" with only numbers
  //              and '-' for range or ',' to separate ranges. No spaces or ';'
  //              are allowed.
  //   $p_path : Path where the files will be extracted. The files will use
  //             their memorized path from $p_path.
  //             If $p_path is "", files will be extracted in "./".
  //   $p_remove_path : Path to remove (from the file memorized path) while writing the
  //                    extracted files. If the path does not match the file path,
  //                    the file is extracted with its memorized path.
  //                    $p_path and $p_remove_path are commulative.
  //   $p_mode : 'tar' or 'tgz', if not set, will be determined by $p_tarname extension
  // Return Values :
  //   Same as PclTarList()
  // --------------------------------------------------------------------------------
  function PclTarExtractIndex($p_tarname, $p_index, $p_path="./", $p_remove_path="", $p_mode="")
  {
    TrFctStart(__FILE__, __LINE__, "PclTarExtractIndex", "tar=$p_tarname, index='$p_index', path=$p_path, remove_path='$p_remove_path', mode='$p_mode'");
    $v_result=1;

    // ----- Extract the tar format from the extension
    if (($p_mode == "") || (($p_mode!="tar") && ($p_mode!="tgz")))
    {
      if (($p_mode = PclTarHandleExtension($p_tarname)) == "")
      {
        // ----- Return
        TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
        return 0;
      }
    }

    // ----- Look if the $p_index is really an integer
    if (is_integer($p_index))
    {
      // ----- Call the extracting fct
      if (($v_result = PclTarHandleExtractByIndexList($p_tarname, "$p_index", $p_list, $p_path, $p_remove_path, $v_tar_mode)) != 1)
      {
        TrFctEnd(__FILE__, __LINE__, 0, PclErrorString());
        return(0);
      }
    }

    // ----- Look if the $p_filelist is a string
    else if (is_string($p_index))
    {
      // ----- Call the extracting fct
      if (($v_result = PclTarHandleExtractByIndexList($p_tarname, $p_index, $p_list, $p_path, $p_remove_path, $v_tar_mode)) != 1)
      {
        TrFctEnd(__FILE__, __LINE__, 0, PclErrorString());
        return(0);
      }
    }

    // ----- Invalid variable
    else
    {
      // ----- Error log
      PclErrorLog(-3, "Invalid variable type $p_index");

      // ----- Return
      TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
      return 0;
    }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $p_list);
    return $p_list;
  }
  // --------------------------------------------------------------------------------

// --------------------------------------------------------------------------------
// ***** UNDER THIS LINE ARE DEFINED PRIVATE INTERNAL FUNCTIONS *****
// *****                                                        *****
// *****       THESES FUNCTIONS MUST NOT BE USED DIRECTLY       *****
// --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarHandleExtract()
  // Description :
  // Parameters :
  //   $p_tarname : Filename of the tar (or tgz) archive
  //   $p_file_list : An array which contains the list of files to extract, this
  //                  array may be empty when $p_mode is 'complete'
  //   $p_list_detail : An array where will be placed the properties of  each extracted/listed file
  //   $p_mode : 'complete' will extract all files from the archive,
  //             'partial' will look for files in $p_file_list
  //             'list' will only list the files from the archive without any extract
  //   $p_path : Path to add while writing the extracted files
  //   $p_tar_mode : 'tar' for GNU TAR archive, 'tgz' for compressed archive
  //   $p_remove_path : Path to remove (from the file memorized path) while writing the
  //                    extracted files. If the path does not match the file path,
  //                    the file is extracted with its memorized path.
  //                    $p_remove_path does not apply to 'list' mode.
  //                    $p_path and $p_remove_path are commulative.
  // Return Values :
  // --------------------------------------------------------------------------------
  function PclTarHandleExtract($p_tarname, $p_file_list, &$p_list_detail, $p_mode, $p_path, $p_tar_mode, $p_remove_path)
  {
    TrFctStart(__FILE__, __LINE__, "PclTarHandleExtract", "archive='$p_tarname', list, mode=$p_mode, path=$p_path, tar_mode=$p_tar_mode, remove_path='$p_remove_path'");
    $v_result=1;
    $v_nb = 0;
    $v_extract_all = TRUE;
    $v_listing = FALSE;

    // ----- Check the path
    if (($p_path == "") || ((substr($p_path, 0, 1) != "/") && (substr($p_path, 0, 3) != "../")))
      $p_path = "./".$p_path;

    // ----- Look for path to remove format (should end by /)
    if (($p_remove_path != "") && (substr($p_remove_path, -1) != '/'))
    {
      $p_remove_path .= '/';
    }
    $p_remove_path_size = strlen($p_remove_path);

    // ----- Study the mode
    switch ($p_mode) {
      case "complete" :
        // ----- Flag extract of all files
        $v_extract_all = TRUE;
        $v_listing = FALSE;
      break;
      case "partial" :
          // ----- Flag extract of specific files
          $v_extract_all = FALSE;
          $v_listing = FALSE;
      break;
      case "list" :
          // ----- Flag list of all files
          $v_extract_all = FALSE;
          $v_listing = TRUE;
      break;
      default :
        // ----- Error log
        PclErrorLog(-3, "Invalid extract mode ($p_mode)");

        // ----- Return
        TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
        return PclErrorCode();
    }

    // ----- Open the tar file
    if ($p_tar_mode == "tar")
    {
      TrFctMessage(__FILE__, __LINE__, 3, "Open file in binary read mode");
      $v_tar = fopen($p_tarname, "rb");
    }
    else
    {
      TrFctMessage(__FILE__, __LINE__, 3, "Open file in gzip binary read mode");
      $v_tar = @gzopen($p_tarname, "rb");
    }

    // ----- Check that the archive is open
    if ($v_tar == 0)
    {
      // ----- Error log
      PclErrorLog(-2, "Unable to open archive '$p_tarname' in binary read mode");

      // ----- Return
      TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
      return PclErrorCode();
    }

    // ----- Read the blocks
    While (!($v_end_of_file = ($p_tar_mode == "tar"?feof($v_tar):gzeof($v_tar))))
    {
      TrFctMessage(__FILE__, __LINE__, 3, "Looking for next header ...");

      // ----- Clear cache of file infos
      clearstatcache();

      // ----- Reset extract tag
      $v_extract_file = FALSE;
      $v_extraction_stopped = 0;

      // ----- Read the 512 bytes header
      if ($p_tar_mode == "tar")
        $v_binary_data = fread($v_tar, 512);
      else
        $v_binary_data = gzread($v_tar, 512);

      // ----- Read the header properties
      if (($v_result = PclTarHandleReadHeader($v_binary_data, $v_header)) != 1)
      {
        // ----- Close the archive file
        if ($p_tar_mode == "tar")
          fclose($v_tar);
        else
          gzclose($v_tar);

        // ----- Return
        TrFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }

      // ----- Look for empty blocks to skip
      if ($v_header[filename] == "")
      {
        TrFctMessage(__FILE__, __LINE__, 2, "Empty block found. End of archive ?");
        continue;
      }

      TrFctMessage(__FILE__, __LINE__, 2, "Found file '$v_header[filename]', size '$v_header[size]'");

      // ----- Look for partial extract
      if ((!$v_extract_all) && (is_array($p_file_list)))
      {
        TrFctMessage(__FILE__, __LINE__, 2, "Look if the file '$v_header[filename]' need to be extracted");

        // ----- By default no unzip if the file is not found
        $v_extract_file = FALSE;

        // ----- Look into the file list
        for ($i=0; $i<sizeof($p_file_list); $i++)
        {
          TrFctMessage(__FILE__, __LINE__, 2, "Compare archived file '$v_header[filename]' from asked list file '".$p_file_list[$i]."'");

          // ----- Look if it is a directory
          if (substr($p_file_list[$i], -1) == "/")
          {
            TrFctMessage(__FILE__, __LINE__, 3, "Compare file '$v_header[filename]' with directory '$p_file_list[$i]'");

            // ----- Look if the directory is in the filename path
            if ((strlen($v_header[filename]) > strlen($p_file_list[$i])) && (substr($v_header[filename], 0, strlen($p_file_list[$i])) == $p_file_list[$i]))
            {
              // ----- The file is in the directory, so extract it
              TrFctMessage(__FILE__, __LINE__, 2, "File '$v_header[filename]' is in directory '$p_file_list[$i]' : extract it");
              $v_extract_file = TRUE;

              // ----- End of loop
              break;
            }
          }

          // ----- It is a file, so compare the file names
          else if ($p_file_list[$i] == $v_header[filename])
          {
            // ----- File found
            TrFctMessage(__FILE__, __LINE__, 2, "File '$v_header[filename]' should be extracted");
            $v_extract_file = TRUE;

            // ----- End of loop
            break;
          }
        }

        // ----- Trace
        if (!$v_extract_file)
        {
          TrFctMessage(__FILE__, __LINE__, 2, "File '$v_header[filename]' should not be extracted");
        }
      }
      else
      {
        // ----- All files need to be extracted
        $v_extract_file = TRUE;
      }

      // ----- Look if this file need to be extracted
      if (($v_extract_file) && (!$v_listing))
      {
        // ----- Look for path to remove
        if (($p_remove_path != "")
            && (substr($v_header[filename], 0, $p_remove_path_size) == $p_remove_path))
        {
          TrFctMessage(__FILE__, __LINE__, 3, "Found path '$p_remove_path' to remove in file '$v_header[filename]'");
          // ----- Remove the path
          $v_header[filename] = substr($v_header[filename], $p_remove_path_size);
          TrFctMessage(__FILE__, __LINE__, 3, "Reslting file is '$v_header[filename]'");
        }

        // ----- Add the path to the file
        if (($p_path != "./") && ($p_path != "/"))
        {
          // ----- Look for the path end '/'
          while (substr($p_path, -1) == "/")
          {
            TrFctMessage(__FILE__, __LINE__, 3, "Destination path [$p_path] ends by '/'");
            $p_path = substr($p_path, 0, strlen($p_path)-1);
            TrFctMessage(__FILE__, __LINE__, 3, "Modified to [$p_path]");
          }

          // ----- Add the path
          if (substr($v_header[filename], 0, 1) == "/")
              $v_header[filename] = $p_path.$v_header[filename];
          else
            $v_header[filename] = $p_path."/".$v_header[filename];
        }

        // ----- Trace
        TrFctMessage(__FILE__, __LINE__, 2, "Extracting file (with path) '$v_header[filename]', size '$v_header[size]'");

        // ----- Check that the file does not exists
        if (file_exists($v_header[filename]))
        {
          TrFctMessage(__FILE__, __LINE__, 2, "File '$v_header[filename]' already exists");

          // ----- Look if file is a directory
          if (is_dir($v_header[filename]))
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Existing file '$v_header[filename]' is a directory");

            // ----- Change the file status
            $v_header[status] = "already_a_directory";

            // ----- Skip the extract
            $v_extraction_stopped = 1;
            $v_extract_file = 0;
          }
          // ----- Look if file is write protected
          else if (!is_writeable($v_header[filename]))
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Existing file '$v_header[filename]' is write protected");

            // ----- Change the file status
            $v_header[status] = "write_protected";

            // ----- Skip the extract
            $v_extraction_stopped = 1;
            $v_extract_file = 0;
          }
          // ----- Look if the extracted file is older
          else if (filemtime($v_header[filename]) > $v_header[mtime])
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Existing file '$v_header[filename]' is newer (".date("l dS of F Y h:i:s A", filemtime($v_header[filename])).") than the extracted file (".date("l dS of F Y h:i:s A", $v_header[mtime]).")");

            // ----- Change the file status
            $v_header[status] = "newer_exist";

            // ----- Skip the extract
            $v_extraction_stopped = 1;
            $v_extract_file = 0;
          }
        }

        // ----- Check the directory availability and create it if necessary
        else
        {
          if ($v_header[typeflag]=="5")
            $v_dir_to_check = $v_header[filename];
          else if (!strstr($v_header[filename], "/"))
            $v_dir_to_check = "";
          else
            $v_dir_to_check = dirname($v_header[filename]);

          if (($v_result = PclTarHandlerDirCheck($v_dir_to_check)) != 1)
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Unable to create path for '$v_header[filename]'");

            // ----- Change the file status
            $v_header[status] = "path_creation_fail";

            // ----- Skip the extract
            $v_extraction_stopped = 1;
            $v_extract_file = 0;
          }
        }

        // ----- Do the extraction
        if (($v_extract_file) && ($v_header[typeflag]!="5"))
        {
          // ----- Open the destination file in write mode
          if (($v_dest_file = @fopen($v_header[filename], "wb")) == 0)
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Error while opening '$v_header[filename]' in write binary mode");

            // ----- Change the file status
            $v_header[status] = "write_error";

            // ----- Jump to next file
            TrFctMessage(__FILE__, __LINE__, 2, "Jump to next file");
            if ($p_tar_mode == "tar")
              fseek($v_tar, ftell($v_tar)+(ceil(($v_header[size]/512))*512));
            else
              gzseek($v_tar, gztell($v_tar)+(ceil(($v_header[size]/512))*512));
          }
          else
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Start extraction of '$v_header[filename]'");

            // ----- Read data
            $n = floor($v_header[size]/512);
            for ($i=0; $i<$n; $i++)
            {
              TrFctMessage(__FILE__, __LINE__, 3, "Read complete 512 bytes block number ".($i+1));
              if ($p_tar_mode == "tar")
                $v_content = fread($v_tar, 512);
              else
                $v_content = gzread($v_tar, 512);
              fwrite($v_dest_file, $v_content, 512);
            }
            if (($v_header[size] % 512) != 0)
            {
              TrFctMessage(__FILE__, __LINE__, 3, "Read last ".($v_header[size] % 512)." bytes in a 512 block");
              if ($p_tar_mode == "tar")
                $v_content = fread($v_tar, 512);
              else
                $v_content = gzread($v_tar, 512);
              fwrite($v_dest_file, $v_content, ($v_header[size] % 512));
            }

            // ----- Close the destination file
            fclose($v_dest_file);

            // ----- Change the file mode, mtime
            touch($v_header[filename], $v_header[mtime]);
            //chmod($v_header[filename], DecOct($v_header[mode]));
          }

          // ----- Check the file size
          clearstatcache();
          if (filesize($v_header[filename]) != $v_header[size])
          {
            // ----- Close the archive file
            if ($p_tar_mode == "tar")
              fclose($v_tar);
            else
              gzclose($v_tar);

            // ----- Error log
            PclErrorLog(-7, "Extracted file '$v_header[filename]' does not have the correct file size '".filesize($v_filename)."' ('$v_header[size]' expected). Archive may be corrupted.");

            // ----- Return
            TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
            return PclErrorCode();
          }

          // ----- Trace
          TrFctMessage(__FILE__, __LINE__, 2, "Extraction done");
        }

        else
        {
          TrFctMessage(__FILE__, __LINE__, 2, "Extraction of file '$v_header[filename]' skipped.");

          // ----- Jump to next file
          TrFctMessage(__FILE__, __LINE__, 2, "Jump to next file");
          if ($p_tar_mode == "tar")
            fseek($v_tar, ftell($v_tar)+(ceil(($v_header[size]/512))*512));
          else
            gzseek($v_tar, gztell($v_tar)+(ceil(($v_header[size]/512))*512));
        }
      }

      // ----- Look for file that is not to be unzipped
      else
      {
        // ----- Trace
        TrFctMessage(__FILE__, __LINE__, 2, "Jump file '$v_header[filename]'");
        TrFctMessage(__FILE__, __LINE__, 4, "Position avant jump [".($p_tar_mode=="tar"?ftell($v_tar):gztell($v_tar))."]");

        // ----- Jump to next file
        if ($p_tar_mode == "tar")
          fseek($v_tar, ($p_tar_mode=="tar"?ftell($v_tar):gztell($v_tar))+(ceil(($v_header[size]/512))*512));
        else
          gzseek($v_tar, gztell($v_tar)+(ceil(($v_header[size]/512))*512));

        TrFctMessage(__FILE__, __LINE__, 4, "Position apr�s jump [".($p_tar_mode=="tar"?ftell($v_tar):gztell($v_tar))."]");
      }

      if ($p_tar_mode == "tar")
        $v_end_of_file = feof($v_tar);
      else
        $v_end_of_file = gzeof($v_tar);

      // ----- File name and properties are logged if listing mode or file is extracted
      if ($v_listing || $v_extract_file || $v_extraction_stopped)
      {
        TrFctMessage(__FILE__, __LINE__, 2, "Memorize info about file '$v_header[filename]'");

        // ----- Log extracted files
        if (($v_file_dir = dirname($v_header[filename])) == $v_header[filename])
          $v_file_dir = "";
        if ((substr($v_header[filename], 0, 1) == "/") && ($v_file_dir == ""))
          $v_file_dir = "/";

        // ----- Add the array describing the file into the list
        $p_list_detail[$v_nb] = $v_header;

        // ----- Increment
        $v_nb++;
      }
    }

    // ----- Close the tarfile
    if ($p_tar_mode == "tar")
      fclose($v_tar);
    else
      gzclose($v_tar);

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarHandleExtractByIndexList()
  // Description :
  //   Extract the files which are at the indexes specified. If the 'file' at the
  //   index is a directory, the directory only is created, not all the files stored
  //   for that directory.
  // Parameters :
  //   $p_index_string : String of indexes of files to extract. The form of the
  //                     string is "0,4-6,8-12" with only numbers and '-' for
  //                     for range, and ',' to separate ranges. No spaces or ';'
  //                     are allowed.
  // Return Values :
  // --------------------------------------------------------------------------------
  function PclTarHandleExtractByIndexList($p_tarname, $p_index_string, &$p_list_detail, $p_path, $p_remove_path, $p_tar_mode)
  {
    TrFctStart(__FILE__, __LINE__, "PclTarHandleExtractByIndexList", "archive='$p_tarname', index_string='$p_index_string', list, path=$p_path, remove_path='$p_remove_path', tar_mode=$p_tar_mode");
    $v_result=1;
    $v_nb = 0;

    // ----- TBC : I should check the string by a regexp

    // ----- Check the path
    if (($p_path == "") || ((substr($p_path, 0, 1) != "/") && (substr($p_path, 0, 3) != "../") && (substr($p_path, 0, 2) != "./")))
      $p_path = "./".$p_path;

    // ----- Look for path to remove format (should end by /)
    if (($p_remove_path != "") && (substr($p_remove_path, -1) != '/'))
    {
      $p_remove_path .= '/';
    }
    $p_remove_path_size = strlen($p_remove_path);

    // ----- Open the tar file
    if ($p_tar_mode == "tar")
    {
      TrFctMessage(__FILE__, __LINE__, 3, "Open file in binary read mode");
      $v_tar = @fopen($p_tarname, "rb");
    }
    else
    {
      TrFctMessage(__FILE__, __LINE__, 3, "Open file in gzip binary read mode");
      $v_tar = @gzopen($p_tarname, "rb");
    }

    // ----- Check that the archive is open
    if ($v_tar == 0)
    {
      // ----- Error log
      PclErrorLog(-2, "Unable to open archive '$p_tarname' in binary read mode");

      // ----- Return
      TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
      return PclErrorCode();
    }

    // ----- Manipulate the index list
    $v_list = explode(",", $p_index_string);
    sort($v_list);

    // ----- Loop on the index list
    $v_index=0;
    for ($i=0; ($i<sizeof($v_list)) && ($v_result); $i++)
    {
      TrFctMessage(__FILE__, __LINE__, 3, "Looking for index part '$v_list[$i]'");

      // ----- Extract range
      $v_index_list = explode("-", $v_list[$i]);
      $v_size_index_list = sizeof($v_index_list);
      if ($v_size_index_list == 1)
      {
        TrFctMessage(__FILE__, __LINE__, 3, "Only one index '$v_index_list[0]'");

        // ----- Do the extraction
        $v_result = PclTarHandleExtractByIndex($v_tar, $v_index, $v_index_list[0], $v_index_list[0], $p_list_detail, $p_path, $p_remove_path, $p_tar_mode);
      }
      else if ($v_size_index_list == 2)
      {
        TrFctMessage(__FILE__, __LINE__, 3, "Two indexes '$v_index_list[0]' and '$v_index_list[1]'");

        // ----- Do the extraction
        $v_result = PclTarHandleExtractByIndex($v_tar, $v_index, $v_index_list[0], $v_index_list[1], $p_list_detail, $p_path, $p_remove_path, $p_tar_mode);
      }
    }

    // ----- Close the tarfile
    if ($p_tar_mode == "tar")
      fclose($v_tar);
    else
      gzclose($v_tar);

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarHandleExtractByIndex()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function PclTarHandleExtractByIndex($p_tar, &$p_index_current, $p_index_start, $p_index_stop, &$p_list_detail, $p_path, $p_remove_path, $p_tar_mode)
  {
    TrFctStart(__FILE__, __LINE__, "PclTarHandleExtractByIndex", "archive_descr='$p_tar', index_current=$p_index_current, index_start='$p_index_start', index_stop='$p_index_stop', list, path=$p_path, remove_path='$p_remove_path', tar_mode=$p_tar_mode");
    $v_result=1;
    $v_nb = 0;

    // TBC : I should replace all $v_tar by $p_tar in this function ....
    $v_tar = $p_tar;

    // ----- Look the number of elements already in $p_list_detail
    $v_nb = sizeof($p_list_detail);

    // ----- Read the blocks
    While (!($v_end_of_file = ($p_tar_mode == "tar"?feof($v_tar):gzeof($v_tar))))
    {
      TrFctMessage(__FILE__, __LINE__, 3, "Looking for next file ...");
      TrFctMessage(__FILE__, __LINE__, 3, "Index current=$p_index_current, range=[$p_index_start, $p_index_stop])");

      if ($p_index_current > $p_index_stop)
      {
        TrFctMessage(__FILE__, __LINE__, 2, "Stop extraction, past stop index");
        break;
      }

      // ----- Clear cache of file infos
      clearstatcache();

      // ----- Reset extract tag
      $v_extract_file = FALSE;
      $v_extraction_stopped = 0;

      // ----- Read the 512 bytes header
      if ($p_tar_mode == "tar")
        $v_binary_data = fread($v_tar, 512);
      else
        $v_binary_data = gzread($v_tar, 512);

      // ----- Read the header properties
      if (($v_result = PclTarHandleReadHeader($v_binary_data, $v_header)) != 1)
      {
        // ----- Return
        TrFctEnd(__FILE__, __LINE__, $v_result);
        return $v_result;
      }

      // ----- Look for empty blocks to skip
      if ($v_header[filename] == "")
      {
        TrFctMessage(__FILE__, __LINE__, 2, "Empty block found. End of archive ?");
        continue;
      }

      TrFctMessage(__FILE__, __LINE__, 2, "Found file '$v_header[filename]', size '$v_header[size]'");

      // ----- Look if file is in the range to be extracted
      if (($p_index_current >= $p_index_start) && ($p_index_current <= $p_index_stop))
      {
        TrFctMessage(__FILE__, __LINE__, 2, "File '$v_header[filename]' is in the range to be extracted");
        $v_extract_file = TRUE;
      }
      else
      {
        TrFctMessage(__FILE__, __LINE__, 2, "File '$v_header[filename]' is out of the range");
        $v_extract_file = FALSE;
      }

      // ----- Look if this file need to be extracted
      if ($v_extract_file)
      {
        if (($v_result = PclTarHandleExtractFile($v_tar, $v_header, $p_path, $p_remove_path, $p_tar_mode)) != 1)
        {
          // ----- Return
          TrFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }

      // ----- Look for file that is not to be extracted
      else
      {
        // ----- Trace
        TrFctMessage(__FILE__, __LINE__, 2, "Jump file '$v_header[filename]'");
        TrFctMessage(__FILE__, __LINE__, 4, "Position avant jump [".($p_tar_mode=="tar"?ftell($v_tar):gztell($v_tar))."]");

        // ----- Jump to next file
        if ($p_tar_mode == "tar")
          fseek($v_tar, ($p_tar_mode=="tar"?ftell($v_tar):gztell($v_tar))+(ceil(($v_header[size]/512))*512));
        else
          gzseek($v_tar, gztell($v_tar)+(ceil(($v_header[size]/512))*512));

        TrFctMessage(__FILE__, __LINE__, 4, "Position apr�s jump [".($p_tar_mode=="tar"?ftell($v_tar):gztell($v_tar))."]");
      }

      if ($p_tar_mode == "tar")
        $v_end_of_file = feof($v_tar);
      else
        $v_end_of_file = gzeof($v_tar);

      // ----- File name and properties are logged if listing mode or file is extracted
      if ($v_extract_file)
      {
        TrFctMessage(__FILE__, __LINE__, 2, "Memorize info about file '$v_header[filename]'");

        // ----- Log extracted files
        if (($v_file_dir = dirname($v_header[filename])) == $v_header[filename])
          $v_file_dir = "";
        if ((substr($v_header[filename], 0, 1) == "/") && ($v_file_dir == ""))
          $v_file_dir = "/";

        // ----- Add the array describing the file into the list
        $p_list_detail[$v_nb] = $v_header;

        // ----- Increment
        $v_nb++;
      }

      // ----- Increment the current file index
      $p_index_current++;
    }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarHandleExtractFile()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function PclTarHandleExtractFile($p_tar, &$v_header, $p_path, $p_remove_path, $p_tar_mode)
  {
    TrFctStart(__FILE__, __LINE__, "PclTarHandleExtractFile", "archive_descr='$p_tar', path=$p_path, remove_path='$p_remove_path', tar_mode=$p_tar_mode");
    $v_result=1;

    // TBC : I should replace all $v_tar by $p_tar in this function ....
    $v_tar = $p_tar;
    $v_extract_file = 1;

    $p_remove_path_size = strlen($p_remove_path);

        // ----- Look for path to remove
        if (($p_remove_path != "")
            && (substr($v_header[filename], 0, $p_remove_path_size) == $p_remove_path))
        {
          TrFctMessage(__FILE__, __LINE__, 3, "Found path '$p_remove_path' to remove in file '$v_header[filename]'");
          // ----- Remove the path
          $v_header[filename] = substr($v_header[filename], $p_remove_path_size);
          TrFctMessage(__FILE__, __LINE__, 3, "Resulting file is '$v_header[filename]'");
        }

        // ----- Add the path to the file
        if (($p_path != "./") && ($p_path != "/"))
        {
          // ----- Look for the path end '/'
          while (substr($p_path, -1) == "/")
          {
            TrFctMessage(__FILE__, __LINE__, 3, "Destination path [$p_path] ends by '/'");
            $p_path = substr($p_path, 0, strlen($p_path)-1);
            TrFctMessage(__FILE__, __LINE__, 3, "Modified to [$p_path]");
          }

          // ----- Add the path
          if (substr($v_header[filename], 0, 1) == "/")
              $v_header[filename] = $p_path.$v_header[filename];
          else
            $v_header[filename] = $p_path."/".$v_header[filename];
        }

        // ----- Trace
        TrFctMessage(__FILE__, __LINE__, 2, "Extracting file (with path) '$v_header[filename]', size '$v_header[size]'");

        // ----- Check that the file does not exists
        if (file_exists($v_header[filename]))
        {

          TrFctMessage(__FILE__, __LINE__, 2, "File '$v_header[filename]' already exists");

          // ----- Look if file is a directory
          if (is_dir($v_header[filename]))
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Existing file '$v_header[filename]' is a directory");

            // ----- Change the file status
            $v_header[status] = "already_a_directory";

            // ----- Skip the extract
            $v_extraction_stopped = 1;
            $v_extract_file = 0;
          }
          // ----- Look if file is write protected
          else if (!is_writeable($v_header[filename]))
          if (!is_writeable($v_header[filename]))
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Existing file '$v_header[filename]' is write protected");

            // ----- Change the file status
            $v_header[status] = "write_protected";

            // ----- Skip the extract
            $v_extraction_stopped = 1;
            $v_extract_file = 0;
          }

          // ----- Look if the extracted file is older
          else if (filemtime($v_header[filename]) > $v_header[mtime])
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Existing file '$v_header[filename]' is newer (".date("l dS of F Y h:i:s A", filemtime($v_header[filename])).") than the extracted file (".date("l dS of F Y h:i:s A", $v_header[mtime]).")");

            // ----- Change the file status
            $v_header[status] = "newer_exist";

            // ----- Skip the extract
            $v_extraction_stopped = 1;
            $v_extract_file = 0;
          }
        }

        // ----- Check the directory availability and create it if necessary
        else
        {
          if ($v_header[typeflag]=="5")
            $v_dir_to_check = $v_header[filename];
          else if (!strstr($v_header[filename], "/"))
            $v_dir_to_check = "";
          else
            $v_dir_to_check = dirname($v_header[filename]);

          if (($v_result = PclTarHandlerDirCheck($v_dir_to_check)) != 1)
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Unable to create path for '$v_header[filename]'");

            // ----- Change the file status
            $v_header[status] = "path_creation_fail";

            // ----- Skip the extract
            $v_extraction_stopped = 1;
            $v_extract_file = 0;
          }
        }

        // ----- Do the real bytes extraction (if not a directory)
        if (($v_extract_file) && ($v_header[typeflag]!="5"))
        {
          // ----- Open the destination file in write mode
          if (($v_dest_file = @fopen($v_header[filename], "wb")) == 0)
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Error while opening '$v_header[filename]' in write binary mode");

            // ----- Change the file status
            $v_header[status] = "write_error";

            // ----- Jump to next file
            TrFctMessage(__FILE__, __LINE__, 2, "Jump to next file");
            if ($p_tar_mode == "tar")
              fseek($v_tar, ftell($v_tar)+(ceil(($v_header[size]/512))*512));
            else
              gzseek($v_tar, gztell($v_tar)+(ceil(($v_header[size]/512))*512));
          }
          else
          {
            TrFctMessage(__FILE__, __LINE__, 2, "Start extraction of '$v_header[filename]'");

            // ----- Read data
            $n = floor($v_header[size]/512);
            for ($i=0; $i<$n; $i++)
            {
              TrFctMessage(__FILE__, __LINE__, 3, "Read complete 512 bytes block number ".($i+1));
              if ($p_tar_mode == "tar")
                $v_content = fread($v_tar, 512);
              else
                $v_content = gzread($v_tar, 512);
              fwrite($v_dest_file, $v_content, 512);
            }
            if (($v_header[size] % 512) != 0)
            {
              TrFctMessage(__FILE__, __LINE__, 3, "Read last ".($v_header[size] % 512)." bytes in a 512 block");
              if ($p_tar_mode == "tar")
                $v_content = fread($v_tar, 512);
              else
                $v_content = gzread($v_tar, 512);
              fwrite($v_dest_file, $v_content, ($v_header[size] % 512));
            }

            // ----- Close the destination file
            fclose($v_dest_file);

            // ----- Change the file mode, mtime
            touch($v_header[filename], $v_header[mtime]);
            //chmod($v_header[filename], DecOct($v_header[mode]));
          }

          // ----- Check the file size
          clearstatcache();

          if (filesize($v_header[filename]) != $v_header[size])
          {
            // ----- Error log
            PclErrorLog(-7, "Extracted file '$v_header[filename]' does not have the correct file size '".filesize($v_filename)."' ('$v_header[size]' expected). Archive may be corrupted.");

            // ----- Return
            TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
            return PclErrorCode();
          }

          // ----- Trace
          TrFctMessage(__FILE__, __LINE__, 2, "Extraction done");
        }
        else
        {
          TrFctMessage(__FILE__, __LINE__, 2, "Extraction of file '$v_header[filename]' skipped.");

          // ----- Jump to next file
          TrFctMessage(__FILE__, __LINE__, 2, "Jump to next file");
          if ($p_tar_mode == "tar")
            fseek($v_tar, ftell($v_tar)+(ceil(($v_header[size]/512))*512));
          else
            gzseek($v_tar, gztell($v_tar)+(ceil(($v_header[size]/512))*512));
        }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarHandleReadHeader()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function PclTarHandleReadHeader($v_binary_data, &$v_header)
  {
    TrFctStart(__FILE__, __LINE__, "PclTarHandleReadHeader", "");
    $v_result=1;

    // ----- Read the 512 bytes header
    /*
    if ($p_tar_mode == "tar")
      $v_binary_data = fread($p_tar, 512);
    else
      $v_binary_data = gzread($p_tar, 512);
    */

    // ----- Look for no more block
    if (strlen($v_binary_data)==0)
    {
      $v_header[filename] = "";
      $v_header[status] = "empty";

      // ----- Return
      TrFctEnd(__FILE__, __LINE__, $v_result, "End of archive found");
      return $v_result;
    }

    // ----- Look for invalid block size
    if (strlen($v_binary_data) != 512)
    {
      $v_header[filename] = "";
      $v_header[status] = "invalid_header";
      TrFctMessage(__FILE__, __LINE__, 2, "Invalid block size : ".strlen($v_binary_data));

      // ----- Error log
      PclErrorLog(-10, "Invalid block size : ".strlen($v_binary_data));

      // ----- Return
      TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
      return PclErrorCode();
    }

    // ----- Calculate the checksum
    $v_checksum = 0;
    // ..... First part of the header
    for ($i=0; $i<148; $i++)
    {
      $v_checksum+=ord(substr($v_binary_data,$i,1));
    }
    // ..... Ignore the checksum value and replace it by ' ' (space)
    for ($i=148; $i<156; $i++)
    {
      $v_checksum += ord(' ');
    }
    // ..... Last part of the header
    for ($i=156; $i<512; $i++)
    {
      $v_checksum+=ord(substr($v_binary_data,$i,1));
    }
    TrFctMessage(__FILE__, __LINE__, 3, "Calculated checksum : $v_checksum");

    // ----- Extract the values
    TrFctMessage(__FILE__, __LINE__, 2, "Header : '$v_binary_data'");
    $v_data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor", $v_binary_data);

    // ----- Extract the checksum for check
    $v_header[checksum] = OctDec(trim($v_data[checksum]));
    TrFctMessage(__FILE__, __LINE__, 3, "File checksum : $v_header[checksum]");
    if ($v_header[checksum] != $v_checksum)
    {
      TrFctMessage(__FILE__, __LINE__, 2, "File checksum is invalid : $v_checksum calculated, $v_header[checksum] expected");

      $v_header[filename] = "";
      $v_header[status] = "invalid_header";

      // ----- Look for last block (empty block)
      if (($v_checksum == 256) && ($v_header[checksum] == 0))
      {
        $v_header[status] = "empty";
        // ----- Return
        TrFctEnd(__FILE__, __LINE__, $v_result, "End of archive found");
        return $v_result;
      }

      // ----- Error log
      PclErrorLog(-13, "Invalid checksum : $v_checksum calculated, $v_header[checksum] expected");

      // ----- Return
      TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
      return PclErrorCode();
    }
    TrFctMessage(__FILE__, __LINE__, 2, "File checksum is valid ($v_checksum)");

    // ----- Extract the properties
    $v_header[filename] = trim($v_data[filename]);
    TrFctMessage(__FILE__, __LINE__, 2, "Name : '$v_header[filename]'");
    $v_header[mode] = OctDec(trim($v_data[mode]));
    TrFctMessage(__FILE__, __LINE__, 2, "Mode : '".DecOct($v_header[mode])."'");
    $v_header[uid] = OctDec(trim($v_data[uid]));
    TrFctMessage(__FILE__, __LINE__, 2, "Uid : '$v_header[uid]'");
    $v_header[gid] = OctDec(trim($v_data[gid]));
    TrFctMessage(__FILE__, __LINE__, 2, "Gid : '$v_header[gid]'");
    $v_header[size] = OctDec(trim($v_data[size]));
    TrFctMessage(__FILE__, __LINE__, 2, "Size : '$v_header[size]'");
    $v_header[mtime] = OctDec(trim($v_data[mtime]));
    TrFctMessage(__FILE__, __LINE__, 2, "Date : ".date("l dS of F Y h:i:s A", $v_header[mtime]));
    if (($v_header[typeflag] = $v_data[typeflag]) == "5")
    {
      $v_header[size] = 0;
      TrFctMessage(__FILE__, __LINE__, 2, "Size (folder) : '$v_header[size]'");
    }
    TrFctMessage(__FILE__, __LINE__, 2, "File typeflag : $v_header[typeflag]");
    /* ----- All these fields are removed form the header because they do not carry interesting info
    $v_header[link] = trim($v_data[link]);
    TrFctMessage(__FILE__, __LINE__, 2, "Linkname : $v_header[linkname]");
    $v_header[magic] = trim($v_data[magic]);
    TrFctMessage(__FILE__, __LINE__, 2, "Magic : $v_header[magic]");
    $v_header[version] = trim($v_data[version]);
    TrFctMessage(__FILE__, __LINE__, 2, "Version : $v_header[version]");
    $v_header[uname] = trim($v_data[uname]);
    TrFctMessage(__FILE__, __LINE__, 2, "Uname : $v_header[uname]");
    $v_header[gname] = trim($v_data[gname]);
    TrFctMessage(__FILE__, __LINE__, 2, "Gname : $v_header[gname]");
    $v_header[devmajor] = trim($v_data[devmajor]);
    TrFctMessage(__FILE__, __LINE__, 2, "Devmajor : $v_header[devmajor]");
    $v_header[devminor] = trim($v_data[devminor]);
    TrFctMessage(__FILE__, __LINE__, 2, "Devminor : $v_header[devminor]");
    */

    // ----- Set the status field
    $v_header[status] = "ok";

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarHandlerDirCheck()
  // Description :
  //   Check if a directory exists, if not it creates it and all the parents directory
  //   which may be useful.
  // Parameters :
  //   $p_dir : Directory path to check (without / at the end).
  // Return Values :
  //    1 : OK
  //   -1 : Unable to create directory
  // --------------------------------------------------------------------------------
  function PclTarHandlerDirCheck($p_dir)
  {
    $v_result = 1;

    TrFctStart(__FILE__, __LINE__, "PclTarHandlerDirCheck", "$p_dir");

    // ----- Check the directory availability
    if ((is_dir($p_dir)) || ($p_dir == ""))
    {
      TrFctEnd(__FILE__, __LINE__, "'$p_dir' is a directory");
      return 1;
    }

    // ----- Look for file alone
    /*
    if (!strstr("$p_dir", "/"))
    {
      TrFctEnd(__FILE__, __LINE__,  "'$p_dir' is a file with no directory");
      return 1;
    }
    */

    // ----- Extract parent directory
    $p_parent_dir = dirname($p_dir);
    TrFctMessage(__FILE__, __LINE__, 3, "Parent directory is '$p_parent_dir'");

    // ----- Just a check
    if ($p_parent_dir != $p_dir)
    {
      // ----- Look for parent directory
      if ($p_parent_dir != "")
      {
        if (($v_result = PclTarHandlerDirCheck($p_parent_dir)) != 1)
        {
          TrFctEnd(__FILE__, __LINE__, $v_result);
          return $v_result;
        }
      }
    }

    // ----- Create the directory
    TrFctMessage(__FILE__, __LINE__, 3, "Create directory '$p_dir'");
    if (!@mkdir($p_dir, 0777))
    {
      // ----- Error log
      PclErrorLog(-8, "Unable to create directory '$p_dir'");

      // ----- Return
      TrFctEnd(__FILE__, __LINE__, PclErrorCode(), PclErrorString());
      return PclErrorCode();
    }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $v_result, "Directory '$p_dir' created");
    return $v_result;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTarHandleExtension()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function PclTarHandleExtension($p_tarname)
  {
    TrFctStart(__FILE__, __LINE__, "PclTarHandleExtension", "tar=$p_tarname");

    // ----- Look for file extension

    if ((substr($p_tarname, -7) == ".tar.gz") || (substr($p_tarname, -4) == ".tgz"))
    {
      TrFctMessage(__FILE__, __LINE__, 2, "Archive is a gzip tar");
      $v_tar_mode = "tgz";
    }
    else if (substr($p_tarname, -4) == ".tar")
    {
      TrFctMessage(__FILE__, __LINE__, 2, "Archive is a tar");
      $v_tar_mode = "tar";
    }
    else
    {
      // ----- Error log
      PclErrorLog(-9, "Invalid archive extension");

      TrFctMessage(__FILE__, __LINE__, PclErrorCode(), PclErrorString());

      $v_tar_mode = "";
    }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $v_tar_mode);
    return $v_tar_mode;
  }
  // --------------------------------------------------------------------------------


  // --------------------------------------------------------------------------------
  // Function : PclTarHandlePathReduction()
  // Description :
  // Parameters :
  // Return Values :
  // --------------------------------------------------------------------------------
  function PclTarHandlePathReduction($p_dir)
  {
    TrFctStart(__FILE__, __LINE__, "PclTarHandlePathReduction", "dir='$p_dir'");
    $v_result = "";

    // ----- Look for not empty path
    if ($p_dir != "")
    {
      // ----- Explode path by directory names
      $v_list = explode("/", $p_dir);

      // ----- Study directories from last to first
      for ($i=sizeof($v_list)-1; $i>=0; $i--)
      {
        // ----- Look for current path
        if ($v_list[$i] == ".")
        {
          // ----- Ignore this directory
          // Should be the first $i=0, but no check is done
        }
        else if ($v_list[$i] == "..")
        {
          // ----- Ignore it and ignore the $i-1
          $i--;
        }
        else if (($v_list[$i] == "") && ($i!=(sizeof($v_list)-1)) && ($i!=0))
        {
          // ----- Ignore only the double '//' in path,
          // but not the first and last '/'
        }
        else
        {
          $v_result = $v_list[$i].($i!=(sizeof($v_list)-1)?"/".$v_result:"");
        }
      }
    }

    // ----- Return
    TrFctEnd(__FILE__, __LINE__, $v_result);
    return $v_result;
  }
  // --------------------------------------------------------------------------------


// ----- End of double include look
}



























// --------------------------------------------------------------------------------
// PhpConcept Library (PCL) Error 1.0
// --------------------------------------------------------------------------------
// License GNU/GPL - Vincent Blavet - Mars 2001
// http://www.phpconcept.net & http://phpconcept.free.fr
// --------------------------------------------------------------------------------
// Fran�ais :
//   La description de l'usage de la librairie PCL Error 1.0 n'est pas encore
//   disponible. Celle-ci n'est pour le moment distribu�e qu'avec les
//   d�veloppements applicatifs de PhpConcept.
//   Une version ind�pendante sera bientot disponible sur http://www.phpconcept.net
//
// English :
//   The PCL Error 1.0 library description is not available yet. This library is
//   released only with PhpConcept application and libraries.
//   An independant release will be soon available on http://www.phpconcept.net
//
// --------------------------------------------------------------------------------
//
//   * Avertissement :
//
//   Cette librairie a �t� cr��e de fa�on non professionnelle.
//   Son usage est au risque et p�ril de celui qui l'utilise, en aucun cas l'auteur
//   de ce code ne pourra �tre tenu pour responsable des �ventuels d�gats qu'il pourrait
//   engendrer.
//   Il est entendu cependant que l'auteur a r�alis� ce code par plaisir et n'y a
//   cach� aucun virus, ni malveillance.
//   Cette libairie est distribu�e sous la license GNU/GPL (http://www.gnu.org)
//
//   * Auteur :
//
//   Ce code a �t� �crit par Vincent Blavet (vincent@blavet.net) sur son temps
//   de loisir.
//
// --------------------------------------------------------------------------------

// ----- Look for double include
if (!defined("PCLERROR_LIB"))
{
  define( "PCLERROR_LIB", 1 );

  // ----- Version
  $g_pcl_error_version = "1.0";

  // ----- Internal variables
  // These values must only be change by PclError library functions
  $g_pcl_error_string = "";
  $g_pcl_error_code = 1;


  // --------------------------------------------------------------------------------
  // Function : PclErrorLog()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclErrorLog($p_error_code=0, $p_error_string="")
  {
    global $g_pcl_error_string;
    global $g_pcl_error_code;

    $g_pcl_error_code = $p_error_code;
    $g_pcl_error_string = $p_error_string;

  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclErrorFatal()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclErrorFatal($p_file, $p_line, $p_error_string="")
  {
    global $g_pcl_error_string;
    global $g_pcl_error_code;

    $v_message =  "<html><body>";
    $v_message .= "<p align=center><font color=red bgcolor=white><b>PclError Library has detected a fatal error on file '$p_file', line $p_line</b></font></p>";
    $v_message .= "<p align=center><font color=red bgcolor=white><b>$p_error_string</b></font></p>";
    $v_message .= "</body></html>";
    die($v_message);
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclErrorReset()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclErrorReset()
  {
    global $g_pcl_error_string;
    global $g_pcl_error_code;

    $g_pcl_error_code = 1;
    $g_pcl_error_string = "";
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclErrorCode()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclErrorCode()
  {
    global $g_pcl_error_string;
    global $g_pcl_error_code;
    
    return($g_pcl_error_code);
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclErrorString()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclErrorString()
  {
    global $g_pcl_error_string;
    global $g_pcl_error_code;

    return($g_pcl_error_string." [code $g_pcl_error_code]");
  }
  // --------------------------------------------------------------------------------


// ----- End of double include look
}






























// --------------------------------------------------------------------------------
// PhpConcept Library (PCL) Trace 2.0-beta1
// --------------------------------------------------------------------------------
// License GNU/GPL - Vincent Blavet - August 2003
// http://www.phpconcept.net
// --------------------------------------------------------------------------------
//
//   The PCL Trace library description is not available yet.
//   This library was first released only with PclZip library.
//   An independant release will be soon available on http://www.phpconcept.net
//
// --------------------------------------------------------------------------------
//
// Warning :
//   This library and the associated files are non commercial, non professional
//   work.
//   It should not have unexpected results. However if any damage is caused by
//   this software the author can not be responsible.
//   The use of this software is at the risk of the user.
//
// --------------------------------------------------------------------------------

  // ----- Version
  $g_pcltrace_version = "2.0-beta1";

  // ----- Internal variables
  // These values must be change by PclTrace library functions
  $g_pcl_trace_mode = "memory";
  $g_pcl_trace_filename = "trace.txt";
  $g_pcl_trace_name = array();
  $g_pcl_trace_index = 0;
  $g_pcl_trace_level = 0;
  $g_pcl_trace_suspend = false;
  //$g_pcl_trace_entries = array();


  // ----- For compatibility reason
  define ('PCLTRACE_LIB', 1);

  // --------------------------------------------------------------------------------
  // Function : TrOn($p_level, $p_mode, $p_filename)
  // Description :
  // Parameters :
  //   $p_level : Trace level
  //   $p_mode : Mode of trace displaying :
  //             'normal' : messages are displayed at function call
  //             'memory' : messages are memorized in a table and can be display by
  //                        TrDisplay() function. (default)
  //             'log'    : messages are writed in the file $p_filename
  // --------------------------------------------------------------------------------
  function PclTraceOn($p_level=1, $p_mode="memory", $p_filename="trace.txt")
  {
    TrOn($p_level, $p_mode, $p_filename);
  }
  function TrOn($p_level=1, $p_mode="memory", $p_filename="trace.txt")
  {
    global $g_pcl_trace_level;
    global $g_pcl_trace_mode;
    global $g_pcl_trace_filename;
    global $g_pcl_trace_name;
    global $g_pcl_trace_index;
    global $g_pcl_trace_entries;
    global $g_pcl_trace_suspend;

    // ----- Enable trace mode
    $g_pcl_trace_level = $p_level;

    // ----- Memorize mode and filename
    switch ($p_mode) {
      case "normal" :
      case "memory" :
      case "log" :
        $g_pcl_trace_mode = $p_mode;
      break;
      default :
        $g_pcl_trace_mode = "logged";
    }

    // ----- Memorize filename
    $g_pcl_trace_filename = $p_filename;
    
    $g_pcl_trace_suspend = false;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : IsTrOn()
  // Description :
  // Return value :
  //   The trace level (0 for disable).
  // --------------------------------------------------------------------------------
  function PclTraceIsOn()
  {
    return IsTrOn();
  }
  function IsTrOn()
  {
    global $g_pcl_trace_level;

    return($g_pcl_trace_level);
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : TrOff()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclTraceOff()
  {
    TrOff();
  }
  function TrOff()
  {
    global $g_pcl_trace_level;
    global $g_pcl_trace_mode;
    global $g_pcl_trace_filename;
    global $g_pcl_trace_name;
    global $g_pcl_trace_index;

    // ----- Clean
    $g_pcl_trace_mode = "memory";
    unset($g_pcl_trace_entries);
    unset($g_pcl_trace_name);
    unset($g_pcl_trace_index);

    // ----- Switch off trace
    $g_pcl_trace_level = 0;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTraceSuspend()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclTraceSuspend()
  {
    global $g_pcl_trace_suspend;


    $g_pcl_trace_suspend = true;
  }
  // --------------------------------------------------------------------------------


  // --------------------------------------------------------------------------------
  // Function : PclTraceResume()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclTraceResume()
  {
    global $g_pcl_trace_suspend;


    $g_pcl_trace_suspend = false;
  }
  // --------------------------------------------------------------------------------


  // --------------------------------------------------------------------------------
  // Function : TrFctStart()
  // Description :
  //   Just a trace function for debbugging purpose before I use a better tool !!!!
  //   Start and stop of this function is by $g_pcl_trace_level global variable.
  // Parameters :
  //   $p_level : Level of trace required.
  // --------------------------------------------------------------------------------
  function PclTraceFctStart($p_file, $p_line, $p_name, $p_param="", $p_message="")
  {
    TrFctStart($p_file, $p_line, $p_name, $p_param, $p_message);
  }
  function TrFctStart($p_file, $p_line, $p_name, $p_param="", $p_message="")
  {
    global $g_pcl_trace_level;
    global $g_pcl_trace_mode;
    global $g_pcl_trace_filename;
    global $g_pcl_trace_name;
    global $g_pcl_trace_index;
    global $g_pcl_trace_entries;
    global $g_pcl_trace_suspend;

    // ----- Look for disabled trace
    if (($g_pcl_trace_level < 1) || ($g_pcl_trace_suspend))
      return;

    // ----- Add the function name in the list
    if (!isset($g_pcl_trace_name))
      $g_pcl_trace_name = $p_name;
    else
      $g_pcl_trace_name .= ",".$p_name;

    // ----- Update the function entry
    $i = sizeof($g_pcl_trace_entries);
    $g_pcl_trace_entries[$i]['name'] = $p_name;
    $g_pcl_trace_entries[$i]['param'] = $p_param;
    $g_pcl_trace_entries[$i]['message'] = "";
    $g_pcl_trace_entries[$i]['file'] = $p_file;
    $g_pcl_trace_entries[$i]['line'] = $p_line;
    $g_pcl_trace_entries[$i]['index'] = $g_pcl_trace_index;
    $g_pcl_trace_entries[$i]['type'] = "1"; // means start of function

    // ----- Update the message entry
    if ($p_message != "")
    {
    $i = sizeof($g_pcl_trace_entries);
    $g_pcl_trace_entries[$i]['name'] = "";
    $g_pcl_trace_entries[$i]['param'] = "";
    $g_pcl_trace_entries[$i]['message'] = $p_message;
    $g_pcl_trace_entries[$i]['file'] = $p_file;
    $g_pcl_trace_entries[$i]['line'] = $p_line;
    $g_pcl_trace_entries[$i]['index'] = $g_pcl_trace_index;
    $g_pcl_trace_entries[$i]['type'] = "3"; // means message
    }

    // ----- Action depending on mode
    PclTraceAction($g_pcl_trace_entries[$i]);

    // ----- Increment the index
    $g_pcl_trace_index++;
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : TrFctEnd()
  // Description :
  //   Just a trace function for debbugging purpose before I use a better tool !!!!
  //   Start and stop of this function is by $g_pcl_trace_level global variable.
  // Parameters :
  //   $p_level : Level of trace required.
  // --------------------------------------------------------------------------------
  function PclTraceFctEnd($p_file, $p_line, $p_return=1, $p_message="")
  {
    TrFctEnd($p_file, $p_line, $p_return, $p_message);
  }
  function TrFctEnd($p_file, $p_line, $p_return=1, $p_message="")
  {
    global $g_pcl_trace_level;
    global $g_pcl_trace_mode;
    global $g_pcl_trace_filename;
    global $g_pcl_trace_name;
    global $g_pcl_trace_index;
    global $g_pcl_trace_entries;
    global $g_pcl_trace_suspend;

    // ----- Look for disabled trace
    if (($g_pcl_trace_level < 1) || ($g_pcl_trace_suspend))
      return;

    // ----- Extract the function name in the list
    // ----- Remove the function name in the list
    if (!($v_name = strrchr($g_pcl_trace_name, ",")))
    {
      $v_name = $g_pcl_trace_name;
      $g_pcl_trace_name = "";
    }
    else
    {
      $g_pcl_trace_name = substr($g_pcl_trace_name, 0, strlen($g_pcl_trace_name)-strlen($v_name));
      $v_name = substr($v_name, -strlen($v_name)+1);
    }

    // ----- Decrement the index
    $g_pcl_trace_index--;

    // ----- Update the message entry
    if ($p_message != "")
    {
    $i = sizeof($g_pcl_trace_entries);
    $g_pcl_trace_entries[$i]['name'] = "";
    $g_pcl_trace_entries[$i]['param'] = "";
    $g_pcl_trace_entries[$i]['message'] = $p_message;
    $g_pcl_trace_entries[$i]['file'] = $p_file;
    $g_pcl_trace_entries[$i]['line'] = $p_line;
    $g_pcl_trace_entries[$i]['index'] = $g_pcl_trace_index;
    $g_pcl_trace_entries[$i]['type'] = "3"; // means message
    }

    // ----- Update the function entry
    $i = sizeof($g_pcl_trace_entries);
    $g_pcl_trace_entries[$i]['name'] = $v_name;
    $g_pcl_trace_entries[$i]['param'] = $p_return;
    $g_pcl_trace_entries[$i]['message'] = "";
    $g_pcl_trace_entries[$i]['file'] = $p_file;
    $g_pcl_trace_entries[$i]['line'] = $p_line;
    $g_pcl_trace_entries[$i]['index'] = $g_pcl_trace_index;
    $g_pcl_trace_entries[$i]['type'] = "2"; // means end of function

    // ----- Action depending on mode
    PclTraceAction($g_pcl_trace_entries[$i]);
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : TrFctMessage()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclTraceFctMessage($p_file, $p_line, $p_level, $p_message="")
  {
    TrFctMessage($p_file, $p_line, $p_level, $p_message);
  }
  function TrFctMessage($p_file, $p_line, $p_level, $p_message="")
  {
    global $g_pcl_trace_level;
    global $g_pcl_trace_mode;
    global $g_pcl_trace_filename;
    global $g_pcl_trace_name;
    global $g_pcl_trace_index;
    global $g_pcl_trace_entries;
    global $g_pcl_trace_suspend;

    // ----- Look for disabled trace
    if (($g_pcl_trace_level < $p_level) || ($g_pcl_trace_suspend))
      return;

    // ----- Update the entry
    $i = sizeof($g_pcl_trace_entries);
    $g_pcl_trace_entries[$i]['name'] = "";
    $g_pcl_trace_entries[$i]['param'] = "";
    $g_pcl_trace_entries[$i]['message'] = $p_message;
    $g_pcl_trace_entries[$i]['file'] = $p_file;
    $g_pcl_trace_entries[$i]['line'] = $p_line;
    $g_pcl_trace_entries[$i]['index'] = $g_pcl_trace_index;
    $g_pcl_trace_entries[$i]['type'] = "3"; // means message of function

    // ----- Action depending on mode
    PclTraceAction($g_pcl_trace_entries[$i]);
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : TrMessage()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclTraceMessage($p_file, $p_line, $p_level, $p_message="")
  {
    TrMessage($p_file, $p_line, $p_level, $p_message);
  }
  function TrMessage($p_file, $p_line, $p_level, $p_message="")
  {
    global $g_pcl_trace_level;
    global $g_pcl_trace_mode;
    global $g_pcl_trace_filename;
    global $g_pcl_trace_name;
    global $g_pcl_trace_index;
    global $g_pcl_trace_entries;
    global $g_pcl_trace_suspend;

    // ----- Look for disabled trace
    if (($g_pcl_trace_level < $p_level) || ($g_pcl_trace_suspend))
      return;

    // ----- Update the entry
    $i = sizeof($g_pcl_trace_entries);
    $g_pcl_trace_entries[$i]['name'] = "";
    $g_pcl_trace_entries[$i]['param'] = "";
    $g_pcl_trace_entries[$i]['message'] = $p_message;
    $g_pcl_trace_entries[$i]['file'] = $p_file;
    $g_pcl_trace_entries[$i]['line'] = $p_line;
    $g_pcl_trace_entries[$i]['index'] = $g_pcl_trace_index;
    $g_pcl_trace_entries[$i]['type'] = "4"; // means simple message

    // ----- Action depending on mode
    PclTraceAction($g_pcl_trace_entries[$i]);
  }
  // --------------------------------------------------------------------------------

  // --------------------------------------------------------------------------------
  // Function : PclTraceAction()
  // Description :
  // Parameters :
  // --------------------------------------------------------------------------------
  function PclTraceAction($p_entry)
  {
    global $g_pcl_trace_level;
    global $g_pcl_trace_mode;
    global $g_pcl_trace_filename;
    global $g_pcl_trace_name;
    global $g_pcl_trace_index;
    global $g_pcl_trace_entries;

    if ($g_pcl_trace_mode == "normal")
    {
      for ($i=0; $i<$p_entry['index']; $i++)
        echo "---";
      if ($p_entry['type'] == 1)
        echo "<b>".$p_entry['name']."</b>(".$p_entry['param'].") : ".$p_entry['message']." [".$p_entry[file].", ".$p_entry[line]."]<br>";
      else if ($p_entry['type'] == 2)
        echo "<b>".$p_entry['name']."</b>()=".$p_entry['param']." : ".$p_entry['message']." [".$p_entry[file].", ".$p_entry[line]."]<br>";
      else
        echo $p_entry['message']." [".$p_entry['file'].", ".$p_entry['line']."]<br>";
    }
  }
  // --------------------------------------------------------------------------------

























// --------------------------------------------------------------------------------
// Register global variables and validate user input
// --------------------------------------------------------------------------------
if     (isset($_POST["package_url"]) == true)     { $package_url     = validateGenericInput($_POST["package_url"]); }
if     (isset($_POST["ftpserver"]) == true)       { $ftpserver       = validateGenericInput($_POST["ftpserver"]); }
if     (isset($_POST["ftpserverport"]) == true)   { $ftpserverport   = validateGenericInput($_POST["ftpserverport"]); }
if     (isset($_POST["username"]) == true)        { $username        = validateGenericInput($_POST["username"]); }
if     (isset($_POST["password"]) == true)        { $password        = validateGenericInput($_POST["password"]); }
if     (isset($_POST["passivemode"]) == true)     { $passivemode     = validateGenericInput($_POST["passivemode"]); }
if     (isset($_POST["targetdirectory"]) == true) { $targetdirectory = validateDirectory($_POST["targetdirectory"]); }
if     (isset($_POST["screen"]) == true)          { $screen          = validateGenericInput($_POST["screen"]); }
if     (isset($_SERVER["SCRIPT_NAME"]) == true)   { $php_self        = $_SERVER["SCRIPT_NAME"]; }
elseif (isset($_SERVER["PHP_SELF"]) == true)      { $php_self        = $_SERVER["PHP_SELF"]; }
$tempdir = dirname(__FILE__) . "/net2ftp_temp_zyzsq";



// --------------------------------------------------------------------------------
// HTML start
// --------------------------------------------------------------------------------

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en" dir="LTR">
<head>
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="net2ftp, web, ftp, based, web-based, xftp, client, PHP, SSL, password, server, free, gnu, gpl, gnu/gpl, net, net to ftp, netftp, connect, user, gui, interface, web2ftp, edit, editor, online, code, php, upload, download, copy, move, delete, zip, tar, unzip, untar, recursive, rename, chmod, syntax, highlighting, host, hosting, ISP, webserver, plan, bandwidth">
<meta name="description" content="net2ftp is a web based FTP client. It is mainly aimed at managing websites using a browser. Edit code, upload/download files, copy/move/delete directories recursively, rename files and directories -- without installing any software.">
<link rel="shortcut icon" href="favicon.ico">
<title>net2ftp - a web based FTP client</title>
<style type="text/css">
.header21 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 20px;
	font-style: normal;
	color: #1D64AD;
	font-weight: bold;
	text-decoration: none;
}
</style>
</head>
<body>
<div class="header21">net2ftp installation script</div><br />
<?php

// --------------------------------------------------------------------------------
// Screen 1
// Select which package to install, and where to install it
// Select which directories to remove
// --------------------------------------------------------------------------------

if ($screen == "" || $screen == "1") {

?>
<form name="ActionForm" action="net2ftp_installer.php?security_code=ctz0ob5t0y5pzn1bo0wh" method="post">
<input type="hidden" name="screen" value="2">
Package <br />
<select name="package" onchange="document.forms['ActionForm'].package_url.value=document.forms['ActionForm'].package.options[document.forms['ActionForm'].package.selectedIndex].value;">
<option value="" selected="selected">&nbsp;</option>
<option value="" style="font-weight: bold; text-decoration: underline;">Blogs</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/evocms/b2evolution-1.8.0-2006-07-09.zip">b2evolution 1.8.0</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/nucleuscms/nucleus3.23.zip">Nucleus 3.23 English</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/nucleuscmsde/nucleus3.23_de.zip">Nucleus 3.23 German</option>
<option value="http://wordpress.org/latest.tar.gz">WordPress (latest version)</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Content management</option>
<option value="http://ftp.osuosl.org/pub/drupal/files/projects/drupal-4.6.8.tar.gz">Drupal 4.6.8</option>
<option value="http://ftp.osuosl.org/pub/drupal/files/projects/drupal-4.7.2.tar.gz">Drupal 4.7.2</option>
<option value="http://ez.no/content/download/137355/877522/file/ezpublish-3.8.3-gpl.tar.gz">eZ Publish 3.8.3</option>
<option value="http://www.geeklog.net/filemgmt/visit.php?lid=747">Geeklog</option>
<option value="http://developer.joomla.org/sf/frs/do/downloadFile/projects.joomla/frs.joomla_1_0.1_0_10/frs5790?dl=1">Joomla 1.0.10</option>
<option value="http://mamboxchange.com/frs/download.php/7368/mambov4.5.3h.tar.gz">Mambo 4.5.3h</option>
<option value="http://mamboxchange.com/frs/download.php/7877/MamboV4.5.4.tar.gz">Mambo 4.5.4</option>
<option value="http://mamboxchange.com/frs/download.php/8046/MamboV4.6RC2.tar.gz">Mambo 4.6 RC2</option>
<option value="http://download.moodle.org/stable16/moodle-latest-16.tgz">Moodle 1.6.1 stable</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpwcms/phpwcms_1.2.5-DEV.tgz">phpWCMS 1.2.5</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpwebsite/phpwebsite-0.10.2-full.tar.gz">phpWebSite 0.10.2 full</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpwebsite/phpwebsite-0.10.2-full.tar.gz">phpWebSite 0.10.2 full</option>
<option value="http://noc.postnuke.com/frs/download.php/987/PostNuke-0.762.tar.gz">Post-Nuke 0.762</option>
<option value="http://siteframe.org/files/2/34/siteframe-5.0.2-768.tar.gz">Siteframe 5.0.2</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/typo3/dummy-4.0.tar.gz">TYPO3 4.0 dummy</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/typo3/typo3_src-4.0.tar.gz">TYPO3 4.0 source</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/xoops/xoops-2.0.14.tar.gz">Xoops 2.0.14</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Customer relationship</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/cslive/craftysyntax2.12.9.tar.gz">Crafty Syntax Live Help 2.12.9</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/helpcenterlive/hcl_2-1-2.zip">Help Center Live 2.1.2</option>
<option value="http://www.phpsupporttickets.com/pages/php_support_tickets/dist/free/PHP_S_Tickets_v2.2.tar.gz">PHP Support Tickets</option>
<option value="http://www.support-logic.com/download/index.php?cmd=download&id=22">Support Logic Helpdesk 1.3</option>
<option value="http://www.sheddnet.net/forums/attachment.php?attachmentid=77&d=1146954912">Support Services Manager 1.0.2</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Development</option>
<option value="http://www.net2ftp.com/download/net2ftp_v0.93.zip">net2ftp 0.93 full version</option>
<option value="http://www.net2ftp.com/download/net2ftp_v0.93_light.zip">net2ftp 0.93 light version</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpmyadmin/phpMyAdmin-2.8.2.tar.gz">phpMyAdmin 2.8.2</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Discussion boards</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpbb/phpBB-2.0.21.tar.gz">phpBB 2.0.21</option>
<option value="http://www.punbb.org/download/punbb-1.2.12.tar.gz">punBB 1.2.12</option>
<option value="http://www.simplemachines.org/download/index.php/smf_1-0-7_install.tar.gz">Simple Machines Forum 1.0.7</option>

<option value="" style="font-weight: bold; text-decoration: underline;">E-Commerce</option>
<option value="https://www.cubecart.com/site/helpdesk/index.php?_m=downloads&_a=viewdownload&downloaditemid=44&nav=0,5">CubeCart</option>
<option value="http://www.oscommerce.com/redirect.php/go,28">OS Commerce 2.2 Milestone 2 Update 051113</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/zencart/zen-cart-v1.1.4d.zip">Zen Cart 1.1.4d</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/zencart/zen-cart-1-2-7-d_full-release.zip">Zen Cart 1.2.7d full release</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/zencart/zen-cart-v1.3.0.2-full-fileset.zip">Zen Cart 1.3.0.2 full fileset</option>

<option value="" style="font-weight: bold; text-decoration: underline;">FAQ</option>
<option value="http://www.lethalpenguin.net/design/faqmasterflex.php?download=true">FAQMasterFlex</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Guestbooks</option>
<option value="http://www.danskcinders.com/download/ViPER_Guestbook_X1.1.zip">ViPER Guestbook 1.1</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Hosting billing</option>
<option value="http://www.phpcoin.com/coin_addons/dload.php?id=108">phpCOIN 1.2.3</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Image galleries</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/coppermine/cpg1.4.8.zip">Coppermine Photo Gallery 1.4.8</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/gallery/gallery-2.1.1a-typical.tar.gz">Gallery 2.1.1a (typical)</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/gallery/gallery-2.1.1a-full.tar.gz">Gallery 2.1.1a (full)</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/gallery/gallery-2.1.1a-minimal.tar.gz">Gallery 2.1.1a (mininal)</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/gallery/gallery-2.1.1a-developer.tar.gz">Gallery 2.1.1a (developer)</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Mailing lists</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phplist/phplist-2.10.2.tgz">PHPlist 2.10.2</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Polls and surveys</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpesp/phpESP-1.8.2.tar.gz">phpESP 1.8.2</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpsurveyor/phpsurveyor-1_00.zip">PHPSurveyor 1.0</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Project management</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/dotproject/dotproject-2.0.4.tar.gz">dotProject 2.0.4</option>
<option value="http://www.phprojekt.com/modules.php?op=modload&name=Downloads&file=index&req=getit&lid=3">PHProjekt 5.1</option>
<option value="http://www.tutos.org/download/TUTOS-php-1.2.20050904.tar.gz">Tutos 1.2.20050904</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Webmail</option>
<option value="ftp://ftp.horde.org/pub/horde/horde-3.1.2.tar.gz">Horde 3.1.2 (required for IMP)</option>
<option value="ftp://ftp.horde.org/pub/imp/imp-h3-4.1.2.tar.gz">IMP H3 4.1.2 (requires Horde)</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/neomail/neomail-1.29.tar.gz">NeoMail 1.29</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/squirrelmail/squirrelmail-1.4.7.tar.gz">Squirrelmail 1.4.7</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Wiki</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/tikiwiki/tikiwiki-1.9.4.tar.gz">TikiWiki 1.9.4</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpwiki/phpwiki-1.2.10.tar.gz">PhpWiki 1.2.10 (stable)</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpwiki/phpwiki-1.3.12p3.tar.bz2">PhpWiki 1.3.12p3 (current)</option>

<option value="" style="font-weight: bold; text-decoration: underline;">Other scripts</option>
<option value="http://www.cacti.net/downloads/cacti-0.8.6h.tar.gz">Cacti 0.8.6h</option>
<option value="http://classifieds.phpoutsourcing.com/classifieds_1_3.tgz">Noahs Classifieds 1.3</option>
<option value="http://www.open-realty.org/release/open-realty232.zip">Open-Realty 2.3.2</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpadsnew/phpAdsNew-2.0.8.tar.gz">phpAdsNew</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/phpformgen/phpFormGen-php-2.09c.tar.gz">phpFormGenerator 2.0.9c</option>
<option value="http://belnet.dl.sourceforge.net/sourceforge/webcalendar/WebCalendar-1.0.4.tar.gz">WebCalendar 1.0.4</option>

</select><br />
<input type="text" name="package_url" size="100"><br /><br />

<table border="0" cellspacing="0" cellpadding="2">
<tr><td>FTP server</td><td><input type="text" name="ftpserver" size="25" value="127.0.0.1"> port <input type="text" name="ftpserverport" size="3" value="21"></td></tr>
<tr><td>Username</td><td><input type="text" name="username" size="25" value="root.andrejvysny.online"></td></tr>
<tr><td>Password</td><td><input type="password" name="password" size="25"></td></tr>
<tr><td>Passive mode</td><td><input type="checkbox" name="passivemode" value="yes"></td></tr>
<tr><td>Installation directory</td><td><input type="text" name="targetdirectory" size="25" value="/andrejvysny.online/web/pages/gallery/6.Nights/Thumbnails"></td></tr>
</table>

<input type="submit" value="Install"> or 
<input type="submit" value="Delete this installation script" onclick="document.forms['ActionForm'].screen.value=3">
</form>

<?php 

}
// --------------------------------------------------------------------------------
// Screen 2
// Install package
// --------------------------------------------------------------------------------

elseif ($screen == 2) {

// ----------------------------------------------
// Get archive
// ----------------------------------------------

// Print comment
	echo "Getting the package...<br />\n";
	flush();

// Open handle
	$handle = fopen($package_url, "rb");
	if ($handle == false) { echo "Could not open the package file $package_url.<br /><span style=\"font-size: 80%\">If you see a PHP warning message above regarding \"php_network_getaddresses: getaddrinfo failed\", check whether allow_url_fopen is set to On in php.ini, and try to restart your web server.</span>"; exit(); }

// Read contents - PHP 4 vs PHP 5
	$contents = "";
	if (version_compare(phpversion(), "5", "<")) { 
		while (!feof($handle)) { $contents .= @fread($handle, 8192); }
	}
	else { 
		$contents = @stream_get_contents($handle); 
	}
	if ($contents == "") { echo "Could not read the package file $package_url."; exit(); }

// Close handle
	@fclose($handle);

// ----------------------------------------------
// Write the archive to a file
// ----------------------------------------------

// Print comment
	echo "Putting the package on the FTP server...<br />\n";
	flush();

// Target
	$archive_file = $tempdir . "/" . basename($package_url);

// Open handle
	$handle = @fopen($archive_file, "wb");
	if ($handle == false) { echo "Could not open the file $archive_file."; exit(); }

// Write contents
	$fwrite_result = @fwrite($handle, $contents);
	if ($fwrite_result == false && @filesize($source) > 0) { echo "Could not write the file $archive_file."; exit(); }

// Close handle
	fclose($handle);

// ----------------------------------------------
// Unzip the archive
// ----------------------------------------------

// Print comment
	echo "Extracting the directories and files from the package...<br />\n";
	flush();

	$list = "";
	$archive_type = get_filename_extension($archive_file);

// Extract zip
	if ($archive_type == "zip") {
		$zip = new PclZip($archive_file);
		$list = $zip->extract($p_path = $tempdir);
	}
// Extract tar, tgz and gz
	elseif ($archive_type == "tar" || $archive_type == "tgz" || $archive_type == "gz") { 
		$list = PclTarExtract($archive_file, $tempdir);
	}

// Check result
	if ($list <= 0) { echo "Could not extract the archive."; exit(); }

// ----------------------------------------------
// Unzip the archive
// ----------------------------------------------

// Print comment
	echo "Copying the directories and files via FTP...<br />\n";
	flush();

?>
<br />
<form name="ActionForm" action="net2ftp_installer.php?security_code=ctz0ob5t0y5pzn1bo0wh" method="post">
<input type="hidden" name="screen" value="1">
<input type="submit" value="Install more packages"> or 
<input type="submit" value="Delete this installation script" onclick="document.forms['ActionForm'].screen.value=3">
</form>
<br />
<?php

// Set up basic connection
	$conn_id = @ftp_connect($ftpserver, $ftpserverport);
	if ($conn_id == false) { echo "Unable to connect to FTP server $ftpserver <br />\n"; exit(); }

// Login with username and password
	$login_result = @ftp_login($conn_id, $username, $password);
	if ($login_result == false) { echo "Unable to login into the FTP server $ftpserver with username $username <br />\n"; exit(); }

// Set passive mode
	if ($passivemode == "yes") { $ftp_pasv_result = @ftp_pasv($conn_id, TRUE); }

// Create directories and put files

	for ($i=0; $i<sizeof($list); $i++) {

		$source = trim($list[$i]["filename"]);
		$target_relative = substr($source, strlen($tempdir));
		$target = $targetdirectory . $target_relative;
		$ftpmode = ftpAsciiBinary($source);

// Directory entry in the archive: create the directory
		if (is_dir($source) == true) {
			$ftp_mkdir_result = @ftp_mkdir($conn_id, $target);
			if ($ftp_mkdir_result == true) { echo "Created directory $target <br />\n"; }
		}
// File entry in the archive: put the file
// If this fails, create the required directories and try again
		elseif (is_file($source) == true) {
			$ftpmode = ftpAsciiBinary($source);
			$ftp_put_result = @ftp_put($conn_id, $target, $source, $ftpmode);
			if ($ftp_put_result == true) { echo "Copied file $target <br />\n"; }
			else { 
				$target_relative_parts = explode("/", str_replace("\\", "/", dirname($target_relative)));
				$directory_to_create = $targetdirectory;
				for ($j=0; $j<sizeof($target_relative_parts); $j=$j+1) {
					$directory_to_create = $directory_to_create . "/" . $target_relative_parts[$j];
					$ftp_chdir_result = @ftp_chdir($conn_id, $directory_to_create);
					if ($ftp_chdir_result == false) {
						$ftp_mkdir_result = @ftp_mkdir($conn_id, $directory_to_create);
						if ($ftp_mkdir_result == true) { echo "Created directory $directory_to_create<br />\n"; }
					} // end if
				} // end for
				$ftp_put_result = @ftp_put($conn_id, $target, $source, $ftpmode);
				if ($ftp_put_result == true) { echo "Copied file $target <br />\n"; }
				else { echo "Could not copy file $target <br />\n"; }
			}
		}
	}


// Close connection
	ftp_quit($conn_id);

// Print comment
	echo "Done.\n";
	flush();

}
// --------------------------------------------------------------------------------
// Screen 3
// Delete the installation script
// --------------------------------------------------------------------------------

elseif ($screen == 3) {
	echo "Deleting the temporary files and the install script...<br />\n";
	flush();

?>
<br />
<form name="ActionForm" action="" method="post">
<input type="button" value="Close this window" onclick="javascript:window.close();">
</form>
<br />
<?php

	delete_dirorfile($tempdir, "execute");
	delete_dirorfile(__FILE__, "execute");
?>
Done.
<br />
<?php

}
// --------------------------------------------------------------------------------
// HTML end
// --------------------------------------------------------------------------------

?>
</body>
</html>