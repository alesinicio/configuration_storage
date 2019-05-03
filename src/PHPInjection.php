<?php
namespace SinFramework\ConfigurationStorage;

/**
 * @author alexandre.sinicio
 * This class makes it easy to use PHP files as storage for configuration data
 * and accessing/changing the values without manually opening files.
 * The data is stored as a single named-array var.
 */
class PHPInjection extends PHPArrayFile {
	private $writeNewVars = false;
	
	/**
	 * Effectively creates the string to be written to the new file, updating values, creating new keys and deleting values.
	 * 
	 * @param array $exclusions Keys that should be deleted
	 * @return boolean Returns whether the new configuration was successfully writen to the configuration file
	 */
	protected function parseNewFile(array $exclusions=[]) : bool {
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
		
		$this->newConfigurationFile = '<?php '.PHP_EOL.'$injection='.var_export($config, true).';'.PHP_EOL.'return $injection;';
		return $this->writeToFile();
	}
}