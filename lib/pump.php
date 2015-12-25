<?php
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

class pump
{
    const PUMP_VERSION = '0.1';
    public static $global_tpl_vars = array();
    public static $global_tags = array();
    public static $global_options = array();
    public $global_settings = array();
    private $tpl = '';
    private $data = array();
    private $tpl_path = '';

    public function  __construct($tpl_path = '../tpl')
    {
        $this->tpl_path = $tpl_path;
        self::$global_tags = [
            'operationOpen' => '{@',
            'operationClose' => '}',
            'interpolateOpen' => '\\${',
            'interpolateClose' => '}',
            'noneencodeOpen' => '\\$\\${',
            'noneencodeClose' => '}',
            'commentOpen' => '\\{#',
            'commentClose' => '\\}'
        ];

        self::$global_options = [
            'cache' => true,
            'strip' => true,
            'errorhandling' => true,
            'detection' => true
        ];

        $this->tagInit();

    }

    public function tagInit()
    {
        $forstart = self::$global_tags['operationOpen'] . 'each\\s*([^}]*?)\\s*as\\s*(\\w*?)\\s*(,\\s*\\w*?)?' . self::$global_tags['operationClose'];
        $interpolate = self::$global_tags['interpolateOpen'] . '([\\s\\S]+?)' . self::$global_tags['interpolateClose'];
        $forend = self::$global_tags['operationOpen'] . '\\/each' . self::$global_tags['operationClose'];
        $include = self::$global_tags['operationOpen'] . 'include\\s*([^}]*?)' . self::$global_tags['operationClose'];

        $this->global_settings['forstart'] = $forstart;
        $this->global_settings['forend'] = $forend;
        $this->global_settings['for'] = '(' . $forstart . ')(((?!each).)*)(' . $forend . ')';
        $this->global_settings['interpolate'] = $interpolate;
        $this->global_settings['include'] = $include;

    }

    public function ParseTemplate($tpl = '', $data = array())
    {
        $this->tpl = $tpl;
        $this->data = $data;
        $this->ResolveInclude();
        $this->ResolveFor();
        $this->ResolveVar();
        return $this->tpl;
    }

    private function ResolveInclude()
    {
        $matches = array();
        if (mb_ereg_match_all($this->global_settings['include'], $this->tpl, $matches, 'ipr', 1)) {
            foreach ($matches as $match) {
                $pump = new pump();
                $tpl = file_get_contents( $this->tpl_path . $match[1] . ".pump");
                $fragment = $pump->ParseTemplate($tpl, $this->data);
                $this->tpl = str_replace($match[0], $fragment, $this->tpl);
            }
        }
    }

    private function ResolveFor()
    {
        $pos = mb_strpos($this->tpl, self::$global_tags['operationOpen'] . 'each');
        if ($pos !== false) {
            $matches = array();
            if (mb_ereg_match_all($this->global_settings['for'], $this->tpl, $matches, 'ipr', $pos)) {
                foreach ($matches as $match) {
                    $sub_matches = array();
                    $interpolate = $match[5];
                    $array_key = $match[2];
                    $section = $match[0];
                    $section_for_start = $match[1];
                    $section_for_end = $match[7];
                    $data = $this->data[$array_key];
                    $interpolate_temp = '';
                    if (mb_ereg_match_all($this->global_settings['interpolate'], $interpolate, $sub_matches)) {
                        $p = 1;
                        $interpolate_html = '';
                        foreach ($data as $list) {
                            $interpolate_temp = trim($interpolate);
                            foreach ($sub_matches as $sub_match) {
                                $key = $sub_match[1];
                                $interpolate_field = $sub_match[0];
                                $pos = strrpos($sub_match[1], ".");
                                if ($pos) {
                                    $key = substr($key, $pos + 1);
                                    $interpolate_temp = str_replace($interpolate_field, $list[$key], $interpolate_temp);
                                } else {
                                    $interpolate_temp = str_replace($interpolate_field, $p++, $interpolate_temp);
                                }
                            }
                            $interpolate_html .= $interpolate_temp;
                        }
                        $interpolate_temp = str_replace($interpolate, $interpolate_html, $section);
                        $interpolate_temp = str_replace($section_for_start, '', $interpolate_temp);
                        $interpolate_temp = str_replace($section_for_end, '', $interpolate_temp);
                    }
                    $this->tpl = str_replace($section, $interpolate_temp, $this->tpl);
                }
            }
        }
    }


    private function ResolveVar()
    {
        $matches = array();
        if (mb_ereg_match_all($this->global_settings['interpolate'], $this->tpl, $matches)) {
            foreach ($matches as $match)
                $this->tpl = str_replace($match[0], $this->data[$match[1]], $this->tpl);
        }
    }


    public function setTags($tag, $value)
    {
        self::$global_tags[$tag] = $value;
        $this->tagInit();
    }

}

/**
 * @param $pattern
 * @param $subject
 * @param array $matches
 * @param string $option
 * @param int $offset
 * @return bool
 */
function mb_ereg_match_all($pattern, $subject, array &$matches, $option = 'msr', $offset = 0)
{
    @mb_ereg_search_setpos($offset);
    if (!mb_ereg_search_init($subject, $pattern, $option)) return false;
    $matches = array();
    while ($r = mb_ereg_search_regs()) $matches[] = $r;
    return !empty($matches);
}