<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name entry.php
 * @date 2014-09-01 17:24:22
 */
 



if ( !class_exists( 'Translation_Entry' ) ):

class Translation_Entry {

	
	var $is_plural = false;

	var $context = null;
	var $singular = null;
	var $plural = null;
	var $translations = array();
	var $translator_comments = '';
	var $extracted_comments = '';
	var $references = array();
	var $flags = array();

	
	function Translation_Entry($args=array()) {
				if (!isset($args['singular'])) {
			return;
		}
				foreach ($args as $varname => $value) {
			$this->$varname = $value;
		}
		if (isset($args['plural'])) $this->is_plural = true;
		if (!is_array($this->translations)) $this->translations = array();
		if (!is_array($this->references)) $this->references = array();
		if (!is_array($this->flags)) $this->flags = array();
	}

	
	function key() {
		if (is_null($this->singular)) return false;
				return is_null($this->context)? $this->singular : $this->context.chr(4).$this->singular;
	}
}
endif;