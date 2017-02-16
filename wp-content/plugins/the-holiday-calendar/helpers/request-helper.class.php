<?php
class request_helper {
	const queryIsModifiedKey = 'thc_request_is_modified';
	const queryWasModifiedKey = 'thc_request_was_modified';
	const surpressTitleFilterKey = 'thc_surpress_title_filter';
	const readMoreTextsKey = 'thc_read_more_texts';
	
	static function get_query_is_modified()
	{
		if(!array_key_exists(self::queryIsModifiedKey, $_REQUEST))
		{
			return null;
		}
		
		return $_REQUEST[self::queryIsModifiedKey];
	}
	
	static function set_query_is_modified($value)
	{
		$_REQUEST[self::queryIsModifiedKey] = $value;
	}
	
	static function get_query_was_modified()
	{
		if(!array_key_exists(self::queryWasModifiedKey, $_REQUEST))
		{
			return null;
		}
		
		return $_REQUEST[self::queryWasModifiedKey];
	}
	
	static function set_query_was_modified($value)
	{
		$_REQUEST[self::queryWasModifiedKey] = $value;
	}
	
	static function get_surpress_title_filter()
	{
		if(!array_key_exists(self::surpressTitleFilterKey, $_REQUEST))
		{
			return false;
		}
		
		return $_REQUEST[self::surpressTitleFilterKey];
	}
	
	static function set_surpress_title_filter($value)
	{
		$_REQUEST[self::surpressTitleFilterKey] = $value;
	}
	
	static function get_read_more_texts()
	{
		if(!array_key_exists(self::readMoreTextsKey, $_REQUEST))
		{
			return array();
		}
		
		return $_REQUEST[self::readMoreTextsKey];
	}
	
	static function set_read_more_texts($value)
	{		
		$_REQUEST[self::readMoreTextsKey] = $value;
	}
}
?>