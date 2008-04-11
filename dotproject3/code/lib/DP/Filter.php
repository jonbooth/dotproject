<?php
/**
 * A generic filter object for lists containing one or more columns/fields.
 *
 * This object can contain multiple filter rules and multiple rules of the same operation.
 * The filter is usually applied when the data set is generated but could also be used with
 * already instantiated lists to iterate through matching items.
 *
 * @package dotproject
 * @subpackage system
 * @todo Better retrieval method for filters, Better method of associating filters with views.
 * @todo Better method for converting filter rules to SQL
 * @todo combine method for combining two filter objects
 */
class DP_Filter implements SplSubject {
	private $_observers;
	/**
	 * Array of filter rules.
	 *
	 * @var array
	 */
	public $filters;
	private $id;

	/**
	 * @var integer $next_fid The next filter id to assign.
	 */
	private $next_fid;

	// Filter Types
	const VALUE_EQUAL = 0;
	const VALUE_LT = 1;
	const VALUE_GT = 2;
	const VALUE_SUBSTR = 10;

	function __construct($id = -1) {
		$this->_observers = Array();
		
		$this->filters = Array();
		$this->id = $id;
		$this->next_fid = 0;
	}
	public function id() {
		return $this->id;
	}
	/**
	 * Add a rule which filters the list by a field's value.
	 *
	 * @param string $field_name Name of the field/column to filter.
	 * @param string $field_value Value the field must be equal to in order to pass the filter.
	 */
	public function fieldEquals($field_name, $field_value) {
		$this->next_fid++;
		$this->filters[] = Array("filter_type"=>DP_Filter::VALUE_EQUAL,
								"filter_id"=>$this->next_fid, 
								"filter_field"=>$field_name, 
								"field_value"=>$field_value);
	}

	/**
	 * Add a rule which filters the list by a field which contains the specified substring.
	 *
	 * @param string $field_name Name of the field/column to filter.
	 * @param string $value_like The substring to use when comparing with the field's value.
	 */
	public function fieldSubstring($field_name, $value_like) {
		$this->next_fid++;
		$this->filters[] = Array("filter_type"=>DP_Filter::VALUE_SUBSTR, 
								"filter_id"=>$this->next_fid, 
								"filter_field"=>$field_name, 
								"field_value"=>$value_like);
	}

	/**
	 * Retrieve the first filter acting upon a given field
	 *
	 * @param string $field_name Name of the field being filtered.
	 * @return array Array containing the first rule which is acting on the specified field.
	 */
	public function getFilter($field_name) {
		foreach ($this->filters as $filter) {
			if ($filter['filter_field'] == $field_name) {
				return $filter;
			}
		}
	}

	/**
	 * Retrieve all filters acting upon a given field
	 *
	 * @param string $field_name Name of the field being filtered.
	 * @return array Associative array of filter rules
	 */
	public function getFilters($field_name) {
		$subset = Array();
		foreach ($this->filters as $filter) {
			if ($filter['filter_field'] == $field_name) {
				$subset[] = $filter;
			}
		}
		return $subset;
	}

	/**
	 * Add filter rules from another DP_Filter instance.
	 *
	 * @param DP_Filter $filter Another DP_Filter instance.
	 * @todo check whether filter rules already exist.
	 */
	public function addFromFilter(DP_Filter $filter) {
		$add_filters = $filter->filters;
		foreach ($add_filters as $f) {
			$this->filters[] = $f;
		}
	}

	/**
	 * Delete the first filter acting upon a given field
	 *
	 * @param string $field_name Name of the field being filtered.
	 * @todo Implement method or use better criteria for retrieving filters.
	 */
	public function deleteFilterByField($field_name) {

	}
	
	/**
	 * Delete a filter by its unique ID.
	 * 
	 * @param integer $filter_id ID of the filter to delete.
	 */
	public function deleteFilterById($filter_id) {
		for ($i = 0; $i < count($this->filters); $i++) {
			$rule = $this->filters[$i];
			if ($rule['filter_id'] == $filter_id) {
				unset($this->filters[$i]);
			}
			$this->filters = array_values($this->filters);
		}
	}

	/**
	 * Delete all of the filter rules.
	 */
	public function deleteAllRules() {
		$this->filters = null;
		$this->filters = Array();
	}

	/**
	 * Get the number of rules in this filter.
	 *
	 * @return integer Number of rules
	 */
	public function count() {
		return count($this->filters);
	}

	/**
	 * Get an iterator for this class.
	 *
	 * @return DP_Filter_Iterator Filter iterator.
	 */
	public function getIterator() {
		return new DP_Filter_Iterator($this);
	}

	// From SplSubject
	
	/**
	 * Attach an observer
	 * 
	 * @param SplObserver $observer The observer to attach
	 * @return null
	 */
	public function attach(SplObserver $observer) {
		if (!in_array($observer, $this->_observers)) {
			$this->_observers[] = $observer;
			$observer->update($this);
		}		
	}
	
	/**
	 * Detach an observer
	 * 
	 * @param SplObserver $observer The observer to detach
	 * @return null
	 */
 	public function detach (SplObserver $observer) {
 		if (in_array($observer, $this->_observers)) {
			$observer_key = array_search($this->_observers, $observer);
			$this->_observers[$observer_key] = null;
			
			$reordered_observers = array_values($this->_observers);
			$this->_observers = $reordered_observers;
		}		
 	}
 	
 	/**
 	 * Notify all observers
 	 * 
 	 */
 	public function notify() {
 		foreach($this->_observers as $ob) {
 			$ob->update($this);
 		}
 	}
}
?>