<?php
namespace SinFramework\ConfigurationStorage;

use Exception;

/**
 * @author alexandre.sinicio
 * This class makes it easy to use PHP files as storage for configuration data
 * and accessing/changing the values without manually opening files.
 * The data is stored as separate vars.
 */
class PHPConfigurationFile extends ConfigurationStorageCommon implements ConfigurationStorageInterface {
	private $configurationFilename	= null;
	private $newConfigurationFile	= null;
	private $arrConfiguration		= [];
	private $writeNewVars			= false;
	
	public function __construct(string $configurationFilename=null) {
		$this->setStorageLocation($configurationFilename);
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::canWriteNewKeys()
	 */
	public function canWriteNewKeys(bool $boolean) : ConfigurationStorageInterface {
		$this->writeNewVars = $boolean;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::setVals()
	 */
	public function setVals(array $arrConfiguration) : ConfigurationStorageInterface {
		$this->arrConfiguration = $arrConfiguration;
	}
	/**
	 * {@inheritDoc}
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::setStorageLocation()
	 */
	public function setStorageLocation(string $filename) : ConfigurationStorageInterface {
		$this->configurationFilename = $filename;
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
	public function getVal(string $key) {
		if (!file_exists($this->configurationFilename)) throw new Exception('Configuration file does not exists');
		require $this->configurationFilename;
		
		if (isset($$key)) return $$key;
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
	 * @see \SinFramework\ConfigurationStorage\ConfigurationStorageInterface::getAllVals()
	 */
	public function getAllVals() : array {
		throw new Exception('Method not available');
	}
	/**
	 * Effectively creates the string to be written to the new file, updating values, creating new keys and deleting values.
	 * @param array $exclusions Keys that should be deleted
	 * @return boolean Returns whether the new configuration was successfully writen to the configuration file
	 */
	private function parseNewFile(array $exclusions=[]) : bool {
		$currentConfig	= $this->readCurrentConfig();
		
		if ($this->writeNewVars) {
			$config = array_merge($currentConfig, $this->arrConfiguration);
		} else {
			$config = $currentConfig;
			foreach($this->arrConfiguration as $key=>$val) {
				if (isset($currentConfig[$key])) {
					$config[$key] = $val;
				}
			}
		}
		
		foreach($exclusions as $key) {
			unset($config[$key]);
		}
		
		$strNewFile = '';
		foreach($config as $key=>$val) {
			$newLine = $this->generateNewLine($key);
			self::nextLine($strNewFile, $newLine);
		}
		
		$this->newConfigurationFile = $strNewFile;
		return $this->writeToFile();
	}
	/**
	 * Helper function to generate new lines/updated configuration for a variable on the configuration file
	 *
	 * @param string $var
	 * @return string
	 */
	private function generateNewLine($var) : string {
		return '$'.$var.' = '.self::formatValues($this->arrConfiguration[$var]).';'.PHP_EOL;
	}
	/**
	 * Reads current configuration and returns it in a key/value array
	 * @return array[]
	 */
	private function readCurrentConfig() : array {
		$handle		= fopen($this->configurationFilename, 'r');
		$vars		= [];
		
		require_once($this->configurationFilename);
		while($strLine = fgets($handle)) {
			if (substr($strLine, 0, 6) != 'define') continue;
			
			$var = substr($strLine, 8, strpos($strLine, '\', ')-8);
			$vars[$var] = constant($var);
		}
		
		$this->arrOldConfiguration = $vars;
		return $vars;
	}
}