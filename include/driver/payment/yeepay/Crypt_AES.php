<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name Crypt_AES.php
 * @date 2014-09-01 17:24:22
 */
 







define('CRYPT_AES_MODE_CTR', -1);

define('CRYPT_AES_MODE_ECB', 1);

define('CRYPT_AES_MODE_CBC', 2);

define('CRYPT_AES_MODE_CFB', 3);

define('CRYPT_AES_MODE_OFB', 4);




define('CRYPT_AES_MODE_INTERNAL', 1);

define('CRYPT_AES_MODE_MCRYPT', 2);



class Crypt_AES extends Crypt_Rijndael {
    
    var $enmcrypt;

    
    var $demcrypt;

    
    var $ecb;

    
    function Crypt_AES($mode = CRYPT_AES_MODE_CBC)
    {
        if ( !defined('CRYPT_AES_MODE') ) {
            switch (true) {
                case extension_loaded('mcrypt') && in_array('rijndael-128', mcrypt_list_algorithms()):
                    define('CRYPT_AES_MODE', CRYPT_AES_MODE_MCRYPT);
                    break;
                default:
                    define('CRYPT_AES_MODE', CRYPT_AES_MODE_INTERNAL);
            }
        }

        switch ( CRYPT_AES_MODE ) {
            case CRYPT_AES_MODE_MCRYPT:
                switch ($mode) {
                    case CRYPT_AES_MODE_ECB:
                        $this->paddable = true;
                        $this->mode = MCRYPT_MODE_ECB;
                        break;
                    case CRYPT_AES_MODE_CTR:
                                                                                                $this->mode = 'ctr';
                                                break;
                    case CRYPT_AES_MODE_CFB:
                        $this->mode = 'ncfb';
                        break;
                    case CRYPT_AES_MODE_OFB:
                        $this->mode = MCRYPT_MODE_NOFB;
                        break;
                    case CRYPT_AES_MODE_CBC:
                    default:
                        $this->paddable = true;
                        $this->mode = MCRYPT_MODE_CBC;
                }

                break;
            default:
                switch ($mode) {
                    case CRYPT_AES_MODE_ECB:
                        $this->paddable = true;
                        $this->mode = CRYPT_RIJNDAEL_MODE_ECB;
                        break;
                    case CRYPT_AES_MODE_CTR:
                        $this->mode = CRYPT_RIJNDAEL_MODE_CTR;
                        break;
                    case CRYPT_AES_MODE_CFB:
                        $this->mode = CRYPT_RIJNDAEL_MODE_CFB;
                        break;
                    case CRYPT_AES_MODE_OFB:
                        $this->mode = CRYPT_RIJNDAEL_MODE_OFB;
                        break;
                    case CRYPT_AES_MODE_CBC:
                    default:
                        $this->paddable = true;
                        $this->mode = CRYPT_RIJNDAEL_MODE_CBC;
                }
        }

        if (CRYPT_AES_MODE == CRYPT_AES_MODE_INTERNAL) {
            parent::Crypt_Rijndael($this->mode);
        }

    }

    
    function setBlockLength($length)
    {
        return;
    }

    
    function setIV($iv)
    {
        parent::setIV($iv);
        if ( CRYPT_AES_MODE == CRYPT_AES_MODE_MCRYPT ) {
            $this->changed = true;
        }
    }

    
    function encrypt($plaintext)
    {
        if ( CRYPT_AES_MODE == CRYPT_AES_MODE_MCRYPT ) {
            $this->_mcryptSetup();

                                                if ($this->mode == 'ncfb' && $this->continuousBuffer) {
                $iv = &$this->encryptIV;
                $pos = &$this->enbuffer['pos'];
                $len = strlen($plaintext);
                $ciphertext = '';
                $i = 0;
                if ($pos) {
                    $orig_pos = $pos;
                    $max = 16 - $pos;
                    if ($len >= $max) {
                        $i = $max;
                        $len-= $max;
                        $pos = 0;
                    } else {
                        $i = $len;
                        $pos+= $len;
                        $len = 0;
                    }
                    $ciphertext = substr($iv, $orig_pos) ^ $plaintext;
                    $iv = substr_replace($iv, $ciphertext, $orig_pos, $i);
                    $this->enbuffer['enmcrypt_init'] = true;
                }
                if ($len >= 16) {
                    if ($this->enbuffer['enmcrypt_init'] === false || $len > 280) {
                        if ($this->enbuffer['enmcrypt_init'] === true) {
                            mcrypt_generic_init($this->enmcrypt, $this->key, $iv);
                            $this->enbuffer['enmcrypt_init'] = false;
                        }
                        $ciphertext.= mcrypt_generic($this->enmcrypt, substr($plaintext, $i, $len - $len % 16));
                        $iv = substr($ciphertext, -16);
                        $len%= 16;
                    } else {
                        while ($len >= 16) {
                            $iv = mcrypt_generic($this->ecb, $iv) ^ substr($plaintext, $i, 16);
                            $ciphertext.= $iv;
                            $len-= 16;
                            $i+= 16;
                        }
                    }
                }

                if ($len) {
                    $iv = mcrypt_generic($this->ecb, $iv);
                    $block = $iv ^ substr($plaintext, -$len);
                    $iv = substr_replace($iv, $block, 0, $len);
                    $ciphertext.= $block;
                    $pos = $len;
                }

                return $ciphertext;
            }

            if ($this->paddable) {
                $plaintext = $this->_pad($plaintext);
            }

            $ciphertext = mcrypt_generic($this->enmcrypt, $plaintext);

            if (!$this->continuousBuffer) {
                mcrypt_generic_init($this->enmcrypt, $this->key, $this->iv);
            }

            return $ciphertext;
        }

        return parent::encrypt($plaintext);
    }

    
    function decrypt($ciphertext)
    {
        if ( CRYPT_AES_MODE == CRYPT_AES_MODE_MCRYPT ) {
            $this->_mcryptSetup();

            if ($this->mode == 'ncfb' && $this->continuousBuffer) {
                $iv = &$this->decryptIV;
                $pos = &$this->debuffer['pos'];
                $len = strlen($ciphertext);
                $plaintext = '';
                $i = 0;
                if ($pos) {
                    $orig_pos = $pos;
                    $max = 16 - $pos;
                    if ($len >= $max) {
                        $i = $max;
                        $len-= $max;
                        $pos = 0;
                    } else {
                        $i = $len;
                        $pos+= $len;
                        $len = 0;
                    }
                                        $plaintext = substr($iv, $orig_pos) ^ $ciphertext;
                    $iv = substr_replace($iv, substr($ciphertext, 0, $i), $orig_pos, $i);
                }
                if ($len >= 16) {
                    $cb = substr($ciphertext, $i, $len - $len % 16);
                    $plaintext.= mcrypt_generic($this->ecb, $iv . $cb) ^ $cb;
                    $iv = substr($cb, -16);
                    $len%= 16;
                }
                if ($len) {
                    $iv = mcrypt_generic($this->ecb, $iv);
                    $plaintext.= $iv ^ substr($ciphertext, -$len);
                    $iv = substr_replace($iv, substr($ciphertext, -$len), 0, $len);
                    $pos = $len;
                }

                return $plaintext;
            }

            if ($this->paddable) {
                                                $ciphertext = str_pad($ciphertext, (strlen($ciphertext) + 15) & 0xFFFFFFF0, chr(0));
            }

            $plaintext = mdecrypt_generic($this->demcrypt, $ciphertext);

            if (!$this->continuousBuffer) {
                mcrypt_generic_init($this->demcrypt, $this->key, $this->iv);
            }

            return $this->paddable ? $this->_unpad($plaintext) : $plaintext;
        }

        return parent::decrypt($ciphertext);
    }

    
    function _mcryptSetup()
    {
        if (!$this->changed) {
            return;
        }

        if (!$this->explicit_key_length) {
                        $length = strlen($this->key) >> 2;
            if ($length > 8) {
                $length = 8;
            } else if ($length < 4) {
                $length = 4;
            }
            $this->Nk = $length;
            $this->key_size = $length << 2;
        }

        switch ($this->Nk) {
            case 4:                 $this->key_size = 16;
                break;
            case 5:             case 6:                 $this->key_size = 24;
                break;
            case 7:             case 8:                 $this->key_size = 32;
        }

        $this->key = str_pad(substr($this->key, 0, $this->key_size), $this->key_size, chr(0));
        $this->encryptIV = $this->decryptIV = $this->iv = str_pad(substr($this->iv, 0, 16), 16, chr(0));

        if (!isset($this->enmcrypt)) {
            $mode = $this->mode;
            
            $this->demcrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', $mode, '');
            $this->enmcrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', $mode, '');

            if ($mode == 'ncfb') {
                $this->ecb = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
            }

        } 
        mcrypt_generic_init($this->demcrypt, $this->key, $this->iv);
        mcrypt_generic_init($this->enmcrypt, $this->key, $this->iv);

        if ($this->mode == 'ncfb') {
            mcrypt_generic_init($this->ecb, $this->key, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
        }

        $this->changed = false;
    }

    
    function enableContinuousBuffer()
    {
        parent::enableContinuousBuffer();

        if (CRYPT_AES_MODE == CRYPT_AES_MODE_MCRYPT) {
            $this->enbuffer['enmcrypt_init'] = true;
            $this->debuffer['demcrypt_init'] = true;
        }
    }

    
    function disableContinuousBuffer()
    {
        parent::disableContinuousBuffer();

        if (CRYPT_AES_MODE == CRYPT_AES_MODE_MCRYPT) {
            mcrypt_generic_init($this->enmcrypt, $this->key, $this->iv);
            mcrypt_generic_init($this->demcrypt, $this->key, $this->iv);
        }
    }
}

?>