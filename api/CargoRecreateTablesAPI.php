<?php
/**
 * Adds and handles the 'cargorecreatetables' action to the MediaWiki API.
 *
 * @ingroup Cargo
 * @author Yaron Koren
 */

class CargoRecreateTablesAPI extends ApiBase {

	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName );
	}

	public function execute() {
		global $wgUser;

		if ( !$wgUser->isAllowed( 'recreatecargodata' ) || $wgUser->isBlocked() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}

		$params = $this->extractRequestParams();
		$templateStr = $params['template'];
		if ( $templateStr == '' ) {
			$this->dieUsage( 'The template must be specified', 'param_substr' );
		}
		$createReplacement = $params['createReplacement'];

		$templateTitle = Title::makeTitleSafe( NS_TEMPLATE, $templateStr );
		$templatePageID = $templateTitle->getArticleID();
		$success = CargoUtils::recreateDBTablesForTemplate( $templatePageID, $createReplacement );

		// Set top-level elements.
		$result = $this->getResult();
		$result->addValue( null, 'success', true );
	}

	protected function getAllowedParams() {
		return array(
			'template' => array(
				ApiBase::PARAM_TYPE => 'string',
			),
			'createReplacement' => array(
				ApiBase::PARAM_TYPE => 'boolean',
			),
		);
	}

	protected function getParamDescription() {
		return array(
			'template' => 'The template whose declared Cargo table(s) should be recreated',
			'createReplacement' => 'Whether to put data into a replacement table',
		);
	}

	protected function getDescription() {
		return 'An API module to recreate tables for the Cargo extension '
			. '(http://www.mediawiki.org/Extension:Cargo)';
	}

	protected function getExamples() {
		return array(
			'api.php?action=cargorecreatetables&template=City'
		);
	}

}
