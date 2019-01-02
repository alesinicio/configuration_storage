<?php
namespace SinFramework\ConfigurationStorage;

use Exception;

/**
 * @author alexandre.sinicio
 * This class makes it easy to use PHP files as storage for configuration data
 * and accessing/changing the values without manually opening files.
 * The data is stored as a single named-array var.
 */
class PHPArrayFile extends ConfigurationStorageCommon implements ConfigurationStorageInterface {
	private $writeNewVars = false;
	
	/**
	 * @param string $configurationFilename (optional) The full path/filename for the configuration file
	 * @param array $arrConfiguration (optional) An array (key/value) with the new configuration to be written to the file
	 */
	public function __construct(string $configurationFilename=null) {
		$this->setStorageLocation($configurationFilename);
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::canWriteNewKeys()
	 */
	public function canWriteNewKeys(bool $boolean) : ConfigurationStorageInterface {
		$this->writeNewVars = $boolean;
		return $this;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::setVals()
	 */
	public function setVals(array $arrConfiguration) : ConfigurationStorageInterface {
		$this->arrConfiguration = $arrConfiguration;
		return $this;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::setStorageLocation()
	 */
	public function setStorageLocation(string $filename) : ConfigurationStorageInterface {
		$this->configurationFilename = $filename;
		return $this;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::commit()
	 */
	public function commit() : bool {
		if (!file_exists($this->configurationFilename)) throw new Exception('Configuration file does not exists');
		
		return $this->parseNewFile();
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::getVal()
	 */
	public function getVal($key) {
		if (!file_exists($this->configurationFilename)) throw new Exception('Configuration file does not exists');
		$config = require $this->configurationFilename;

		if (isset($config[$key])) return $config[$key];
		return null;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::deleteKey()
	 */
	public function deleteKey(string $key) : bool {
		if (!is_array($key)) {
			$key = array($key);
		}
		return $this->parseNewFile($key);
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\\ConfigurationStorage::getAllVals()
	 */
	public function getAllVals() : array {
		if (!file_exists($this->configurationFilename)) throw new Exception('Configuration file does not exists');
		$config = require $this->configurationFilename;
		
		if (!is_array($config)) $config = [];
		
		return $config;
	}
	/**
	 * Effectively creates the string to be written to the new file, updating values, creating new keys and deleting values.
	 * 
	 * @param array $exclusions Keys that should be deleted
	 * @return boolean Returns whether the new configuration was successfully writen to the configuration file
	 */
	private function parseNewFile(array $exclusions=[]) : bool {
		$config = require($this->configurationFilename);
		if (!is_array($config)) $config = [];

		foreach ($this->arrConfiguration as $key=>$val) {
			if (isset($config[$key]) || $this->writeNewVars) {
				$config[$key] = $val;
			}
		}
		foreach($exclusions as $key) {
			if (isset($config[$key])) unset($config[$key]);
		}
		
		$this->newConfigurationFile = '<?php '.PHP_EOL.'$config='.var_export($config, true).';'.PHP_EOL.'return $config;';
		return $this->writeToFile();
	}
	/**
	 * Helper function to generate new lines/updated configuration for a variable on the configuration file
	 *
	 * @param string $var
	 * @return string
	 */
	private function generateNewLine($var) : string {
		return '\''.$var.'\'=>'.self::formatValues($this->arrConfiguration[$var]).','.PHP_EOL;
	}
}