<?php
if (!defined('ROOT')) { die(); }

class Template
{
    private
        $dir = './templates/';
        
    public function showTemplate($tpl, $values = array())
    {
        $tpl_file = trim($tpl).'.tpl';
        
        if (file_exists($this->dir.$tpl_file))
        {
            $stream = file_get_contents($this->dir.$tpl_file);
            
            preg_match_all('/{+(.*?)}/', trim($stream), $var, PREG_PATTERN_ORDER);
            
            foreach ($var[1] as $key)
            {
                if (array_key_exists($key, $values)) { $stream = str_replace('{'.$key.'}', $values[$key], $stream); }
            }
            
            echo $stream;
        }
        else
        {
            echo @file_get_contents($this->dir.'template_not_found.tpl');
        }
    }
}

$TEMPLATE = new Template;
?>