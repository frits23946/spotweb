<?php

class Services_Upgrade_Base {
	private $_daoFactory;
	private $_dbStruct;
	private $_phpSettings;
	
	function __construct(Dao_Factory $daoFactory, Services_Settings_Base $settings, $dbEngine) {
		$this->_daoFactory = $daoFactory;
		$this->_dbStruct = SpotStruct_Abs::factory($dbEngine, $daoFactory->getConnection());
		$this->_settings = $settings;
	} # ctor
	
	/*
	 * Upgrade de settings
	 */
	function settings() {
		$svcUpgradeSettings = new Services_Upgrade_Settings($this->_daoFactory, $this->_settings);
		$svcUpgradeSettings->update();
	} # settings

	/*
	 * Upgrade de users
	 */
	function users() {
		$svcUpgradeUser = new Services_Upgrade_Users($this->_daoFactory, $this->_settings);
		$svcUpgradeUser->update();
	} # users
	 
	/*
	 * Creeert en upgrade de database
	 */
	function database() {
		$this->_dbStruct->updateSchema();
	 } # database

	/*
	 * Optimaliseert de database
	 */
	function analyze() {
		# Instantieeer een struct object
		$this->_dbStruct->analyze();
	 } # analyze

	/*
	 * Reset users' group membership
	 */
	function resetUserGroupMembership() {
		$svcUpgradeUser = new Services_Upgrade_User($this->_daoFactory, $this->_settings);
		$svcUpgradeUser->resetUserGroupMembership($settings->get('systemtype'));
	} # resetUserGroupMembership

	/*
	 * Reset securitygroup settings to their default
	 */
	function resetSecurityGroups() {
		$svcUpgradeUser = new Services_Upgrade_User($this->_daoFactory, $this->_settings);
		$svcUpgradeUser->updateSecurityGroups(true);
	} # resetSecurityGroups

	/*
	 * Reset users' filters settings to their default
	 */
	function resetFilters() {
		$svcUpgradeUser = new Services_Upgrade_User($this->_daoFactory, $this->_settings);
		$svcUpgradeUser->updateUserFilters(true);
	} # resetFilters
	 
	/*
	 * Perform a mass change for users' preferences
	 */
	function massChangeUserPreferences($prefName, $prefValue) {
		$svcUpgradeUser = new Services_Upgrade_User($this->_daoFactory, $this->_settings);
		$svcUpgradeUser->massChangeUserPreferences($prefName, $prefValue);
	} # massChangeUserPreferences

	/*
	 * Reset a systems' type to the given setting
	 */
	function resetSystemType($systemType) {
		$svcUpgradeUser = new Services_Upgrade_User($this->_daoFactory, $this->_settings);
		$svcUpgradeSettings = new Services_Upgrade_Settings($this->_daoFactory, $this->_settings);

		# change the systems' type
		$svcUpgradeSettings->setSystemType($systemType);
		
		# and reset all the users' group memberships for all users to match
		$svcUpgradeUser->resetUserGroupMembership($systemType);
	} # resetSystemType

	/*
	 * Reset a users' password
	 */
	function resetPassword($username) {
		$svcUpgradeUser = new Services_Upgrade_User($this->_daoFactory, $this->_settings);

		# retrieve the userid
		$svcUpgradeUser->resetUserPassword($username, 'spotweb');
	} # resetPassword

} # Services_Upgrade_Base
