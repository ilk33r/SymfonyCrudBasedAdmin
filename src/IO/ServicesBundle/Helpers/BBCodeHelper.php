<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 08/12/15
 * Time: 10:31
 */

namespace IO\ServicesBundle\Helpers;


/*
$bbcodes = array(
	'b' => array('replace'=>'<span style="font-weight: 700;">%s</span>'),
	'i' => array('replace'=>'<span style="font-style: italic;">%s</span>'),
	'u' => array('replace'=>'<span style="text-decoration: underline;">%s</span>'),
	's' => array('replace'=>'<span style="text-decoration: line-through;">%s</span>'),
	'url' => array('replace'=>'<a href="%s" target="_blank">%s</a>'),
	'img' => array('replace'=>'<img src="%s" style="max-width: 45vw;" alt="" />'),
	'video' => array('replace'=>'<iframe src="%s" width="640" height="480" frameborder="0"></iframe>'),
	'list' => array('replace'=>'<ul>%s</ul>', 'keys'=>array('1'=>'<ol>%s</ol>')),
	'\*' => array('replace'=>'<li>%s</li>'),
	'left' => array('replace'=>'<p style="text-align: left;">%s</p>'),
	'center' => array('replace'=>'<p style="text-align: center;">%s</p>'),
	'right' => array('replace'=>'<p style="text-align: right;">%s</p>'),
	'size' => array('replace'=>'<span>%s</span>', 'keys'=>array(
		'50'=>'<span style="font-size: 12px;">%s</span>',
		'85'=>'<span style="font-size: 14px;">%s</span>',
		'150'=>'<span style="font-size: 18px;">%s</span>',
		'200'=>'<span style="font-size: 22px;">%s</span>'
	)),
	'quote' => array('replace'=>'<div class="quote" style="width: 45vw;background-color: #DEDEDE;padding: 4px;">%s</div>',
		'replacealt'=>'<div class="quote" style="width: 45vw;background-color: #DEDEDE;padding: 4px;"><cite style="display: inline-block;width: 100%%;">%s:</cite>%s</div>')
);
*/

class BBCodeHelper {

	private $bbcodeList;
	private $htmlString;

	public function __construct($bbcodeList) {
		$this->bbcodeList = $bbcodeList;
	}

	public function convertBBcodesToHtml($string) {

		$bbcodeFound = false;
		$transformedContent = str_replace("\n", '<br />', $string);;

		do
		{
			foreach($this->bbcodeList as $bbcodeTypeKey => $bbcodeTypeValue)  {

				$pattern1 = '/\[' . $bbcodeTypeKey . '\](.*?)\[\/' . $bbcodeTypeKey . '\]/s';
				$pattern2 = '/\[' . $bbcodeTypeKey . '\=(.*?)\](.*?)\[\/' . $bbcodeTypeKey . '\]/s';

				if(preg_match($pattern1, $transformedContent, $matches)) {

					$bbcodeFound = true;

					if($this->getBBcodeContent($transformedContent,
						$bbcodeTypeValue, $matches, false)) {

						break;
					}
				}elseif(preg_match($pattern2, $transformedContent, $matches)) {

					$bbcodeFound = true;

					if($this->getBBcodeContent($transformedContent,
						$bbcodeTypeValue, $matches, true)) {

						break;
					}
				}else{
					$bbcodeFound = false;
				}
			}

		}while($bbcodeFound);

		$this->htmlString = $transformedContent;
	}

	private function getBBcodeContent(&$content, $replaced, $matches, $keyExists) {

		$matchesArrayCopy = array();
		for($i = 1, $matchesCount = sizeof($matches); $i < $matchesCount; $i++) {

			$matchesArrayCopy[] = $matches[$i];
		}

		if(!$keyExists) {
			$replacedString = $replaced['replace'];
		}else{
			if(isset($replaced['replacealt'])) {

				$replacedString = $replaced['replacealt'];
			}elseif(isset($replaced['keys'])) {

				$replacedString = '';

				foreach($replaced['keys'] as $keysKey => $keysValue) {

					if($matchesArrayCopy[0] == $keysKey) {

						$replacedString = $keysValue;
						unset($matchesArrayCopy[0]);
						break;
					}
				}

			}else{
				$replacedString = $replaced['replace'];
			}
		}

		$matchesCopyCount = sizeof($matchesArrayCopy);
		preg_match_all('/\%s/is', $replacedString, $replaceStrCount, PREG_SET_ORDER);

		if($matchesCopyCount == sizeof($replaceStrCount)) {

			$convertedString = vsprintf($replacedString, $matchesArrayCopy);
			$content = str_replace($matches[0], $convertedString, $content);
			return true;
		}else{
			return false;
		}

	}

	public function getHtml() {
		return $this->htmlString;
	}
}