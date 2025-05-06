<?php

namespace App\Http\Traits;

trait CSVSeeder {	
	
	/**
	 * csv_to_array
	 *
	 * @param  String $filename
	 * @param  String $delimiter
	 * @return Array
	 */
	public function csv_to_array($filename = '', $delimiter = ',') {
		if (!file_exists($filename) || !is_readable($filename)){
			return FALSE;
		}

		$header = NULL;
		$data = [];
		if (($handle = fopen($filename, 'r')) !== FALSE) {
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
				if (!$header) {
					$header = $row;
					$header[] = "created_at";
				}
				else {
					$row[] = now();
					$data[] = array_combine($header, $row);
				}
			}
			fclose($handle);
		}
		return $data;
	}
}