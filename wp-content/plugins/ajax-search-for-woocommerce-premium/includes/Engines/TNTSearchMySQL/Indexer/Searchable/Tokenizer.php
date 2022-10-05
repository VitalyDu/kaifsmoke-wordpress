<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Indexer\Searchable;

use DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support\TokenizerInterface;


class Tokenizer implements TokenizerInterface {

	/**
	 * Tokenizer context
	 * Tokenizer may be used in indexer or search process
	 *
	 * @var string
	 */
	private $context = 'search';

	/**
	 * Set context
	 *
	 * @return void
	 */
	public function setContext( $context ) {
		switch ( $context ) {
			case 'search':
				$this->context = 'search';
				break;
			case 'indexer':
				$this->context = 'indexer';
				break;
		}
	}

	public function tokenize( $text, $stopwords = array() ) {

		$chars      = $this->getSpecialChars();
		$text       = mb_strtolower( $text );
		$charsRegex = empty( $chars ) ? '' : '\\' . implode( '\\', $chars );

		$split = preg_split( "/[^\p{L}\p{N}" . $charsRegex . "]+/u", $text, - 1, PREG_SPLIT_NO_EMPTY );

		if ( ! empty( $split ) ) {
			$split = $this->createExtraVariations( $chars, $split );
		}

		$tokens = array_diff( $split, $stopwords );

		if ( $this->context === 'search' ) {
			$tokensLimit = apply_filters( 'dgwt/wcas/tokenizer/tokens_limit', 10 );
			// Limit the number of tokens
			$tokens = array_splice( $tokens, 0, $tokensLimit );
		}

		$tokens = apply_filters( 'dgwt/wcas/tokenizer/tokens', $tokens, $text, $this->context );

		return array_filter( array_unique( $tokens ) );
	}

	/**
	 * Get special chars that should be ignored during tokenization process
	 *
	 * @return array
	 */
	public function getSpecialChars() {
		$chars = array( '-', '_', '.', ',', '/' );
		if ( $this->context === 'search' ) {
			$chars = array();
		}

		return apply_filters( 'dgwt/wcas/tokenizer/special_chars', $chars, $this->context );
	}

	/**
	 * Creates extra variations of words
	 * e.g for phrase "PROD-1999/2000" creates variations:
	 *
	 * prod-1999/2000
	 * prod
	 * 1999/2000
	 * prod1999/2000
	 * prod-1999
	 * 2000
	 * prod-19992000
	 * 1999
	 * 19992000
	 * prod1999
	 * prod19992000
	 *
	 * @param $chars
	 * @param $tokens
	 *
	 * @return array
	 */
	private function createExtraVariations( $chars, $tokens ) {

		if ( ! empty( $chars ) && is_array( $chars ) ) {
			foreach ( $chars as $char ) {
				foreach ( $tokens as $token ) {
					$elements = explode( $char, $token );
					if ( count( $elements ) > 1 ) {

						if ( $this->context === 'indexer' ) {
							$elements[] = str_replace( $char, '', $token ); // Binds by special chars
						}

						$tokens = array_merge( $tokens, $elements );
					}

				}
			}
		}

		return $tokens;
	}
}
