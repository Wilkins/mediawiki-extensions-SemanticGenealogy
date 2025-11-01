<?php

namespace SemanticGenealogy\Gedcom;

use SemanticGenealogy\PersonPageValues;
use \SMWQuery;
use \SMWQueryProcessor;
use \SMWQueryResult;
use \SMW\ResultPrinter;

/**
 * Printer class for creating GEDCOM exports
 *
 * @file    GedcomResultPrinter.php
 * @ingroup SemanticGenealogy
 *
 * @licence GNU GPL v2+
 * @author  Thomas Pellissier Tanon <thomaspt@hotmail.fr>
 */
class Gedcom5ResultPrinter extends ResultPrinter
{
	public $ids = [];

	/**
	 * Get the mimetype of the result printer
	 *
	 * @param string|object $res the result printer
	 *
	 * @return string the mimetype
	 */
	public function getMimeType( $res ) {
		return 'application/x-gedcom';
	}

	/**
	 * Get the filename of the result printer
	 *
	 * @param string|object $res the result printer
	 *
	 * @return string the filename
	 */
	public function getFileName( $res ) {
		if ( $this->getSearchLabel( SMW_OUTPUT_WIKI ) != '' ) {
			return str_replace( ' ', '_', $this->getSearchLabel( SMW_OUTPUT_WIKI ) ) . '.ged';
		} else {
			return 'GEDCOM.ged';
		}
	}

	/**
	 * Get the query mode
	 *
	 * @param string $context the context
	 *
	 * @return string the query mode
	 */
	public function getQueryMode( $context ) {
		return ( $context == SMWQueryProcessor::SPECIAL_PAGE )
			? SMWQuery::MODE_INSTANCES : SMWQuery::MODE_NONE;
	}

	/**
	 * Get the Result printer name
	 *
	 * @return the name of the result printer
	 */
	public function getName() {
		return wfMessage( 'semanticgenealogy-gedcomexport-desc' )->text();
	}

	/**
	 * Get the result test of the result printer
	 *
	 * @param SMWQueryResult $res the result
	 * @param integer $outputmode the output mode chosen
	 *
	 * @return string the result text
	 */
	protected function _getResultText( SMWQueryResult $res, $outputmode ) {
		$result = '';

		if ( $outputmode == SMW_OUTPUT_FILE ) {
			$people = [];
			$row = $res->getNext();
			while ( $row !== false ) {
				$people[] = new PersonPageValues( $row[0]->getResultSubject() );
				$row = $res->getNext();
			}
			$printer = new Gedcom5FilePrinter();
			$printer->addPeople( $people );
			$result = $printer->getFile();
		} else {
			   // just make link
			if ( $this->getSearchLabel( $outputmode ) ) {
				$label = $this->getSearchLabel( $outputmode );
			} else {
				$label = wfMessage( 'semanticgenealogy-gedcomexport-link' )->inContentLanguage()->text();
			}
			$link = $res->getQueryLink( $label );
			$link->setParameter( 'gedcom5', 'format' );
			if ( $this->getSearchLabel( SMW_OUTPUT_WIKI ) != '' ) {
				$link->setParameter( $this->getSearchLabel( SMW_OUTPUT_WIKI ), 'searchlabel' );
			}
			/*
			if ( array_key_exists( 'limit', $this->m_params ) ) {
				$link->setParameter( $this->m_params['limit'], 'limit' );
			} else {
				   // use a reasonable default limit
			}
				$link->setParameter( 20, 'limit' );
			 */
			$result .= $link->getText( $outputmode, $this->mLinker );
			// yes, our code can be viewed as HTML if requested, no more parsing needed
			$this->isHTML = ( $outputmode == SMW_OUTPUT_HTML );
		}
		return $result;
	}


    /**
     * @see ResultPrinter::getResultText
     *
     * {@inheritDoc}
     */
    protected function getResultText( \SMWQueryResult $res, $outputMode ) {

        if ( $outputMode !== SMW_OUTPUT_FILE ) {
            #return $this->getDsvLink( $queryResult, $outputMode );
			$people = [];
			$row = $res->getNext();
			while ( $row !== false ) {
				$people[] = new PersonPageValues( $row[0]->getResultSubject() );
				$row = $res->getNext();
			}
			$printer = new Gedcom5FilePrinter();
			$printer->addPeople( $people );

			$result = $printer->getFile();
            //echo "site : ".strlen($result)."<br>\n";
            //echo mb_detect_encoding($result);
            //echo mb_detect_encoding($result);
            $result2 = mb_convert_encoding($result, 'ISO-8859-1', 'UTF-8');
            //echo "encoding : ".mb_detect_encoding($result2)."<br>\n";

            //echo "site : ".strlen($result2)."<br>\n";
            //echo mb_detect_encoding($result);

            header('Content-Type: application/octet-stream');
			#header('Content-Type: text/plain');

            header('Content-Description: File Transfer');
            header("Content-Disposition: attachment; filename=\"gedcom_".date('Y-m-d-H-i-s').".ged\"");
            header("Content-Length: " . strlen($result2));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            echo $result2;
            exit;
            /*
            strlen($result);
            echo $result;
            exit;
            echo iconv('UTF-8', 'ISO-8859-1//IGNORE', $result);
            exit;
			return iconv('UTF-8', 'ISO-8859-1//IGNORE', $result);
            */
        }

        return $this->buildContents( $queryResult );
    }

    private function buildContents( QueryResult $queryResult ) {
        $lines = [];

        // Do not allow backspaces as delimiter, as they'll break stuff.
        if ( trim( $this->params['separator'] ) != '\\' ) {
            $this->params['separator'] = trim( $this->params['separator'] );
        }

        /**
         * @var ResultPrinter::mShowHeaders
         */
        $showHeaders = $this->mShowHeaders;

        if ( $showHeaders ) {
            $headerItems = [];

            foreach ( $queryResult->getPrintRequests() as $printRequest ) {
                $headerItems[] = $printRequest->getLabel();
            }

            $lines[] = $this->getDSVLine( $headerItems );
        }

        // Loop over the result objects (pages).
        while ( $row = $queryResult->getNext() ) {
            $rowItems = [];

            /**
             * Loop over their fields (properties).
             * @var SMWResultArray $field
             */
            foreach ( $row as $field ) {
                $itemSegments = [];

                // Loop over all values for the property.
                while ( ( $object = $field->getNextDataValue() ) !== false ) {
                    $itemSegments[] = Sanitizer::decodeCharReferences( $object->getWikiValue() );
                }

                // Join all values into a single string, separating them with comma's.
                $rowItems[] = implode( ',', $itemSegments );
            }

            $lines[] = $this->getDSVLine( $rowItems );
        }

        return utf8_decode(implode( "\n", $lines ));
    }

    /**
     * @see ResultPrinter::getParamDefinitions
     *
     * {@inheritDoc}
     */
    public function getParamDefinitions( array $definitions ) {

        // You should always get the params added by the parent class,
        // using the parent.
        $definitions = parent::getParamDefinitions( $definitions );

        $definitions[] = [
            'name' => 'separator',
            'message' => 'smw-paramdesc-separator',
            'default' => '',
        ];

        return $definitions;
    }


	/**
	 * Get all the parameters
	 *
	 * @return array the base parameters and the export format parameters
	 */
	public function getParameters() {
		return array_merge( parent::getParameters() );//, $this->exportFormatParameters() );
	}
	/*
	 */
}
