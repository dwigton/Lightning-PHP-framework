<?php
/*
 * This class contains universal methods and parameters
 * 
 */
class App
{
    private static $_output_buffer;
    
    public static function getBuffer(){
        return self::$_output_buffer;
    }
    
    public static function setBuffer($buffer){
        self::$_output_buffer = $buffer;
    }
    
    public function formatOutput($html){
        
        $dom = explode("\n", self::$_output_buffer);
        
        foreach($dom as $line_num=>$line){
            $line = trim($line);
            $line = preg_replace('/(^<\/[a-zA-Z]+>)[ \t]*(<)/',"$1\n$2", $line);
            while(preg_match('/\n<\/[a-zA-Z]+>[ \t]*</', $line)){         
                $line = preg_replace('/(\n<\/[a-zA-Z]+>)[ \t]*(<)/',"$1\n$2", $line);
            }
            $dom[$line_num] = $line;    
        }
        
        self::$_output_buffer = $this->formatXmlString(implode("\n", $dom));
    }
    
    public function formatXmlString($xml) {  

          // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
          //$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

          // now indent the tags
          $token      = strtok($xml, "\n");
          $result     = ''; // holds formatted version as it is built
          $pad        = 0; // initial indent
          $matches    = array(); // returns from preg_matches()

          // scan each line and adjust indent based on opening/closing tags
          while ($token !== false) : 

            // test for the various tag states

            // 1. open and closing tags on same line - no change
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
              $indent=0;
            // 2. closing tag - outdent now
            elseif (preg_match('/^<\/\w/', $token, $matches)) :
              $pad--;
            // 3. opening tag - don't pad this one, only subsequent tags
            elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
              $indent=1;
            // 4. single character opening tags.
            elseif (preg_match('/^<\w>.*$/', $token, $matches)) :
              $indent = 1;
            // 4. no indentation needed
            else :
              $indent = 0; 
            endif;

            // pad the line with the required number of leading spaces
            $line    = str_pad($token, strlen($token)+$pad*2, ' ', STR_PAD_LEFT);
            $result .= $line . "\n"; // add to the cumulative result, with linefeed
            $token   = strtok("\n"); // get the next token
            $pad    += $indent; // update the pad size for subsequent lines    
          endwhile; 

          return $result;
    }
    
    public static function getModel($file_path)
    {
        require_once $file_path;
        //return new $class_name;
    }
}
