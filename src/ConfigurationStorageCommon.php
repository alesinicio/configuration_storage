<?php
namespace SinFramework\ConfigurationStorage;

use Exception;

abstract class ConfigurationStorageCommon {
	protected $configurationFilename	= null;
	protected $newConfigurationFile		= null;
	protected $arrConfiguration			= [];
	
	/**
	 * @throws Exception Throws Exception on error
	 * @return bool Returns whether the new configuration was successfully writen to the configuration file
	 */
	protected function writeToFile() : bool {
		if (!file_exists($this->configurationFilename)) throw new Exception('Configuration file does not exists');
		
		$result = file_put_contents($this->configurationFilename, $this->newConfigurationFile);
		if (!$result) throw new Exception('Error writing new configuration to file.');
		$this->reset();
		return true;
	}
	/**
	 * Helper void method that resets the operations after writing to file.
	 */
	protected function reset() {
		$this->arrConfiguration		= [];
		$this->newConfigurationFile	= '';
	}
	/**
	 * Helper function to determine how the values should be stored on the configuration file.
	 * Strings should be around quotes, and arrays should be written properly as arrays, for instance...
	 *
	 * @param string $value Value
	 * @return string
	 *
	 */
	protected static function formatValues($value) : string {
		if (is_array($value)) {
			$output = '[';
			foreach($value as $val) {
				$output .= self::formatValues($val).', ';
			}
			$output = rtrim($output, ', ');
			$output .= ']';
			return $output;
		}
		if (is_object($value)) {
			return self::formatValues(serialize($value));
		}
		if (is_string($value)) {
			return '\''.str_replace('\'', '\\\'', $value).'\'';
		}
		if (is_numeric($value)) {
			return $value;
		}
	}
	/**
	 * Helper function to concatenate lines
	 *
	 * @param string $strNewFile String with all data already parsed for the configuration file
	 * @param string $strLine New line to be added to the configuration file
	 */
	protected static function nextLine(string &$strNewFile, string $strLine) : void {
		$strNewFile .= $strLine;
	}
}