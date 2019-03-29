<?php declare(strict_types = 1);
	/**
	 * Created by Javier De Juan Trujillo.
	 * License: GPL2
	 * Sorry for the spaghetti code in this file. This just run the real code and show messages. Please, don't judge me.
	 * Date: 2019-02-22
	 */

	include 'vendor/autoload.php';

	use CVREADER\Helpers\Colors;
	use \CVREADER\CvReaderAbstract;
	use \CVREADER\DocCvReader;
	use \CVREADER\PdfCvReader;
	use \CVREADER\Helpers\EasyExcelReport;

	//deshabilitar algunos warnings de la librería de pdf
	error_reporting( E_ERROR | E_PARSE );

	$pdfReader   = new PdfCvReader();
	$docReader   = new DocCvReader();
	$colors      = new Colors();
	$excelReport = new EasyExcelReport();

	print( "==========================================" . PHP_EOL );
	print( " Hello, starting to read the CVS folder:" . PHP_EOL );
	print( "==========================================" . PHP_EOL );

	$mailsFound     = 0;
	$processedFiles = 0;
	$failedFiles    = 0;
	foreach ( glob( __DIR__ . '/cvs/*' ) as $file ) {
		$processedFiles ++;
		$fileExtension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
		$fileName      = $colors->getColoredString( pathinfo( $file, PATHINFO_BASENAME ), 'cyan' );

		if ( in_array( $fileExtension, CvReaderAbstract::VALID_FORMATS ) ) {
			$parser = ( 'pdf' === $fileExtension ) ? $pdfReader : $docReader;
			if ( $emails = $parser->extractEmailInfo( $file ) ) {
				$excelReport->addToRow( $emails );
				$mailsFound += sizeof( $emails );
				print( " - On {$fileName} where found those emails: " . $colors->getColoredString( strtolower( implode( ", ", $emails ) ),
						'green' ) . PHP_EOL );
				unlink( $file );
			} else {
				$failedFiles ++;
				print( " - " . $colors->getColoredString( "No emails found on {$fileName}", 'red' ) . PHP_EOL );
			}

		} else {
			$failedFiles ++;
			print( " - {$fileName}" . $colors->getColoredString( " is not a valid file. Valid files are : ", 'red' )
			       . $colors->getColoredString( implode( ", ", CvReaderAbstract::VALID_FORMATS ), "green" ) . PHP_EOL );
		}
	}

	if ( 0 < $processedFiles ) {
		print( "==========================================" . PHP_EOL );
		print( " I've finished, this is what I did:" . PHP_EOL );
		print( "==========================================" . PHP_EOL );
		print( "  - " . $colors->getColoredString( $processedFiles, 'cyan' ) . " files were processed." . PHP_EOL );
		print( "  - " . $colors->getColoredString( $mailsFound, 'green' ) . " mails were found." . PHP_EOL );
		print( "  - " . $colors->getColoredString( $failedFiles, 'red' ) . " files didn't have any mail." . PHP_EOL );
	} else {
		print( $colors->getColoredString(" There's no files in cvs folder.", "green") . PHP_EOL );
	}
	print( "==========================================" . PHP_EOL );

	if ( 0 < $mailsFound ) {
		try {
			$excelReport->generate();
			print( "  - Excel generated " . $colors->getColoredString( 'successfully', 'green' ) . PHP_EOL );
		} catch ( \Exception $e ) {
			print ( $colors->getColoredString( $e->getMessage(), 'red' ) );
		}

		print( "==========================================" . PHP_EOL );
	}
