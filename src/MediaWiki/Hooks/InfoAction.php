<?php

namespace SMW\MediaWiki\Hooks;

use IContextSource;
use OutputPage;
use ParserOptions;
use ParserOutput;
use SMW\ApplicationFactory;
use SMW\MediaWiki\MessageBuilder;
use Title;

/**
 * InfoAction hook to add text after the action=info page content
 *
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/InfoAction
 *
 * @license GNU GPL v2+
 * @since 2.3
 *
 * @author ?
 */
class InfoAction {

	/**
	 * @var string
	 */
	protected $pageInfo = null;

	/**
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 * @param array $pageInfo
	 * @param IContextSource $context
	 */
	public function __construct( IContextSource $context, &$pageInfo ) {
		$this->context = $context;
		$this->pageInfo =& $pageInfo;
	}

	/**
	 * @return true
	 */
	public function process() {
		return $this->canPerformUpdate() ? $this->performUpdate() : true;
	}

	private function canPerformUpdate() {

		$title = $this->context->getOutput()->getTitle();

		if ( $title->isSpecialPage() ||
			$title->isRedirect() ||
			!$this->isSemanticEnabledNamespace( $title ) ) {
			return false;
		}

		return true;
	}

	private function performUpdate() {
		$cachedFactbox = ApplicationFactory::getInstance()->newFactboxFactory()->newCachedFactbox();

		$cachedFactbox->prepareFactboxContent(
			$this->context->getOutput(),
			$this->context->getWikiPage()->getParserOutput( new ParserOptions( $this->context->getUser() ) )
		);

		return true;
	}

	private function isSemanticEnabledNamespace( Title $title ) {
		return ApplicationFactory::getInstance()->getNamespaceExaminer()->isSemanticEnabled( $title->getNamespace() );
	}


}
