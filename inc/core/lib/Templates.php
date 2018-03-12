<?php
/**
* This file is part of Batflat ~ the lightweight, fast and easy CMS
*
* @author       Paweł Klockiewicz <klockiewicz@sruu.pl>
* @author       Wojciech Król <krol@sruu.pl>
* @copyright    2017 Paweł Klockiewicz, Wojciech Król <Sruu.pl>
* @license      https://batflat.org/license
* @link         https://batflat.org
*/

namespace Inc\Core\Lib;

/**
 * Templates class
 */
class Templates
{
    /**
     * Variables for template usage
     *
     * @var array
     */
    private $data = [];

    /**
     * Temporary directory for Templates cache
     *
     * @var string
     */
    private $tmp = 'tmp/';

    /**
     * Template tags list
     *
     * @var array
     */
    private $tags = [
                '{\*(.*?)\*}' => 'self::comment',
                '{noparse}(.*?){\/noparse}' => 'self::noParse',
                '{if: ([^}]*)}' => '<?php if ($1): ?>',
                '{else}' => '<?php else: ?>',
                '{elseif: ([^}]*)}' => '<?php elseif ($1): ?>',
                '{\/if}' => '<?php endif; ?>',
                '{loop: ([^}]*) as ([^}]*)=>([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $2=>$3): ?>',
                '{loop: ([^}]*) as ([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $key => $2): ?>',
                '{loop: ([^}]*)}' => '<?php $counter = 0; foreach (%%$1 as $key => $value): ?>',
                '{\/loop}' => '<?php $counter++; endforeach; ?>',
                '{\?(\=){0,1}([^}]*)\?}' => '<?php if(strlen("$1")) echo $2; else $2; ?>',
                '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)}' => '<?php echo %%$1; ?>',
                '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|e}' => '<?php echo htmlspecialchars(%%$1, ENT_QUOTES | ENT_HTML5, "UTF-8"); ?>',
                '{(\$[a-zA-Z\-\._\[\]\'"0-9]+)\|cut:([0-9]+)}' => '<?php echo str_limit(strip_tags(%%$1), $2); ?>',
                '{widget: ([\.\-a-zA-Z0-9]+)}' => '<?php echo \Inc\Core\Lib\Widget::call(\'$1\'); ?>',
                '{include: (.+?\.[a-z]{2,4})}' => '<?php include_once(str_replace(url()."/", null, "$1")); ?>',
                '{template: (.+?\.[a-z]{2,4})}' => '<?php include_once(str_replace(url()."/", null, $bat["theme"]."/$1")); ?>',
                '{lang: ([a-z]{2}_[a-z]+)}' => '<?php if($bat["lang"] == "$1"): ?>',
                '{/lang}' => '<?php endif; ?>',
            ];

    /**
     * Instance of Batflat core class
     *
     * @var \Inc\Core\Main
     */
    public $core;

    /**
     * Templates constructor
     *
     * @param Inc\Core\Main $object
     */
    public function __construct($object)
    {
        $this->core = $object;
        if (!file_exists($this->tmp)) {
            mkdir($this->tmp);
        }
    }

    /**
    * set variable
    * @param string $name
    * @param mixed $value
    * @return Templates $this
    */
    public function set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * append array variable
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function append($name, $value)
    {
        $this->data[$name][] = $value;
    }

    /**
    * content parsing
    * @param string $content
    * @return string
    */
    private function parse($content)
    {
        // replace tags with PHP
        foreach ($this->tags as $regexp => $replace) {
            if (strpos($replace, 'self') !== false) {
                $content = preg_replace_callback('#'.$regexp.'#s', $replace, $content);
            } else {
                $content = preg_replace('#'.$regexp.'#', $replace, $content);
            }
        }

        // replace variables
        if (preg_match_all('/(\$(?:[a-zA-Z0-9_-]+)(?:\.(?:(?:[a-zA-Z0-9_-][^\s]+)))*)/', $content, $matches)) {
            $matches = $this->organize_array($matches);
            usort($matches, function ($a, $b) {
                return strlen($a[0]) < strlen($b[0]);
            });

            foreach ($matches as $match) {
                // $a.b to $a["b"]
                $rep = $this->replaceVariable($match[1]);
                $content = str_replace($match[0], $rep, $content);
            }
        }

        // remove spaces betweend %% and $
        $content = preg_replace('/\%\%\s+/', '%%', $content);

        // call cv() for signed variables
        if (preg_match_all('/\%\%(.)([a-zA-Z0-9_-]+)/', $content, $matches)) {
            $matches = $this->organize_array($matches);
            usort($matches, function ($a, $b) {
                return strlen($a[2]) < strlen($b[2]);
            });

            foreach ($matches as $match) {
                if ($match[1] == '$') {
                    $content = str_replace($match[0], 'cv($'.$match[2].')', $content);
                } else {
                    $content = str_replace($match[0], $match[1].$match[2], $content);
                }
            }
        }

        return $content;
    }

    /**
     * Organize preg_match_all matches array
     *
     * @param array $input
     * @return array
     */
    protected function organize_array($input) 
    { 
        for ($z = 0; $z < count($input); $z++) {
            for ($x = 0; $x < count($input[$z]); $x++) { 
                $rt[$x][$z] = $input[$z][$x]; 
            } 
        }    
        
        return $rt; 
    } 

    /**
    * execute PHP code
    * @param string $file
    * @return string
    */
    private function execute($file, $counter = 0)
    {
        $pathInfo = pathinfo($file);
        $tmpFile = $this->tmp.$pathInfo['basename'];

        if (!is_file($file)) {
            echo "Template '$file' not found.";
        } else {
            $content = file_get_contents($file);

            if ($this->searchTags($content) && ($counter < 3)) {
                file_put_contents($tmpFile, $content);
                $content = $this->execute($tmpFile, ++$counter);
            }
            file_put_contents($tmpFile, $this->parse($content));

            extract($this->data, EXTR_SKIP);

            ob_start();
            include($tmpFile);
            if (!DEV_MODE) {
                unlink($tmpFile);
            }
            return ob_get_clean();
        }
    }

    /**
    * display compiled code
    * @param string $file
    * @param bool $last
    * @return string
    */
    public function draw($file, $last = false)
    {
        if (preg_match('#inc(\/modules\/[^"]*\/)view\/([^"]*.'.pathinfo($file, PATHINFO_EXTENSION).')#', $file, $m)) {
            $themeFile = THEMES.'/'.$this->core->settings->get('settings.theme').$m[1].$m[2];
            if (is_file($themeFile)) {
                $file = $themeFile;
            }
        }

        $result = $this->execute($file);
        if (!$last) {
            return $result;
        } else {
            $result = str_replace(['*bracket*','*/bracket*'], ['{', '}'], $result);
            $result = str_replace('*dollar*', '$', $result);

            if (HTML_BEAUTY) {
                $tidyHTML = new Indenter;
                return $tidyHTML->indent($result);
            }
            return $result;
        }
    }

    /**
    * replace signs {,},$ in string with *words*
    * @param string $content
    * @return string
    */
    public function noParse($content)
    {
        if (is_array($content)) {
            $content = $content[1];
        }
        $content = str_replace(['{', '}'], ['*bracket*', '*/bracket*'], $content);
        return str_replace('$', '*dollar*', $content);
    }

    /**
    * replace signs {,},$ in array with *words*
    * @param arry $array
    * @return array
    */
    public function noParse_array($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->noParse_array($value);
            } else {
                $array[$key] = $this->noParse($value);
            }
        }
        return $array;
    }

    /**
    * remove selected content from source code
    * @param string $content
    * @return null
    */
    public function comment($content)
    {
        return null;
    }

    /**
    * search tags in content
    * @param string $content
    * @return bool
    */
    private function searchTags($content)
    {
        foreach ($this->tags as $regexp  => $replace) {
            if (preg_match('#'.$regexp.'#sU', $content, $matches)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Replace dot based variable to PHP version
     * $a.b => $a['b']
     *
     * @param string $var
     * @return string
     */
    private function replaceVariable($var)
    {
        if (strpos($var, '.') === false) {
            return $var;
        }

        return preg_replace('/\.([a-zA-Z\-_0-9]*(?![a-zA-Z\-_0-9]*(\'|\")))/', "['$1']", $var);
    }
}
