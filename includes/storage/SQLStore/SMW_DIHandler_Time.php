<?php
/**
 * @file
 * @ingroup SMWDataItemsHandlers
 */

/**
 * This class implements Store access to Time data items.
 *
 * @since 1.8
 *
 * @author Nischay Nahata
 * @ingroup SMWDataItemsHandlers
 */
class SMWDIHandlerTime extends SMWDataItemHandler {

	/**
	 * Method to return array of fields for a DI type
	 *
	 * @return array
	 */
	public function getTableFields() {
		return array( 'o_serialized' => 't', 'o_sortkey' => 'f' );
	}

	/**
	 * Method to return an array of fields=>values for a DataItem
	 *
	 * @return array
	 */
	public function getWhereConds( SMWDataItem $dataItem ) {
		return array( 'o_sortkey' => $dataItem->getSortKey() );
	}

	/**
	 * Method to return an array of fields=>values for a DataItem.
	 * This array is used to perform all insert operations into the DB.
	 * To optimize return minimum fields having indexes.
	 *
	 * @return array
	 */
	public function getInsertValues( SMWDataItem $dataItem ) {
		return array(
			'o_serialized' => $dataItem->getSerialization(),
			'o_sortkey' => $dataItem->getSortKey()
			);
	}

	/**
	 * This type is sorted by a numerical sortkey that maps time values to
	 * a time line.
	 *
	 * @since 1.8
	 * @return string
	 */
	public function getIndexField() {
		return 'o_sortkey';
	}

	/**
	 * This type does not have a label. The string value that we store
	 * internally is a technical serialisation that is not of interest for
	 * asking queries about, in particular since this serialisation might
	 * be changed.
	 *
	 * @since 1.8
	 * @return string
	 */
	public function getLabelField() {
		return '';
	}

	/**
	 * Method to create a dataitem from an array of DB keys.
	 *
	 * @since 1.8
	 * @param $dbkeys array of mixed
	 *
	 * @return SMWDataItem
	 */
	public function dataItemFromDBKeys( $dbkeys ) {
		return SMWDITime::doUnserialize( $dbkeys[0] );
	}
}
