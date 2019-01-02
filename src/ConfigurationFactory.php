<?php
namespace SinFramework\ConfigurationStorage;

use Exception;

class ConfigurationFactory {
	const DATABASE		= 1;
	const PHPFILE		= 2;
	const PHPDEFINESFILE= 3;
	const PHPARRAYFILE	= 4;
	
	/**
	 * Gets an instance of the $type ConfigurationStorageInterface.
	 * 
	 * @param string $type
	 * @param array $configuration
	 * @throws Exception
	 * @return ConfigurationStorageInterface
	 */
	public static function getInstance(string $type, array $configuration=[]) : ConfigurationStorageInterface {
		switch ($type) {
			case self::PHPFILE:
				if (!isset($configuration['storage'])) throw new Exception('Configuration should have key "storage".');
				
				return self::createPhpConfigurationFile($configuration);
				break;
			case self::PHPDEFINESFILE:
				if (!isset($configuration['storage'])) throw new Exception('Configuration should have key "storage"');
				
				return self::createPhpDefinesFile($configuration);
				break;
			case self::PHPARRAYFILE:
				if (!isset($configuration['storage'])) throw new Exception('Configuration should have key "storage".');
				
				return self::createPhpArrayFile($configuration);
				break;
			default:
				throw new Exception('Unknown configuration type.');
				break;
		}
	}
	/**
	 * Gets instance of PHPConfigurationFile.
	 * 
	 * @param array $config
	 * @return PHPConfigurationFile
	 */
	private static function createPhpConfigurationFile(array $config) : PHPConfigurationFile {
		$obj = new PHPConfigurationFile($config['storage']);
		return $obj;
	}
	/**
	 * Gets instance of PHPDefinesFile.
	 * 
	 * @param array $config
	 * @return PHPDefinesFile
	 */
	private static function createPhpDefinesFile(array $config) : PHPDefinesFile {
		$obj = new PHPDefinesFile($config['storage']);
		return $obj;
	}
	/**
	 * Gets instance of PHPArrayFile.
	 * 
	 * @param array $config
	 * @return PHPArrayFile
	 */
	private static function createPhpArrayFile(array $config) : PHPArrayFile {
		$obj = new PHPArrayFile($config['storage']);
		return $obj;
	}
}