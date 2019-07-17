<?php

use Carbon\Carbon;
use Morilog\Jalali\Facades\jDateTime;

if (!function_exists('toJalali')) {
    function toJalali($time, $format = 'Y/m/d H:i:s')
    {
        return unConvertNumber(jDateTime::strftime($format, strtotime($time)));
    }
}

if (!function_exists('numberConvert')) {
    function numberConvert($number)
    {
        return (int)str_replace(',', '', $number);
    }
}

if (!function_exists('toGregorian')) {
    function toGregorian($time, $format = 'Y/m/d H:i:s')
    {
        return jDatetime::createDatetimeFromFormat($format, convertNumber($time));
    }
}
if (!function_exists('diffDate')) {
    function diffDate($time, $format = "%y year %m  month %d day", $by = 'now')
    {
        $date = Carbon::parse($time);
        if ($by) {
            $date_diff = Carbon::parse($by);
        } else {
            $date_diff = Carbon::now();
        }

        $diff = $date->diff($date_diff)->format($format);

        return $diff;
    }
}

if (!function_exists('persianTime')) {
    function persianTime($time)
    {
        $today = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        $yesterday = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
        $tomorrow = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
        $time_date = date("Y-m-d", strtotime($time));
        if ($today == $time_date) {
            return 'امروز ' . unConvertNumber(date("H:i", strtotime($time)));
        } elseif ($yesterday == $time_date) {
            return 'دیروز ' . unConvertNumber(date("H:i", strtotime($time)));
        } elseif ($tomorrow == $time_date) {
            return 'فردا ' . unConvertNumber(date("H:i", strtotime($time)));
        } else {
            $date = unConvertNumber(jDate::forge($time_date)->format('%y/%m/%d'));
            $date .= ' - ' . unConvertNumber(date("H:i", strtotime($time)));
            return $date;
        }
    }
}

if (!function_exists('persianTimeColor')) {
    function persianTimeColor($time)
    {
        $now = Carbon::now();
        if ($now <= $time) {
            return 'green';
        } else {
            return 'red';
        }
    }
}

if (!function_exists('arabicToPersian')) {
    function arabicToPersian($str)
    {
        $arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', 'ي', 'ك');
        $persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', 'ی', 'ک');
        return str_replace($arabic, $persian, $str);
    }
}

if (!function_exists('convertNumber')) {
    function convertNumber($value)
    {
        $western = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
        $eastern = ['۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰'];
        return str_replace($eastern, $western, $value);
    }
}

if (!function_exists('unConvertNumber')) {
    function unConvertNumber($value)
    {
        $western = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
        $eastern = ['۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰'];
        return str_replace($western, $eastern, $value);
    }
}

if (!function_exists('persianConvert')) {
    function persianConvert($string, $separator = '-')
    {
        $_transliteration = array(
            '/؆|؇|؈|؉|؊|؍|؎|ؐ|ؑ|ؒ|ؓ|ؔ|ؕ|ؖ|ؘ|ؙ|ؚ|؞|ٖ|ٗ|٘|ٙ|ٚ|ٛ|ٜ|ٝ|ٞ|ٟ|٪|٬|٭|ہ|ۂ|ۃ|۔|ۖ|ۗ|ۘ|ۙ|ۚ|ۛ|ۜ|۞|۟|۠|ۡ|ۢ|ۣ|ۤ|ۥ|ۦ|ۧ|ۨ|۩|۪|۫|۬|ۭ|ۯ|ﮧ|﮲|﮳|﮴|﮵|﮶|﮷|﮸|﮹|﮺|﮻|﮼|﮽|﮾|﮿|﯀|﯁|ﱞ|ﱟ|ﱠ|ﱡ|ﱢ|ﱣ|ﹰ|ﹱ|ﹲ|ﹳ|ﹴ|ﹶ|ﹷ|ﹸ|ٌ|ٍ|ﹸ|ﹹ|ْ|ﹺ|ﹻ|ﹼ|ً|ُ|ِ|َ|ّ|\]|\[|\}|\{|\||ٓ|ٰ|‌|ٔ|ء|ﹾ|ﹿ/' => '',
            '/أ|إ|ٱ|ٲ|ٳ|ٵ|ݳ|ݴ|ﭐ|ﭑ|ﺃ|ﺄ|ﺇ|ﺈ|ﺍ|ﺎ|𞺀|ﴼ|ﴽ|𞸀|إ|أ|آ/' => 'ا',
            '/ٮ|ݕ|ݖ|ﭒ|ﭓ|ﭔ|ﭕ|ﺏ|ﺐ|ﺑ|ﺒ|𞸁|𞸜|𞸡|𞹡|𞹼|𞺁|𞺡/' => 'ب',
            '/ڀ|ݐ|ݔ|ﭖ|ﭗ|ﭘ|ﭙ|ﭚ|ﭛ|ﭜ|ﭝ/' => 'پ',
            '/ٹ|ٺ|ٻ|ټ|ݓ|ﭞ|ﭟ|ﭠ|ﭡ|ﭢ|ﭣ|ﭤ|ﭥ|ﭦ|ﭧ|ﭨ|ﭩ|ﺕ|ﺖ|ﺗ|ﺘ|𞸕|𞸵|𞹵|𞺕|𞺵/' => 'ت',
            '/ٽ|ٿ|ݑ|ﺙ|ﺚ|ﺛ|ﺜ|𞸖|𞸶|𞹶|𞺖|𞺶/' => 'ث',
            '/ڃ|ڄ|ﭲ|ﭳ|ﭴ|ﭵ|ﭶ|ﭷ|ﭸ|ﭹ|ﺝ|ﺞ|ﺟ|ﺠ|𞸂|𞸢|𞹂|𞹢|𞺂|𞺢/' => 'ج',
            '/ڇ|ڿ|ݘ|ﭺ|ﭻ|ﭼ|ﭽ|ﭾ|ﭿ|ﮀ|ﮁ|𞸃|𞺃/' => 'چ',
            '/ځ|ݮ|ݯ|ݲ|ݼ|ﺡ|ﺢ|ﺣ|ﺤ|𞸇|𞸧|𞹇|𞹧|𞺇|𞺧/' => 'ح',
            '/ڂ|څ|ݗ|ﺥ|ﺦ|ﺧ|ﺨ|𞸗|𞸷|𞹗|𞹷|𞺗|𞺷/' => 'خ',
            '/ڈ|ډ|ڊ|ڌ|ڍ|ڎ|ڏ|ڐ|ݙ|ݚ|ﺩ|ﺪ|𞺣|ﮂ|ﮃ|ﮈ|ﮉ/' => 'د',
            '/ﱛ|ﱝ|ﺫ|ﺬ|𞸘|𞺘|𞺸|ﮄ|ﮅ|ﮆ|ﮇ|ۮ/' => 'ذ',
            '/٫|ڑ|ڒ|ړ|ڔ|ڕ|ږ|ݛ|ݬ|ﮌ|ﮍ|ﱜ|ﺭ|ﺮ|𞸓|𞺓|𞺳/' => 'ر',
            '/ڗ|ڙ|ݫ|ݱ|ﺯ|ﺰ|𞸆|𞺆|𞺦/' => 'ز',
            '/ﮊ|ﮋ|ژ|ۯ/' => 'ژ',
            '/ښ|ݽ|ݾ|ﺱ|ﺲ|ﺳ|ﺴ|𞸎|𞸮|𞹎|𞹮|𞺎|𞺮/' => 'س',
            '/ڛ|ۺ|ݜ|ݭ|ݰ|ﺵ|ﺶ|ﺷ|ﺸ|𞸔|𞸴|𞹔|𞹴|𞺔|𞺴/' => 'ش',
            '/ڝ|ﺹ|ﺺ|ﺻ|ﺼ|𞸑|𞹑|𞸱|𞹱|𞺑|𞺱/' => 'ص',
            '/ڞ|ۻ|ﺽ|ﺾ|ﺿ|ﻀ|𞸙|𞸹|𞹙|𞹹|𞺙|𞺹/' => 'ض',
            '/ﻁ|ﻂ|ﻃ|ﻄ|𞸈|𞹨|𞺈|𞺨/' => 'ط',
            '/ڟ|ﻅ|ﻆ|ﻇ|ﻈ|𞸚|𞹺|𞺚|𞺺/' => 'ظ',
            '/؏|ڠ|ﻉ|ﻊ|ﻋ|ﻌ|𞸏|𞸯|𞹏|𞹯|𞺏|𞺯/' => 'ع',
            '/ۼ|ݝ|ݞ|ݟ|ﻍ|ﻎ|ﻏ|ﻐ|𞸛|𞸻|𞹛|𞹻|𞺛|𞺻/' => 'غ',
            '/؋|ڡ|ڢ|ڣ|ڤ|ڥ|ڦ|ݠ|ݡ|ﭪ|ﭫ|ﭬ|ﭭ|ﭮ|ﭯ|ﭰ|ﭱ|ﻑ|ﻒ|ﻓ|ﻔ|𞸐|𞸞|𞸰|𞹰|𞹾|𞺐|𞺰/' => 'ف',
            '/ٯ|ڧ|ڨ|ﻕ|ﻖ|ﻗ|ﻘ|𞸒|𞸟|𞸲|𞹒|𞹟|𞹲|𞺒|𞺲|؈/' => 'ق',
            '/ػ|ؼ|ك|ڪ|ګ|ڬ|ڭ|ڮ|ݢ|ݣ|ݤ|ݿ|ﮎ|ﮏ|ﮐ|ﮑ|ﯓ|ﯔ|ﯕ|ﯖ|ﻙ|ﻚ|ﻛ|ﻜ|𞸊|𞸪|𞹪/' => 'ک',
            '/ڰ|ڱ|ڲ|ڳ|ڴ|ﮒ|ﮓ|ﮔ|ﮕ|ﮖ|ﮗ|ﮘ|ﮙ|ﮚ|ﮛ|ﮜ|ﮝ/' => 'گ',
            '/ڵ|ڶ|ڷ|ڸ|ݪ|ﻝ|ﻞ|ﻟ|ﻠ|𞸋|𞸫|𞹋|𞺋|𞺫/' => 'ل',
            '/۾|ݥ|ݦ|ﻡ|ﻢ|ﻣ|ﻤ|𞸌|𞸬|𞹬|𞺌|𞺬/' => 'م',
            '/ڹ|ں|ڻ|ڼ|ڽ|ݧ|ݨ|ݩ|ﮞ|ﮟ|ﮠ|ﮡ|ﻥ|ﻦ|ﻧ|ﻨ|𞸍|𞸝|𞸭|𞹍|𞹝|𞹭|𞺍|𞺭/' => 'ن',
            '/ؤ|ٶ|ٷ|ۄ|ۅ|ۆ|ۇ|ۈ|ۉ|ۊ|ۋ|ۏ|ݸ|ݹ|ﯗ|ﯘ|ﯙ|ﯚ|ﯛ|ﯜ|ﯝ|ﯞ|ﯟ|ﯠ|ﯡ|ﯢ|ﯣ|ﺅ|ﺆ|ﻭ|ﻮ|𞸅|𞺅|𞺥/' => 'و',
            '/ة|ھ|ۀ|ە|ۿ|ﮤ|ﮥ|ﮦ|ﮩ|ﮨ|ﮪ|ﮫ|ﮬ|ﮭ|ﺓ|ﺔ|ﻩ|ﻪ|ﻫ|ﻬ|𞸤|𞹤|𞺄|ة/' => 'ه',
            '/ؠ|ئ|ؽ|ؾ|ؿ|ى|ي|ٸ|ۍ|ێ|ې|ۑ|ے|ۓ|ݵ|ݶ|ݷ|ݺ|ݻ|ﮢ|ﮣ|ﮮ|ﮯ|ﮰ|ﮱ|ﯤ|ﯥ|ﯦ|ﯧ|ﯨ|ﯩ|ﯼ|ﯽ|ﯾ|ﯿ|ﺉ|ﺊ|ﺋ|ﺌ|ﻯ|ﻰ|ﻱ|ﻲ|ﻳ|ﻴ|𞸉|𞸩|𞹉|𞹩|𞺉|𞺩/' => 'ی',
            '/ٴ|۽|ﺀ/' => 'ء',
            '/ﻵ|ﻶ|ﻷ|ﻸ|ﻹ|ﻺ|ﻻ|ﻼ/' => 'لا',
            '/\؟/' => '',
            '/ﷲ/' => 'الله',
            '/﷼/' => 'ریال',
            '/ﷳ/' => 'اکبر',
            '/ﷴ/' => 'محمد',
            '/ﷵ/' => 'صلعم',
            '/ﷶ/' => 'رسول',
            '/ﷷ/' => 'علیه',
            '/ﷸ/' => 'وسلم',
            '/ﷹ/' => 'صلی',
            '/ﷺ/' => 'صلی الله علیه وسلم',
            '/ﷻ/' => 'جل جلاله',
        );

        $quotedReplacement = preg_quote($separator, '/');
        $merge = array(
            '/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
            '/[\s\p{Zs}]+/mu' => $separator,
            sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
        );
        $map = $_transliteration + $merge;
        unset($_transliteration);
        return strtolower(preg_replace(array_keys($map), array_values($map), $string));
    }
}
if (!function_exists('slug_seo')) {
    function slug_seo($string, $separator = '-')
    {
        $_transliteration = array(
            '/ä|æ|ǽ/' => 'ae',
            '/ö|œ/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
            '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
            '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
            '/ç|ć|ĉ|ċ|č/' => 'c',
            '/Ð|Ď|Đ/' => 'D',
            '/ð|ď|đ/' => 'd',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
            '/Ĝ|Ğ|Ġ|Ģ/' => 'G',
            '/ĝ|ğ|ġ|ģ/' => 'g',
            '/Ĥ|Ħ/' => 'H',
            '/ĥ|ħ/' => 'h',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
            '/Ĵ/' => 'J',
            '/ĵ/' => 'j',
            '/Ķ/' => 'K',
            '/ķ/' => 'k',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
            '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
            '/Ñ|Ń|Ņ|Ň/' => 'N',
            '/ñ|ń|ņ|ň|ŉ/' => 'n',
            '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
            '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
            '/Ŕ|Ŗ|Ř/' => 'R',
            '/ŕ|ŗ|ř/' => 'r',
            '/Ś|Ŝ|Ş|Ș|Š/' => 'S',
            '/ś|ŝ|ş|ș|š|ſ/' => 's',
            '/Ţ|Ț|Ť|Ŧ/' => 'T',
            '/ţ|ț|ť|ŧ/' => 't',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
            '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
            '/Ý|Ÿ|Ŷ/' => 'Y',
            '/ý|ÿ|ŷ/' => 'y',
            '/Ŵ/' => 'W',
            '/ŵ/' => 'w',
            '/Ź|Ż|Ž/' => 'Z',
            '/ź|ż|ž/' => 'z',
            '/Æ|Ǽ/' => 'AE',
            '/ß/' => 'ss',
            '/Ĳ/' => 'IJ',
            '/ĳ/' => 'ij',
            '/Œ/' => 'OE',
            '/ƒ/' => 'f',
            '/\_/' => '-',
            '/\?|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)/' => '',
            '/\؟/' => '',
        );

        $quotedReplacement = preg_quote($separator, '/');
        $merge = array(
            '/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
            '/[\s\p{Zs}]+/mu' => $separator,
            sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
        );
        $map = $_transliteration + $merge;
        unset($_transliteration);
        return strtolower(preg_replace(array_keys($map), array_values($map), $string));
    }
}

if (!function_exists('original_url')) {
    function original_url($url)
    {
        return env('APP_URL_ORIGINAL', 'http://dadsun.ir') . '/' . $url;
    }
}
