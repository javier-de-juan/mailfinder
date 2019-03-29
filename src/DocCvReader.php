<?php declare(strict_types = 1);

	namespace CVREADER;

	use CVREADER\Helpers\DocxConversion;
	use LukeMadhanga\DocumentParser;

	class DocCvReader extends CvReaderAbstract {

		public function extractEmailInfo( string $filePath ): array {
			try {
				$emails = array();
				$file   = strip_tags( DocumentParser::parseFromFile( $filePath ), '<p>' );

				if ( $pageEmails = $this->extractEmail( $file ) ) {
					array_push( $emails, $pageEmails );
				}

				//second parser just for try
				if ( empty( $emails ) ) {
					$docConverter = new DocxConversion( $filePath );
					$file         = $docConverter->convertToText();
					if ( $pageEmails = $this->extractEmail( $file ) ) {
						array_push( $emails, $pageEmails );
					}
				}

				return array_unique( $emails );
			} catch (\Exception $e) {
				return array();
			}
		}
	}