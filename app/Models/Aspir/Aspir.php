<?php

/**
 * Aspir
 * 	This spell absorbs DATA from ANYWHERE
 * 	By scanning multiple sources of data, it builds CSV files matching database structure
 *
 * WARNING - PUBLIC METHODS ARE RUN IN ORDER
 *  Methods are pulled using get_class_methods(),
 *  then their type is filtered with ReflectionMethod to test if it's public
 * Exception: __construct() and run() are required to be public, and are excluded manually
 */

namespace App\Models\Aspir;

use Storage;

abstract class Aspir
{

	public function __construct(&$command)
	{
		set_time_limit(0);

		$this->command =& $command;

		$this->gameSlug = strtolower((new \ReflectionClass($this))->getShortName());

		$this->manualDataLocation = base_path('../data/' . $this->gameSlug);

		$this->data = array_map(function() {
			return [];
		}, array_flip(array_merge(config('aspir.entityTables'), config('aspir.pivotTables'))));
	}

	public function run()
	{
		// Run every single `public` method available
		//  Does not run __construct(), run() or assets()
		$methodList = collect(get_class_methods($this))
			->diff(['__construct', 'run', 'assets'])
			->filter(function($methodName) {
				return (new \ReflectionMethod($this, $methodName))->isPublic();
			});

		$rowCounts = $this->getRowCounts(false);

		foreach ($methodList as $function)
		{
			$beginningRowCounts = $rowCounts;

			$this->command->line('Executing ' . $function . '()');
			$this->$function();

			$rowCounts = $this->getRowCounts();

			foreach ($rowCounts->diff($beginningRowCounts) as $dataPoint => $count)
				$this->command->info($dataPoint . ' now have ' . $count . ' rows');
		}

		$this->saveData();
	}

	/**
	 * Array Handling Functions
	 */

	protected function getRowCounts($reduce = true)
	{
		return collect($this->data)->map(function($array) {
				return count($array);
			})->filter(function($amount) use ($reduce) {
				return $reduce ? $amount > 0 : true;
			});
	}

	protected function setData($table, $row, $originId = null, $mergeOnly = false)
	{
		// If id is null, use the length of the existing data, or check in the $row for it
		$id = $originId ?: (isset($row['id']) ? $row['id'] : count($this->data[$table]) + 1);

		if (isset($this->data[$table][$id]))
			$this->data[$table][$id] = array_merge($this->data[$table][$id], $row);
		elseif ( ! $mergeOnly)
			$this->data[$table][$id] = $row;

		return $id;
	}

	/**
	 * File Saving Functions
	 */

	protected function saveData()
	{
		$this->command->comment('Saving Data');

		// Saving files down as CSV, so MySQL can use LOAD DATA INFILE later
		foreach ($this->data as $filename => $data)
			$this->writeToCSV($filename, $data);
	}

	protected function writeToCSV($filename, $data)
	{
		$filePath = 'game-data/' . $this->gameSlug . '/' . $filename . '.csv';

		if (empty($data))
		{
			$this->command->comment('No data for ' . $filename);
			Storage::delete($filePath);
		}
		else
		{
			$this->command->info('Saving ' . count($data) . ' records to ' . $filename . '.csv');

			$fp = fopen(Storage::path($filePath), 'w');

			fputcsv($fp, array_keys(current($data)));

			foreach ($data as $row)
				fputcsv($fp, $row);

			fclose($fp);
		}
	}

	// protected function writeToJSON($filename, $list)
	// {
	// 	if (empty($list))
	// 		$this->command->comment('No data for ' . $filename);
	// 	else
	// 		$this->command->info('Saving ' . count($list) . ' records to ' . $filename . '.json');

	// 	Storage::put('game-data/' . $this->gameSlug . '/' . $filename . '.json', json_encode($list, JSON_PRETTY_PRINT));
	// }

	/**
	 * File Retrieval Functions
	 */

	protected function getCleanedJson($path, $debug = false)
	{
		$content = file_get_contents($path);

		// http://stackoverflow.com/questions/17219916/json-decode-returns-json-error-syntax-but-online-formatter-says-the-json-is-ok
		for ($i = 0; $i <= 31; ++$i)
			$content = str_replace(chr($i), "", $content);

		$content = str_replace(chr(127), "", $content);

		// This is the most common part
		$content = $this->binaryFix($content);

		return json_decode($content);
	}

	private function binaryFix($string)
	{
		// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
		// here we detect it and we remove it, basically it's the first 3 characters
		if (strpos(bin2hex($string), 'efbbbf') === 0)
		   $string = substr($string, 3);

		return $string;
	}

	protected function readTSV($filename)
	{
		$tsv = array_map(function($l) {
			return str_getcsv($l, '	');
		}, file($filename));

		array_walk($tsv, function(&$a) use ($tsv) {
			$a = array_combine($tsv[0], $a);
		});
		array_shift($tsv);

		return collect($tsv);
	}

	/**
	 * Helper Functions
	 */

	protected function error($message)
	{
		throw new \Exception($message);
	}

	protected function humanReadable($size)
	{
		$fileSizeNames = [" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB"];
		return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) .$fileSizeNames[$i] : '0 Bytes';
	}

}
