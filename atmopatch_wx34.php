<?php
/*
 Script to patch Weather34 (wx34) Home Weather Station, Cumulus variant template (https://github.com/ktrue/CU-HWS) to work with ATMOCOM data file.
 1. Create a dedicated directory on your web server for HWS template files.
 2. Place this script in the directory you created in 1). Run by opening the script in a web browser, for example: http://mywebsite.com/wx34/atmopatch_wx34.php
 3. Follow additional instructions provided in the web page output.
*/
$do_patch = false;
$usr_msg = array();
if (array_key_exists('wxldr_url', $_REQUEST)) PatchData::$wxloader_url=$_REQUEST['wxldr_url'];
if (array_key_exists('pws_model', $_REQUEST)) PatchData::$weatherhardware=$_REQUEST['pws_model'];
if (array_key_exists('wu_id', $_REQUEST)) PatchData::$wuid=$_REQUEST['wu_id'];

if (array_key_exists('patch_btn', $_REQUEST)) $do_patch=true;


if($do_patch)
{
    $usr_msg[0]="";
    if(strlen(PatchData::$wxloader_url) == 0) {
        $usr_msg[0]= "Path to script wxloader.php cannot be empty";
        $do_patch = false;
    }
    if(strlen(PatchData::$wuid) == 0) {
        $usr_msg[1]= "Weather Underground ID not set";
        $do_patch = false;
    } else PatchData::$wuid = strtoupper(PatchData::$wuid);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<style>
body{font-family:Arial,sans-serif;font-size:12px}div.tab{overflow:hidden;border:1px solid #ccc;background-color:#f1f1f1}
div.tab button{background-color:inherit;float:left;border:none;outline:0;cursor:pointer;padding:14px 16px;transition:.3s;font-size:11px}
div.tab button:hover{background-color:#ddd}div.tab button.active{background-color:#ccc}
.tabcontent{display:none;padding:6px 12px;border:1px solid #ccc;border-top:none}th{text-align:left}
.switch{position:relative;display:inline-block;width:44px;height:18px}.switch input{opacity:0;width:0;height:0}
.slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#ccc;-webkit-transition:.4s;transition:.4s}
.slider:before{position:absolute;content:"";height:16px;width:16px;left:1px;bottom:1px;background-color:#fff;-webkit-transition:.4s;transition:.4s}
input:checked+.slider{background-color:#2196f3}input:focus+.slider{box-shadow:0 0 1px #2196f3}
input:checked+.slider:before{-webkit-transform:translateX(26px);-ms-transform:translateX(26px);transform:translateX(26px)}.slider.round{border-radius:4px}
.slider.round:before{border-radius:20%}hr{display:block;border-color:#eeeeee;margin-top:.5em;margin-bottom:.5em;margin-left:auto;margin-right:auto;border-style:inset;border-width:1px}
</style>
</head>
<body>
<p><img src="data:image/png;base64,<?=PatchData::$LOGO_BASE64 ?>"></p>
<input type="hidden" name="_post_data" value="0">

<div class="tab">
  <button class="tablinks" style="font-size: 14px; font-weight:bold; color:#707070;" id="defaultOpen"><strong>ATMOCOM -- Weather34 Home Weather Station (Cumulus) template patcher configuration</strong></button>
</div>
<div id="Instr" class="tabcontent" style="display:block;">
<span><strong>Instructions for patching Home Weather Station for Cumulus template (CU-HWS)</strong></span><br /><br />
<span>1. Verify that Atmocom support scripts <a href="https://github.com/atmocom/php-parsers" target="_blank"><strong>atmolog.php</strong></a> and 
<a href="https://github.com/atmocom/php-formatters" target="_blank"><strong>wxloader.php</strong></a> are installed and working correctly.</span><br /><br />
<span>2. Download and unzip latest Cumulus HWS template distribution from <a href="https://github.com/ktrue/CU-HWS" target="_blank"><strong>https://github.com/ktrue/CU-HWS</strong></a> 
(or <a href="https://github.com/ktrue/CU-HWS/archive/master.zip"><strong>Download ZIP</strong></a> directly).<br /><br />
<span>3. Upload CU-HWS files to the same directory on your web server where this script is located.</span><br /><br />
<span>4. Enter your WU station and website specific information in the form below and click Patch button.</span><br /><br />
<span>5. If patch completes with no errors, configure remaining template settings using the the standard CU-HWS configuration method 
(see <a href="https://github.com/ktrue/CU-HWS#home-weather-station-weather-website-template-for-cumuluscumulus-mx" target="_blank">HWS GitHub readme</a> for instructions).</span><br /><br />
<span>6. After patching, in order to secure website you should delete <strong>atmopatch_wx34.php</strong> from the CU-HWS directory.</span><br /><br />
<span><strong>NOTE:</strong> This script is provided as is. Help and support for CU-HWS template can be obtained via its GitHub page or <a href="https://www.wxforum.net/" target="_blank"><strong>wxforum.net</strong></a> weather community forum.</span><br /><br />

</div>
<div id="Device" class="tabcontent" style="display:block;">
<br />
  <form action="" method="post">
  <table style="width:800px">
  <tr>
    <td width="200px">Full URL to wxloader.php script</td>
    <td>
        <input type="text"  style="width:350px;" name="wxldr_url" value="<?=PatchData::$wxloader_url?>">
	</td>
  </tr>
  <tr>
    <td width="200px">Weather Underground station ID</td>
    <td>
        <input type="text" style="width:350px;" name="wu_id" value="<?=PatchData::$wuid?>"> 
	</td>
  </tr>

  <tr>
    <td width="200px">Weather station brand and model</td>
    <td>
        <input type="text" style="width:350px;" name="pws_model" value="<?=PatchData::$weatherhardware?>"> 
	</td>
  </tr>
  </table>
  <br>
  <p><input type="submit" name="patch_btn" value="Patch"></p>
  </form>
<?php

if($do_patch)
{
    if(check_backup_restore()) 
    {
        echo "<hr>";
        patch_livedata();
        patch_settings1();
        patch_index();
        bindump_logo();
        echo "<br />";
        fmtprint_message("Patch script finished", 1);
    }
}
else
{
    if(count($usr_msg) > 0)
    {
        echo "<hr>";
        fmtprint_message("Not patched due to errors:", -2);
        foreach($usr_msg as $str)
        {
            if(strlen($str) > 0) fmtprint_message($str, -1);
        }
    }
}

function check_backup_restore()
{
    //Check that all relevant files exist except image, which we create from embedded binary blob
    if(!file_exists('livedata.php')) {
        fmtprint_message("Unable to patch livedata.php, file not found", -1);
        return false;
    }

    //Append wxloader.php to end of URL
    if(!endsWith(PatchData::$wxloader_url, "wxloader.php"))
    {
        if(!endsWith(PatchData::$wxloader_url, "/")) PatchData::$wxloader_url .= "/";
        PatchData::$wxloader_url .= "wxloader.php";
    }

    $check_handle = @fopen(PatchData::$wxloader_url, 'r');

    // Check if script is where it is supposed to be
    if(!$check_handle){
        fmtprint_message("Required script wxloader.php not found at URL " . PatchData::$wxloader_url .", unable to patch", -1);
        return 0;
    }
    else @fclose(PatchData::$wxloader_url);

    //If backup file exists, restore it and start from beginning
    //Otherwise make a new backup
    if(file_exists('livedata.bup.php')) copy('livedata.bup.php', 'livedata.php');
    else if(file_exists('livedata.php')) copy('livedata.php', 'livedata.bup.php');

    if(file_exists('settings1.bup.php')) copy('settings1.bup.php', 'settings1.php');
    else if(file_exists('settings1.php')) copy('settings1.php', 'settings1.bup.php');

    if(file_exists('initial-settings1.bup.php')) copy('initial-settings1.bup.php', 'initial-settings1.php');
    else if(file_exists('initial-settings1.php')) copy('initial-settings1.php', 'initial-settings1.bup.php');

    if(file_exists('index.bup.php')) copy('index.bup.php', 'index.php');
    else if(file_exists('index.php')) copy('index.php', 'index.bup.php');

    return true;
}

function patch_livedata()
{
    $fcont = file_get_contents("livedata.php");
    
    //Check if already patched
    if(strpos($fcont, 'atmocom') !== FALSE)
    {
        fmtprint_message("Script livedata.php already patched, nothing to do...", 0);
        return;
    } 

    $pos = strpos($fcont, PatchData::$match_str1);
    $pos += strlen(PatchData::$match_str1) + 1;

    $fcont = substr_replace($fcont, PatchData::$ATMOCOM_MAP, $pos, 0);

    file_put_contents("livedata.php", $fcont);
    fmtprint_message("Script livedata.php patched", 0);
}

function patch_settings1()
{
    $tgt = 'settings1.php';
    $tmp = 'settings1.tmp';

    if(!file_exists($tgt)) {
        $tgt = 'initial-' . $tgt;
        $tmp = 'initial-' . $tmp;
    }

    $fin = fopen($tgt, "r");
    $fout = fopen($tmp, "w");

    if ($fin && $fout) {
        while (($line = fgets($fin)) !== false) {
            // process the line read.
            if(strncmp($line, PatchData::$ldata40A, strlen(PatchData::$ldata40A)) === 0)
            {
                $line = PatchData::$ldata40A . PatchData::$ldata40B . PHP_EOL;
            }
            else if(strncmp($line, PatchData::$ldata41A, strlen(PatchData::$ldata41A)) === 0)
            {
                $line = PatchData::$ldata41A . ' = "' . PatchData::$wxloader_url . PatchData::$ldata41B  . PHP_EOL;
            }
            else if(strncmp($line, PatchData::$ldata42, strlen(PatchData::$ldata42)) === 0)
            {
                $line = PatchData::$ldata42 . ' = "' .  PatchData::$weatherhardware .'";  //ATMOCOM patched'  . PHP_EOL;
            }
            else if(strncmp($line, PatchData::$ldata43, strlen(PatchData::$ldata43)) === 0)
            {
                $line = PatchData::$ldata43 . ' = "' .  PatchData::$wuid .'";  //ATMOCOM patched'  . PHP_EOL;
            }
            else if (strlen($line) === 0) $line = PHP_EOL;      
            fwrite($fout, $line);
        }

        fclose($fin);
        fclose($fout);

        unlink($tgt);
        rename($tmp, $tgt);
    } else {
        // error opening the file.
        if(!$fin) fmtprint_message("Could not open $tgt", -1);
        if(!$fout) fmtprint_message("Could not create $tmp", -1);
        return;
    } 
    fmtprint_message("Script $tgt patched", 0);
}

function patch_index()
{
    $fin = fopen("index.php", "r");
    $fout = fopen("index.tmp", "w");

    if ($fin && $fout) {
        $patched=false;
        while (($line = fgets($fin)) !== false) {
            // process the line read.
            if(strpos($line, 'atmocom') != false)
            {
                fmtprint_message("Script index.php already patched, nothing to do...", 0);
                $patched=true;
                fclose($fin);
                fclose($fout);
                return;            
            }
            if(!$patched && strpos($line, PatchData::$match_str2) !== false)
            {
                $pos=strpos($line, 'if');
                $line_x = substr_replace($line, 'else', $pos, 0);
                $line = PatchData::$LOGO_HTML . PHP_EOL . $line_x;
                $patched=true;
            }
            else if (strlen($line) === 0) $line = PHP_EOL;      
            fwrite($fout, $line);
        }

        fclose($fin);
        fclose($fout);

        unlink("index.php");
        rename("index.tmp", "index.php");
    } else {
        // error opening files.
        if(!$fin) fmtprint_message("Could not open index.php", -1);
        if(!$fout) fmtprint_message("Could not create index.tmp", -1);
        return;
    } 
    fmtprint_message("Script index.php patched", 0);
}
function bindump_logo()
{
    $img_dir = 'img';
    $img = $img_dir.'/'.'atmc_g125x25.png';

    if(file_exists($img)) {
        fmtprint_message("Logo image already exists, nothing to do...", 0);
        return;
    }

    if(!file_exists($img_dir)) mkdir($img_dir, 0755, true);
    $fimg = fopen($img, "wb");
    if($fimg) {
        $bindat = base64_decode(PatchData::$LOGO_BASE64);
        $numbytes = fwrite($fimg, $bindat);
        if($numbytes != strlen($bindat))
        {
            fmtprint_message("Unable to write binary image data", -1);
            fclose($fimg);
            return;
        }
    }
    else {
        fmtprint_message("Unable to create image", -1);
        return;
    }

    fmtprint_message("Logo image saved to directory $img_dir/", 0);
    fclose($fimg);
}

function fmtprint_message($msg, $level)
{
    $fontcol = '#009900';
    $xmsg = "";
    if($level == -1) {
        $xmsg = "ERROR: ";
        $fontcol = '#FF0000';
    }
    else if($level == -2) $fontcol = '#FF9900';
    else if($level == 1) $fontcol = '#000000';

    echo "<span style=\"font-size: 14px; font-weight:bold; color:#000000;\"><strong>&#8226;</strong></span>&nbsp;&nbsp;";
    echo "<span style=\"font-size:14px;font-weight:regular;color:$fontcol;\"><strong>$xmsg$msg</strong></span><br />";
}

function startsWith($str, $searchstr)
{
     $length = strlen($searchstr);
     return (substr($str, 0, $length) === $searchstr);
}

function endsWith($str, $searchstr)
{
    $length = strlen($searchstr);
    if ($length == 0) {
        return true;
    }

    return (substr($str, -$length) === $searchstr);
}

class PatchData
{
    public static $wxloader_url = "https://mywebsite.com/wx34";

    //settings1.php: Line 40-41
    public static $ldata40A = '$livedataFormat';
    public static $ldata40B = ' = "atmocom"; //ATMOCOM patched';
    public static $ldata41A = '$livedata';
    public static $ldata41B = '?id=" . strtolower($id) . "&fmt=j"; //ATMOCOM patched';

    //settings1.php: Line 97
    public static $ldata42 = '$weatherhardware';
    public static $weatherhardware = "Brand Model 5000";

    public static $ldata43 = '$id';
    public static $wuid = "";
    //Patch data for livedata.php
    public static $ATMOCOM_MAP = '

//ATMOCOM patched
if ($livedataFormat == "atmocom" && $livedata)
{
    $file_live = file_get_contents($livedata);
    $json = json_decode($file_live, true);
    $weather["date"]               	= $json[0]["DATE"];
    $weather["time"]               	= $json[0]["TIME"];
    $weather["datetime"]           	= $weather["date"] . " " . $weather["time"];
    $weather["barometer"]          	= round($json[0]["BARO"],2);
    $weather["barometer_trend"]    	= $json[1]["BARO_TREND"];
    $weather["barometer_max"]      	= $json[1]["BARO_MAX"];
    $weather["barometer_min"]      	= $json[1]["BARO_MIN"];
    $weather["barometer_units"]    	= $json[0]["PRESS_UNIT"];
    $weather["temp_units"]          = $json[0]["TEMP_UNIT"];
    $weather["temp"] 				= round($json[0]["TEMP"],1);
    $weather["humidity"] 			= $json[0]["RHUM"];
    $weather["heat_index"]	        = heatIndex($json[0]["TEMP"], $json[0]["RHUM"]);
    $weather["windchill"]			= $weather["heat_index"];
    $weather["temp_today_high"]     = round($json[1]["TEMP_MAX"],1);
    $weather["temp_today_low"]     	= round($json[1]["TEMP_MIN"],1);
    $weather["temp_trend"]          = $json[1]["TEMP_TREND"];

    $weather["dewpoint"] 			= round($json[0]["DEWPT"],1);
    $weather["temp_indoor"]  		= round($json[0]["INTEMP"],1);
    $weather["humidity_indoor"]    	= round($json[0]["INRHUM"],1);
    $weather["temp_indoor_feel"]   	= heatIndex($json[0]["INTEMP"], $json[0]["INRHUM"]);
    $weather["uv"]                 	= $json[0]["UVIDX"];
    $weather["uvdatetime"]         	= $weather["datetime"];
    $weather["solar"]               = round($json[0]["SOLAR"],0);
    $weather["swversion"]           = "ATMOCOM firmware " . $json[0]["FIRMWARE_REV"];

    $weather["maxtemptime"]         = $json[1]["TEMP_MAXTIME"];
    $weather["lowtemptime"]         = $json[1]["TEMP_MINTIME"];
    $weather["thb0seapressmaxtime"] = $json[1]["BARO_MAXTIME"];	
    $weather["thb0seapressmintime"] = $json[1]["BARO_MINTIME"];

    $weather["wind_direction"]      = $json[0]["WINDDIR"];
    $weather["wind_direction_avg"]  = round($json[1]["WDIR_AVG"],1);
    $weather["wind_speed"]          = round($json[0]["WINDVEL"],1);
    $weather["wind_gust_speed"]     = round($json[0]["WINDGUSTVEL"],1);
    
    $weather["wind_gust_speed_max"] = round($json[0]["WINDGUSTMAX"],1);
    $weather["maxgusttime"]         = $json[0]["WINDGUSTMAXTIME"];
    
    $weather["rain_rate"]           = round($json[1]["RAIN_RATE"],1);
    $weather["rain_today"]          = round($json[1]["RAIN_TODAY"],1);
    $weather["rainydmax"]           = round($json[1]["RAIN_YESTERDAY"],1);
    $weather["rain_month"]          = round($json[1]["RAIN_MONTH"],1);
    $weather["rain_year"]           = round($json[1]["RAIN_YEAR"],1);;
    $weather["rain_units"]          = $json[0]["RAIN_UNIT"];
    
    $weather["temp_feel"]           = apparent_temperature($weather["temp"],$weather["humidity"],$weather["wind_speed"]);

    //Solar radiation approx. conversion
    $weather["lux"]				    = round($weather["solar"]/0.0079, 0);
};';

    //Logo PNG image
    public static $LOGO_BASE64 = "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";
    
    //index.php: Line 229
    public static $LOGO_HTML = ' if ($livedataFormat == \'atmocom\'){
        echo \'<a href="https://atmocom.com" title="Atmocom" target="_blank">
        <img src="img/atmc_g125x25.png" width="125" height="25" alt="https://atmocom.com" 
        style="margin-left: 20px"></a>\';
    } //ATMOCOM patched';

    public static $match_str1 = 'error_reporting(0);';
    public static $match_str2 = '$livedataFormat';
    public static $match_str3 = '$livedata';
}

?>

</div>
</body>
</html>
