<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name mo.php
 * @date 2014-09-01 17:24:22
 */
 



require_once dirname(__FILE__) . '/translations.php';
require_once dirname(__FILE__) . '/streams.php';

if ( !class_exists( 'MO' ) ):
class MO extends Gettext_Translations {

	var $_nplurals = 2;

	
	function import_from_file($filename) {
		$reader = new POMO_FileReader($filename);
		if (!$reader->is_resource())
			return false;
		return $this->import_from_reader($reader);
	}

	function export_to_file($filename) {
		$fh = fopen($filename, 'wb');
		if ( !$fh ) return false;
		$entries = array_filter($this->entries, create_function('$e', 'return !empty($e->translations);'));
		ksort($entries);
		$magic = 0x950412de;
		$revision = 0;
		$total = count($entries) + 1; 		$originals_lenghts_addr = 28;
		$translations_lenghts_addr = $originals_lenghts_addr + 8 * $total;
		$size_of_hash = 0;
		$hash_addr = $translations_lenghts_addr + 8 * $total;
		$current_addr = $hash_addr;
		fwrite($fh, pack('V*', $magic, $revision, $total, $originals_lenghts_addr,
			$translations_lenghts_addr, $size_of_hash, $hash_addr));
		fseek($fh, $originals_lenghts_addr);

				fwrite($fh, pack('VV', 0, $current_addr));
		$current_addr++;
		$originals_table = chr(0);

		foreach($entries as $entry) {
			$originals_table .= $this->export_original($entry) . chr(0);
			$length = strlen($this->export_original($entry));
			fwrite($fh, pack('VV', $length, $current_addr));
			$current_addr += $length + 1; 		}

		$exported_headers = $this->export_headers();
		fwrite($fh, pack('VV', strlen($exported_headers), $current_addr));
		$current_addr += strlen($exported_headers) + 1;
		$translations_table = $exported_headers . chr(0);

		foreach($entries as $entry) {
			$translations_table .= $this->export_translations($entry) . chr(0);
			$length = strlen($this->export_translations($entry));
			fwrite($fh, pack('VV', $length, $current_addr));
			$current_addr += $length + 1;
		}

		fwrite($fh, $originals_table);
		fwrite($fh, $translations_table);
		fclose($fh);
	}

	function export_original($entry) {
				$exported = $entry->singular;
		if ($entry->is_plural) $exported .= chr(0).$entry->plural;
		if (!is_null($entry->context)) $exported = $entry->context . chr(4) . $exported;
		return $exported;
	}

	function export_translations($entry) {
				return implode(chr(0), $entry->translations);
	}

	function export_headers() {
		$exported = '';
		foreach($this->headers as $header => $value) {
			$exported.= "$header: $value\n";
		}
		return $exported;
	}

	function get_byteorder($magic) {
		
				$magic_little = (int) - 1794895138;
		$magic_little_64 = (int) 2500072158;
				$magic_big = ((int) - 569244523) & 0xFFFFFFFF;
		if ($magic_little == $magic || $magic_little_64 == $magic) {
			return 'little';
		} else if ($magic_big == $magic) {
			return 'big';
		} else {
			return false;
		}
	}

	function import_from_reader($reader) {
		$endian_string = MO::get_byteorder($reader->readint32());
		if (false === $endian_string) {
			return false;
		}
		$reader->setEndian($endian_string);

		$endian = ('big' == $endian_string)? 'N' : 'V';

		$header = $reader->read(24);
		if ($reader->strlen($header) != 24)
			return false;

				$header = unpack("{$endian}revision/{$endian}total/{$endian}originals_lenghts_addr/{$endian}translations_lenghts_addr/{$endian}hash_length/{$endian}hash_addr", $header);
		if (!is_array($header))
			return false;

		extract( $header );

				if ($revision != 0)
			return false;

				$reader->seekto($originals_lenghts_addr);

				$originals_lengths_length = $translations_lenghts_addr - $originals_lenghts_addr;
		if ( $originals_lengths_length != $total * 8 )
			return false;

		$originals = $reader->read($originals_lengths_length);
		if ( $reader->strlen( $originals ) != $originals_lengths_length )
			return false;

				$translations_lenghts_length = $hash_addr - $translations_lenghts_addr;
		if ( $translations_lenghts_length != $total * 8 )
			return false;

		$translations = $reader->read($translations_lenghts_length);
		if ( $reader->strlen( $translations ) != $translations_lenghts_length )
			return false;

				$originals    = $reader->str_split( $originals, 8 );
		$translations = $reader->str_split( $translations, 8 );

				$strings_addr = $hash_addr + $hash_length * 4;

		$reader->seekto($strings_addr);

		$strings = $reader->read_all();
		$reader->close();

		for ( $i = 0; $i < $total; $i++ ) {
			$o = unpack( "{$endian}length/{$endian}pos", $originals[$i] );
			$t = unpack( "{$endian}length/{$endian}pos", $translations[$i] );
			if ( !$o || !$t ) return false;

						$o['pos'] -= $strings_addr;
			$t['pos'] -= $strings_addr;

			$original    = $reader->substr( $strings, $o['pos'], $o['length'] );
			$translation = $reader->substr( $strings, $t['pos'], $t['length'] );

			if ('' === $original) {
				$this->set_headers($this->make_headers($translation));
			} else {
				$entry = &$this->make_entry($original, $translation);
				$this->entries[$entry->key()] = &$entry;
			}
		}
		return true;
	}

	
	function &make_entry($original, $translation) {
		$entry = new Translation_Entry();
				$parts = explode(chr(4), $original);
		if (isset($parts[1])) {
			$original = $parts[1];
			$entry->context = $parts[0];
		}
				$parts = explode(chr(0), $original);
		$entry->singular = $parts[0];
		if (isset($parts[1])) {
			$entry->is_plural = true;
			$entry->plural = $parts[1];
		}
				$entry->translations = explode(chr(0), $translation);
		return $entry;
	}

	function select_plural_form($count) {
		return $this->gettext_select_plural_form($count);
	}

	function get_plural_forms_count() {
		return $this->_nplurals;
	}
}
endif;