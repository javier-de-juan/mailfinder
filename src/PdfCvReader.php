<?php declare( strict_types=1 );

	namespace CVREADER;

	use \Smalot\PdfParser\Parser;

	class PdfCvReader extends CvReaderAbstract {

		public function __construct() {
			$this->parser = new Parser();
		}

		public function extractEmailInfo( string $filePath ): array {
			try {
				$emails = array();
				$file   = $this->parser->parseFile( $filePath );

				$pages = $file->getPages();

				foreach ( $pages as $page ) {
					if ( $pageEmails = $this->extractEmail( $page->getText() ) ) {
						array_push( $emails, $pageEmails );
					}

				}

				if ( empty( $emails ) ) {
					return $this->secondTry( $filePath );
				}

				return array_unique( $emails );
			} catch (\Exception $e) {
				return array();
			}
		}

		private function secondTry( string $filePath ): ?array {
			$emails  = array();

			exec("/usr/local/bin/pdftotext '{$filePath}' -", $texts);

			foreach ($texts as $text) {
				if ( $pageEmails = $this->extractEmail( $text ) ) {
					array_push( $emails, $pageEmails );
				}
			}

			return $emails;
		}
	}