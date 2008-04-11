<?php
/**
 * Interface used to define a data source for a DP_View
 * 
 * This interface must be implemented for any object to be used as a data source for
 * a view object rendering a collection of items.
 * 
 * @package dotproject
 * @subpackage system
 * @version 3.0 alpha
 * 
 */
interface DP_View_List_DataSource {

	/**
	 * Notify the datasource that the client is about to render its view.
	 * 
	 * This gives the datasource a chance to refresh its query before the items are iterated through.
	 */
	public function clientWillRender();
	
	/**
	 * Get an array of column names
	 */
	//public function getColumns();
	
	/**
	 * Get an iterator for the data source
	 */
	//public function getIterator();
}
?>