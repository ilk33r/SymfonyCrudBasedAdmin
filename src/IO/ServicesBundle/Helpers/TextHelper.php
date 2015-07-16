<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 11/07/15
 * Time: 00:39
 */

namespace IO\ServicesBundle\Helpers;

class TextHelper
{
	private static $foreignCharacters	= array(
		'/ä|æ|ǽ/'								=> 'ae',
		'/œ/'									=> 'oe',
		'/Ä/'									=> 'Ae',
		'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ/'				=> 'A',
		'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/'				=> 'a',
		'/Ç|Ć|Ĉ|Ċ|Č/'							=> 'C',
		'/ç|ć|ĉ|ċ|č/'							=> 'c',
		'/Ð|Ď|Đ/'								=> 'D',
		'/ð|ď|đ/'								=> 'd',
		'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/'					=> 'E',
		'/è|é|ê|ë|ē|ĕ|ė|ę|ě/'					=> 'e',
		'/Ĝ|Ğ|Ġ|Ģ/'								=> 'G',
		'/ĝ|ğ|ġ|ģ/'								=> 'g',
		'/Ĥ|Ħ/'									=> 'H',
		'/ĥ|ħ/'									=> 'h',
		'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/'					=> 'I',
		'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/'					=> 'i',
		'/Ĵ/'									=> 'J',
		'/ĵ/'									=> 'j',
		'/Ķ/'									=> 'K',
		'/ķ/'									=> 'k',
		'/Ĺ|Ļ|Ľ|Ŀ|Ł/'							=> 'L',
		'/ĺ|ļ|ľ|ŀ|ł/'							=> 'l',
		'/Ñ|Ń|Ņ|Ň/'								=> 'N',
		'/ñ|ń|ņ|ň|ŉ/'							=> 'n',
		'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ö/'				=> 'O',
		'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ö/'			=> 'o',
		'/Ŕ|Ŗ|Ř/'								=> 'R',
		'/ŕ|ŗ|ř/'								=> 'r',
		'/Ś|Ŝ|Ş|Š/'								=> 'S',
		'/ś|ŝ|ş|š|ſ/'							=> 's',
		'/Ţ|Ť|Ŧ/'								=> 'T',
		'/ţ|ť|ŧ/'								=> 't',
		'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ü/'		=> 'U',
		'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|ü/'		=> 'u',
		'/Ý|Ÿ|Ŷ/'								=> 'Y',
		'/ý|ÿ|ŷ/'								=> 'y',
		'/Ŵ/'									=> 'W',
		'/ŵ/'									=> 'w',
		'/Ź|Ż|Ž/'								=> 'Z',
		'/ź|ż|ž/'								=> 'z',
		'/Æ|Ǽ/'									=> 'AE',
		'/ß/'									=> 'ss',
		'/Ĳ/'									=> 'IJ',
		'/ĳ/'									=> 'ij',
		'/Œ/'									=> 'OE',
		'/ƒ/'									=> 'f'
	);

	public static function  removeInvisibleCharacters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);
		return $str;
	}

	public static function wordLimit($text, $wordCount)
	{
		$words				= explode(' ', $text);
		$limitedText		= '';
		$activeWordCount	= 0;
		foreach($words as $word)
		{
			if($activeWordCount < $wordCount)
			{
				$limitedText	.= $word.' ';
				$activeWordCount++;
			}else{
				break;
			}
		}
		return $limitedText;
	}

	public static function characterLimit($text, $characterCount)
	{
		$words					= explode(' ', $text);
		$limitedText			= '';
		$activeCharacterCount	= 0;
		foreach($words as $word)
		{
			if($activeCharacterCount < $characterCount)
			{
				$limitedText		.= $word.' ';
				$activeCharacterCount	+= strlen($word) + 1;
			}else{
				break;
			}
		}
		return $limitedText;
	}

	public static function convertForeignCharacters($text)
	{
		return preg_replace(array_keys(self::$foreignCharacters), array_values(self::$foreignCharacters), $text);
	}

	public static function slugifyText($text)
	{
		$convertedText		= self::convertForeignCharacters($text);
		$permitedCharacters	= 'a-z A-Z0-9\_\+\-';
		$pattern			= '/^['.$permitedCharacters.'\/]+$/';
		$resultSEO			= '';

		for($i = 0; $i < strlen($convertedText); $i++)
		{
			$subStringText	= substr($convertedText, $i, 1);
			$match			= preg_match($pattern, $subStringText, $out);
			if($match)
			{
				$resultSEO	.= $subStringText;
			}
		}

		return urlencode(strtolower(trim(strtr($resultSEO, array(' '=>'-')))));
	}
}