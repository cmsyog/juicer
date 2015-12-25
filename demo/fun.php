<?php
function mb_ereg_match_all($pattern, $subject, array &$subpatterns)
{
    if (!mb_ereg_search_init($subject, $pattern))
        return false;
    $subpatterns = array();
    while ($r = mb_ereg_search_regs()) {
        $subpatterns[] = $r;
    }
    return true;
}

$pattern = '[^\s　]+';      // スペースまたは全角スペースでないものが続く文字列の意
$subject = 'こんにちは　私は    ナンシー　です。';
$result = array();
mb_ereg_match_all($pattern, $subject, $result);
print_r($result);