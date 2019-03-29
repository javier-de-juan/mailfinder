<?php declare(strict_types = 1);

	namespace CVREADER;

	abstract class CvReaderAbstract {

		private const EMAIL_PATTERN = "/[_A-Za-z0-9-]+(\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*(\.[A-Za-z]{2,})/m";
		public const VALID_FORMATS = array( "doc", "docx", "docm", "rtf", "txt", "pdf" );

		protected $parser;

		protected function extractEmail( string $textPage ): ?string {
			preg_match_all( self::EMAIL_PATTERN, $textPage, $matches );

			return $matches[0][0] ?? '';
		}

	}