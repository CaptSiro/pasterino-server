<?php
  const BASE64_CHARS = "QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789-_";
  
  /**
   * @throws Exception
   */
  function genBase64($length = 32): string {
    $str = "";
    
    for ($i = 0; $i < $length; $i++) {
      $str .= BASE64_CHARS[random_int(0, 63)];
    }
    
    return $str;
  }