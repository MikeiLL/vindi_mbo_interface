<?php
//For Testing
if ( ! function_exists( 'mZ_write_to_file' ) ) {
	function mZ_write_to_file($message)
	{
			$header = "\nMessage:\t ";

			if (is_array($message)) {
					$header = "\nMessage is array.\n";
					$message = print_r($message, true);
			}

			file_put_contents(
					'/Applications/MAMP/logs/mZ_mbo_reader.php', 
					$header . $message, 
					FILE_APPEND | LOCK_EX
			);
	}
}
//Format arrays for display in development
if ( ! function_exists( 'mz_pr' ) ) {
	function mz_pr($message) {
		echo "<pre>";
		print_r($message);
		echo "</pre>";
	}
}

?>