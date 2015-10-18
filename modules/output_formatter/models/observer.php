<?php
class Output_Formatter_Observer
{
    public function formatOutput($observer)
    {
        $headers = headers_list();

        if (isset($headers['Content-Type'])) {
            if (preg_match('/^text\/html/',$headers['Content-Type'])) {
                App::setBuffer($this->formatHtml(App::getBuffer()));
            }
        } else {
            App::setBuffer($this->formatHtml(App::getBuffer()));
        }
    }
    
    public function formatHtml($html)
    {  
        // Trim white space at beginning and end of lines.
        $html= preg_replace('/\s*\n\s*/',"\n",$html);
        // Indent the tags
        $token      = strtok($html, "\n");
        $result     = ''; // holds formatted version as it is built
        $pad        = 0; // initial indent
        $matches    = array(); // returns from preg_matches()

        // scan each line and adjust indent based on opening/closing tags
        while ($token !== false) {
            $indent = 0; 

            // Test for the various tag states.
            if (preg_match('/^<\/\w/', $token, $matches)) {
                // Closing tag - outdent now
                $pad--;
            } elseif (preg_match('/^<\w[^>]*(?<!\/)>/', $token, $matches)) {
                // Opening tag, pad subsequent tags.
                $indent = 1;
                if (preg_match('/<\/\w+>/', $token, $matches)) {
                    // Open and closing tags on same line - no change
                    $indent = 0;
                }
            } elseif (preg_match('/<\/\w+>$/', $token, $matches)) {
                // Closing tag - outdent next line
                $indent = -1;
            }

            // pad the line with the required number of leading spaces
            $line    = str_pad($token, strlen($token)+$pad*2, ' ', STR_PAD_LEFT);
            $result .= $line . "\n"; // add to the cumulative result, with linefeed
            $token   = strtok("\n"); // get the next token
            $pad    += $indent; // update the pad size for subsequent lines    
        }
        return $result;
    }
}
