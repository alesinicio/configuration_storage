<?php
namespace SinFramework\ConfigurationStorage;

interface ConfigurationStorageInterface {
	/**
	 * Sets if instance can write new keys (TRUE) or only overwrite existing ones (FALSE).
	 * 
	 * @param bool $boolean
	 */
	public function canWriteNewKeys(bool $boolean) : ConfigurationStorageInterface;
	/**
	 * Sets configuration values in a key/pair fashion.
	 * 
	 * @param array $arrConfiguration
	 */
	public function setVals(array $arrConfiguration) : ConfigurationStorageInterface;
	/**
	 * Effectively commit changes.
	 */
	public function commit() : bool;
	/**
	 * Gets configuration value.
	 * 
	 * @param string $key
	 */
	public function getVal(string $key);
	/**
	 * Deletes configuration key.
	 * 
	 * @param string $key
	 */
	public function deleteKey(string $key) : bool ;
	/**
	 * Sets the storage location, in a context that makes sense for the specific implementation.
	 * 
	 * @param string $strStorageLocation
	 */
	public function setStorageLocation(string $strStorageLocation) : ConfigurationStorageInterface;
	/**
	 * Returns all available configuration in array format.
	 * 
	 * @return array
	 */
	public function getAllVals() : array;
}