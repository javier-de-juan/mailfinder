<?php declare(strict_types = 1);

	namespace CVREADER\Helpers;

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	/**
	 * Class EasyExcelReport generates a one column excel with emails list.
	 * @package CVREADER\Helpers
	 */
	class EasyExcelReport {

		private $spreadsheet;
		private $sheet;
		private $cell;


		public function __construct() {
			$this->spreadsheet = new Spreadsheet();
			$this->sheet       = $this->spreadsheet->getActiveSheet();
			$this->cell        = 1;
			$this->setHeader();
		}

		/**
		 * Adds all possible emails to the sheet.
		 *
		 * @param array $row Array with n emails.
		 */
		public function addToRow( array $row ): void {
			array_map( function ( $email ) {
				$this->setCellValue( $this->cell, strtolower( $email ) );
				$this->cell ++;
			}, $row );
		}

		/**
		 * Sets the header for sheet.
		 */
		private function setHeader(): void {
			$this->setCellValue( $this->cell, 'Email' );
			$this->cell ++;
		}

		/**
		 * Set the cell value for A column.
		 *
		 * @param int $cell Cell number for A column
		 * @param string $value Email value
		 */
		private function setCellValue( int $cell, string $value ): void {
			$this->sheet->setCellValue( "A{$cell}", $value );
		}


		/**
		 * Saves the excel in the project root
		 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
		 */
		function generate(): void {
			$this->sheet->getColumnDimension( 'A' )->setAutoSize( true );
			$writer = new Xlsx( $this->spreadsheet );
			$writer->save( __DIR__ . '../../../emails-in-cvs.xlsx' );
		}
	}