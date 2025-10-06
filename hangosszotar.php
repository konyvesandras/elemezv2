<?php
$mute = isset($_GET['mute']) && $_GET['mute'] === '1';
if (isset($_GET['mute'])) echo audioplayer($mute);


function mp3_google_translate_replace($string) {
	global $hangos_mp3_fordito_tomb;
	
	$linksvg='<svg viewBox="0 0 24 24"><path d="M6.9,11.5L0.5,8.5c-0.6-0.2-0.6-0.7,0-0.9l2.2-1.1l4.2,2c0.6,0.2,1.4,0.2,2,0l4.3-2l2.2,1   c0.6,0.2,0.6,0.7,0,0.9L9,11.4C8.4,11.8,7.5,11.8,6.9,11.5L6.9,11.5z"></path></svg>';
	
	$linksvg='
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">
<style type="text/css">
	.st0{opacity:0.42;enable-background:new    ;}
	.st1{opacity:0.72;enable-background:new    ;}
</style>
<g id="XMLID_5_">
	<path id="XMLID_3_" class="st0" d="M6.9,15.2l-6.4-3.1c-0.6-0.2-0.6-0.7,0-0.9l2.2-1.1l4.2,2c0.6,0.2,1.4,0.2,2,0l4.2-2l2.2,1.1   c0.6,0.2,0.6,0.7,0,0.9L9,15.2C8.4,15.4,7.5,15.4,6.9,15.2L6.9,15.2z"/>
	<path id="XMLID_2_" class="st1" d="M6.9,11.5L0.5,8.5c-0.6-0.2-0.6-0.7,0-0.9l2.2-1.1l4.2,2c0.6,0.2,1.4,0.2,2,0l4.3-2l2.2,1   c0.6,0.2,0.6,0.7,0,0.9L9,11.4C8.4,11.8,7.5,11.8,6.9,11.5L6.9,11.5z"/>
	<path id="XMLID_1_" d="M6.9,7.9L0.5,4.8c-0.6-0.2-0.6-0.7,0-0.9l6.4-3.1c0.6-0.2,1.4-0.2,2,0l6.4,3.1c0.6,0.2,0.6,0.7,0,0.9   L8.9,7.9C8.4,8.1,7.5,8.1,6.9,7.9L6.9,7.9z"/>
</g>
</svg>';

	
	
	$query_string=htmlentities(urlencode($string));
	$google_api_url='https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&tl=en&q='.$query_string;
	$google_api_url='http://translate.google.com/translate_tts?ie=UTF-8&total=1&idx=0&textlen=32&client=tw-ob&q='.$query_string.'&tl=hu-HU';
	$get_mp3_from_string='<a target="_blank" href="'.$google_api_url.'">'.$string.'</a>';
	
	$nevezetes=$string;
	
		$hangos_mp3_fordito_tomb["$nevezetes"]='<audio preload id="'.$nevezetes.'"><source src="'.$google_api_url.'" type="audio/mpeg"></audio><a target="_blank" href="'.$google_api_url.'"><img onmouseover="this.src=\'./dropbox/hangosszotar/zold.png\'; document.getElementById(\''.$nevezetes.'\').play()" onmouseout="this.src=\'./dropbox/hangosszotar/piros.png\'" src="oip.jpg" width="9" height="9"/>'.$nevezetes.'</a>
		<div class="svg-container alpha"><a target="_blank" href="https://imgur.com/search/score?q='.$query_string.'">'.$linksvg.'</a></div>
		';	
	
	
if (isset($hangos_mp3_fordito_tomb["$nevezetes"])) return($hangos_mp3_fordito_tomb["$nevezetes"]);
}



function mp3_hangosszotar_replace($string) {
	global $ismert_szavak_lista_tomb,$hangostalalatoklistaja_tomb;

	$lista='';	
	if (isset($ismert_szavak_lista_tomb) and count($ismert_szavak_lista_tomb)>0) 
		foreach($ismert_szavak_lista_tomb as $nevezetes => $filename) {
			if (isset($nevezetes) and strlen($nevezetes)>3)
			$hangos_mp3_fordito_tomb["$nevezetes"]='<audio preload id="'.$nevezetes.'"><source src="./../bbtfilereader/dropbox/hangosszotar/'.$nevezetes.'.mp3" type="audio/mpeg"></audio><a target="_blank" href="./../bbtfilereader/dropbox/hangosszotar/'.$nevezetes.'.mp3"><img onmouseover="this.src=\'piros.png\'; document.getElementById(\''.$nevezetes.'\').play()" onmouseout="this.src=\'piros.png\'" src="piros.png" width="9" height="9"/></a> '.$nevezetes.'';
			}//foreach



if (isset($hangos_mp3_fordito_tomb)) 
	foreach($hangos_mp3_fordito_tomb as $key => $value)  
		if (strpos($string, $key)) 	
			$string = str_replace(' '.$key, ' '.$value, $string);			
			
			
			


	if (isset($string)) return($string);	
}


function mp3_hangosszotar_reader() {
	$local = dirname(__FILE__);
	$dropboxdir='/../bbtfilereader/dropbox/hangosszotar/';
	$localdir=$local.'/../bbtfilereader/dropbox/hangosszotar/';

	if (is_dir($localdir))  {			
	$kvt = opendir ($localdir);
		while (gettype($filename = readdir($kvt)) != "boolean")    	    	
			if (file_exists($localdir."/".$filename) and (substr($filename, -4, 4))=='.mp3') {
				$filename_sort=substr($filename, 0,-4);			
				$filemtime=filemtime(($localdir."/".$filename));			
				if (isset($filemtime))
					 $files_tomb["$filename_sort"]=$filename;
			}
		closedir($kvt);
		} else echo $localdir;




if (isset($files_tomb))	ksort($files_tomb);	

		if (!isset($files_tomb)) {
			$holisvanez_tomb=array(__LINE__,__FILE__,__DIR__,dirname(__FILE__),__FUNCTION__,__TRAIT__,__METHOD__,__NAMESPACE__);

			echo '<pre>';
			print_r ($files_tomb);
			
			echo '</pre>';	
			exit;
			}	



	
if (isset($files_tomb)) return($files_tomb);		

}


function mp3player($mp3url='') {

if (isset($mp3url) and $mp3url=='')	
$mp3url='https://slagerfm.netregator.hu:7813/slagerfm128.mp3?time=1619180852';	
	

$mp3url='audio/nyelvtan.mp3';	
	
$mp3player='
<div class="play-music">
	<div id="music-animation" class="music-animation">

	</div>
	<div class="music-toggle">	
		
	<a name="musicon"></a>
	<a onClick="togglePlay()" id="toggle" data-text-swap="Music off" class="adv-cover-tag neon">Music on</a></div>
</div>
	
<audio id="music" loop="loop" src="'.$mp3url.'"></audio>	

<script>
var music = document.getElementById("music");
var isPlaying = false;
music.volume = 0.2;
function togglePlay() {
	if (isPlaying) {
		music.pause();
	} else {
		music.play();
	}
}
music.onplaying = function () {
	isPlaying = true;
	document.getElementById("music-animation").classList.add("on");
};
music.onpause = function () {
	isPlaying = false;
	document.getElementById("music-animation").classList.remove("on");
};

var button = document.getElementById("toggle");
button.addEventListener(
	"click",
	function () {
		if (button.getAttribute("data-text-swap") == button.innerHTML) {
			button.innerHTML = button.getAttribute("data-text-original");
		} else {
			button.setAttribute("data-text-original", button.innerHTML);
			button.innerHTML = button.getAttribute("data-text-swap");
		}
	},
	false
);
</script>';	

return($mp3player);
}



function audioplayer(bool $mute = false): string {
  $html = '';

  // 🔊 Audio lejátszó
  $html .= '<audio autoplay loop ' . ($mute ? 'muted' : '') . '>
    <source src="https://khpr-ice.streamguys1.com/khpr2.mp3?uuid=4ojxxbe7n" type="audio/mpeg">
    A böngésződ nem támogatja az audio lejátszást.
  </audio>';

  // 🎚️ Felragadó gomb
  $html .= '<a href="hangosszotar.php?mute=' . ($mute ? '0' : '1') . '" class="audio-btn-fixed">'
        . ($mute ? '🔇 Némítva – koppints a bekapcsoláshoz' : '🔊 Hang szól – koppints a némításhoz')
        . '</a>';

  // 🎨 Stílus
  $html .= '<style>
    .audio-btn-fixed {
      position: fixed;
      right: 1rem;
      bottom: calc(60px + 1rem + env(safe-area-inset-bottom));
      z-index: 2000;
      border: none;
      border-radius: 999px;
      padding: 0.6rem 1rem;
      cursor: pointer;
      background: rgba(0,0,0,0.6);
      color: #fff;
      font-weight: bold;
      text-decoration: none;
      backdrop-filter: blur(4px);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .audio-btn-fixed:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.25);
    }

    @media (max-width: 600px) {
      .audio-btn-fixed {
        font-size: 0.9rem;
        padding: 0.5rem 0.8rem;
      }
    }
  </style>';

  return $html;
}



function mediamp3playerold($mp3url='') {
$mediamp3player='	
<div class="media">


  
<!-- Gomb a hang kezeléséhez -->
<button id="audioToggle" aria-pressed="true" class="audio-btn">
  🔇 Némítva – koppints a bekapcsoláshoz
</button>

<!-- Háttér audio: némítva indul, loopol -->
<audio id="bgAudio" preload="metadata" muted autoplay loop>
  <source src="https://khpr-ice.streamguys1.com/khpr2.mp3?uuid=4ojxxbe7n" type="audio/mpeg">
  
  A böngésződ nem támogatja az audio lejátszást.
</audio>
</div>

<style>  
.audio-btn {
  position: fixed;
  right: 1rem;
  bottom: calc(var(--bottom-bar-height) + var(--audio-btn-offset) + env(safe-area-inset-bottom));
  z-index: 2000;
  border: none;
  border-radius: 999px;
  padding: 0.6rem 1rem;
  cursor: pointer;
  background: rgba(0,0,0,0.6);
  color: #fff;
  backdrop-filter: blur(4px);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

:root {
  --bottom-bar-height: 26px;
  --audio-btn-offset: 1rem; /* extra távolság a tálcától */
}


.audio-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.25);
}


@media (max-width: 600px) {
  .brand {
    font-size: 1.1rem; /* kisebb betű */
    white-space: nowrap; /* ne törjön új sorba */
  }
  .menu-toggle {
    font-size: 1.4rem; /* hamburger méret optimalizálás */
  }
}

</style>
';

$mediamp3player .= '<script src="audiotoggle.js"></script>';


return($mediamp3player);
}

?>