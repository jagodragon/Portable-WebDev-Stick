<?php
/** Adminer Editor - Compact database editor
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2009 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.1.2
*/namespace
Adminer;const
VERSION="6.1.2";error_reporting(24575);set_error_handler(function($Bc,$Dc){return!!preg_match('~^Undefined (array key|offset|index)~',$Dc);},E_WARNING|E_NOTICE);$Zc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Zc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$rj=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($rj)$$W=$rj;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Gb=adminer()->credentials();$F=Driver::connect($Gb[0],$Gb[1],$Gb[2]);return(is_object($F)?$F:null);}function
idf_unescape($r){if(!preg_match('~^[`\'"[]~',$r))return$r;$Qe=substr($r,-1);return
str_replace($Qe.$Qe,$Qe,substr($r,1,-1));}function
q($P){return
connection()->quote($P);}function
idx($va,$t,$i=null){return($va&&array_key_exists($t,$va)?$va[$t]:$i);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_user_type($T){return
in_array($T,idx(driver()->structuredTypes(),lang(0),array()));}function
full_type_sql(array$k){$T=$k["type"];return(is_user_type($T)?idf_escape($T).substr($k["full_type"],strlen($T)):$k["full_type"]);}function
is_searchable(array$k,array$W){if(!isset($k["privileges"]["where"]))return
false;if(preg_match('~NULL$~',$W["op"]))return
true;$T=$k["type"];$Dh=$W["val"];$La='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$La~",$T))return
false;if(preg_match(number_type(),$T)){$z='-?\d+(\.\d+)?';$Df='';if(preg_match('~IN$~',$W["op"])){$Dh=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$Dh);$Df="( *, *$z)*";}elseif($W["op"]=="BETWEEN")$Df="\\s+AND\\s+$z";return(bool)preg_match('~^'.$z.$Df.'$~i',$Dh);}if(preg_match('~^(small)?date|^timestamp~',$T))return(bool)preg_match('~^\d+-\d+-\d+~',$Dh);if(preg_match('~^time~',$T))return(bool)preg_match('~^\d+:\d+~',$Dh);if(preg_match('~^bool~',$T)||(JUSH=="mssql"&&$T=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Dh);return
true;}function
remove_slashes(array$Y,$Zc=false){$F=array();foreach($Y
as$t=>$W)$F[stripslashes($t)]=(is_array($W)?remove_slashes($W,$Zc):($Zc?$W:stripslashes($W)));return$F;}function
bracket_escape($r,$Ea=false){static$Wi=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($r,($Ea?array_flip($Wi):$Wi));}function
url_escape($P){static$Wi=array();if(!$Wi){$Wi=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$Ua)$Wi[$Ua]=sprintf('%%%02X',ord($Ua));for($p=0;$p<256;$p++){if($p<32||$p>126)$Wi[chr($p)]=sprintf('%%%02X',$p);}}return
strtr((string)$P,$Wi);}function
min_version($Kj,$hf="",$g=null){$g=connection($g);$Zh=$g->server_info;if($hf&&preg_match('~([\d.]+)-MariaDB~',$Zh,$x)){$Zh=$x[1];$Kj=$hf;}return$Kj&&version_compare($Zh,$Kj)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_set($cg,$X){return(function_exists('ini_set')?\ini_set($cg,$X):false);}function
ini_bool($ne){$W=ini_get($ne);return(preg_match('~^(on|true|yes)$~i',$W)||(int)$W);}function
ini_bytes($ne){$W=ini_get($ne);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
max_input_vars($G,$ng){$lf=(int)ini_get("max_input_vars");return($lf?(int)floor(($lf-$ng)/$G):0);}function
max_input_vars_error(){$ne="max_input_vars";return
lang(1,"<b>$ne = ".ini_get($ne)."</b>");}function
sid(){static$F;if($F===null)$F=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$F;}function
set_password($Jj,$L,$U,$B){$_SESSION["pwds"][$Jj][$L][$U]=($_COOKIE["adminer_key"]&&is_string($B)?array(encrypt_string($B,$_COOKIE["adminer_key"])):$B);}function
get_password(){$F=get_session("pwds");if(is_array($F))$F=($_COOKIE["adminer_key"]?decrypt_string($F[0],$_COOKIE["adminer_key"]):false);return$F;}function
get_val($D,$k=0,$qb=null){$qb=connection($qb);$E=$qb->query($D);if(!is_object($E))return
false;$G=$E->fetch_row();return($G?$G[$k]:false);}function
get_vals($D,$d=0){$F=array();$E=connection()->query($D);if(is_object($E)){while($G=$E->fetch_row())$F[]=$G[$d];}return$F;}function
get_key_vals($D,$g=null,$ci=true){$g=connection($g);$F=array();$E=$g->query($D);if(is_object($E)){while($G=$E->fetch_row()){if($ci)$F[$G[0]]=$G[1];else$F[]=$G[0];}}return$F;}function
get_rows($D,$g=null,$j="<p class='error'>"){$qb=connection($g);$F=array();$E=$qb->query($D);if(is_object($E)){while($G=$E->fetch_assoc())$F[]=$G;}elseif(!$E&&!$g&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$F;}function
unique_array($G,array$ie){foreach($ie
as$s){if(preg_match("~^(PRIMARY|UNIQUE)$~",$s["type"])&&!$s["partial"]){$F=array();foreach($s["columns"]as$t){if(!isset($G[$t]))continue
2;$F[$t]=$G[$t];}return$F;}}}function
where_function($yd,$d,array$k){if($yd=="md5")return
driver()->md5($d,$k)?:$d;return(in_array($yd,driver()->functions)||in_array($yd,driver()->grouping)?apply_sql_function($yd,$d):$d);}function
where(array$Z,array$l=array()){$F=array();foreach((array)$Z["where"]as$t=>$W){$t=bracket_escape($t,true);$d=idf_escape($t);$k=idx($l,$t,array());$Uc=$k["type"];$ze=$k&&(is_blob($k)||preg_match('~binary~',$Uc));$F[]=$d.($ze&&!is_utf8($W)?" = ".driver()->quoteBinary($W):(JUSH=="sql"&&$Uc=="json"?" = CAST(".q($W)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($W)."::jsonb":(JUSH=="sql"&&is_numeric($W)&&preg_match('~\.~',$W)?" LIKE ".q($W):(JUSH=="mssql"&&strpos($Uc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$W)):" = ".unconvert_field($k,q($W)))))));if(JUSH=="sql"&&preg_match('~char|text~',$Uc)&&preg_match("~[^ -@]~",$W))$F[]="$d = ".q($W)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$t)$F[]=idf_escape($t)." IS NULL";foreach((array)$Z["col"]as$p=>$fb){$W=idx($Z["val"],$p);$F[]=where_function(idx($Z["fun"],$p),idf_escape($fb),idx($l,$fb,array())).($W!==null?" = ".q($W):" IS NULL");}return
implode(" AND ",$F);}function
where_columns(array$l){$F=array();foreach((array)$_GET["null"]as$t)$F[$t]=true;foreach(array_keys((array)$_GET["where"])as$t)$F[bracket_escape($t,true)]=true;foreach((array)$_GET["col"]as$fb)$F[$fb]=true;return
array_intersect_key($F,$l);}function
where_check($W,array$l=array()){parse_str($W,$Wa);remove_slashes(array(&$Wa));return
where($Wa,$l);}function
where_link($p,$d,$X,$bg="="){$ag=($X!==null?$bg:"IS NULL");return"&where[$p][col]=".url_escape($d).($ag!=first(adminer()->operators())?"&where[$p][op]=".url_escape($ag):"")."&where[$p][val]=".url_escape($X);}function
convert_fields(array$e,array$l,array$J=array()){$F="";foreach($e
as$t=>$W){if($J&&!in_array(idf_escape($t),$J))continue;$wa=convert_field($l[$t]);if($wa)$F
.=", $wa AS ".idf_escape($t);}return$F;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($y,$X,$We=2592000){header("Set-Cookie: $y=".rawurlencode($X).($We?"; expires=".gmdate("D, d M Y H:i:s",time()+$We)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($y=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($yj,$yb){$http_response_header=null;$Cc=array();set_error_handler(function($Bc,$j)use(&$Cc){$Cc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$j);return
true;});$F=file_get_contents($yj,false,$yb);restore_error_handler();$Pd=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($F,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($Pd,0,''),$x)?$x[1]:''),(array)$Pd,($F===false?implode("\n",$Cc):''),);}function
json_decode_exact($De){$De=preg_replace('~"(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$De);return
json_decode(preg_replace('~"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$De));}function
json_scalar($W){return(is_string($W)&&substr($W,0,1)=="\1"?substr($W,1):$W);}function
json_encode_exact($W,$fd=0){return
preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','$1$2$3',json_encode($W,$fd));}function
get_settings($Bb){parse_str($_COOKIE[$Bb],$di);return$di;}function
get_setting($t,$Bb="adminer_settings",$i=null){return
idx(get_settings($Bb),$t,$i);}function
save_settings(array$di,$Bb="adminer_settings"){$X=http_build_query($di+get_settings($Bb));cookie($Bb,$X);$_COOKIE[$Bb]=$X;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($id=false){$_j=ini_bool("session.use_cookies");if(!$_j||$id){session_write_close();if($_j&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($t){return$_SESSION[$t][DRIVER][SERVER][$_GET["username"]];}function
set_session($t,$W){$_SESSION[$t][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($Jj,$L,$U,$h=null){$xj=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($Jj=='mssql'||$Jj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$xj,$x);return"$x[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Jj!="server"||$L!=""?url_escape($Jj)."=".url_escape($L)."&":"")."username=".url_escape($U).($h!=""?"&db=".url_escape($h):"").($x[2]?"&$x[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($df,$wf=null){if($wf!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($df!==null?$df:$_SERVER["REQUEST_URI"]))][]=$wf;}if($df!==null){if($df=="")$df=".";header("Location: $df");exit;}}function
query_redirect($D,$df,$wf,$kh=true,$Kc=true,$Qc=false,$Mi=""){if($Kc){$ri=microtime(true);$Qc=!connection()->query($D);$Mi=format_time($ri);}$oi=($D?adminer()->messageQuery($D,$Mi,$Qc):"");if($Qc){adminer()->error
.=adminer()->error().$oi.script("messagesPrint();")."<br>";return
false;}if($kh)redirect($df,$wf.$oi);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($D){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$D:(preg_match('~;$~',$D)?"DELIMITER ;;\n$D;\nDELIMITER ":$D).";");}function
queries($D){remember_query($D);return
connection()->query($D);}function
apply_queries($D,array$S,$Ec='Adminer\table'){foreach($S
as$Q){if(!queries("$D ".$Ec($Q)))return
false;}return
true;}function
queries_redirect($df,$wf,$kh){$eh=implode("\n",Queries::$queries);$Mi=format_time(Queries::$start);return
query_redirect($eh,$df,$wf,$kh,false,!$kh,$Mi);}function
format_time($ri){return
lang(2,max(0,microtime(true)-$ri));}function
relative_uri($xj=''){return
preg_replace_callback('~^[^?]*~',function($x){return
str_replace(":","%3A",$x[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($xj?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($tg=""){return
substr(preg_replace("~(?<=[?&])($tg".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($y,$Qb=false){$Vc=$_FILES[$y];if(!$Vc)return
null;foreach($Vc
as$t=>$W)$Vc[$t]=(array)$W;$F=array();foreach($Vc["error"]as$t=>$j){if($j)return$j;$m=$Vc["name"][$t];$Ti=$Vc["tmp_name"][$t];$xb=file_get_contents($Qb&&preg_match('~\.gz$~',$m)?"compress.zlib://$Ti":$Ti);if($Qb){$ri=substr($xb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$ri))$xb=iconv("utf-16","utf-8",$xb);elseif($ri=="\xEF\xBB\xBF")$xb=substr($xb,3);}$F[]=array($m,$xb);}return$F;}function
get_file($t,$Qb=false,$Ub=""){$Yc=get_files($t,$Qb);if(!is_array($Yc))return$Yc;$F='';foreach($Yc
as$Vc){$xb=$Vc[1];$F
.=$xb;if($Ub)$F
.=(preg_match("($Ub\\s*\$)",$xb)?"":$Ub)."\n\n";}return$F;}function
upload_error($j){$qf=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(3).($qf?" ".lang(4,$qf):""):lang(5));}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
utf8_length($W){return
strlen(preg_replace('~[\x80-\xBF]~','',$W));}function
format_number($W){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$x);$fi=strlen($x[3]);$F=number_format($W,0,".","");$F=preg_replace('~\B(?=(\d{'.(strlen($x[2])?:$fi).'})*\d{'.$fi.'}$)~',$x[1],$F);return
strtr($F,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$R,$t){$W=idx($R,$t,'?');if(!is_numeric($W))return
h($W);if($W<0)return'?';$ra=($t=="Rows"&&(JUSH=="sqlite"||$R["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));$W=in_array($t,array("Data_length","Index_length","Data_free"))?adminer()->formatSizeValue($W):format_number($W);return($ra?"~ ":"").$W;}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($Q,$Rc=false){$F=table_status($Q,$Rc);return($F?reset($F):array("Name"=>$Q));}function
column_foreign_keys($Q){$F=array();foreach(adminer()->foreignKeys($Q)as$md){foreach($md["source"]as$W)$F[$W][]=$md;}return$F;}function
fields_from_edit(){$F=array();foreach((array)$_POST["field_keys"]as$t=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$t];$_POST["fields"][$W]=$_POST["field_vals"][$t];}}foreach((array)$_POST["fields"]as$t=>$W){$y=bracket_escape($t,true);$F[$y]=array("field"=>$y,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($y==driver()->primary),);}return$F;}function
dump_headers($ae,$Hf=false){$F=adminer()->dumpHeaders($ae,$Hf);$og=$_POST["output"];if($og!="text"||$F=="tar"){$nb=($og!="text"&&$og!="file"&&preg_match('~^[0-9a-z]+$~',$og)?".$og":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($ae).".$F$nb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$F;}function
dump_csv(array$G){$hj=$_POST["format"]=="tsv";foreach($G
as$t=>$W){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($hj?'\t':'[,;]|^$').'~',$W))$G[$t]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($hj?"\t":";")),$G)."\r\n";}function
parse_csv($Jb,$K){$F=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Jb,$jf);foreach($jf[0]as$G){preg_match_all("~((?>\"[^\"]*\")+|[^$K]*)$K~",$G.$K,$kf);$F[]=$kf[1];}return$F;}function
csv_value($W){return(preg_match('~^".*"$~s',$W)?str_replace('""','"',substr($W,1,-1)):$W);}function
apply_sql_function($n,$d){return($n?($n=="unixepoch"?"DATETIME($d, '$n')":($n=="count distinct"?"COUNT(DISTINCT ":strtoupper("$n("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$rd=@fopen($m,"c+");if(!$rd)return;@chmod($m,0660);if(!flock($rd,LOCK_EX)){fclose($rd);return;}return$rd;}function
file_write_unlock($rd,$Mb){rewind($rd);fwrite($rd,$Mb);ftruncate($rd,strlen($Mb));file_unlock($rd);}function
file_unlock($rd){flock($rd,LOCK_UN);fclose($rd);}function
first(array$va){return
reset($va);}function
password_file($Eb){$m=get_temp_dir()."/adminer.key";if(!$Eb&&!file_exists($m))return'';$rd=file_open_lock($m);if(!$rd)return'';$F=stream_get_contents($rd);if(!$F){$F=rand_string();file_write_unlock($rd,$F);}else
file_unlock($rd);return$F;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($W,$w,array$k,$Ki,array$Fg=array()){if(is_array($W)){$F="";if(array_filter($W,'is_array')==array_values($W)){$He=array();foreach($W
as$V)$He+=array_fill_keys(array_keys($V),null);foreach(array_keys($He)as$Fe)$F
.="<th>".h($Fe);foreach($W
as$V){$F
.="<tr>";foreach(array_merge($He,$V)as$Fj)$F
.="<td>".select_value($Fj,$w,$k,$Ki,$Fg);}}else{foreach($W
as$Fe=>$V)$F
.="<tr>".($W!=array_values($W)?"<th>".h($Fe):"")."<td>".select_value($V,$w,$k,$Ki,$Fg);}return"<table>$F</table>";}if(!$w)$w=adminer()->selectLink($W,$k);if($w===null){if(is_mail($W))$w="mailto:$W";if(is_url($W))$w=$W;}$W=driver()->value($W,$k);$F=adminer()->editVal($W,$k);if($F!==null){if(!is_utf8($F))$F="\0";elseif($Ki!=""&&is_shortable($k))$F=shorten_utf8($F,max(0,+$Ki),"",$Fg);else$F=highlight_matches($F,$Fg);}return
adminer()->selectVal($F,$w,$k,$W);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(0),array()));}function
is_identity_always(array$k){return$k["auto_increment"]&&(JUSH=="mssql"||$k["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($uc){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$ic='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Eg="$xa+(\\.$xa+)*@($ic?\\.)+$ic";return
is_string($uc)&&preg_match("(^$Eg(,\\s*$Eg)*\$)i",$uc);}function
is_url($P){$ic='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($ic?\\.)+$ic(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$P);}function
is_ipv6($ja){$o='[\da-f]{1,4}';$ye='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($o:){7}$o|($o:){6}$ye|(($o:)*$o)?::(($o:)*($o|$ye))?)$~iD",$ja);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($Wd){return(strpos($Wd,":")!==false?"[$Wd]":$Wd);}function
server_parts(array$_g){return
array("scheme"=>(string)$_g["scheme"],"host"=>(string)$_g["host"],"port"=>(string)$_g["port"],"socket"=>(string)$_g["socket"],"path"=>(string)$_g["path"],);}function
parse_server($L){if($L=="")return
server_parts(array());if($L[0]==":"&&!is_ipv6($L)){$th=substr($L,1);if(preg_match('~^\d+$~D',$th))return
server_parts(array("port"=>$th));return(preg_match('~^/[-\w.:/]*$~D',$th)?server_parts(array("socket"=>$th)):null);}$Ch="";if(preg_match('~^([-+.\w]+)://~',$L,$x)){$Ch=strtolower($x[1]);$L=substr($L,strlen($x[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$L,$x))return(is_ipv6($x[1])?server_parts(array("scheme"=>$Ch,"host"=>$x[1],"port"=>$x[3],"path"=>$x[4])):null);if(is_ipv6($L))return
server_parts(array("scheme"=>$Ch,"host"=>$L));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$L,$x))return
server_parts(array("scheme"=>$Ch,"host"=>$x[1],"port"=>$x[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$L,$x)?server_parts(array("scheme"=>$Ch,"host"=>$x[1],"port"=>$x[3],"path"=>$x[4])):null);}function
count_rows($Q,array$Z,$_e,array$o){$D=" FROM ".table($Q).($Z?" WHERE ".implode(" AND ",$Z):"");return($_e&&(JUSH=="sql"||count($o)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$o).")$D":"SELECT COUNT(*)".($_e?" FROM (SELECT 1$D GROUP BY ".implode(", ",$o).") x":$D));}function
slow_query($D){$h=adminer()->database();$Ni=adminer()->queryTimeout();$hi=driver()->slowQuery($D,$Ni);$g=null;if(!$hi&&support("kill")){$g=connect();if($g&&($h==""||$g->select_db($h))){$Ie=number(get_val(connection_id(),0,$g));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$Ie&token=".get_token()."'); }, 1000 * $Ni);");}}ob_flush();flush();$F=@get_key_vals(($hi?:$D),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$F;}function
get_token(){$hh=rand(1,1e6);return($hh^$_SESSION["token"]).":$hh";}function
verify_token(){list($Ui,$hh)=explode(":",$_POST["token"]);return($hh^$_SESSION["token"])==$Ui&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($P,$Yb=""){$pa=array_flip(str_split(compress_alphabet()));$u=strlen($P);$Ij=($u?13*($u-1)/2-$pa[$P[0]]:0);$La="";$th=0;$uh=0;for($p=1;$p<$u;$p+=2){$th=($th<<13)+$pa[$P[$p]]*93+$pa[$P[$p+1]];$uh+=13;while($uh>=8&&$Ij>=8){$uh-=8;$Ij-=8;$La
.=chr($th>>$uh);$th&=(1<<$uh)-1;}}if($La=="")return"";if($Yb!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$Yb)),$La,ZLIB_FINISH);return($Yb==""&&function_exists('gzinflate')?gzinflate($La):inflate($La,$Yb));}function
inflate($La,$Yb=""){$Te=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$Ue=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$bc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$dc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$F=$Yb;$C=0;do{$ad=inflate_bits($La,$C,1);$T=inflate_bits($La,$C,2);if(!$T){$C=($C+7)&~7;$u=inflate_bits($La,$C,16);$C+=16;$F
.=substr($La,$C>>3,$u);$C+=$u<<3;}else{if($T==1){$bf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$ec=array_fill(0,30,5);}else{$af=inflate_bits($La,$C,5)+257;$cc=inflate_bits($La,$C,5)+1;$eg=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$zf=array_fill(0,19,0);$yf=inflate_bits($La,$C,4)+4;for($p=0;$p<$yf;$p++)$zf[$eg[$p]]=inflate_bits($La,$C,3);$_f=inflate_table($zf);$Ve=array();while(count($Ve)<$af+$cc){$yi=inflate_symbol($La,$C,$_f);if($yi==16)$Ve=array_merge($Ve,array_fill(0,inflate_bits($La,$C,2)+3,end($Ve)));elseif($yi==17)$Ve=array_merge($Ve,array_fill(0,inflate_bits($La,$C,3)+3,0));elseif($yi==18)$Ve=array_merge($Ve,array_fill(0,inflate_bits($La,$C,7)+11,0));else$Ve[]=$yi;}$bf=array_slice($Ve,0,$af);$ec=array_slice($Ve,$af);}$cf=inflate_table($bf);$gc=inflate_table($ec);while(($yi=inflate_symbol($La,$C,$cf))!=256){if($yi<256)$F
.=chr($yi);else{$u=$Te[$yi-257]+inflate_bits($La,$C,$Ue[$yi-257]);$fc=inflate_symbol($La,$C,$gc);$Vf=strlen($F)-$bc[$fc]-inflate_bits($La,$C,$dc[$fc]);for($p=0;$p<$u;$p++)$F
.=$F[$Vf+$p];}}}}while(!$ad);return($Yb==""?$F:substr($F,strlen($Yb)));}function
inflate_bits($La,&$C,$Db){$F=0;for($p=0;$p<$Db;$p++){$F+=((ord($La[$C>>3])>>($C&7))&1)<<$p;$C++;}return$F;}function
inflate_table(array$Ve){$Q=array();$eb=0;for($Ma=1;$Ma<=max($Ve);$Ma++){foreach($Ve
as$yi=>$u){if($u==$Ma){$Q[$Ma][$eb]=$yi;$eb++;}}$eb<<=1;}return$Q;}function
inflate_symbol($La,&$C,array$Q){$eb=0;$Ma=0;do{$eb=($eb<<1)+inflate_bits($La,$C,1);$Ma++;}while(!isset($Q[$Ma][$eb]));return$Q[$Ma][$eb];}function
script($mi,$Vi="\n"){return"<script".nonce().">$mi</script>$Vi";}function
script_src($yj,$Rb=false){return"<script src='".h($yj)."'".nonce().($Rb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($Fc,$Gd,$sa=null){$ua=array();foreach(array_slice(func_get_args(),2)as$W)$ua[]=json_encode($W,256);return" data-on$Fc='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$Gd(".implode(", ",$ua).")")."'";}function
input_hidden($y,$X=""){return"<input type='hidden' name='".h($y)."' value='".h($X)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($P){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$P);}function
nl_br($P){return
str_replace("\n","<br>",$P);}function
checkbox($y,$X,$Ya,$Le="",$c="",$cb="",$Ne=""){$F="<input type='checkbox' name='$y' value='".h($X)."'".($Ya?" checked":"").($Le==""&&$cb?" class='$cb'":"").($Ne?" aria-labelledby='$Ne'":"").$c.">";return($Le!=""?"<label".($cb?" class='$cb'":"").">$F".h($Le)."</label>":$F);}function
optionlist($_,$Kh=null,$Aj=false){$F="";foreach($_
as$Fe=>$V){$dg=array($Fe=>$V);if(is_array($V)){$F
.='<optgroup label="'.h($Fe).'">';$dg=$V;}foreach($dg
as$t=>$W)$F
.='<option'.($Aj||is_string($t)?' value="'.h($t).'"':'').($Kh!==null&&($Aj||is_string($t)?(string)$t:$W)===$Kh?' selected':'').'>'.h($W);if(is_array($V))$F
.='</optgroup>';}return$F;}function
group_system(array$Lf,$Bh=false){$F=array();$zi=array();foreach($Lf
as$y){if($Bh?driver()->isSystem(DB,$y):driver()->isSystem($y))$zi[]=$y;else$F[]=$y;}if($zi)$F[lang(8,'')]=$zi;return$F;}function
html_select($y,array$_,$X="",$c="",$Ne=""){static$Le=0;$Me="";if(!$Ne&&substr($_[""],0,1)=="("){$Le++;$Ne="label-$Le";$Me="<option value='' id='$Ne'>".h($_[""]);unset($_[""]);}return"<select name='".h($y)."'".($Ne?" aria-labelledby='$Ne'":"")."$c>".$Me.optionlist($_,$X)."</select>";}function
html_radios($y,array$_,$X="",$K=""){$F="";foreach($_
as$t=>$W)$F
.="<label><input type='radio' name='".h($y)."' value='".h($t)."'".($t==$X?" checked":"").">".h($W)."</label>$K";return$F;}function
confirm($wf=""){return
on('click','confirmClick',$wf?:lang(9));}function
print_fieldset($q,$Se,$Nj=false){echo"<fieldset><legend>","<a href='#fieldset-$q' class='toggle'>$Se</a>","</legend>","<div id='fieldset-$q'".($Nj?"":" class='hidden'").">\n";}function
bold($Na,$cb=""){return($Na?" class='active $cb'":($cb?" class='$cb'":""));}function
js_escape($P){return
str_replace("<","\\x3C",addcslashes($P,"\r\n'\\"));}function
js_escape_re($P){return
addcslashes(preg_quote($P,"/"),"\r\n");}function
pagination_href($A){return
remove_from_uri("page|next").($A?"&page=$A".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($A,$Kb){return" ".($A==$Kb?($A?"<b>".($A+1)."</b>":$A+1):'<a href="'.h(pagination_href($A)).'">'.($A+1)."</a>");}function
hidden_fields(array$Zg,array$de=array(),$Rg=''){$F=false;foreach($Zg
as$t=>$W){if(!in_array($t,$de)){if(is_array($W))hidden_fields($W,array(),$t);else{$F=true;echo
input_hidden(($Rg?$Rg."[$t]":$t),$W);}}}return$F;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$wj){$wj=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($wj?on('submit','uploadProgress',ME."upload=$wj",SESSION_NAME."=$wj"):"");}function
file_input($c,$th=""){$mf="max_file_uploads";$nf=ini_get($mf);$qf="upload_max_filesize";$rf=ini_bytes($qf);$Og=ini_bytes("post_max_size");if($Og&&$Og<$rf){$qf="post_max_size";$rf=$Og;}$sf=ini_get($qf);return(ini_bool("file_uploads")?"<input type='file'$c".on('change','fileChange',(int)$nf,lang(10,"$mf = $nf"),$rf,lang(10,"$qf = $sf")).">$th":lang(11));}function
enum_input($T,$c,array$k,$X,$xc=""){preg_match_all("~".driver()->enumLength."~",$k["length"],$jf);$Rg=($k["type"]=="enum"?"val-":"");$Ya=(is_array($X)?in_array("null",$X):$X===null);$F=($k["null"]&&$Rg?"<label><input type='$T'$c value='null'".($Ya?" checked":"")."><i>$xc</i></label>":"");foreach($jf[0]as$W){$W=stripcslashes(idf_unescape($W));$Ya=(is_array($X)?in_array($Rg.$W,$X):$X===$W);$F
.=" <label><input type='$T'$c value='".h($Rg.$W)."'".($Ya?' checked':'').'>'.h(adminer()->editVal($W,$k)).'</label>';}return$F;}function
input(array$k,$X,$n,$Ca=false,$vj=false){$y=h(bracket_escape($k["field"]));echo"<td class='function'>";$Ac=driver()->enumLength($k);if($Ac){$k["type"]="enum";$k["length"]=$Ac;}$_=($k["type"]=="enum"||$k["type"]=="set");if(is_array($X)&&!$n&&!$_)$n="json";$De=($n=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($De&&$X!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($X)||!$_POST["save"]))$X=(is_array($X)?json_encode($X,128|64|256):json_encode_exact(json_decode_exact($X),128|64|256));$sh=($vj&&is_identity_always($k));if($sh&&!$_POST["save"])$n=null;$zd=(isset($_GET["select"])||$sh?array("orig"=>lang(12)):array())+adminer()->editFunctions($k);$c=" name='fields[$y]".($_?"[]":"")."'".($Ca?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$Q=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($zd[""])."<td>".adminer()->editInput($Q,$k,$c,$X);else{$Jd=(in_array($n,$zd)||isset($zd[$n]));$bd=0;foreach($zd
as$t=>$W){if($t===""||!$W)break;$bd++;}echo(count($zd)>1?"<select name='function[$y]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($zd,$n===null||$Jd?$n:"")."</select>":h(reset($zd)))."<td".($bd&&count($zd)>1?on('input','skipOriginal',$bd):"").">";$pe=adminer()->editInput($Q,$k,$c,$X);if($pe!="")echo$pe;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$c value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked":"")."$c value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$c,$k,(is_string($X)?explode(",",$X):$X));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$y'>";elseif($De)echo"<textarea$c cols='50' rows='12' class='jush-json'>".h($X).'</textarea>';elseif(($Ji=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$X)){if($Ji&&JUSH!="sqlite")$c
.=" cols='50' rows='12'";else{$H=min(12,substr_count($X,"\n")+1);$c
.=" cols='30' rows='$H'";}echo"<textarea$c>".h($X).'</textarea>';}else{$kj=driver()->types();$ij=$kj[$k["type"]];$va=preg_match('~\[]~',$k["full_type"]);if($va)$tf=0;elseif(preg_match('~date|time|year~',$k["type"])){$Qg=($k["length"]==""&&JUSH=="pgsql"?6:$k["length"]);$sd=(preg_match('~time~',$k["type"])&&preg_match('~^[1-9]\d*$~',$Qg)?$Qg+1:0);$tf=($ij?$ij+$sd:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$x))$tf=(preg_match("~binary~",$k["type"])?2:1)*$x[1]+($x[3]?1:0)+($x[2]&&!$k["unsigned"]?1:0);else$tf=($ij?$ij+($k["unsigned"]?0:1):0);echo"<input".((!$Jd||$n==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!$va?" type='number'":"")." value='".h($X)."'".($tf?" data-maxlength='$tf'":"").(preg_match('~char|binary~',$k["type"])&&$tf>20?" size='".($tf>99?60:40)."'":"")."$c>";}echo
adminer()->editHint($Q,$k,$X),(count($zd)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$r=bracket_escape($k["field"]);$n=idx($_POST["function"],$r);if($n=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($n=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$Vc=get_file("fields-$r");if(!is_string($Vc))return
false;return
driver()->quoteBinary($Vc);}$X=idx($_POST["fields"],$r);if($X===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$X=idx($X,0);if($X=="orig"||!$X)return
false;if($X=="null")return"NULL";$X=substr($X,4);}if($k["auto_increment"]&&$X=="")return
null;if($k["type"]=="set")$X=implode(",",(array)$X);if($n=="json"){$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}return
adminer()->processInput($k,$X,$n);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Nh="<ul>\n";foreach(table_status('',true)as$Q=>$R){$y=adminer()->tableName($R);if(isset($R["Engine"])&&$y!=""&&(!$_POST["tables"]||in_array($Q,$_POST["tables"]))){$E=connection()->query("SELECT".limit("1 FROM ".table($Q)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($Q),array(),$R)),1));if(!$E||$E->fetch_row()){$Wg="<a href='".h(ME."select=".url_escape($Q)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$y</a>";echo"$Nh<li>".($E?$Wg:"<p class='error'>$Wg: ".adminer()->error())."\n";$Nh="";}}}echo($Nh?"<p class='message'>".lang(13):"</ul>")."\n";}function
on_help($Ji,$ei=0){return
on('mouseover','helpMouseover',$Ji,$ei).on('mouseout','helpMouseout');}function
on_help_value($mh="",$rh=""){return
on('mouseover','helpValueMouseover',$mh,$rh).on('mouseout','helpMouseout');}function
edit_form($Q,array$l,$G,$vj,$j='',$D='',$Mi=''){$Di=adminer()->tableName(table_status1($Q,true));page_header(($vj?lang(14):lang(15)),$j,array("select"=>array($Q,$Di)),$Di);adminer()->editRowPrint($Q,$l,$G,$vj,$D,$Mi);if($G===false){echo"<p class='error'>".lang(16)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$tc=false;$Tj=($vj&&!isset($_GET["select"])?where_columns($l):array());$zb=(count($Tj)!=count($l));if(!$zb)$Tj=array();if(!$l)echo"<p class='error'>".lang(17)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ca=!$_POST;foreach($l
as$y=>$k){echo"<tr".($Tj[$y]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($y));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$oh))$i=$oh[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$X=($G!==null?($k["type"]=="set"&&is_array($G[$y])?implode(",",$G[$y]):(is_bool($G[$y])?+$G[$y]:$G[$y])):(!$vj&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($X))$X=adminer()->editVal($X,$k);if(($vj&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($X,'',$k,null);else{$tc=true;$n=($_POST["save"]?idx($_POST["function"],bracket_escape($y),""):($vj&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$vj&&$X==$k["default"]&&preg_match('~^[\w.]+\(~',$X))$n="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$n="now";}if($k["type"]=="uuid"&&$X=="uuid()"){$X="";$n="uuid";}if($Ca!==false)$Ca=($k["auto_increment"]||$n=="now"||$n=="uuid"?null:true);input($k,$X,$n,$Ca,$vj);if($Ca)$Ca=false;}}if(!fields($Q)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($tc){echo"<input type='submit' value='".lang(18)."'>\n";if(!isset($_GET["select"])&&$zb){$Zb=($Tj&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($vj?lang(19):lang(20))."' title='Ctrl+Shift+Enter'$Zb".($vj?on('click','ajaxForm',lang(21)):"").">\n";}}echo($vj?"<input type='submit' name='delete' value='".lang(22)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($Eg,$u){return
str_repeat("$Eg{0,65535}",$u/65535)."$Eg{0,".($u%65535)."}";}function
shorten_utf8($P,$u=80,$wi="",array$Fg=array()){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$u).")($)?)u",$P,$x))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$u).")($)?)",$P,$x);$u=strlen(isset($x[2])?$x[1]:preg_replace('~\n[^\n]*\z~',"\n",$x[1]));return
highlight_matches($P,$Fg,$u).$wi.(isset($x[2])?"":"<i>…</i>");}function
highlight_matches($P,array$Fg,$u=null){if($u===null)$u=strlen($P);$F="";$C=0;if($Fg&&@preg_match_all("((?|".implode("|",$Fg)."))su",$P,$jf,PREG_OFFSET_CAPTURE)){foreach($jf[0]as$x){list($Ji,$ri)=$x;if($Ji!=""&&$ri<$u){$yc=min($ri+strlen($Ji),$u);$F
.=h(substr($P,$C,$ri-$C))."<mark>".h(substr($P,$ri,$yc-$ri))."</mark>";$C=$yc;}}}return$F.h(substr($P,$C,$u-$C));}function
icon($Zd,$y,$Yd,$Pi,$c=""){return"<button ".($y?"type='submit' name='$y'":"draggable='true' tabindex='-1'")." title='".h($Pi)."' class='icon icon-$Zd".($y?"":" jsonly")."'$c><span>$Yd</span></button>";}function
copy_icon(){$Cb=lang(23);return"<a href='' class='jsonly icon-copy' title='$Cb'><span>$Cb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('%c01diDWB2N@+*{U)+O40SzmMXgW+A-kAV1E>/C6W40MS%--E$3qy%d%ctUw<85"+>JTGsn4mlgd@]BM~12S{CPmNirLRATb=madbKkG)A."SK1-u%
uk.}Q/<!rnEC3|gZt>FKc>.DPjc$]IwOs;L+.`"QH@1q@Ood`}6u_eqgjy>EWa.RV).Rtu700Gr]"`V`e[[c8REDH7T@vPeYL<epve_&(&4qQpx
!mVo7S=HaJ</;giWs9,j/`39^|XF<J=W,Gm92Z-04%$wlC^$>QI3
+
_uh$rSDW!)6#I["$wh`#dgKwW6yN>:jXMT{[C6f2kjXgUJbS:@Xe}@^iYo|FY-M:}$XUITRvPu+!QZ~.!Q
_c$pLm%?_URlH5V--*5gDt)9u
xKy)D.TA!=Nu`+KsJ-qI62_X+9ju&wA4UL[DTs+E>}f)e|t09p;tNLa%qK;(xSnP8}!QpHq2NSttX/](+"diZ}Sa$aV>(M^<t6eNMygs&7s]>7I3LNd]L}%/of-ne9iXVgo9&J<(1bWe,V]A[eXdq&1=II$l?jN&^mf
_+kGL*KnO$>v5o6LQyfL8.m:70R9?#]@e
id`8?``&ba%W;5!>[N!.Fg_!3qBSOmVgg%eUF+=T!6+gTaITOBkLE/"
I4F4
p/fvCYQ56aZ*XL>v>4W80D%Mp9[k|+8rFf;%y"{PG8+T626&ZS85Wi=G+bv]1a>#pk3B{c<v9p}>h.BNZgbE>^$$mWuFlud[_q&&<>yNg;$<xt5"Dt[lt"n=+;s-KO$Yj>Y^<WQNsuK!{XtpNaM5Sn^/}"aKc;G(p&Kqm>@j(aO:x/~6%h1GeRN!l"B_AkMYmtU)M4L#GEU
X&Ycp)Sr?NB7uB|K7;$
XNvlEO}hXijvq;L)2*G
Orr-6/Uo5sR&;Eir,Qfh/oe[~`&[m0R;E6-X:8=rH;"eA+Z]SuVZ"vYH>;$?xHe(:7k+7#t<]<Yd1@G231uK!iMMTkD-{,*qc8U%k;H4Y%@OY1SdeHBmm^-tXs_0i2v[9wGk2X|hX?NZ|0*Z;
u5NPnxA^<Ia=SJHpL?x00_{m]WGB+E+2WgRa$%D.o9WJ^RMucaHEXZBhioFw*jqRGpAhZw?lpu=
O,!m!"r.cy[V&xx>J#j6!Q]_d1-Q1pu$>"sx?jBi-3Y=ycq/t!s6tD
]U5~`#bcB
2s30,M?C%DJB^x1y[CMeSo6JWm!CU,cHj;CM0g[?v!qpjMIK"
5_;*v5n{!NX~Fh>kxz&2*_>TYKPM493ye5rMJ.f)#KQ,7aqS-DqK&,?<-I)4Gkr++*k#LC9GTH
a27C/681$m|NH1G8iC%jg3CTTAMAp/HP/kI01#5$rU3*;+=IHYdkhHP])Gdvdo~Vr)wR7_gbkbvVkZVU@Y+MIQer7D.ry"Gb.boJ5T>+MSfJz6?C1WgmAH1m=q#-z#"#Zy90|1YPxvGQ^]n>"b]H^qf5t4XJ4:h&PNUj[Y$=n
,#%TLFP72&eDlKpjc-}gM6G931e^>`67?8a.?3lPVKk.z/r%.l,^jqBZXEZI_<$a.xk[A#z"<A}(
x#dc#&NP=mfs!y+T88DZl8^]]*vOI%1/%&@Opcmkdl;fAAUeep
=-b*DflrFPm9sb6u9EB-a8n9[P[M
6e/Gn,7fXIMQ][56xS,,MES]q/p4w<?N3SBb0Qir3K/0hLD*_*Uw-:qq/N1oCUZh$UgT]h0WKE[mbKUwB-)@;4<gJMD2^+%TjA`+nc7uX5V?&5cf#%-Jm="`%w>GdGAEYf6OHs
fcqAr80qXE5NbdaO%u4u{4f4*+-IpS]g{?M([-~ZeTQx|-!az+!YO.s.<<vF7(am`cwqX!/9:16#&_f
T2EbmdofC+U1g
x3DlM[F"D&3=tE;p7YzExWF:]Idb"v"28cI-)&e?g!xTc))U*sQ95hP8b*|+Yg:quDG1#eC&L4#_HOF;BR/<cjm:$d9)C$a4D1eFgZ6@"A=C}G|^UhDW*+^-Ce{y5(<k|>~vvG>GwQ7x7,XpH%|H&Q0gfn)vVbg[9;nk:pw40vC&x6,a{UN!RRu9$d>W-+B<?D6T8WgwG]Ps+3bjzlQZ2XMW[In89amSj&dE`$LOJ;u3BuZrD
(*wT#D=%#%W=_4Fv3UIBXviY)P<40yUpT8{?=nb4>$9x^,|,m8!WRwVD.#vo~X&^)%sF>9w.JDc1ududI1JhEYg&uS2`a-
^E_BqL1t(Cv+
}`dxz$.ZQ[CL:+g>c5~F>#1Z"<g%/%6D4b<$AAJ[P76BIDIC9h,5uRnP>ZD1VR3#lK5DM%)6n*=*3V{V_`{6;eAyDB&p
2NN+@>"oLaFwLmgL*8nQA/5p:;,C7;UXJrjxHEr
AV,vV?#j:tZlT6wLotY
Ae@(va2>OceqCqLg^<ttEydv4njnsAUaBlJ3*^${A
h`P1K$2x+rc2/z`|a&e4SCc+dL3:3u+?3@%
@U,F_OhY.eCss+@?t/GG@g"V%`c|o1TF7[bV`?6<*_I%VIp;AtU|hmrXif4LS[OX<]5p-?"/%C<?q+xvhp#n`N-Xic=R^Z1XZUb@A#7?4shhRheY,K$2)n*bGF`+WLY.)WM~j_i*qst`/dq,%&1m3-Aj
HycMPK+N"x&]kwx,ga)_h7^?H"sb}hayMY#,_fkuYcP!Ts3,MlQz)WQuw8Wv[lh]qKpeIKSxJ$Wuk7sWeQ5Mn7yffB}tKtIuwxByc]7B{IVA?ct59q$"k;T4s>QeMCl[4SjLN,g!7(pwoBZ?%)^Za*011DSg(*8yfnmM_.Yqk*QJT1q>sDLMxrZ,ml~fXE0Mfc{J8-Nlsom9soGn7Ly`MGLa~LG^BWPAZ7/L~hI2Du=^un#%S;[^!N>sX+vlo5r[d&H"?:d<V<%GVDe@5K|9},gxr^-unkZQ&E[J6Xvo1vGN7jO0FbKq9[Kr(V8*f9-!{BNF"l}Q81uW2N(feBnK]0"8T`D_
yN).O]&1D.RRz!7@)qfCL|.va<c8P"a1<7AflF?U%=
r!6qrH;TlcE3tm@Rl4uX}
0LI^i*FGk^MAr40w@DogJ@{XRRCc{A,w`KT)ytj92dBo~xbN$6xnLIrx`WMc=yEQ]tu)F4[DF2?w~GN1`6tido7R%d=b]K@x+A`FNT,_eL1FI&Qs~"J?n>[5:]u(nUc2C;0OcZ]<QtV`?IDFj!Ux*vmKR@zy?i{k/""6|Q$1lawCBuZh)..IS")7y:KP8+]`LQ#p!ZK.wXz`zD3byP^bl9(eW:.V_:]q~V,:..8*}NqM9p%`SI/Qx3a*1/m@>P?0QDMcWg:/BVe%v7v+$R=yt%94E-#t8H
?%FiyWaFOV-FK{Q08OcB
PHCe]u*P2(isXo)+
rxI8AEyzZp89Hk.)sB)&OxKE.=VPt@XLbyX?::6|8UpkHYQ)1eUDIahnab`~L3Hafq^N.<(Cu&Vm7JL9R-Q@%G`,)9YI^2T5B#2;(IWLifq$RVEEtm.s0Z_NSq804,%7S80f:Thm"^rVT]ZHu8_^p"&XQcSPsPlc%2BPu:&&');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('$OsWO6/V>iQ0U*-2v8,"EPe+1oaU8]vYHI~JEs,`RS(%eB<
edzp?GDTrqJa&,byu%Qo
RN#YcDBt37%=UM2XW3[y=wB4+1CJ0}>9fg$)oQ&zfCBsZi?1Le0|$I;W)8ZpxIB-x49>c2QfWz0O?ibwDoF}E%C,vd(0OTjx`mgOd7
0n~F~X(pt
g
u>9U=`3(A+#!x.=4->HO(LL3srh&pDm-{G|_YWFZ1j0JnB*K*:V4_"S!V;@7XZRS]Z{RJB5@|EK0lxYGiH[kg.L<@>guR9Mf[]0>^,VYVG!P/I3Gar6UNSj)`gTl<J7]wP$4NXEa<3mfR^=y;!`Mlbk<(NB1;:K3lTebA]C!;%_hMb`J"uedgrN:`:Vy}sag{m6$gSAgRZ8q4GM+H]jWP6#+b8
LWk$:NrAi7/9uHn{^eWbAghGBcqwyuJ6T;!JrnY7:5&&oXaKL
C_1qn*?D?I-j7_3:NtD*S_`kD=P?5.
jCj_u
W31;PuL8.Nq)E-0?zkq"o&C-!tbvnwfeUKdk|<VJh]]LHfghXq{,fwGV#A/:FcA<SInA26IB|`7$/mra73~>Y+vSbS^MTYaknr_D!qi
2vdwG`p8(KA`kk#N:K@^lw&l3yYJ`[M;?kHntt=y)`S[@^{I=N[$1E]OmU4:<@sClBME2v-7D]h]m4tZS2BvV2%OHE7*MhVh*LO<.nSp#281Nca[Hu`b
Gw@g*psms
3Iy,I$3.aZghNUHYxPjbP:2_ZI(u*X<">GfZ:uUnR4Zb?2#k4K@AHt+
`6V@)$utkbDbFoO(GMq9Pz&",&"#V$tuDnZ|>zSmB3aZr]e)HMaNM{D74JlPZx`P+sdyt3Re3to1');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('!X/GfnsWL2O6ulI6;O>RUFFnCa&VJSJGDn$ho;GI-:YXhDpuUONOa>Jg::(]oY%x]#&(>;zc8y79gH
":&.l|384=BYLN
]TaZ>AE

+BUMK{HC_cXAGV<[C@8|47JX./5f["jbxr`0H>rhe^Qm,^?pJ|wJalc"*APW[hO<l>*|n-b!pZMxuHP*TEiXNl7w(P*4wxusLz<6I5M{K1PSW]r}3J.0_]XaV`%#E-[D4TbqYE+G:{/BGFx[W@66,"&qjjVC`ft.K>
aY:>zc0FcBykzkYy!l&4;])n;t+E~6x65q636pi$RHGo;]QOI2D?AIc_<3Hsji/JCs~
ln60Av[iMm?=JvnP]siWqZs2?W9"mo^&G]5_B+y+`spQcu`
EjJJf]L.u4NLi?cX~
_Tlc^8teq_O<G>
A3MrY&hpmxQ;%v.3$p1&aq=!xLs^Z!ErumuN]~1.5b0%;ujnh+uXb}T65z/^qb9CmRM^SFt6L~cvSQq,KcWXpAcdCaKO,sH5/8O@56iHR%1JjI0[!
rY(e@.tyhqX
@<vJKr^T*bN=Xew
tLhU$NK(8L<h%0
(?N/ct3ap&MMrLniU,X?VN4,vaK,aE`?a),rU!ncuabWo>qh"gt6C`K(Xj>e;u~LNUn-0Esu>
<ll
G`!*Z>*u*"j$4_rH@TFF-^w/pGk1
0NION697K3oN5p
:;6:?+l_fTTMq_K)]k~_Al{[+i~X;%2g[mN3G]Q:[rRTw`)6n%No#paK0G[._ag4_aW67wAG/Eqv?D7Y1=Q71X6I[.@Q^2awJiL,2:e3SDaVV+8q?N6UXA%TzOey[6.]rX|q:KS$YINvYR_?C"p%)
;(!F?a7,7h,2SC?`{h!%.
pLvbG@~h2gGa[F*Zc51s<,"tAm!k^[Zwc55,Za-Q9c0am0+>-K]C{fwWnV[01(1M9wq
>o[Qq9,QsWG!&t<pra@dkM;l"i|+%wE(^4#S4weJS`Ggejl@L7T?x41Bo?gN54NXYZt`C;l6E@>wLhDduXOUBX;+XZ_hqo![BRRj>F8I>(~:tT@b;-HL_b.!
pxO%tR>4/WpEju6%:`vddzrj?cr1%Hb$E#dJWu[A+*S2JF=2h!&UY&nwH&u=m:>u>7"(aqiB!ON!_F:+^HS].icRf?V;oP:fQ0T{?Ysq5Dh{u;KXQ*9ht]:jv~#s:A,o0sm-BqtfKev:yXSp)NT"TCV`P_eMP6=,"NV7*6Kt_,9*]eRP0mD&g$4N+Vcp$/vP@yh`j_6EU],+77UTusX6J"ru^*Y5*}rw;o5~KeF)m)Y|^ZIi=(?)3?4ndOF5w59*Y?w$QwQ((SO)]K!?[qW=WHj?vCb[;K@i]kjL
o8kRB04X}xrRYEOGpuosT)`g/l}7__k2Qq]-"5wxC9!2yk+t@])^73K[np_Ea,cYu2JWR2T0]Y)Cd>Oi>hc2MxX`zb9V0:gq"lW*73`=vu3Cs/<CY;&h0icuks(iGcqfWu>s/w90v+</pn#Bi!mqQps_"m.`9NT="mzw[>2[c@a9#qkYP6$U[APE[PT64q#sO9xe_J$DC*xf7^fE_l59P)NN@Cy7L`nG-NHc<[6Qk`K"#Nn%::WR4mT_3duF?G96>Jyd2?{6}>yoMa+r?^y>nqB8x:gE<;y!io&%
v?#+S`ha61"6XeB&Ou`q/t!|0}E{kkltuYoe"hJ
oK0m[&UIS`7BCj4x@nPFVyK^0v`l</9?EqoP6%UZjd(VTMdM7^[DZ^q:Jv,)x}Ctw*$*NA*D9vTU6N;x7f>#UT?wa1uAsoDCW]<=$&!E)ust.,L|7$Rh:=[Pj.Xt+t1L2Rn)27s&,6;.6vqe6<"+`~QKI0l5/,&QsD)el.I(1
z#?HGRjPkG_?L5*<Jd!gYnX~?ESDw47t"RN1*}7xg&m|JL!x9>4{/c`zf-,#cp:0%@V1G^7|/R(!:Oe94^J1a%nm,qsNc*yD]JNUz(eXU<].QTgkL0q$6]5@4w.y@wx_CswQ)kKk3.+J]qR&&WB.r?]IWv:)j/"xx,mg&|T~U6nN7S,Nr$H
:N+"+^HuJW;U&of_h-<S9Oa@vZKK1jA#g0v`%FO}"A[mHUg*--Wikz.pSX.5fOPXF+j"-tsN&y:SCw].T6Xje7C3y?yO%lUfrpq>4b^~XqN_sfD<gedKgEys.v.2"6"%*<O<.hfL$4H!-#6K4KkI!%I
7kdASjbONsS{G^d3]NoKXexL#S]bs}<l.?4wHv;xQ;cA5ym2E8O;`(8[+KAN)6xP(eB}b=qgEqHenu@Dea?~fPF%^d6{+AiBOK119=T[%An#>R<0"SjPR+k{A`MDkaRbF)*eH|"%?3I]HF^Ul,Q)?AhW8CamjKX(&S+|EAL{b_!2@/c=H*gI26=yB(H5[_,|E1jJ%4jb20&#.P?Y93Mz6,=6`P+EG2>x-sTlSyw`+]$e($8y[2MPSbr1F-X.Vj1Qfj/:E7A]I?LQUXNscF)bS!KFq8:dIw;Bjkl"r2CA+lqM+IsOD=T,MtCCXuX)f,MTKm;.npdCZLXMjL):KE4Qeq.MyAQ~ke%i:3uxK
8<"No{-(#remjqSU9=^+bEE
7/axPTlB75q#:*4I,^[9#+IE(:(f4hM)w)lvE3cDZw1|xlbj6Ck<&]F*X/VMwUSgKpWw"<rvvFg?JTOHH)KTD1-y(zZ7th.S)J(_6BlF/H#X3I)dIl"Fi]1JNoVG1e[{VbBbL|VNe=i4u:(@
5PX(zX4#Q)JS<6+
tu4Z>"u;?ah7POB9GSihSnYyGo~N$yI#[Xl*umf:S+i;7m4WYF4k4-L0Xr*3qoP*!_jXgPdpe1"Ekx:5kX>[yCgJ5a-B)!2$4H6e;#4kq?5E"jq]88*U?aYu:Z{f:n
,50
6+^yvhdK,DnK?^nY,2+c/nJZI5%z+}LG1NY%:tEl)-]a:^]vF|NEu})G?)
(K8k*[WMKR!<7u/-k^ufOqgE&b+
bgbV>l13-
E:5jW.t@43+_ku@I"ND,`+IliPW7H62SrySo4*c,#Y>
edfLu8P]%K
,pK]M^rogSfS`z6%bDD5`=CkQf@Z:(/Ld9UT)beMwsG
aHeh-}L%G2xW)J#c
L97POn;^9%MiUF`Ed[=FIlH5nZi[eA|))H;HlU6UQV[&HC#P_51
P,])[epK1"cWN8C4>5CKu=wv>JGCmk{tbk(l{K#tWwXwB6.wiL5lDXTIiT%+tci6XAZ+6u,hZvN"RN9/6tul{>`;c)[^
D6wL;(#Nh5Yigk",0=L1%-!h!cvv4qR10xf&2B?8S)6`$kU)Usg(8H"]t/"T0J+bCg(eU4=#+Xch^L$!_,f=wyJ"BBo3PW<}Uk
>jPVWVdOU:Zs*D%mq5!#uB|V#:?e
Qqn9:r*.H*G8BDw*/YrVIqXE(8ML^NJHQ6LIxm
%$zRI:[D@s[ts9-D%UWP]5:HZ3T+mE?pvdjtdd8l$cT]"B{#S%X#X<g?/6NhwaJ$HaKI15M)/ACKZ
|Wu*@!t[CBF]b0ets`P*e)0fA(:8oo=@`Z_Hx^sy9k+3I7V-oh4#yma95:nvQf)7;j0Hk8dU4i
r5j,hm<}bCW>UX,s9[$*lV#TvgFX8L6;/:^H*<;Yh;)emH<@IV^Qu5,Y:D)!t>gc81N
9ra
A&D;AJfu9T8V::%kVX;4tOWOGmj@C6io.4ju!&KTJ1)Vah0Q+,rcvwnK
`5$2_elVF#73#9EgSV#K.OJSrhJ5!x=?ElvsO2Ng|wz!9Np*_t-Dy[1#J7{sZ;K0#6aZ2h>$ZuBHHC7/xS$Yvw@Ye$;8KQPe_K+#V-}[j.lC&y83lG;cs:)>}OO0R&_O*(]D=ktjsA:80".i:O)KjQ^yQBn)+9.fy<,+E`F1|WbmBXOY^2b:<gfQo5qsr"jw-4<Q3L&L+edxW_d=JW+_L%:AJ/H+5d4Tup,<{h^gf4oP#-(S0w`gxoCY)yo:]x$3$oa`m^Ml7+hoARENq#qWH1LGse--49@rMjlbQ7U:rv{u:XUmuI9s:G^u4xh1RO3_KZ0giA;N1S^4D:;!B:sb3SEVU4Yq|X0n#HuHrod0U47w@c.LVDiTbp]wcqMST
?6cC$IVlm9N*fNt^*vdLst$W-RN!<b=Lk@A%uEBmzdB&C1U4sS`/RS<G`I0i<x+hL(xT,?0jB(2e
wC;Ujtc:A"<0wq(x#J(m
LPW#+/>l{Dyu#_:Op.S?DsOXp;Ir.aupIYyVsk!.s
)k5Cy0*d04iowDN/EW;]8Dytj8TAKCiPr:KYZ3|9uQL%}Fx@3Q"&G=?RT[Mf&!;2Yc;F+E@5LP7BRl"
1?W^""{Eh3m2V80%G>)`6de,20-*aro5<OePR6BLeV6`(gusG18Ar`&=)D`^-9AMhZ3fi"ja:g=Z97FiKGNV~"/VY@w9wYdO?,t0ZK0K@+=+M*uLB"K0T;_p>I&w5oIRn<mc<T"fm%j,Tij*8a_Y5%PgJ;a`EAe$<r|Nx"{c-A7c>>1ayRLH"3WwDF;yPv&,n8I$Pq/cw+iy~"fq4@m>N*urX0B__hl(byOb6iHxU39O%lB0[AbfIw.B3l^Mf;J!Y104~fUgh53ITd-r-oI=f^WIop^bM4HtE&Z)2("8iz$38J5fnyq)`5M8cIKT*-[!z:wZVZx=vd9vYtkF+&I!lMidg@RoU&pLKe1G9Gt`tBkSb8N&^kqy}-]w[fB(E/P-=w+XETY<eP!x
Z5)fU6Xvgvl$j;T"H@mM*#ftBHe^9wdm)%x@29KC(UNwtj,Tgwt+MRKmc,IceWeQ<O</DabUS;"wW*
tsC+,l^[c+<e3pWBGN5(ajbB1I~3XXLLi#`3w%V=O?j%>G@kJf(IlhP:F!-Wbq2A`iC
*y3]Nu3/"MT)U4byPvaKVHH>*v[bLdX7}toq:dXBXe&U1^?:-GU_FEd]qL("/#/h0^qUP@h`DjSSp"$S"Zb1&X)O~vT7~TMb_$T@d1UGE@Ic/nT%AJJ`%3=fb-wf72rO|mDH$mhxOfOb9fHWNEEK!9)WG++s_k7iJkNR2Roxgs>wwUeQasVJjteIj/`
b<v/e17U!D&B&b.bbg1HA)LtX6,fF@n1nn}nl[agwxWT0pqKHuCKo53&M]0W<c6:2).o~%^l8#-oauBs4#EP(YYUf-Ua`"r=<,e6]M1yG#zz(M-!J"SQK&&@~`5+wQv+U;App+E!eUQ&uo,Vg880-/5DyvX6UV4v<Yga3(+&p;;U>E}IaDGJxSWNYqjZigu:MQwqVN~yvz%=T=$rV%k<=Y,9z_aEVHWprK:*=
W0`?b?=Q0UDGi"";OqvQj"GDfgJv`OZo+(G/f<AYvWi?.d!QeG2J5_f
5kpi3>?wbTP:q.SO`vyAY/RRv1XSW`PRFi/Q9XVjr-QqGoR?W`ALE2STPMa*Nd6fj#o8Gnq%@I)o38h4?o5!4&]kE:h2s^6b"QFvG8fI`eiiZ$./ebM-`;?&W[Xhq]WISxK+-G$@9VS3OHd!(Ym*bX/ww%mwHB.LNKPZs,):C?bc`c0p^,_Z;kJj~#3GGek,YYW-
W1)Db$WH;3*`[w`(e>Pol*,3Dj/R$6@&%UHQn;bo$6.))]PP?fp1?7>meE1pZhVivn9%+04#9mA:r}U;6_fC1{RC`c%KtYrO4^.0_lB^%nqf6LPkqcw`+=2*>7XP.3qu#FTD;>,&<i<w>0.@f_MHj9W{U3M=%MU*YCyBq%TV!jau+-XN[J8!>TmwV,n""Uc@$-"q?ohQF,HKd6s?E.1g^]Fr(b$HHQRXii=x85LpWXam6z#UY#m0T4bglL/v[pC5PN!"_/3
gjnq>!3Y)PQTy7TPr)W5*b_32M-cwpK5CkCxCRV_Qo"~<+wj0@nTgN.CoC3<_)ZXlG#iE(;t$s)n1}8RKjvSq7Y<a9.tpf6K#`J*BN0M$N[nF!J2[.dJ)|VUU1aP9W9.->R:F/yJE>h6*3rlyhTY;JCW(.%vx}SVt~9#o7r3;91Sg8oprmxqm^N[r=/aw_WtD+rEOB:Pp[u[g@PvglI_xz#Kbb04,DbgFhD<t-N*CLbcDP<B)?qipLsL(`FGi*&7W1)j-TE|`Zao#ap$tow5fyodRMbBCmh=*xrbVFD:MpKE(Rta2#m5$eS-=MGYW@B9W?N^O7Z+(gHI,f+d,yU}`>Eb[6E!(!,AiarF32G{w1
y)=qHc,^EnY/60zgz@J_Brm)}T)uf-//uWp`Ueif1r_:sel4V%iN*,`xFTX[{ehck+3QFf9r/EF.tt:/K?`e]0pM(F>ww5mhbP
x3RNilPP=,Wb*gD]8:[Hsd(LGx=iejd4?
Bi5I6N*Tp:UcR%9GhuQ0taAss>o%$*`UD^8-9`6"nvW{5ut2?i]RM&
:w0%)?:"v4u2;XX=
`MC/q`$I(|;lggQC6FWNN)=$PUs}
j?*?ZCUJmNnBARK5uPjS/F]Q!:STT$^YO=]?g*h&
5BsKI)1f*m`VrNntl>c"eRQo9ynu><%3?-wy;6aa7&gDZ@VI-V;UMp`GE%;4/xb$R$t&W[s|u]$Is|`nq8IuB#D_fcA[^d%y?_+KJkR#=R1^.E0t?O_Q7M@lhaA_"09o/{[adjNzp.e)*|bY
x64Loj%r2;CNJ/hPsKFHds#%J
Qwb02!KF
^jqEg^d;A}<>
IS(AWMAM~<.*U>8%Fwrei</4R2mm*>9mFjuEc:wfCYpr<d%hb`i!no`k0w$ak+e_)V!/MiU-LP>%L_XXa=<wRM4Ra*(U[sVd:iEc7t.[KyMlPQ<QbR!@;?SEy!)tIQKsuUv]>WAcnt=ID
gy.g"MLi-yi8~&O6-nAC<r3b;4Wi7/mInUE<5:MDDd^1U@R/D.|jsiLIwCuS3X_97#YVDX
#Yk|W4L/9(E]6l
XBR6#+9k0Khq}>vC`0S9h`V+7RrAH@ENSjg22!_oFRs<J^/xzcHhJ:GwdMe2^+Tid!)3M-mnyGrjF^ffy:GkZxxV?evy^L!.fjnj@tD;H,!"m_*[rVO_v%Ne|ck9w:lB0rBZz)3B)AmKZ*Vret8t*1=nQf<<yQoSA"dAFmYVz;S%"6{FE7wZ`rBqw3RN!95q@gMaluH3>LlO}vAZmeWlydbt8Smx?PBs)pC<-Q!_p)t1GdkNG4U6y[f<lYug^M7aVHM"vbY&mDUY>?HbaEirT6`w|XqgE(7W&1T*XwzsbJeZy-TZ=__bfvoZ1esVrn*`8y5F~-|C_V[d!"b');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('%s_VkbOV?&!t"do^rQ`p`ZU/yp&Ye3upHt|/H&y4sA3gG1#^TM/psE"F.!L"b-TOg,=_&!?SQvUpK1NG?UVTm6[[a>X*ZfBqY!LKF
fO{QWHay6P%Mxk-@i/qV|wo57>CjpjQuWGGgYH{O@sDx@a=t3J8^4xXkLUkz!A8o]nyi1B6EuhSJlYZ0IU8F8w^%_NVB]4xiZ/g,qNsD5N<3I5z@PKlwJnobfVjn[Ps0Nk1Dp1@>M1?3j7#!a_W13^gLnlX:$#{3
8i+/0=Y3h6/1,{i{28SyT]@Eu=r"Cz(I8et3V~F)%#.@C6^yX&2~ff.YQQ(5WnhDY<DSV20%H2f?Um0n)kC6+)X&<0DhT=GdXfG>W}N
_itFLhYXgQ-?9$q+dW7/s)Vvp*s9<u=9Wu"5]B@h-)l%Z0$vcYCQ:>M#CF$ONU$8f3.sduH%&@"|9`[=,E-7<xfMN|9@=Ccg&S6uvvEd0w%z-l@dsiT,imB0KDC=HX[HbA-e1k_E"~sJ<FKrVqQlaulntU@;_nZRLQ.qyk*ch&y@KSbULF^1JuDW`W+bWA."U,D&Z89.[5Y.EDYJ$A]=t5LNi>n}`Oc
*0;=MJ_8W,XQa$w^=ohbWF!H30]5ctm(Bky7@-Lh7OogMB"*');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$ah=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$ah=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($ah["bytes_processed"])?array($ah["bytes_processed"],$ah["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$Zc);$_POST=remove_slashes($_POST,$Zc);$_COOKIE=remove_slashes($_COOKIE,$Zc);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($r,$z=null){$ua=func_get_args();$ua[0]=Lang::$translations[$r]?:$r;return
call_user_func_array('Adminer\lang_format',$ua);}function
lang_format($Yi,$z=null){if(is_array($Yi)){$C=($z==1?0:(LANG=='cs'||LANG=='sk'?($z&&$z<5?1:2):(LANG=='fr'?(!$z?0:1):(LANG=='pl'?($z%10>1&&$z%10<5&&$z/10%10!=1?1:2):(LANG=='sl'?($z%100==1?0:($z%100==2?1:($z%100==3||$z%100==4?2:3))):(LANG=='lt'?($z%10==1&&$z%100!=11?0:($z%10>1&&$z/10%10!=1?1:2)):(LANG=='lv'?($z%10==1&&$z%100!=11?0:($z?1:2)):(LANG=='ro'?(!$z||($z%100>0&&$z%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($z%10==1&&$z%100!=11?0:($z%10>1&&$z%10<5&&$z/10%10!=1?1:2)):1)))))))));$Yi=$Yi[$C];}$Yi=str_replace("'",'’',$Yi);$ua=func_get_args();array_shift($ua);$od=str_replace("%d","%s",$Yi);if($od!=$Yi)$ua[0]=format_number($z);return
vsprintf($od,$ua);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(24).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(25)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$aa="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$aa=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$aa=$_SESSION["lang"];else{$da=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$jf,PREG_SET_ORDER);foreach($jf
as$x)$da[$x[1]]=(isset($x[3])?$x[3]:1);arsort($da);foreach($da
as$t=>$dh){if(idx(langs(),$t)){$aa=$t;break;}$t=preg_replace('~-.*~','',$t);if(!isset($da[$t])&&idx(langs(),$t)){$aa=$t;break;}}}define('Adminer\LANG',$aa);class
Lang{static$translations;}function
get_compressed($Oe){switch($Oe){case"en":return'&X.mXaM+.<-B)tk?T(E&A;6#g>JQiJpNnF94b.N-Hf_S.n8CavxX]TQ^%(
KC(r(k6ymISwiy1Dn9`b7hw?H35mj<&U<YgWvH+WC[JT-^Li9br7Uq^?5j+%Fc,$W4F0;dIDg6)`D5>8Mv*K0rkOF!*rv;<U.Y**JWvG;;*+g{Z]x7Q].hwaGYPvBLh<(_m%L161s84z0TScvaf_o;S8iVqa?SJXz&xFn7lXQ"J09q3uq!d`h;8?u*=@nl9b:i;M^:`-73A68dvjgx1F)4V/6:OH1X^^%](#)|oCrutZkBa)W=(+`]AgLt0cIS%sTz1;25@dZ^@DE0vBIBA>LrHtF4%<I,G!6Bp68k5,.$fix]w@(Ila<A5Yn{++)2I,
2^y1sfY[SyB;1239!Z(cO+"`#S"KDt16Fq@GaAQIDG,,j<@ut=w_}@8<$au%Uy7w>QCf|BVo))?nJ9tOZ^;,4vz07txBl5D%ght.:U-8coe.>g.[qV1Sn!rl%Z(:"LWwzd<a+x<%f?4!mN,o-Lh/"wtH;A@$xL/d
V$/q/_POi+;fOpi|%!c)hqr0r+PLQJ8(Yct{Up?Q[F0nOX(i.WrpW[Xl$5lbvr?uJ(TFo-E@H0Wa
-_l_LmG?P5oTPTr4gc`D9;~@KcE.`p][#@DxQ[3,N]=WSah7QcCD3(wSSLkP]y_J9s<iG[x>J1g)QcbY8^lGiYw-B#X5a#=de9=5-&|dhT2bDhgKKn}f_Hjp&o`x#GoxXxOD""0>/U&_<<>:PFph,)<-G,_o0Y=l|7k$$]{6V%TR]Ac],&6WrD$gktxIwse*mMlA%:rTGnjtN:/]Pjy@?PAJi3Mo^C&j3Aj!yX3fAK]@z:o<a9~<&EM&wN|Po8K"m/D7h"r5NEux07KK8[56
CQpLij2|_%;N_1^in_I+Y?BEf6[:C-nm)5#ZU0F"Nr9Ij|Is39kTd%h9)!/FGuKcpXgJ=7j`WH"U89%{<HC.dX(+aOqPIm:j/8"`]$J"<mClIS,Cq"E^2+%/VGbfgQ&UZ^rjFJpfFd>",QjN!]>X-PbPyKv%7_6bJKSK0R?>D]m(uhQZaT>3C!LPUP@$]wb&CkeD<|V+Y3l/tNpsjrKK)XL7UPAgwW$TNairer6PCDj)fVkyE{B($Up4J.iuncXlC:i(VQ*,Z2=_/FN0E=Xp=?&%o@j;e867G&fz-<!1OOQzrxc#]
0xAJZ%NqK#p>j)hz1d0~=t2$]Y"H3_k{e94*^wgf<76}oor^Na2:(QZy9Y+3j,gq*7]lfzV/6DnMC!V$qRaF29_h,fx#vC
BJgSTH76"Mm%wf!a`2a`Y[|&-oSZhmysOb:^KFhWvCR>{=)F
u96itZ&I_=^HI$N**&eo5x]nGW<Cd@koVwVd>{oCNng=8qvy-p;LAJDE15w.4qRcu^p(BNW>X^$u]4>V=FjFLUF)+U)w(iW0
=<!oBT(vZ%&__ashv/?ob&VLDX2MJ;ysHb?+AgIcwH[X)i?[4(bWX8c&NNr.?ON!oX".fd#OAWjVfg_<SLoIWX6"~qhp/=maP?NsgUSq6X{IdRWHN2s0;2;i/MSg|B1IiwNPR/DIOs=nkTRXY&g[;xJpg)sAAg-IJ4>5{K$hUJqEW.l@PJju%i04B:BxvdYi}*6d-^D>L&kqEG),J#/-gZHT
u7;!/+@FlpJ22Y,{R%r~69Nb<1<4`maMHg^auLAIG/e~`A:aV#TFERxo,hd
aUfQG"66mqARW|dMmUv~qH:*
!xhKvP`!A';case"id":return'*Z}%@cw-d$uo(Orw-,C-I5i$h)j:;--:C)SpGE1,>l"@rr{:fL3k{])Wk5<#S+>a=r!n1rNJQ)t`,C`I~j_-aP)DLnWkLS?4npZK|)a9ZCiDNkbwAFPR-u@j*Mxf|T[&ZPN"pB?Yvu04|10lFv.c9h?G1jfV^GnJt5dWx
AFAZ2O*O7c4xuaTmTN@fkHm7.e}x>"5le5ZQbv9K{SRE>e:)I%Gx,
arX)sfj:"*D=|D+p0"_7k5SuBnA5,h:0BJPE$DDy_Ith61z2L+.!@Ic*:cqiA*u@nJ)ae!C7wZqbQC$((i]@JSP2]kvGNofX596g0>aiqAo(;WCH)*hbCh:H"<}?cp=jA0]LD?,m_<%t8t7boOU:u62EyXsW1jr
=v>P%]v..aHvTX1Fhx5iV0,
9/1vzmvi;.xp$np/f@WY<K-Y}!OTIeC?$01uy9d]lP}x{AkL
*zTRu$NA-N:o=],}x)gI?i.)MB!=AQOyh^Uj`Y`~*8[Mhm2OaPaW7{FtisW4)s44D~#C!#-oo8*]T`f]PYK,s@P(T(;=w:6Hpb?gVPvQ"<oX=WH?@L(=QWmqpZ6dXAX;d$DCSd9pwW@Bwc.|p~M8u$pI&OwRBCRvp%
WlwptTMf0@2Zd?nq&aTGof,)NqRLan2*)"ty}y#R<;JrUxZX.?eL7U(yxLgkn3l5QB}bI
1C8*T2Mgww#R)S37(>Fo:;EpDgNvN:R5%58SUY;DGx/rpooo[xSsF@fMg<Eva3A0vkBaqUQjtN|!+LMp7EaKsBhiVOTPGw,1^H(ZCbt*0(LE18F@u@kIuo9qi:v&TYwq:pFU<B89v0WfW>Vbh,!5&Y&ql[d7|Se<ooN:;q"BvIvJWkp[)[lx]#Jsi-t24Fr0:Ik9;9^Zr_k^b=P7;Ffhq1AH+a^yif4k`_Xl"WHg_-[7H,,SP@3Z9G#DGI+mf2pPp1`fZsL^&6(Sw
_3x=~h[.$$P@dn?-$X(M!XRWO@:,woV-.*(G"xIm$tt.1t=o[3XhQ[rN^afb7qtagxKl<+qfUuRV6/#!9^@MPG-;h[>O=gs/uX>N3F9^%Cg=srN7(3e`xkaH1IHv
K>UH>oT6Y#1~6vGJ50A^GmHFl+H_y0$,>u+
n@-"EMG;x|eCo`*z=OA&RnX`27@(y`2O*v1%KMVe!n*8j
5U9AH|m(xfy=^Cv+)@3jsKR1i#Lti(c
QNIy6|vR<8DLbCQgL&PC1dSgl3T@lM2;yF5>y+_NtYU%Pi!qsiI]%/NhrDSw$LD
ej;:0Z^R<X;@$Wi]h:N%m]$)=mQMNlOr57ii/6+{H7u~(z4dW<9`mterM`BW)/uxY%obB1*PJxm-C]Z0_mWL,]h-Z_9NU8)8C
kRl(sSfDLh[GUpO4WS*ppcL"Dr3&Mj)Owu;N/|>sRLR.#zFkg8JVB;p98zJ^DQZ9n[$^Z6yD5f%m,.6]f(_!r_v-g~JoFFH=%}HVJ+S3kk^iai:IVR.@%BHznA?mG0p.n)B6FMZ`>f[LHt)Pnfo%LA)a-!K|V0;gn~*:';case"ms":return'&Zu%@crWb&)qDx*PJ@|K,,^Q]kbYoT,A?Td@zRV@RO^D`.N:FB}ydVb1NFZ#;P_!xxgVy32wsD7mU(1chH&r,#dJT+f-5"#j8.)dg3e8Q[[74WN=fqA26kyHuPtT(D|@0){R;N<rEuJ"e#my1:^)zEkYjkh@{lp+}[86ur|ip>_OkjgO<E)T.(i)5kDqWUkw
E*y*M_y;afAz+(oPL}@W/W73hA1$@0j>&%x?;Uil6_mr)UBSp/*1$>&@%NZh#Wxi)Y8,h/ZZR4CnTj)g&t8@z!7CtQo%BY-H!!`Z03#*y3(ojwpA4?hD&vA=%oFFgf?)Tmh5Q}oSo,mt$=if"l!tt<!`r)2CAB/L9*J/2.LqG3KIFOj+<(pPILAU3-gaYq&1=vkmIPNgd0M8.Ll7H],"0^g3A[>U1+(2Q!r@i:L^ovNq0K<rAoC;3mU^!{2K(w<tIlUib5ioY"Wt[UVgc4D-_TO*8/Q.DR&|avWwf}Ag!tY!KsMvHC&k"emEQ%5oxoY/$YBne&_93r:5q)2k/^5Fp|An$=R!<<bMIWP,5#OncS1I;9cPnL&$tk:rIsI^Iq.b+L*_AxD]c}g)_)S>*u/&6Yi/C$ch(Mf:2kMG$tuMjyaVEAXl7J6mK>flsbtGWzX@9AHLOH6;)Jw)y*i*Z|nfkf*]hZ"=(L&I5fa!"gTL5:^>]%Lho]$tBuSB3=9DdF&|xmyaxWG=[l%^.M$"b/w/U2xk-!VNc)MI%M^/$[<(*$1eZnh8OKiVJ*(u+ugCK!]FSiJLDS1XN/@i(,k$=W3o#rJ:)hS&MoU(Fd[5Tu,V/q@8%//DmkI:Ub829f;Am$fUiAsU,f33GSMZ_g
.&yhDhQKhnQ>+qQ+MlBOLF^Lj7~+AT$TW<<SF/(KEV!RQQ0NF#"tqxiE>m]-C^i#DyC:1F/&0+Yhe>/L3_]Afc~@eubfKe@*kiSbOVH`#7i(f23bb$LS?,$SYS~ZxfS_>-g9&"|A:.V>=SSh3oDSpQNJ-w9B9,ljLhQ60cA/g+O_=ld
2amJq`GX-*s/OYGK=h[@=N(qq:6SCayJs>#D6_;Pc
%k=^vG<K1HO;TO)tiEQ5x:K/+^Y](Fj6~0b$Q,~8u/f9-bhNHcI.;>O^oly&9`Ht[bn5PuK+zvrC{#gLe`|-,*TH-[2*.$"XsR3NXoBbdd(UWv&HbZ+w&Xqfqa`gW`.`dip+-si>H(Qt/ot$JGr1|CJgH*i2>w(X|<XwVEluMfaBD9IW.>^Nvjfhlo"t+JnTQ%@Ik)eKXEfoH;N
_
>)*o:%WKgkJ`7)DZQ;}
!LB2(%ZD_/u*),12&!h.r[NgJ)gA{D|bo/VCXP+oc(EAH;F;@f-y%AA3d&*xRq((*]Lgq=f[J=YmWc7,7++3J=9
}H;O#1@8`R*:r%FUrg2t%X{FyPU)+S*a+Y:>pZB"$48>(S:C5>p$d>zG9WWrSK8R,]TA&Y$TcWqVvMqmC@m[X1o#9HY1Uv<45kk=
Jr
!l<>mu,EQ`Fo?y!iEXj)Vs;9obLC1ofdCxc@>t7"R';case"bs":return'*Zu*!h".!%$%viY#,4-<BAhO@F@hY;sX6@Plc#1tEj&IU%s?Y-{"^YXHXyH,L!(v5bNt4$6!}`4tukdD3b.v8G
fxZP@-u5?9k$:G/$H1boKL$t.kE)Z,Smb4cxA|g<[96u5%jPXH"#]
;
Sd^(efnOX[p9go0"WPFhIPrIu|@e8yTM?Ti1da?ygT[;b&v[Y
ht><p3)-ixn+wM%L=K*u!E.n)wBqOpI9oPgdlMZo"c0Dhn-CTLjPM
`SK"Var}H0c,tRmKHQP<HbG/fvwbEWhp!Q5+MPth9+a4)bp<&4
+4[,*dko
GAh+X=c>KL7[uuB%Oe-8k7.DMA_E[8V)XLAI8*RQ9+ZAx<hO"[s,:-l"Zi&}"P*WiWAT396CLGnCsq6|1a`Yb{H,t&jV
9hHLaowhiade:]lL/UNi:&&lI-{gq)#j_jVC/0~ViPl])IplhU
H!#>NUdhUG*6TG71k#g0sEZb^L9?SO"UEeGCM=>^-bSt5Ke3(|[{5m=!!#^P*FPL79(*!a)YaYAeA7oa*Y,rUWyCIsP_L[^0tbww*j,;L9%jb?=%MM,Mg.&>YNWBUt[hjMPiM&4j-FHa&u9ke?HwHR>lE]t.wORG!%k]7G+^";L#.<KUp)9s5f@K(n6C.<Ptn-<EumOaumiY*q6*eieKr<3R(GnFuqA4Vh#5RY8$df[Le4j<w3:x[5DlpISpSKM17[lpESuuv54O
v$2*%z(ND<&s8%L:d!.*fKmk,C.nE9!K4T9b|eH8`5}i}Q$eO-zYran1B=fAlidx2G=9VY>KyCeap8T7+-KuH2J9viq^^*>o/IML*6Cqq9o<^YLyTe,,j_yPV*B*aTNlstq.f^XY28ig1Y>!!/MUX`OU4Iy7"-Y%qN$9m@r$E7)&$mc7S4
V#V,
-I8TEBcoQRT,(/E"5lVU1$xB7U[jB#x1&W)E5ekU^<anZR>N;x
unB)n@r7"{B!dJ2;cl90x)5;;Vclic62
{MB9HaxW!-c1.Lc)?+8.=K~A;[9V)!ft50oU/GYO;/.,D#hOO:~u0>M4l&KY{&[x"&YXgWn%@%3PGQV=k@mq&wrq
z(Q<^
@E%yaqHeL8-E=blh?jM7m195l-4N^$8npy9RY)neo!C0CjpQRi1;22_yK~N7TD6549(w3ePP0RQsHA?e_ys@iV?WhJ=Lu{t5C]Ru8gfu_M$1aE:,sX=%jpmG]?V(FpJcwH1+*^x}D9F<$#j_^,P{XX_i?"gR?ol|.vI-nh^-NiqwUXI4puu{twalfAkNc=
{5MF3r7UJ/=e#Z%.I2o`7;`n~WFaPkrf7N}U-b5J#^*LH#>JzTpthHPmzM-T2kTL25v@sLb5M$>.z^j%ici/FFT(cL2+7f]8jP<=y.+YdqSL!%<Q^(m?!D|x3M7=]42R;k5lRy*%UOk#0N/`O&a=p;3DcZtu%&X"XPe?W="sE`V8g>`0OYsXb6Kbq>VZ.,F/jw*bzWO=FC*X}kw(i402
.KWr17+EH*O?
5U+o6[LblPA+y]Rg]Ck6#60aD*xpG#]_*/KS0YGJlh)d-ifg*-Ylh*G]3$Nv%2S7[Suxp!&8I.elYbKOA?]4o[YpdvPFCv];z1b.Q/L?"4$-81ITb8/GZI
$<#
<1M&1",(4]U*9*I|#ITDTru1=boO]`gsN@w5sVDk!]F59_v{MOO`=xS}5,,x8EO4-}omMol9HSaeKI3AVNCeb@^A5k:,_
$Qa9QwI[QbZF_.:jDCJ[1<328NKc5!v}taV~ucpPQO"D;,VY84thc=7Nyoa?';case"ca":return',]^*gbT-d:%,otlA!c82"ig+?eL=UG5:.:lDXTZI#EWI|H#GWgk]^ovJfN~9P1?d1_1:i*Vx]Feb+(5oOQx5&]miUKyEPmSlC3@`v1;=zOxKSJ|9P^30yp8A)S<W&AExpK1+8I#pbC
1XAr&pS.T
q>O$O*bva94DL+i"`_,Pc]O}-0]WmKPrZNoN.
w3boH.Z6Xxu:st?&rX]k6;C$jPax_Q6@^}-^&j%t$:s!FL0sH}YQFb(r7amR.rnUZy:j(0N+o4f%9C?`sTaYNxHc3$9)
xf?)SBHe=bz[J]?Hu`&KU/5Ax2HJd>eP=)GEj-HOQ-sgABAPM7j7u
_hlB7F@rBy4
Z]y?$C>Msst.1oA?udXH@<(JBM0APl5^dkwvYM!wMhwrL5Or_XDtXX@ZLKDc3H*awtPf
P<=ZFp*=!qvI"`45"5?p2Olg#)@.1AR2J(V
R,0VN0
GQMl/R_wX
B_He,+!Z@N.hx3ud6>r@dhFWm%-F#0ZffG{*HG6Hy?d%/)G&,?&SLWN&.<pH(e",/hHf/c9a4U9
sv_pE.3y[QeoP#I=sZ<l]+jh?O*Xx%kM+mHPvTo-1<UVK`-yDSDMIehBL+gu*"[bCh?J==.D+dB[ITXrZwaGYYA,;O0K9Vg;{7hh4:^-g[n!xth63Um"O]>:PucYk9t/&xNP&(lYEAcS.gIy%X;OrA%l:;~)G2IhwSdFefHZAFYHZ$bC
>xez"Jd|e]91-(CBrq<bhPy;t9qnACWZC"#6Oa(TB$PUBJH9:4#tegQ`(B3M9LZ,G%qx)q3dMfyNwV-GW1IId_Hv6DN/T"SF4vhd@FO[2zWS^Jog6c5yg,miUN1D#x0,[.l:yPCC8.d*QM%7]K*X,g9]
)p9w%A[Ha:Yg3YG!F;]w_[xfQAvDr>$>`0:$E8D!h.`LOL&erd#eQ1)"WPqZn0m<F2MyQ!{)$LN+?p!$fo-SK=,^_V2r2V9RKhdr(gH?<Qo(c;IQQ7Ope@lRn89$XY5Q~0vD{B"<iW"[-rQ:A?>-NgECTIh:FJ9_n,/vEV/Mz^b:kRM,X?g[nNw7QSOy=v*:.q*$fnx7Fjljal|AXVSGT60].jFiHJLz$tmh|,xRI!J*hgBPpuzk-IT55k|u@9M!+sYJ&ZW
>;)*OU{E*U^o)0yN<K&w8((N+y4RzAuR8nny()!!`U8&Ltgc;8[*:W8l_v5(-b9;rGU4BC^%:RpqsCN4yU#)hR,-TT&?c5?*Kd^H[<{?PBu*nQKX5_WH^sZGFhFPz19"Qu^L4[;x<N2p^cEm!M^f(01.nN~VHa*OS0Q?vlNjq=L,;]kV{`5i&Nd-zL$qW9rev1fwgO^`eYSnrG2h~4.GAkf&6UuQ[W6jxang*3Pi)s@ZT*B19#HcP>6*j^#txNT!H08fKy~N4JU-Ud[W1x]"(ZbJU!4a3InpYwa,{k;b7IgcKdI]KV4k(T<dr28rHF)LZG$*ottW*Cj0IY#j>NuRaj1QTDZPUORB,!,eMvpmwB!v>8KIDx|nIm~p5WhDm_J?^W*i~#uM(tu3^[DLp1J[6)7@UD(:ksRL84/bNm54MR*>A!Zcz@.n*eqfc`{4)lrvGYcuVe+JgpT*Z?9.xFqdZfdX-FFAe9Mh[Ua<N(+x&KgsFodH_VXRg]dV=<7oqKo,2L|=Rje0p/o.3I/QIi|/@v^4Yef;aPNJ?Jk#8gmhO;oN>KkE,l>CB^Md*cY%%i006JmXKUo5U;)C_Kp
`Vj[eT<TpT>TtDk,5$"^"tm;P!;0aC3Mc8$';case"cs":return'%]]qdcvZ+%fW8i`$&hx&o$0!ulAg3aCEC$g%E)XQQ`~"v>Q=
MC1A?(Pn4r*OgsUC![R"Bb4DSO:h;8gDbaT;7$$TR$D9@*Jhi.T]qfd[U!1#P;7+mE0*ccSS]cXwH>Zct5ol-e+dB1RhmSMQ@46{a:eMIP9qx)fI$ghi?fmQ#X/Fyb/7kRsG
1^trY3-b]MH6;Fx>=*#J"H3n|3oR&.?,%Is6K[$j<BaHS$GV?%Fw&$<-"^%HDsTYdmWa(].H[&]jcQ`PhnYK-ToV+Q.?Sx/$c%R5<bH:cl0>DJ;j>6WqblVS<CMOU`LdO,y#wN>OT@@_,!+CbXQ%!?{EC
DIt49a>Y3D_v1%*3s=umMD]^rrDpKm/D1UL7{6Y
wtFH3J_^6H.IR0*dz.2AN7f#!WrY*W*=ZKd!bOovKjG4w7e:yJFF;Lpa4(=Y75V9JFGr!x?j#g-Cvd{yGp!do<DcoR`j?;^NN#0lRk]409E;4ZGHOa96`r)-Gg0X}6P=BUA1n=pWsZJV&m4pYqWw=vW"!-s<F<ub@m^[6LU`5YT9V&dv1IsVi4I-i!n2H
k
pukij2UOcNEm(SY+uf6$?DG4%e=fM@|LY6vfa?St{S$/yK,7:x!t|4N[*4]bWia#JbfH4Y(,-9.$])e<?[[m!qwrG7MQA3qBi(Sh&@yZr#=q*q=$Br)@W"G>Hr9)B^eKA5~VRKM_ds_t,F+[sm)0W0|:<wJ-oB~j(-+Kb*<Q/4p_`sdP
yS"#Els)RpGo!Ja729^&3%ik"=#H2m8jJ9%oXp#Hq:gQB<-G(7,bi-GbbT,tML[MPr;K%9,_^CiMaHHVp3^`So`TOB(Y(P%7YBI;
XxX8|Z0wKIXOnnLCS(TG,/]@R%T8J)9vnon5;mBB,y}!-3at3%I9,bV/khlBI4*mRR=ny1iYwEkCxKmgg*F4<Cp;e$wQse6.a(lWCq.-ev[%N(X4GH7g!SOg{`OZe0ZJnIu"wdVygYS!lMyL0?9*.D0Yhlrvc=RSh`l.D%oobL">:x53ui~8!B:1A-5qPN:42:El<phh!Flcl5EYHlYqBPQaWN8.#S)4)c%PZ!TH)Yo,Row^:(f(FM|&`[|F1m)*..bIglEo(rKR<.h92J[)]uO2jp9ncZicbSXY&xijf2yO{*eHf)a/9s?1{@7+OOi6M[.836-`F[BWANqs8VxrysL*O=G_2#Uqf^e901gf+B.J.nOR>%yU33zCS+l%,P(L}<+3c""@I*=<eD0o@mc,&w`.fKBXAL(-94FNS4#&Bhg<_2rOj"=T>%:J%kI+QM`%>Hx.!oOp/y#_^g+k>-_/|e.*D$@4eJlqeY1B%/HMk5CfzYM#{gl-D8hwGo{C9<^WgTb
%a@+140Z+3C2%G>H4trJwt_90O
`tZs[:2mcT!7r1<^dANWKpjZ&TQJ^?%fwy>&pN(adWC1M&g=OdJZ+.6s;}2JM(F7#_dOU=SpsMrodVGo?
FN/Y"T0c6molkyCIA,^nS7i1(vvo/sA0Y<G{wbO4,L!m0}`u9kd/u@rTBy&S(Zn5,X,NP.j!J*w!%p-u*FvRTzKrK+Y=E)I;)`v^.$Gb2|gcWn!}R!U-D&lS5XFO9E4Ka)x=D2Z1-?)w/eM(rb9x6Jn,?_FeiYt>Jf=-8Of&^,,}Zu0IH_LFS_%$kj<VZY?vAX=|u(rm3uoo__Y4w3?Wq3I3!3_Ww>WcVjLoBk_qn%4gUniBem%p@h<S,+eKJ/m;e@GD;u5WMSN@Y*pmHi;Lxd`i8EP3j@7N2FWATWaNswI#$%AFqW=d5Tmrfo^!ntXmP~EjEWMGmA>TGvZp!tqx>N)6pmQoNqr2b4(.GySG`cOrs2"h)
gZfuL@,TF=
d7mqqc/Hk2zAicp);31a_w.n<&ifL%-<0g^mglLEty
LPHHhnqrV/DbcGKLNyR#s.*2';case"da":return'.Z}*p6KWB:u^N?4:KsB8@[2>$Q[NJ>?3e@/Z
GB#vW%g7<CF^_y,>s*<mRhn5JVnz;<@tU=lS4+.3"f)0Y
0;]27mIvJnoF#FhW^@<$NYkScbT%FIHMH[?~*P.vsdU[a"]k5}kKsg#a3Z1)K=f":?W23#+J
k4M+rU-_XV-:]MQqpnA>-T_6FVY@o?^0u^]3tyFn%XxMCqg&;ay52Tg=He)1e<7>;`aB<u/>]aqe9EIO05KPIJSSK>%O~J[LZ_6AnX{MH0}UZBWQ993[1Sob
Rx`$o>KQk,j
b}={$S6"+F(Qbj35PKo>FM[B[yb1%z5ka;;*`8@W&t1*u)]pJsTh,j=HYf+hG(]3-^em*_nZKL?U!$i^tOhs5IP6CJ&2heNNX`bv;e*qh9lxWi;`&?$ksd#xj8t`K7^zJ[Ye93DB_@ChyKhDc5^R86j<>n9|qBW#;XBk6:9B6DdTUa6HY&QCyOGWLN/t;x92Sduk1T]8-eXjD;NnZnJy<jiI@+C,4(_rqNcX%=rk<XDD#$na#apk(JVJ,XU6^q2M0-GVB"->rf8Rg6D~AO22qAupFov|%Ud_+X_fi/"^K/<cF[=d6W.0(d$4M]Z9jr#C?~?Fn;K
lWQlu*)<L((<]rs?c_oE7l&mOzw5"@F#8ExkXpum8SZ,c3j.%g(lFfd#p"yK</7Qd^.vy?3jXj/ZWvw@Y8fcei4Sei-nOFdCZ0f-+AAh.4g4g$;<d]0YCkLRM{5:A<N_5PiLRPS@jH+-)SIuLkF*0`3+h*Mc[dwya!/&D|&Y*cOHN5A1hrL?$?FoS_Z]Ub45=@b(8U=pj;H+HeN3x%CV`vO}G3>XPukGw~x]j3t#?~8IL]bE(7LcV%d$plBKNC^b9_(O_{*+D2EE)C&X8~QE,zF%Cwi1etk4Jt^9sotD>%,MJN$tt^)_fjG5OLFoBQ8i^(KKSPvWT5Om]mEPq}b"q3DXGgLaxFxyv{h&8M/J,T_e-%GRQ/En#mV"B/1Q
E+rZ7ZgVs$K.IXV6QcPk.Y7M<59E=sDrMI-jA/NE%;jTz>.Zc40?clOJ^gNvUo:!!2Od.AFJDaFHwPcD`B|:]xFn:NX!3ZE,VIt%II{2-8yj
"K<r-Z/T[pDhZ_[}iM_Bod(kt=OGBY>.0&_qxgI|&Gx{uWg?qb2.RB8>-tX(,ymp,a?ld;ShDrcK6O^Q(<IDrkqigLtYG^wE%:.R#WWp+xlT$FdHxs*4jN9"79j$_%9qeN(+m1AP=.OJgvlf
W=7+6TWa3Yt@,_1U
/dZIQm#3p_$NA{REZq(Zq7I84N=W$?LS3,;I9GyoSP*3OZb*hX[`5k_sL2:%<>MExX=5DZ+!QxpOOY69Nx/N/pbe8dv^:omb,Tmx/X2o=I";He=iB-$J.!al3)`*Ge^g"<"0Jbop&(,k@R@{QXE(CilY4fgc<oU^jW@NF4/YX>(8jf^e#I7iuwZ$t=frFj+`2~P`ZO=<p=V3[d)9rt<tLD<=C0ttNJt+uwD=-KQZ2NCN]fSsLB7%&xd&d,:[!MX.Sl!8N_Wj(:w,.Kxe6}n|tDxtA#ey4F<Ma0Y;uZCyaWz&B1Zls81/
s9BT
xm&X9p<D9OlI]H$EjMci;tIfN&';case"de":return'&]^*g6P.!:%S,k)Rz&y>^Z/!y`%b&a_$l3~N@.Z#v`Ka(a-]{!C*-/zMv(+skG@?6ykR0;Fw+nk$,9[Rt"e;kqf?x_jt?*-&Z4K`Q;jV#h7r89@$R&rCwDX<hpbCUhGHg14]!BujL)$6~0{*IE?y.n87LK/
]#0mJf%q8TMnMy$os6e`;Jpt<E<cAi]mtCwec`U
=dGV$<
u=SN
:mY?By~,uWC="*,wWx.oLGSb[6qz&x"y6I6.s6@kU/nd-]:QRJ7Xz<su=((F5&m]C)J=u,8sJRcHV6EvrI8OSD#)eXx)G>gE5mKa])Dx[`USpwS<^t+oj^F*6h~u-q;F:c[yJJ)=}))stBO2U#@/9`l?*F-o`^>^%nE"F^g+5xL_aL@7tuib>yuu[N+&baA`tCP4#,W1E3
LWK)@m]=q6A0_N_xsHi&I~7JB2Vy-hFV7dgh_OkzwoWEKKhI:,(S7$]U=et:cQ>q#_ZJ(gqI9s,Om<!%Ql#a@]#FD26-HR#:Z-BI>,Uw
$/QXy@d-ZF&rG9S-jwf6BD}"%0xW;UAQXyP4j.^[)bYVX8u;1-FUq)R!kFik_%LKDbi+TFF`-mu88x)*
/|DH^TBkU99$o/9{&ZW`m`jj/|S/<cH[0-%&JpE0T`)QCsfUwOq^0wWo.@mIfd4R+[s8Uc&wJD%lf|cAdKvT(#T{s9>jZ2%n6$9(q{O7k9n"q%nZ:$b?fWQ:DvaZ1).3n>uA#?JX,3SEhpA`JTFa/_Z$9OrfJf3AQ0Pf>OH9IaO5,493`7SxcMgEki;<L&*#!"<e*>F0xS*16]XwPGp25>>xbj._!|%TyW",a;yuuDCW7c.TXC<4]-)tk-d0Z&O%5"SYYti.SYTd5ZdUyw.?FoUaaqGA.*PRiRV>sAElPBgQBvDL(!BNkW?GE4qu+p+j0YRc,%kBD3ZY_9^5wkX&Qul-"}Ayoq[16TVs>Ux3_=bG_=^8tL>V>YC:4!#;DlhE)|L@SD>
Ga$N=U*aYs?tjnI<n.!{OK2`G2mDJYwTofkv
1XFSGP?>[Cn5ZAbxCKj9JcdB^"$N|nLt`CEl-@B"inu:OkA+c<Gyq@)(+]wV9@j9J#I
#NO"d$^#Doz5?mH$]m[Hao:$cO7E?`TWOeciu_|`OtOBke86>Dwg1.9XqgIm&S<WNt/XJTct3T4"TnMqP%|?[[)h&NrPl]k2,FT/LlBwT1RDF"X
#m86i%aj;F^WnS.Gu=Ex_tbP@(=X<f~[44lg>FkC^4iVn#L,W`5fZ"aFjFx_-Le@J/764nbxT
Y4puT=tcgsjVP]5SJO/&<TSR}
,qdF!hcD(*`>|&hy?KpNSoy.`=YN>9sOI
>[-rF[-DMEH:lGE4y"&Z"%S#S:m.F@}rPqTW$wzD:,`L)bR@VJoM*22jbMJKzJ#

H$M>%P:]?^O)-EwO7WgbIObL5GDCAr)(/x[y9;Q[qo8Jv?*@8{9E4p9*V-kF[p0:/+HS<J?c-EH[m$NFs3ug"&K6j{@nli/:PzYqNn-CKd1)r$A7gjM`b"<Wk33UtO0-`Rc|-OprPnNIa:xF[wgUk-F*rwNq9i%aG`@{
4B5WZt
M"$A9,$e6cybw!=>Q|`IYRU:jo1@>k)6)7Db4DYO9s[5^yg)^IN[>D-ut_mTd5_}9)DaKgRt$AR>1AdEanx@rzqT_mc}/)k7D^o>P$1nfwp::!.wRb^uccNzVJW;4b>s".YFGu$v+0C%cd*}Z;`~(xp<I2bdV[dw!^0Ps|dsT.Ap&NaaK%W%Un;S.0Q?SS(En.:[_M.cLV:MWW:.0p$/.&>8SJO_K;Mqtq>>)@y>ass2+6%EW|.Q3A!]l>-~va!o$|v#Z"av@,^:[rKwBM(jMtLFgw.hDRi7>M=<J|9
[F=i/"081>u}uL:
,t=zv]N6';case"et":return'(sh*?bKY($uY")1em;*`":2$hM`gq-}#yv%m<b+pfq6V5c1h;gD
]b@xxl0/(P:mP(9*{:r;f"w;ibHt1V}8#q=<}YOjLG>yq^7?qd;HK.X044-IVPP
vZ"J52N)N=TR;Z..!eRP;07QkGgSQ4VVgrKRbO;-,8$]X$gCY@CJ`In4o3E,2?l8EJEaHd3SA[XiQq)M"HP&})e_0*jt{iG^K(?jK!Obp3:9>>Pt+!F8+?a%uCJ[$vmjh:|a{D#Q:EhHG!c/I21.Wn8Jjnm9M`TiUg#yv[Xr33gM4#Vc6u>uINAW8*PwCx!PU$^[2s_+`*]Sqg2X[tY[:v/1#*]g[JQt2J
I
jz[$J~odnnf@7q,SBO9~hUu6A`_Jx~2Up=7{:A["O@N@2GC.-)D.Np5M*-]]Au
@*vSAwBkk@="!G*BzHH[Hs/O>sP%
?AG=KW={nR,$TX"}>:rWXs^|7{Yae9Jgi|M_=6Tv3CcYyu=.dY6M&gK_gymkLx42Rte}Z];lbNtOh5`"3.s!@L)6AI`~%)irw7kfdCw
h&99!43F9,j0K;5sD?P;Z0L[]9tc#@v1y&c(wbKsrd1%m
cu3$#Fsptarx$9Wmn-UK:|]maOSc:6"kteks@{Ndx80O2E^WF38ngsTP%<kq,S&k7T5ZUaeSvb-JpjNU!.pNG]$8QQ7p5Rd*y2);OxW9nmCJ(:.!sM:1y30L#5e_u?x/?cpmC/d6VIoJO`u:pMLdKrKVX~:r#v-]3!Cu>C(
*?!
((%%c/Z/Y2?d51Yn]cHi)Zo`g45xT!YXg6nuB{RE#c00+i)yp~!YT9S,>-gwQ`>iPy`JCS[hX@d}3&b:p$D#W"cH)xmk6Pb2?k&+,zE
#@PTv1Lu=l4<ol=C%iQm:9QS0P$y(A_*BDt7OV5Zb2cS#(X"VoR(VoqN_e=#pB[2#m.MI/,
3Ixnt{lzfL718SM,72oi
lwSm0gCO1K)<JySjt.2(<-0%-=Oo,Ee%K';case"es":return'-]^*obQ-d:%^)xn;1bG%P#{G;#-+,e[Es8jp"1<c>-<DWEdpB9^Ah?r36)A
mvg$I5700p?tAWDMw"J[!g-u6a;n%M;qbxqi6P=%<N#H7LQ`HV;_Yf7-;Lx1GlfGb#dQDok<NEbib0Nxz!Yt7/_qphdNS"
hb.fo%UL4Jiht$S]aH,S4<$1%"BsTgk8"&W&Ry=/kyO_QJObJMB<]wX|hJ^]TX-Fr5y~qdh`DO6{S&[:s&xa6OW~tX#O/xR*J1bPIy.|_xM}k
>!2p.0fgknx/-z@GJDAHO[Vx6wgaJIP<o7`koI%g&6/UpPPi_6LZl*S)XR3F#iBC7rid;%Wr4kd&eH%,UmC?MYt4E+wDXD./:V=`l)><Agdj+PZg42KNU?vT!`ChRL"cpJcRiJZLhp7k=z8*Ln)?>(if?X6_R"BhyTJoNdQ?H{nvPj`~L9R~whVFu+VZR2<p+aH{I_L}9+UCc37SjkS2:/-](&*$CEN5#F&d:XEbZ)d)bl4Ym3S;,P0-Y~yW5}pNpPn/:k/B#dTIIHW,#dpb^Rg0L+twg"<Q-!epC*Wo#:!2wVp_,H_.rkm<[j)[?Bu
Mc2_m=^v^77W#L!o=1nMa9m0^k#)=vq6Su3v9h1O"}6km>5V%-I.maOZ""G1:Oj1s_e{Ro"Qu|Ayi;tq&l=,7wrKC&i+6-SCl:py7=YH:x4Erx,>(1:}tw^wkSo8hOh<JzC)M:#PN=/-_FI=-l4Q
WAmBC7|vHBa9)$OYbO--j8N]:i91f(t6<3=jE)y]NBNJOSy2>0?H410/D%svkg*ow:vf1%c*{g92$`=<Vh@A#q~onrF$I_Yj$rWl]XuXCOBMOF?(9P#aj0"iddy6GewAw>b>0q3yQH~`9d
?gT&F
(r!=j!@niN(!089*OyRP+oXv8[=yk.HZ67hyn6PzY&
L<B<P3qS3!Oyb2w7n6Y9VQVgO$SqK4.He:
6I]z
vkc"l_~i~5@tHRh`,ker4)0!V,Ti5tx:63ht{=-]9$wm8QiZoW>&>h{y8vkOkl-
h=EKE!,wm,|jn[jdkJ`i@bjk"?+?|l-S~YG>`v=hSy}%z"ZvOY+u"l:Oc;u>*lg^27wa,Ao=8^g&{>:Bqj/_|(G%oVAB<_I"?Df##8X;F*v[@9*$sKsI(t](,j,/aVIo@
zPdd8":;Z^X,/ALlCNh-^m~a@#GCa!H2L9S7nFKP8>R/s5,UuX.G):},7-R9TF]izKdmQSId6=-nl-0/!C/TlA[(TNsK$4*C>.B[EXoL;Z2
8v^X9;$g&4)YeGyM@@$>]r}IF8vB.>Z5H5}8=o,T6>Md-"dGbpa;p+qsQ>N87A@K+qxBP!uxfG-vq-v0y%WT[+=uIH#`"q+S?o/XHtt-{p^_Fk7R{X$9lL-PDx}lken(ujxWd@T<eUv",1og,kmvueZm$I7Y*kIW
F<$9#2TI2?I|Pe/Qfx!iYmgDlg>+w"Nr!NZ#hg>Zi91Cr&Ga(t?H+bTR0Fp>*b;pEVH10S_LFsbD?3Z%[L:1?+BmNe]FV?:H5|18nH3Nqq7cr$L5Y=CZ,*NkR$A~R!8U^fZ$vR?5k0g4r3RYb30UMegI:)&
7RA-/Z!sX-qS%c6|>lOTA#UUpaQY,j
2vSdKWpZ;:+A/B%r]6YvA?rME;JVRSbib,vVBRXZg@x_+S(F5#Py
JG7Hq?SC"oCpbJab>6`_*Fr],0)GVD"%j+Eqmf!2HzdRb47cl9M}!Z<hM
F1T{O-kX"FcolbXGk.XPeR>A.b(ywCd(';case"fr":return'(Zu/_6l-t%&LOlPO0]XZ?m|9S86J<9e;NN/gI8DV{6U^?yQfcGFtHGyoSu<KY8$:sI6ovwEw6BmrO]%KEG:IPm6m^`s?0^AklcYZtcfFy>!3KfGPH/ql.Lh5$Lhr2jogDn&g]<z`:18<R`62Gf.k_!?]%FfK7GsU?3UBB2{tJeqFm4BUhkKu4e(VNP|=M)C4W9MRVvL_nANc~,oUsvIEi(BQ~,!Mby~*C"EpHs:OZw@QO7lC
#cNedlXkq1cZV7G_>P!=/f(
@l`VyH`Xud$cIYp]H)xn`A&}=e<yN)Y~rJKL_<l%^y-wm7G2^cYro7QLV_5_Czp,ttU6RJ-rkxeVffma
Lc_nlGxC#x^aO
gO)Lwk@`771)VQ1E{a46Ck}>xe.x9hraO$!5-24vK873oK}5S_P"2OzP
)to--l$jd(NqLr6|$t0vZZ&(+b(hbc`5"pYLex,;eAc+&}/dV~(;abDf/W#qV^Y7t[
J*Xmv7dtO
bWQA~pE,,%<GpQ)L!!U.&78KJGY@UMg!fJt.gEs8.o]<D`a.j^W"ZR08=!vpI;7VZa6$isIfZ9e
*13mYv(0>b,S3woj:";=Md|+USVs%D80Qs|PN4Hhrk%xj-7Ag.KPmv,TAPV,xU%
2Z!I$F`"pZ-)~t;c0UF]Apb@CwsV?`ow/j[?g^tB0G).oY=Ti(Z8U-a^2air3.]YSFI10F
/pNk*_&b#^`uy#6O2rNC,hV:s2KL7`*,s7c!l#"pMBQRYiisCh+fMH]exU:uG?hKo)9sMWx!_94y<edVbTXst=l+c~OA3qLk
r_%Xf*xC.Z5hW:%s?x%`r.*N>jjU83Z7xD##H/yAG9
GLQHWd+[f4d_
>g(G!yA`COxha89)gc}YNN$o4sOwIai^&R&]r^jm.Nx"+rT&i.Km..JI9"amQPEsIxCAB6`Aqw-CASHDMuwefDgFe%wWX&u983vdH)%r]1o<Y+}[|qS;KPYEsMPvjr.y&+`3tW8EX"8B0o9Yg[}5=l/B1W4_>w3)NYh@("WSf<JDVPoQ<lkgrAXCNVz<;]pD_hi?!%Qp:,O]:s}nQ]t9QAT_.I0y>":)cIfv)xmo-I69o6,i`0p!}!7H|)dxe?(Rge?(YTQnSd)&DuJw)VHk.mRf$DeH]aF-?clu=r8a81"0<h-(:KZc!j`7]:x`f;2JosjU?ob^`>>e$ab-n$k7_4sjDZA>>L*Z/X.6X1]!e#VBDW$.gE|=Y2BKYF5s&
9W(]Rm}*g-E]]V5xnp"300Qx,2//>0<W/af/E>;At[=IgT!.|K&f__gNa?Qk<<ExW%3y]i@$;/}%bAqPCg%C`($rB<CN)9Tqq5DIfP}U6sOq#Jeh^uS@?`,TqnGYd.bCMVI1e"-#a"vFO=L.{gaA1>04v9.Td"Ri<)m`Xa?>{h1`KWWJMWPf"1b.w`LsA$x
e"dW5(*?;`A7.$/ES0p0|Imh^aY7SCNyyHR@b?@&4NAeg!$UvXpP1^q)|5?B%Q%KpN2SMR{:Uv1hhLV%V@.4@k}"T_QL)s8
Sx^@{^jsa(S<UJTTULc^UsLrkahSCk;x|@MDXbE+Wt!(?%,XExo:=<"PZ>i@rP&#a4}3`.ncjE&Q`qwuEKe-9T|!M1T1&?B,(kX13GC"b4RPWVIuGG32o]RNU[WdU4B)uL*h(cD;x6(P0Q("Hqi]lMYFa?<SyyAfZ#QBBjn.wwB:BFeiP4ySf]gQ_0;M{M&-vp]YL-e970A+D#WL5^&@s7t(p
Om+XEZcwh-8oned%#hBX@T5,x$(';case"gl":return'+Zu%@]A.7#?v}_#"6:r%+o9h)ed@>5wNn7F"b-BFZr{;.`k<ULHp|s!l>S&$*$yb?qbyiBtB
^33k<X&u)_l.FUw/
d^Aw8WvnvSf0;yDs$u(NnsiEN9ZrR>G
IE,9nA]Lkl..<p<IHl`&j#|4Spbc%59)aY.<3lmK<#y&:`0xM:fL?QLJc(oUsM#OA"*4noc,xF3kaOVI[M>AIZ8iBGwK(<J&-5vyA?T*Vpo"MSTtMvr4?XR+5Jfr9st!Sig%g0ckFu(C;`y2Cp3n{gHIiRl9bHSW<F6P?xK+8qt3pmX/0W69MX&(=c4t,L
MQLeksjjY|5<=j`C^%3Fos)i,8%"n*$%P4cAZ8-9<+8@%vs<kE2z
J,uEA%z6;EoK3#vsIcu&D7ar2:~@p"EpFw~GQ$*52up!OKUei2yl/%$/CdZvAqMRkuoc]TqIN8v3?#$J
i+!aVTP.<:O%*H;XBux*g#[T>=?]mc&n,sa|8ch5I135q7+V"@2K^xQ#qr;{Dz<hYPFI-{O5D`:llBbcS*(e^V9DN;$an%<K+l(U(Xe;-SUt:3%zL97M&r-)e0uwFJV2-,>`QX$p[^T:1Zx3y94@ipFinIdYj2"/J^Kjw!Jin;;:]QNhG3Y!/U2`i]H7a[;x<,]A50b%0k;Grs:>2oGEfymtGw(94r"bw|PHU"s2vLP{[Ux^LoBLk[,jHce"j`0M@01m4v=H*`-C/:wZ,cOwK}teT+>6WX.^n
LN8e`Yn+rQxnMFhkLyda6mr%[NMUa%`yOzkVZF"dMFVP8&dzRJV/;f."/>!=YRr=32W5SBe[Fx;tVx7bTU)H>{V[=F,cqu9BRBJ3%QKt%l+:S@rWrAORZ-h19z*mk:V?ED`Sm?@$VY^8.+Fb_lMq`EF-iSp;Qq?.c/i0lGHr<:1zZOFUC)@8tEKnk
MR$|u"I*hW,.Vn%?A-yO%&&ea#77-EwE@{&_u[<@j/$j
]hsC>3H`9,swC#k>10NTWIoRc116h3u]SXWvZA}vE@`#j
&bR3Z#buL%IKyKzF>xFTt6f6X-N,i&{;bEjaor`u{e186SB/.Dh*SL4/7hka^sbIN>S+i8N8Yh{PFe4Yx=e+mUb32jeXaRBmw]5(RA"doV-+TwGni%zT<RzozWkNdxS8g?4";K?sR1PS^D]Cvfsok-k8fb[DLt"`(-}(pGJZ5kp/8,40`=_GPbCr,0vG72Y8&=n4,j$6@**^_sKL[O{rbA~)G16p=;EWc
aWZ;`_6;+F5bjCtnF]ZEIMrk81H&Wn+=&x3vtsi[zE3XJ1k2r
_.$v5uzCOv=&A"%(%GX%`][)O<BozM6(:F9BdU:pIJ(HH84;R:,TVQ+&Q^dRa*:B/PG@A8JG*>-GXI0Kcf;c#8M2?r3pH]K]/6zMhEVWKDsu%tL<@2wu
=0gtNDP9+6edAq&DAxC[8G.,52)wdA?[fu=1ItKAa6%1TY5,QPv!_FImoP;%Dk[vVxS7GX^=5nBXqI.,,k0MV],~p~
9a9>rdJ>Y&Wa$Fa:%c:i=-AQ|>7c;2LF!wi;?=jPN_JfvJ""QuO[BLy@gvAjT&;+C5j8x-Qyb70`u6j>oCl62trX}Yv>VK9[J6Mk*_ZcPYlXqG?_2JLV87XeJTs$N%kA6cMxP&}q$x_W!pJJv*,o+GdionM.X>](%k:5QgpYIrqu2V[by9{*?n?Ng$Y19bEEE10L~K9<&-SfcjBe]
A1?1fZ}qAnOMc=S';case"hr":return'.]^*!h".!%$(_i]#U:<c6h%C!sL?EVhiy;o8xYtX:g4$]l7$}o)p,[U(s#S0CMoc@EMu9<9]hG6fFG>X73
[<JUPF]gFlcgdRVY@B7NG|Fk1cee(L5Ij75pk.ZdtC<.#([G0j)Lx$!ZC/Q1A>wj<
dIY"]z:/7igdcrY{d%fOqpES#n]+N?iD9.U|QjUuP/bW):W*+H/A=jm"=~dH*;PNe@c_)Xn|E*CB$z?]]AOXgP=eRTwF!yWejw.}/`n@.~tb!9DI]rXudgYihgCCN
-f
?"$cm1pGspsCEb`UeE6s6H[q:0Y/R6uj6`!@g&WT0bOSo9C8Z:;x7<SIzW.8V3/M=I?EUpD)"<7-?`}"PHV`%_km1cor;3B]zZ
--AnW-giMRETpD0@2{alx[Y;m}X?8uC8C=g^ELvA2gH]5_^pom$4w>7thy$gvyS#HPCL/_O{T1+|"e"YF.7k&(FU$*Z]t&UK*"fmKB#tHrRPDtnwC*c<fYpo;EQOEIJsXe5p&*KN8OO~DmypWe0LB;Qkf_woz$N$$zw0!?O!#D&y37,Nue_eLZWkU2yy(5%o2[+p5,&:-CX]l3!REzKn>`3PsTAe9>3B2=KdEDN;F/[RwX-/%or?S2o
"2Td(
IW7^pN!+j+0$[/yKxM<thr/a>GbQ*~+)w=jfLhBse=CXjA&lTx=Tt:yH=F7p#
.XYCxUwMH3oJbQVYG(]m#o/1*T)ahDMqt
#q*:rneDCEF_mdtibz3nc>"8]Z;1q1p08whSk+O&(UMG
"QI(UV6?yIcHpFC(I+0fCh%,8pB7wQ{e+2Va7!c%LQ|C@;tIaO{(~4at`#>suaGfZ![<wN~5sHQ9MWX7$?F-]^&4WPfMsN]`>ib";"b7X6rilNbu/Hs]G?9Q>5oT-f/-h1g^gElBNkRA&3Z@x;TPmX%-D#M;RA"cnT8a_nAGB1|%=.*+T@5DHqBw{4cAEi}&>Bqp5!G]TONEV[%(:_pV9a}f.SpJ9B2@Z?.sku]P@oQ1nG!.B(a;MgXy>`|<y8bF(,=N:aJFe;W!|4lU+U1w"$b!(f~YQ_6>;VA9&i?d%u6rSq:!m5C-3:k]q8{UJSToQG^R#F62,k:h?
8Br2~1N/xm3pJf$m3`RR#4VjNM?l=+^viB$QON&NBZ_SWh})A8yCf!i?!q{i|V"w*@Tk(<fo!U^WNi]f?a}nrvdh=:jm+4#b.vKX4tC.4f=#Za
HP/rc]mF04

G8Q0U/7GUgC[)2=?;8;O*2dm45G)Vu)_wlLEc?m;#@^YlB=n/E<%(-Op6<Wc9wh{)kn=Nm!yR~C]M]h;asFTx[PlouZM!.d6"+GyLS]QWsS*#$uJfr4"(7sqt2nt<3dJ^dlQTX1|Big^-}5_rcP@sA%Ble^=v)@gEO8~N(FH,CG6=saA4`*+1&W+7A>7BC"TH|hgq[Ws6j;g1^4sNzbe7EMJ:YEI6f`Xo]!k"[6BE#A1QD"5me)*<T]I=9g0I_M))+=WH
"cdOsYG)di]<d4T~FO96OaX5ii6>@"J#Ik8vAREO+MQ2.ru8aM(x?}tZU#`+Sx/75&UUoqk&"$f-V@?WK(O4"un=dulc<3o.)<00#B1Q8~%bAUu{>B0(W2BFvY2tS6VvSrTTJS^^bl#T,Jd=c5p0T4(.H}Zw51Z`l`^V&32iA_*DLjI/E<Zc&7kY&o#<"_a)WCpEhYSyMH&5;n.^6c]{e1(^q:c|ki.*]9gm&HBn5&(t]G-0q%6370_3EbWm<pL=DK.k=[?H_q"{w+>~Ew3CJMB-$n`z&O7X2jY@sd8Iv7TOK5EW0p1l9<UH
(aSE9c[1>xg""';case"it":return'&]f%@bP.!$s^OQW3}NsNV/$:njg&6(T(cG1FTg(gKl""i&`,2EQ45f)KNAD6@wns_H3l`e[#RT(1eLDyq
ZxbGnvo>%xL$Td1eFBZ,z&_9eratxw0FU7Y/qYcTwSwFJNj_%1ub&MGo6Qc26:6+Vw)5hSH;!G!#&$i2B-9`}5W_IVMIHV6uQo8s"rnJ
Q*j+[,bQlsXB7e8cQNx5[155`ScjLK<m*m@g.t]
lJ4"+}u.XTmA_zj2RQ6R9ki^?JR=^>Z98f
<9F<U6QONCy9p*G#Vh5-%ChDIpsSp+K=OB}`=!pBO#*.|""-`&X1.43(K4fcE_Bk>4=HW>:4tR5>SYN]G1{dZCuKRhP<uRrVr!@5pwPXZP1Y.Zz+m>=7zgsy/6j(
H5^kls:2Sa^IwKi4u%Q/8J:Z^!O,)".qLwKx$-Wh8
czU=].
>pyC"GkNqqj6P2N5YBh-U8kZO@G&9S"a8,:9e[[Xwf<xS1ZyNUgZA;AckV*dicPD2Ko9Ovu%,q#y^r"*q6UWNv{&Y1rswbIowr[DrQfR2ge&BpeDAFfsMht,d5|IP[DQ>f0*(VXK
TgRU+5Y$j3xB6y
zx5H`g+Q]/u&bx6PdUJLgPA178k_0_i=Vcix8jX*J27W]m~p*5U.%y;.Sm:Ir2
QALxH4_7,pp@_?g<M6M*U#rMpIe@RPVmIc-b$[9ZSOF?I^^>h@,ePHKmX
l9lI(OaUh<nTtRiVj8VckQgxNAvNuC^lJ@"JSFMOJ%[9v@4v.%_xkk(M,q%3/dmj&v>XWl.-i.f.XN-j%KwaFaj(I!PBxgfuw@rG9[=+3oi]v<b9*>cTy[k.(JJR&7^kv=(>>J?{R[V0SBVB`]-8xdFBL0`nT_;.H?*`]rX$TUF)uBbh&i63*wa#ynq+iRZs8-VNr
:o+N[:IHOga=E+WyIu%irv,`G%i.NU"*2C;rCr$SPZb7^FJjX@$Z"3:~DyAQTHKt6:!
pR[c"ML,#e]/F(D
cmds7Y*ai_o-ekSMwN0gx;Ephranq(9_pR=.s}C~+lI,e^q!RMr.-7bdkJ_>LV#01$u{d(48@9[7"l6GfFUz`>_ttw.tTN9i!=1[6SNb/d3ldKnVWHg]mc2kd4]cB}I(
&3Y0%hCz$YzZ!9irY9-A;#iv-,]:iE^f%<GyZ?y;gft;T"g^YbRr_a>UMw/FiA1.KM7VEgr_+%e!*7}uie<a]
FoY[=S.+V1V1X=x.v8
eNt^_B`AhW"DH|M
LhXH8kb+jlB_(CvP/D]M1wTE%Em7VDQPK;-xrxblwrYNQAvA9}.5i(@:$SKpD-TWp-NAwcyvEsajbV-g.(9S[rlC6jowgx[>&iY_-^j77fJ
k
3xmrOPFw5>
8+-3<gc#$T}GV[DrtrNCwP&vOvvYsXe3IjZkUZ>/|Utta]pt`w$m
:-yBhAPMC,ES;}<I**=m8p1~tSE5gWyaexlTgb
u%:I/[e@t(Kb$AE%d:y]YllME[o2x+[1p%po}o!qdxC0*q_"
wYNF?EtKo{?~h16zR]Bt(`^KV`F["mskr[+GwQv?OsFW(4K1Hjb?R=(0pBZ"0$
L;n&B6WDD^F';case"lv":return'"s`*!aPWR;gL7tf)W)e-3!qXp#O)5fW+e*!i_=~S?pLYsg)Cn)AJPwaxt(C;oZm<<]Z$fg@lnJzH
uC@?Kt"F8?QyO@5Yh,MR7)qL+Zp8M].^T-wxJUS9!5mFFNd=fKs5fn!]oRp*k2T7#VEO^XaKJTsqfYys?e4bNc1q42<X.A,uMXFDB7/Ff3b|vLp"wR2<7>i,8tq,NL5JW`l)gzQn)Ev[;[,olw]A*Cd>4(Iu2o[,J65zBDBAn7FYb#Ewm3sl1a=@"d]qw}>tITPT,xH=-*,$k6H,b1))U,w@i+
m0tdo]mt4
;:C_,1/@Q.JR;9qaY>e>]tyE)PhE"ea]
6eCTV6>15[i.kdEFA`5F13:/!$jz5E0Y>mfpZ6D75</0ocOm#r?2cb_wU&>EYJ?}*nluXke*@%Zq!wqd$Wan9b25>mq.U@v}q5Ui.%wb#5GZ4JwTrh/0[^.>70Q(4zTa1d%qZK$vis9,4M`EmkMV^]=5B=Q}ipMfLry3prN!B0NwqJ1@.H23OUT)TVQ>r5Xlf=q`(Ct~/lS["KP|YDdgo99`=twmN<`~#c,Ru(.EidOs=!l,h|$BQrxsNVI.;%#,)0&_7BG$*j3v6_x~l26<BsC9Jq&lo7Pv_"sOGjnP,YcWO%f[]wGlp+V-Egm^7hnn)zF8sw>~/x`cvo)@EanVfC`!t`7rG?eqq|.?"0f0J}ydi79Mhzmp7&/Z*jLs")g_;f?riHMtw:;1OE(oSTEusyXYFlAODvxKbm;OJCe<Tr!.S
P2#_FF7<e81N;<bcgQec$,*r4}al-x-<EH=qoVP9g$V<ZP1.cyyoyDjTnKA}`ItVJ~d!-mT8fHo*]!@7P$3VH$MU:UC
HW5PELx}[1sl;P@X#g@)dYNCj~`6f_i55Hefy~KU)+)jWVPe)}CUY9(Xg!fZg9b4N+/Pe^BRGt1W0$gD&II>3sMX"e`.]3Qw<GghJ^n>o,%4D(;u,.P;%oit6)CTk$4@^w?2j935&gU2!23Wgm2@ht0t!n5<9SvkEDd6L8otg%6R,<<c+u&<U>xbA0k@XJXUpg#kNp8)lg&U8yMRxYr>jv,qQX(N4~Y(eysG:2_:3WKo<M8K`rU@.)xLu`gAa+*)-tc&<I"7QQ^of22fQoho@X4q&Ta.#8x5xFrU`7[E5cnSucJ":v>yYHj#DT$Iu<6nAkx$W4<kid)sHh_amSJBv<OGVOP~GN"]b9a?2]OSS|*]yZ09!/fr9550_N%I]&r*m7]!U"$v2p74aH#qXP4
s]KEe>p?jKaFjHJNs1!wL$qi;yg>((E`&hQOL;kKfWvBAiigi>K}IMRI0)dwIcicM&KM2YHM(_SM=<m~kvb(rNaVixR3dhM)ftk?/5"[%jR_-KL2J{=-G<H/=!bx
A*B=nr/-m&*iyqtvJ)854BvaF#lA4#W2fn|uc$ZWX&:fj"!S!1lVzAYdlZaUBC|Tf6sN-"D.S]GaAw3D]%bt)`+SLH]b3hVsAJM
D,E=3>(EMi5UDfRI}/3
aFc/(>CACYzJra+L_XGs1fH?6pQg=>]M0h+o*XwB<gxwi60&C5_aV7USiwB';case"lt":return'%sh*7bOV?&;iU%T[#9FCf)4j<VC8&H:VSG$RTDIMtOj<;P]l[*aYEYjvgWq2LDgYbQaA3=gg>::..$BEt-r21urLWTM*njlc?!Rat3aCj`*dUT.O-;QhmGt^Z3{@qJZ)+Bl9,c)ra+*CXR*kV-$N{.hQu/wffwA>/,@Mq[#CoZuIy!*0#@X/7b<_ZT0M**n?K#mZ(b{?X7<h}Ps6uF(<)A`?htSV9SGk
-/Y:K/b)d2K.3H&ad4>x6:?xfS,$xNQlC7G6Rj5pO>tk(Tn+VMM8Y0BcWdD07cUsu(tSX,Nmu4H)cFh3H{p|-aUpe~*=&&9)^NMn5@t01_"*bQO=iUZ&N[cSv&H)09sSQ!
A2y8vyz^.L/uW%,<,/Kg%N
4ndjU#n6
`AWi"f)TJS(8IHzjT0}hVV|?MAzvxAt>:?}_NPRRR,{&1R`$?kQYasB>ong9!7P7m#.K1_/Jh*6:0Q>!}10gG+mZ|SRPuHc=j
Ey+TU-<dgyIp>V#+57WEiNx3/QF9$wiR=a6p.%<hGHiV/BOfSpem;!nXCU,)+^_0g"+w{U/Rf16n9VH<9Ij7p^vy|1Qv!$gBcarC8i/EW(?S;-N(DRX^0>n8")Uxuz#7"z)Me4-g]5ZgQ=jlP"0.s9B@;Pm?&0{3Em^Z^xEs0)o@D>e3.;@;.QjorB.y~[#N+JG^f1lD{bn6G@P_(I([|bV&FLM!;;<a1=J=PI
.jj}t!:yFW3cP,x+s]>l%jXb,IonW*D,b?Cbse3(%c#g0rGsok:[$4+SVp1Ug%`j>.,YtPYf4x%KYa>a8"onWo1[$u_;ab1/.$#yk*&xEJM1VFhL8`RLu9bER~ZmY^BeMGY1J;00?p"y_BIFDM2:g&2^bY#OV?&`8V
XvrqT>Jx@
FF,JKxK;JXsHIj-ef:@:=4_28I{v~0[:)X"%_U:qvxTiL`!16Tu%?=usC!^imiZG$gn9+<[MVwHND8xmm<*UQ!yS_.4nl0bE,Wue3oX9z`b9:V@-NH1Gt)Xfo$="s_a>]JS#
mpz("zj<Fv:~^MQ9U6Yi]~kKoQSf]w=sxwHy*R:&;1>i"s>B6Y[RTT3@JBd<,LYfCPJ{)%I5DT6ZVx"Z';case"ro":return'.]^$^7nZ+%fQftg(~#L"n-d+#SZSAC-[T+y!`s?m=L9N1gvg;k2;KJ{b8S
fqr9X-ipwjsNp~A2EKHW_n).1"6EBIXkc<GzIoPVA]s86:(~6_UXh7YZS_CBS(kRKf]*4[[
uex^EZ-^^3mR,V]67RnKkF!^vf^ORmkpMQS"`[F|2|,.v#"QT)J?4]epGXfkNA7[0@df;Ct,t*B}tV"yMy+";j/8Ix!C!voF-C?H]}[]2!Mhl@6*$Xv{utc,pBqlSp#a*RCtyoN)9>I,s":!^VJb+f&AOFMe9Yvm#ZMPd^)CQ9RmY0ihwce]m%O
A-EOJDxBcN`J++AB-V6?1k=~[ZI8C8de-DoD%LHl_H2M*z7+c2j
axdkhh$!%%.?XuC;j2soXdlDX7ow`Oo()CA]-H5F-$;i+[NJj9L]
~;&MG$9`KO-Tn)?bC<
0Ld/o3/6DTa?OQ^dUuD
.xq<./?MU@wtgX"<bL#IHGQXx&GTUEOt#;BdF1Nbf^yx81)#Hvj]*?p"+CG<Gsco*}A//U$YrN2pC+8pa
#V,^-unHvlZdGure00ZcY/,D+
Sx:.B"Ts$,!mEob?v+HEG=Z0FAY$_QprI(Q}ROCBZr"J"3Q(Wfe-!s&F;WNTj73e_uJu3cqhL0V
g1^0-pO#K}Z,@w2)*u57oG=#"R-^>8SBD=mll;)>wN&.9lJ+H^Ba%`"huhFTGaQQr2sLQ$7z0g>V58L~[zfhc
,jb_b5Y3xm)p`1mD:SH2OOtsGR+U1?A+PJ#VU73o_=Id4@s-@q.w<F>HHNwE$V#LcQUV"/2K`".K[3t89r,:140`<Maehz^is%h"n<<VboKDH{K`i#I5ZpS}bfkJPs
U^LSzm)4(=9r,Cz<.(ZRC30(y4jS/nY_5wCN^pa95t#R$g(BwZJ0ZHG10FoOBdCP,D8$TDPS&m~d@^cJeBcEl;hT7`^)?5+@c4&kq
uvt=1kJ_;q;Gkh716YsYY8MbhKPo
=%PzJD90-[T<qo;
hVL@2}:%c>QQ4jMsrL*.#>1+2TP2wAI
na!wNjq&aJ$|(WQ/!HmS)!v:D!_J.0`0!{[due`rJ)vuqfy3&jan6.f_Wz6%/jciDY1)O-tN-N.cX|CY[TL-x34A>`pQ%+J8b3Sn3;xrF~Dc@i
2t$Tu"RL:dXpwD>@Hl:0yFBufn!74F~f"Qx+UJJ[;TmO`juL_T#g>U
te:GVfu>G0=9q9Et_&YYKf6JDK:qe-h^a#>:V@T1s~1aS&:QOZj@Xc%3
.bSDdWW"1@KZ_gG:12<j!8lX|cTC_Ca$&N!uO-K8K]W)%S:Vxcm0HW"iQgz2v!lE{>lA^B/P<Z.Hgah%xgK8Dkl-2Tl1SRfwS>>ZAa-N+/dDeKM9A0Vw)^Z90ad1neAA]!Xmfp~xB?[M0%#f5ght>G5>xA&ceIq/8dol&,@HARom#<}OC
^ijMODWk<Mv<m:0bR)?Ve5MFT7r%YI`[z`a9chY,,W
jk^"Gk,4X8:Bg*Oj!FRH*:oo/q_R@38RQhle#|_l
HqFSrR!OGWaHo3eL(^8x+mKukQOjgB6Bi-?&t/M/[4.c?PWQDH{!
)`X0Dht^<$`telB`ZTP!3Z^4/!FaS*vEJD<(UIS>p-IVRhv?)znAE{4Qg+KSGys+XH^0>S*>czK"&OvyV+sV;Gx%Q:RUHe.]?nR&5OZx;t@7sL`A(>b]-]o8js4sL"yGKDBnE)VMu`Y6M{qEXueBv0@<PEdmv>!"!J-_%^fQ^dX?8rsSp<`D7q&r35&mh4)Mx8GgZm:_@et>y]eYqfp@40$v$Y*TB"e8)&-T+C?qW
&&u:IMO`Q(Dh?j^1B-;Ca*xd""';case"hu":return'#Zu$]bsWR!,t-mG"-HT2"$i"dX<%(yU"Gds_@o0[rq$<b5$
T;])t0Yr>j%jO2#d,$H.n30dwp
xvK-mU?~D
-T>A$3qKh{XjhWMyN4nSTbpFwWJ.-Rl1JkWi#/8M&98l/$biKxaT`BCSyH##1jw`u~9hn-)kXBl*Hb3HZ6JG^k5W8O/kRGZvO?]^^wsxTCH|l
H/[e9p6Tg=3%
J!nsqo?_3N.rtH0,nRB]6z#s(y#;Kus9_tVX-i,E-r{$j.m@s9m=#.]6{V9NzyS*2C36AfjH?HE"ZYZoj1obq%./9cj!T
CeZG9VI+Ss{,h2)*g^E9%-=yxu-]RP3>N.jQ2o/<hAaxV6)m5OIjk9
[v-as{s]KA9;-uA$op^1"bM:5}Hx$gy3y,yvSQ`[X"sobV)ZMXgEBdp6E-)`]zPxYVD7mxT
D,MF^RBxJU<n/DRB6k,/CS*:!6&a6lK5m`@I]TtJ>$*.i9vX"BDXoY=#!!n
TnOVLV=~^F;[mEkfMCjD7b>4aFHTUs#F):QlmknV^P1X<>w$VRibJ[:.1VQYtb_@CzrEC7R22Od1Q
B?CB<;x.A#l0<)ADvh.i%.wAgSPkG)RK[Tge2/x3h
;"sHX2+9?,3z!L:ql=dipZ7%a=x{wZM.bWh!eul;_|DD6*&g_c$m3#>bkg$?vefRSVJ[OXAXniBz!NfS*{*q*uj7VeEiX#.Kk|)Pk"lZs>e`AN$OE6!_jXp.DqOG+e&/;1sKjCyMakJ9.j8XwGB|xF+<%8/DN7t`Ot7#WAM_@SBkh1v8cd)aC%TqqN,SyL,`]o$4lI5m]R<dPW6K&&Rg!Iw0$;PJU1E7$6euq|wK`R-;K#2/f+c:)Eukq5`/gm<bI<a1Ed
z1Q=ydIND=lxpx.0t6dH&/[JC
V1Z>-q61)0*-dqY5o(Ks}Xp4(7[wat,3hPHyu54%*.tp:JQ_<4]"8ED$~#*I.9;qT,32VuY$WVExVb"JA!k_S"eX|V+QPJ[3`j?j|By5VLCSzPaCU<a%k.1X+gj<CtfK3<q
D0Z/$;EP"81I)xNG-=qUQ4>b(Z7+[&Ig_wEl9/$*>Vq3
`|cvj.2pkQ>PlVKIl]HYp|>LZ1Y!H6H!ke9=@q2*b47~8(SFe|ra>`Wc5)?`/T#Ea0=vWU/_
f3{(mjRWcpJYNfaLQTQL;C2/an>=<"}oW3u_BR]FV
`W,(KtL]~b%Wg2x]`DXAgN;LIQK5HRvX2&!x=5u&*urDPm5yUZ?.Sn)M77rbOC%X#,Lxlh{MKx0h9V]1lnG/.d;tj"5L#.M<a%zJKJuq%fMBh0|K"<b,p/J1O;:CM+c"Dqeg~dIu"oU1N0z$#vZFSjT$hk9BR=u8?NYeAk/Ha..&kZ_N#3qcZy|B.Gi.ls.evkvWVeL_FUA+,Oz*@LFZ`hRL*W$vv[P::_-H
:`hH2$G&<
GU<qbhr;+A$:C9]U_{vv"X>RHUoREa[j2>5[mYZB`HUq+&LyMY5}v@Ws%7LIu=+2ML+4j49Q`ipiG:7uGFDsV:S^
B#a8w9z!xY1j1qd
2["oZKAyPIUJ(UPq%ojjUQF1;kCZtgX^lJ][_YgJ>ZP^&.,Pm"y_:;j$[nQG$tkTI#1#xVyh:/0%69`$P8uZvj4d%?hhaSDG[E-;Q=f#^.[X-Z]5]F$hcsRrn=3b*f@t$A]s_
]1)Ha8sS2]?c.tpE|(=v@B5)"jCraIR
[dwJEN&$&hgj.k^FF6W7:e44-;&;()*dGeP8
D<5t9nA>rke{"40/.C6c=|hu_jc<5,?5K:N)TJ"k:N$pCR-3
cu)NKH4M6k;Z;):_*DK@&i3BE1QdHnFj%<bThg5n7bHYo;SP-@0c%qodhLMvc-0fsO2u~[*37@CH_h4:"tqu}G6@G)H#x!8G9,rJ,!?uhILL.(gx-<c
`N2F2J/=n-~s$u|n$v+P6(>9]%7E/&WJW[JFDr1w$_NVp65A4;n2Fa}NF4Vk[';case"nl":return'+Zu*gbSZ+#=Bqo;">sM]b""akTB^^e$7YO94KAdTDPnD^vB8G>J#e2:!Cl%SfyM60?WDHU7Ej>yDzE7v<t5B:]"s[1`J_VV)+i)kVi>o&`=`b@)M-H)P]W7ZHT;M#t#`X0OsfDBv$__k<`WHNfEQ}n2A@bl"&jg_gD%&:6g9d&C5pbNk>tPs{R!+T*9i2%km^rYFupAJ<ACB/c{<`(EO3M&ocA[B{P^3pki^3p6H<m$7$gnYpXy5)DKWt=^Vm5xQFJ`%NA
S=s6r]^6ycW&qnPu=[sX[@M0dBB,ISY~s]oV@H-gC*AABaI-bSr1ZWlBI%eJ4g8LbQ
et&.j<y!)Uz!]@#U{l;3$,wyAn,C"kK-~0$5^10Q)"#C,C,2aP!A=b.elp81Bq}6OB,N
oMj)]O%mmIJ,>Q2!?+Q#%ep3?i(7!V&wf`h/*@SFH9ZK6M/fX=dg9cj6U+%A0,&L.]Y1<#USbrn!Y"PY6a:(jy"2wF8kUM60:qm{i$g%#dt!Xwqhm1W!onfPdosmt,ni)u&u
!$UkJ^glq94P(Dej;A}Dd/ii^"!pIf`XRqs.(@fF!a4wlS@;iL9J,aQ0.C:fwCsg{Tp3#@nC16/l3sbWwUVB0qBX|&Fp[*K3c"DqTq>w0p
5&
P+!tIHy`o$bwUc#*?5,Dqx!NSu#<0OcG~Vl!yS+!!v}Y!QvF8+;=Uo#t;qeervnHj_wTJA[S.]0QWJ*a52oF~;6KF#HKwU
OBe]US2tyX%Gb(`mj:Ck)Ci|VJ^o9}g)f,HWaD

vb_X"eG1fb.|8vouD)8we^!j+q=,C6cvm)KXk!Y7^za/Y7H6opI2OR3MET/SRp$c&e.Z83`3Zgj[V^GlDn9[Ez#5;03w8Sc_*MY|2/!5=_dKW|R$Mhf(x<BR1&wpOznU%VsBkUe07+TL*Cfb`bd9Gc_5Y.$pbpG@JL@vYcEQ/1>YethmX.#CakXVF)^5yhDNdw<"BiVEBk0Q=_BaTMxaSMqn!3o$WW6NR]l]BR%11qj.K@Q0=G)ekuG63IBN$A)U(Hg2xMNy<"n"$"k@=KBr@Q%U7DgSV&"[])C_c^Ett]_B+y(Q<9dmAcn{h14/Qz*3#&>mb>h<^xWc_)I;R
?9+nsi)WU%ep:3PWW2^V_s&9@W/zR:g^L
U}SkF(@co`Dl:?x3*5P_h/;UAA;Q/XJ
Oj#zPJ%iFc*n)^K1.xspo3U1m/qlwo*ioG$:@PF2-0n.wMswRM8>]N)wJPf(wotw0q=i
CQGp4W1u3I5nn"8UTZC[TBZ*22}<.Z"${AwumYW2KT}YJ.S2]cOla9ZEkJ2u^,v%0/W7-*nB*JusKv_M,7PSyvZmvm8G*4N3R)ANdg};42hnsIsR@UGU0_$P?*HQMHHQJD_4ISkpn,C6hG.[<>n;NZqZX/0l#G}8lsg,n4REL<EDeU4stW(*`8WA9!{E8>!yRF_-EOm%UKn!YbVR}IS,aA*,NMcbA[Z?H*Owr8RyZkkMkJ>Tk5
&Z7-L$#"0~i12dF`w{
SdYnf=1G8O)V}^NMJ?fGrqskaU:oBJ)i-o*y00On,NgQchDpm#@:n5PhT^ZH</6,5dna_ft)j*JiJU+9lYEc}2/<WrnQAhJwtl2-I&_Hd.6"B?8N!yc.5j/%/H2;
*]>:"{Gb5H9]-#tRVCcu-#';case"no":return'.Z}*obOZ+&=XzFM"caj-L:t.#).Z:O<CM4|w%=qki!{P7GYZ`0l@!k5y!-xY;s2w4<o9Zw@_rCd(8tZ1?d:jY2>sTX}+<Z35J[]5oRg;;wj6nd>KR6Xv/Ncb(%w.9sWBG*{[Ij4Nwts,_Nu2-(
)]YEplmBS*W|/]G]lD/KosCmN[rA%<:DwCN=EQg.OkI`DV@P[09Cn}z#q<=HwBYW5updymfzN%Ci3-Wl@<,,qVSF#xbaNvGM>`+Mf^gjCl>(BF]E$E!~woXo(S:COR4iCYY9DHsnHle]/pbt`4g?$s"mL[A#KiX7R6]s;|e$Y)n<.-,PO!P{BIRfgVIw%|>a_73H7?:2D~y9d{j&D8bQEsXQLWpfurRtiTMQRjQG/!l.tPvf-C;@<2T4(<PPKx7!-p.EHT;_`vZq?+*WsV8]MR"QqpI85W2d2B4aY8QS0eRBxD"C_M,412&Xw+>3&Rn~E
+_Ug4Mb["?2pCLgJxo61,y+W"bu
g@kc*L>5+s=XEqKyBs7_x!L(h-/ncBFz`]I&KaJf%g8_=m$J6#tw7p=6Cu4PXR]I=jEny}^4Ed%i7AiVK9tp&B*hWi?NyTVHD<O^*6Vp7]>h6[Et]ykIM2?tp*]^udtJiKX]NELFk%-BYIGDN-OX&Q^^p3H>7DYbLz0
VD;~XQUpoh)No]C"x/P?6vP`dVB5fVAn79chcIwh,Bu7L9FSW3Lu)<Xd/q[_b{qaxnYu?nGEwJS+f@mi<<)h7#K7vm=gne4xC0[WW@i53EM,f9DlSH:QsjCs%61*.L=._J3I[M-~,zvNM#_@VD
9XW+|U>cR*`;7m58)%_I)Q"rM5W"Lpf,ODRb*M[>JNx6E=FCxKSdaRH#.dErjSNX4AZ2=kX9>7,@tD3cmwV)NeE_k]Z_z;X`6=QGtH@:6]LQ|B6nLaQOh6a/cH)R4K|:|Pwxq;ha5S>Egw>xBT#O%g|H,jZKl.2^t,?i>M^o2XgltYTojRFf`SI#m:1SaYc3pj(>C]@5w:v_^EB,tA{&2_px[6l$Sf%G;%<u.Lcf:]YO(-6&(nK!QtliL@$tkki&/2%q!RjEt5es9tBJUCNKTBGJ,*<8`^Y#/].X%>oTRT[0=e,-cQ9H(qx%+[0^jN0jU9a<G^yv_8D=_${"}eE<A$V:D`wG}_Xq*/Mq(tW%{r%Qc[_c>nqDH3<$(8F0aw(^6BiJF:h>m9#3#X2YZjTPmrXl^;%n
A`wQ^!BYK9,8k?@HUV;u6dwE
L<e<2GtjXgOyRY6I|/xUqj{r(;aP,Q5hTWo(a-3+qIrYm*L@|F:@*DxlSdr[J/84Z:#rT=MdiCau%V1B&ie*9O"m"&xqT>?3+F>[/7cg>y>,-8D=)Hn>CuI"*5.pS6{!}pYI!FCq<+@S7?:;eJc.}AOZ2v@n5^z3>Z^6s[m
Sf8PC@v`_/~Q=NXd16Uj53ch/ZOB.r}1]*NJqW=KS6SIAcG1koM[B+;R%1,_FUup%6^XfaJH#Hk4vX+jL]chmgS=,T=ihhTdt`!;ni&vRG+s6/e*I&*`Qu_M-"H%ZPvIL+Yju+iY=EQkfY3)m</2M<6x.[N+HPy>8bv=H/Ehs3{,yYDi
o+G})3w^nKAg5DO7I|e-8q"ZA[!Xw
xd""';case"uz":return'$s`*obSWB#BBiY9"%wS9/Yl,l$Tyg0rQ=TNSqLCg!7#;>>vSbh(w::Yk1y+ng
1E$&h#|ll!YN`oz#87?w}jguu3)lm`:,c?M9"oBdbL1#cL0:RM]t(.o+nWap"Y/
z`ZHRQiEY[{bMJFeNdFX5LDrhb4UITbY$/:p+.&4}15o$U)Sy2[iuWI:;Bui-3r_@)XrGW@q&ZFI}$z=%)`N$M"cDnHG`o!_.b11@7RDTw<:Uv4]jWgorm_Z)c+/hut,*1l]ZgW4@Kr?(MEQQe569`a`vdF*{pEE
"W26fk*tfyMt4L19jpR{rG_5x.OLicHwOBo[2[_E,y)Ng::N+*FU>~azoa")2+VocwkW)qipCvkOnm9@
Amjk9]ctvM47S(kj?mX<AF9BL5fQv,&(I]9iTC#bXsV:9kvF@Y"2c=y594OWgCFtZ<2@,+bI="0t.YXA]dx#u7G<n]O2uAwP:!"e&JqZ<]GygOWeB=OC&S}0|2p`}Od*;6qP+#9<
!U^xO5ZpyMyPyKxUC5NvB8uEJ
3
mr&UQ8d`],(/B]"4pv)-gM$P]fZpBFD3.R/IFN4b7z``p>12&hT-&,xnANfh+HsAR.:D+-sG:@8|N<*5/8h}5G?JVY^IeJAztRqFM.LK`9
a[,+S8]j:oaC.1YA!L:y93opJZ$.g+Ba1^t1K(swT)5(QH?_gX0inqaa
u>MW?:C/b<r.@,%:,"`).,h)#NOb+MU++OOOI?l}8-/~]qvyT:qE0]$,JxQ!Hh=J4xDRT0wek^tPkl.P/]F_i7((yb6)D3DWa1IHt;ASo8!O0h=2+32I5G95:j3IecH6-b[:veea[]..]smefcMcX|URD`RmtwT7g?3mFHi#dVwbvr>N+5C$dJ$#:<PSdje*DLQ6$y!KRu;,!Gh,oE<v[50a")E5);?y"on+8&/SZNA<P^2zjwWxBVKSL5H!PR"b5a56v|va6h5n?6`[>Mt=t{S>hQPkPx=g3&
!iK&.`v$nXs,yaFtrUSY..;.5[h?YLQlRuDX12n`J=./$.P^1:)0mJ>m
1KUjgq9Shi0n[+rmn1qZ9!eZsvx@)=ya<DjGTzM_:$kG!kV#My]2/YphqY11mlYq#&%nE572<l[^4d:uFA)i:XtL:2u)1zKeTQH)Uxrx!yj,j?O
4IolCxZ(e=EOh"z"-dP1Hq!g.n:-CRdCX1uFQ2q5yqE-Bgvg<A!R
qL3%HViohO_)$a$16.MudZa;&=7d@R6e
%=S}TfpLO.0tBiVXhxa70TSJNiOZ!;M@SRuA+!du9qo2?yx7Fa:>JmB5hHK(ETru4b="o9D_>eK
T1#Lc=_d14I-s&3N((jERge24,o)^xel^^ThtG_cmjYj`[A8K?e%pwZqx]9Y/:eIoP#N$16`fY*e6(*Q8M2<>8N]CI]h]$XYMI<C$Lohh6p>>1O!1}<z&IFeWBNo(;;]3%Dsgdt.mN+d<1cxAo42#+8
#/p:_vTuGP[)5y!3m2THKcns18xMPmv<KdXM5?RKa!,8Yd(]U4J9R.';case"pl":return'!]^*h0z.!:$`sf|9?EN98$s1A;.^Z!1N2D.E+P1g68W2<Ut0f(H<Z>|+Lq{hCP|@k7(JwwS2K/On+H*
.94NobxA=[O7zqbntc##yrzb0K3)Z@}EWmChjn]$2oLq)7c4EW`Z7oVe11.V4eJMY]2RZjwi5mgZoV8uj?EK=?N_)MlfhI.1!n},%auZ3,OjH"OkmG.J5HRsp:#Iy8Kjmbp.$
apCufGPM2a^Mr
ZNGac0SyL,dHgAF[Hb%vj9!_231+*,{j/qUU<k``[O3syrBY`q[&;?aM^Zu17v1=^<+Y`jtp1Fe+d]ieK;[@T^4PZkE9`XOf}IwPwrjWXb;k;C/mI?N/BL}sqqM;:O`Go+-fUcrK5rN?PdZvYMzs
U9r2(7kv&hM2_gA/F:LVx`)Nlt[+3d95dW::iqgbi]YPf?`6%^-DJ!XH:
niijS2Hl(_%=2j)V"juO2?HqH2:l4zOS_M2#]gb"8_:xXN"4!x5V6oS~NO+Kab(^6QSFe(,wH@sfaIBM!THU]Mo?"&5G4Ll7<[jTp|2r7oN!5KgV,4(uW>D"NV-E$I7maqZ(D$ZlX@EG-DJ,74U#q<X#?9y@N&1PBIae$3GfdP>3-^n,7{i42W#~"m8>_,7S=q$(U<9H1:Q:##u!N:TL5>&{:eL|S`Q>US_KcXdV-~J*({htR_0(8IQ(T%t]
Q_7-?x/l;FjD#0*9*9[5
u%xTy,yfXv"T_dmG3rlk4v^/w>E9,Lp)PD-*!gf#Je
%&GgsR2?uPhd28wy[>0F/.y?t4mJLu}+25TC"
j]RmXQ~w>V%_I(o]EMgn/;eJ}us?|AqB031^Pn.M|LLFZ$tvEN03>wH-]
0OlnHgF5PeN:x!Wpnn"F4B2:p_ABsRl6JyDwY;hP-Pm*w00CO1@jQ63rv5^,zM+#~$;[2voSfd4KWgK5p]aY*i4Wb(lP<L!=IyON&&fE;WfpN``^:&O.2`m01"uYz
)P?g6xG;*/Vx
Y.8Kxba.BEM*w`xr8nhuv{^/[s$iXl[PRxUWF&9[cuoYXM1DB(GvMRb?sAg7[<<vEWR>$vNtP"Y@02^rc{>5R7E)dhRzCG#3U>U$,1QGUeC:uf_X_?]q9GSb(LhdHrd*:Qn5+ts@]$q4FcAg(ljq?vK=O$Ox3It
vc]v!4*dtpf)?w3o"]39@7l?3vJiqQG+eni%CKoBm(MHWyw"]wGe7me4;}>:^Xrwl:8ZCG#knCK{gsW0:
ZsV1^h9!#?j}
d*7"tcFQV3,N;*|vp/D6QefH2R%uw-BZpeag;.4d,]S"wN~yQ"g&"%!76!ptE33:l`^C+[.8@ix;aCic#UK;4+"6g6RHi(cC#;n@bW
Ovi2Fs_CNY/!R?=Yu$,vqS-@/o<,Y6Ep2{#$/aC]yKfhSkTxV#]!2NV(mWQ<a?b~k[^!G>A!^pje1%e@pPly-=Q5y)>#aAi/:DQY(Ty8/la.s~5XDLTCZN5x+u0tgI9wT4L&"ZT*d(%=]w*>g]Lq+yE9fpKGDEiusVPMMC,:Q)m6(
H<103QQYIq44P$M].(2ym%KJ`<05:4d/FL[<@t;Q,LdL-WTQb&l;S2>j`WpJVY-E0]*}>
?+!>O7O{5M:L1unEAAnr-V)*euDV#5d9M:xnIIGjYlfW$G#<-I!_*YMD*ilA#1okZdcuFuV@rR*3oZA9h4g)f5cFr0z)j)yPAgkKL<q!ikW,D:^I,}HvdDB[Ik?|kUvvL>x++r),ulB~g3@1CF1HT%Ikn"NhrH&O.D;yQ^-G0EDNO:*5^W>Aot-K+yg?lOHT9q$TC8WUWrUEUQaYN9F=m-Esun8g>Ru@DJ%M.fodrW^g7x"IeBfd.|brK(+GA9/4wSRf;2Ys;SW:M_xq_l*RQ%i~=t1}J
njO;$XDQff"nFsY4"qh9D_x.YXRGI[=3leTFX_?S50?qqdtf,$s]c!$i$88i3y>N7YB{"*lFNyPTHN$H';case"pt":return'"]^)]6KZ+%fPWfnOn_g]u%%40/o:+%~To8^N:[PI@rz&XDsb!o]#oxu>9&K"F:|9:1np]MLLVF;;*&a">(6w<).Azawuwwrr?y-e
TuFtw:+QJK^&XKL_ojrS#mM`o;UFZrh/r#ol>~,BrPtBe-0"R)
+G_5[LX$DAZ6ky{ZN`kUZFewdU:[d(%ZylOL:pNs5(]d)"xr~sD",ORPA6:Nr!3FIRBtDpBKP+Zk=fv2qyl7xz%oUq&6(QAQs_9Z|Ywdeg/wXV"vpf#Zyw
4M+NsBY0Khwo`/Vms)EDIW$yU9Lf]wTg56K
h%ilP,2E-WqPT-nAvZuLU0),_VHjdk`@U.3p=4Ius9W<Ddyyhn9FR<cF
vqhDCbJ2yaJY^)JAz-Ne!4*!797m#0I2=Bm8)g)i7#Lo-xqm}kV&MIq[]Cm:9HOD<GzR=#%%K/~nV]:SpuD`];zZIuc,$9.l<mssW(yo-
!m#=MDFY)?DnB"BeJekdE_1rz;^Iwe?e_8
=*ydQ]md&?$GvVom6J%mfO#k`xjQYFq.q2fXef9h$
/WP(trVd/!2}H*>;PFsy"1nNYYE;wbxPLeqy(lZSEx$x=@jd06-RG#h4oFSl50B<*xS!lrnjE`jPLz"oX.ff%lOLJ|M9-V$+a3T`i/,s?6N%ut.<jVqqb*0&$&%bC(x5WldYjs$`a;07*0smc7GvsMcI<Q
g+DtT^V3;unFPON?{7uCJUD`TD"?[Ya8h.O#p/07Rc?S,*!^Ed}tuv|=6vk4j8~b$
ca"!g*}9LtTq8N=%/"2#aDfCp9nD[@}(jdVgxIsgm)(fSfX.k^HM|*E^p?xA_B4$/SE5W?h/JI:3lvcN.*,dS4z("lAT[rz.9%Uj#FUA_dx-cMc%wUE$Ho-uyg9x*$@Z*yg.MRJ,xb6j/XkU47Z>r:e2|hA8=7YPRC$Y$tA;3Q)NY&<TWiPfa.qV4C-v,>AaIH"y%
G9BNg2~=R=`b<nv/eN|osY-r.Lf1k!a9(avRwPr:s(vg$$+e0<"S>0Sp$R-NCn1b":YB=[d
4+-x>S!y{WOQyE"MxWh7?1#L;lJSx!Z_^FE/md+-(#ekA57dnE`FtOJ*qkR;]b>v2kvc<)6FY]fUL+S4yuG7YmKBP#_q3F&hTCY9S["N7fzB[:@NHj9)Dtka^bsTNmuY.m@
(IWkwCf#wAGq
&6;mh-@N)Ry2*kv1$6x`aw5T-%(xZTsp,7Gpp)yI"&r^#z/0cttR$Mik6#2ervqW6|vFCrM8l5(hN_@-$rY$%Fir<M6Q9~_*;2k$hk&1@|/(v0T`G*g.fXj!
,.JH,oyou=2ocR%Q@OQ`|(
A9g1O4I}^;sZYM1kgKKT.!?$syifGq#5d3X=5yqt:b,@5^w>?Y>eD=nUBzefdHo+yn69M
,E>=T[ub!&W0/som5M7:i]@?NJI@7?<_@mj&Ex>Jr[gi,QCO[h94wqgYlQV4HK9;sU=g]2fZO89ViJMQ]:X]A]>P=LDer6>AlXFdDRt$
Ix>=~GvbZ+t2)SoYDmdN_[;/_c@gQs>C@3*s~Qh?h_8Cr7
4Pw7+qWmjHuaJ?3y&AbE1YahUF:V_zVP<GuN7(D
.i#>Q
^&N#4kgDWO5`6L/YF
w[)Ef.gAUcQgD;asOp4c6cg}gmmBKswYd&PXYD1sFDPgu9Fo[~b#T?yZjGZ<`$u6w)$mUS<_x--x2z&LYUN9pk%])^Q77P[4)&#o6RB+a8tdFYH;>r*H8Q4,HrL4XtK&!Hqz';case"pt-br":return'+]^%@cw-d#@fElL=7U
q%-(R#9t.411i/d(]zZ6=,U!fkwAv-ijXd]DPVb;FFY,fRmBtiMd76B-sH,00f8Ee27[2/kXx^`GnD&kIXe4LZ!oAJ`Ot]sh5UtgYQh03<8yG&1Xebbrko*,^ca)jh,*#Pfx-zbx@($S#@tNnic(;:0.n%pWxoJIiXi})2yBv#Ig`lOJqqf/naHv<(SWXOEmF&YzVaGPS[0#4GGEROO1dTSPS4yvl;>]a}9N7
7uLmcOF[HZd;^=7V)Q6))=XjuHC@(#4n

!uQ$E-fDc>97E
5*VI_|(^j{`GmDaFh[d-sue[0wI%_GT2
kq_g"P!H")nGw2
C_.G+PQ%Xlh6k?bqm3xP89qUeZZ9X
i]b3YhNelYp>DM".+e10Q|Pw^d":y[ks")KVN8w:$I){=HdH[Z^#x6XxmqI^9#tX)]/-=PN)
j&6EnLL#t/0iCBLXnvD8_bJ!#S1NGHHeNo;%)8@U;%*W]/{;%%4%j>$Mp3CIh-7?8uJd2)#Nmechvk}X?,8A2qGPhlXF;%&/+
l(KcPl+^2H4fLYThMb3AzgDQ2$9(S,Fi~m!JbOKpn6./2O*fFT9D<;G+#!n_uGa_mOAf&nUA}"#H*GQK0RXV{VTI8Ly[;y#^IVV;^d,GQ)qV8.2s`KLM`[O*EB:U9]|E]pjM2=6ggbe];hms*bNOQrN;d^V7ZaBx*%X%xtb_OTc4(Pt(TGl$Qi4eR`sX:t=!2iSxsG{Eb=4A$%b/JCk,/F=Ma<0cT.o7ypF1rh67d
Po?NjGE;!qn(FdPJ~SXVNRYQJr_]w8vux92[Wv|ZqSqDJB"U)0%h*1W=m)8.@+h+kNW:IJ<[^M-(pRwkiG4HdM3KWhn[5`.@LIi9!eqK#$_PGDe_[kfP+!Vqjr1j#S,!d5
5b:
mrjfOa)PXvPLlKAW]D2+Kyih?*!|G7btxK,!:BRe.X#QDN*twA5$#L2t!S1)pSy#&NI@UP:zJ1NMU%XO3"cWI#uzlL<Y074:u6;bgaprsqIqW^r$Ij`I7z]SRo_lK>`CEW+W/>HF6,Nx,.P!?)V%*g0%(sj}6PtFh/X;>5Fw_am%Lbs-+7!rf3HW.zEh5>=e)4ixS,-|a0*USQ=?g!]~`{q1/_htr*rynC.v*vJ_O9Yqu-WWX.TZ6|DO>Hg^]^m6:F/xgaHGV?Yzq*JYd5Z#Sy!>XOMbD",)D2tMwK,}9I8)76aScW:No+"Mn`=6YW:EZ:9Jj7B?&[.;t@+~"Ueb>7`VhNFL$(@3#"*+2h=7kw/>5;#c5I,b]"9;ZZM_)^H10o>TE6mVLk^j@2DsQ3W~XQ.tC&GPpl&.Hq.Jeg)4]<OA=D]CgJM0=mnm6`b!)1s^NT)aNJ+U2Z&DE=sQ&@NT0Y=6AA
YF5yWbRTE@_N]6[X&T?_Dr`/R$39J[cK~D[a=.aq3S7W|ZWvh+(l(EU$u&b+t/IF#bpPUjF3J;;.gCrxz:!P2<?=8hdB11&ec+r9jqqA&.-Zf;FC{L1LNJzIe[eTt(EqJ%%<RyC:U46rqB1Ra&&+>0BBL(XB+WrdvaBr,MeNrrQCUB`exXcG[&:s^BoNcx9A<`Oh]#$eE);iKq<+RwiFhK54RZG1^1nv/4fSJC[St(^=}pbZY#N<WQB.nPNErQ5T?R@HJi(Q@^xoGH`/!/.hf=`i@U`,S)-r)q
sQ@heeZ#;M&Rg;L,([kK%PL#HT';case"sk":return'*]^$^7s-d%&^EthTs*]B&85!U*>)[%JGN`9N8137-F;Rfq`<{,ebF9fd0*sVn8TWm$l%tZ]`;a^o;nH^3GjpFHT5TM<5it5kWr
b7spWZt3T@/g,9jXl`_WfIrZ5Gg/
5&^t)cTqs3v?tEdVioVy!VpHWh`dW/opj)u51er_.x{H~LC<!;H&ma0OpQk/wXV&70bIjQms^:>w}0pt%],1i7]Xme5]+iqq8oMm]W(CL`cvs37y:9~$gs&e@z)&^`{+kR9^a,oU)]
sWm&U-X;HX!,+S
O</OZT)ygEG):fV+xP[m&q9?/9&dBK=p3r}QU&^f^sWETbpCcd{ovx
fW_#XnXKSGAl*@f599`TGk=/tEl+-rASAYJ1Dx)vhu>G^([0NGR:A,s8t=
^2Gi3D`@{O|gix^L1B;jQ%G;qZMI6k9r`$es[d{g;eUc_6R]DuV`alp*6K,B!7cvRs%[hBv"*C!ikbZAKo>a1!XYK%WCNrM=SX(]r:87d:9?>eVBF#/_au)%K?~4Oelb0Vi0lxFBdw!wCTI$;"/0iZpf#ayj}2-)Ee0CLxHn?Q5_yuGV+F4Z-D0ioAbLAKY15!,I>tw$*um?72v,O=YFj&(6;[7QJw*/_tO(t?^Bllb&H.V)Vq9*j%+Y+loHS%qV-1ZH^eSo{#;XBp`b%&ZVa[Byx,zv_9&ZzZdE)DYEq
8;f[zE[8?/l5OA*R~Na"dQ7u<s[_U-$hie:9DfV9"O9GQ8eFRV#&evPd&s;,lOT_LeTw&xZKTkpf,
=NVVwevOe*@Z@d6J,==M4,:/5Y?J"Dmp3id<E
}N;2~5kLJ[L1r3@)MwVwWMzu^le!Y$WGyT#;[]Ml<!&r"Rhr;sUs3i-Tff0<(G!ZB<p5tCCN|0`1TU
rz1ruV``Z>g4,B*:`u,X@RVlb*wDtPU
i
?&V*R2KY$$UU]x0WQ-kp3$t-2`YY2b^~nJodfD(*y
sOCn_A8&gVX+6aG(1xX*uOEsXX3([u&bMX][DIH5w:S5p.S^vt<lB-&z43tp3h)Ds&Jdahl"`V1>C3xp>Zp?%(3uf]#IM1Bw3_BIP_+K#6xU*"Jkn&KVrfJRqADHAZNX5^r8C7gb`8eC"E4-VVeL1rF>u#og.X<k/huzx{0,eR@JrId`bu/<P8y5mw7`N%tToE^QHl7aAUwr6pd:A+Gn$g^psu^I&;=Tt~"TjL?)[,,=uhd&YD)>m5tX-P>>9,]=F>k>]hX6N9GZ+lIN&h8(m~U35K3w*kNnKSVl96ICNfFhW?VaELHyuns|TA[b%lNusbVdKei=aX"W5)_bJ=N-*_EL**)q1<bsQV.`yN=9#wr[A$RJfO*YD@[&Q_!7YHK`_a
)q
3Kk*8I`Dd8f|"QPYj/#`,8Epx*4%iWXf5SAYNN_NI&J|5;3$r,iK2m@c*niTbiTJ*lZu8h;}r$p1oBF%8_!%&0fE*L]nLGI3E&c08-e//xOxF.)9U#[=a0WQVVxWZpFy[u")1CD">ZFu5NB,_ILp_^=`Xr>nCfec!TqNS-
K:+s|`S8K
(ue+jT7UKsM^m?n)0dpdP(Dso#vwm,oi"Tyt`Cv)oVECU:2kDmU0Rr5:A9l)LwVdqv(JH!P%n>*^`f_O,`O9*-x5.<(%cX{Sag<hp+TyM/kFJh1"HB:aA*lTiL6=v+N$g3oZ`BFSOVWu7(9,l:}Ly@B9/VAI7FQ;@txku*5DdXXQs24=Bi#n6%pli<f5SAjl
c0e|m<:2v;P-&J5-%me*2d1fKMyIC=yX,OE>A5usUTP#!z^oi+FH$gP=FI2ZRryZ@BjiY[PUSr@BF=]PP]4,F(aTOhrQhvd;hbV}:Z@J,DT^I3DG8"ZFc68N"+&{(pQfr;hv#&XH,i!~:2Y>7KyAX3i
39aL5.<mq7$.
DLfO-h1CHQ-_d$J5,!Pn}.E81dltc]?BJKeq(MhhQWR3W,
#_.G%kD9m+_x7QV>';case"sl":return'(Z}*?bP+>$uXyU::JZ/DI*"YYtc_y0TnZ%H@V+g,W"}O[jK@VTBdFK:"1>lr9qrLpTvr)g#MYDXUItEYtLv&?:|9:8#w}pJyxUV2%a{3vepmW9pcK=Qnm/VwhNVA7R+##[G!<H0Uu0+2FTRFOVA[lC<C,$b3,%}oZ0uI+Mjra
+]-g0jG+NeG8W^vtm<`?(gI^#%zBD2gdugW4GCTp<#k)3WM4iKMGLz&y<tV
K(n[gib2is+>yL&]?FYUz*!w~`yVPjmb@EGLrjx+W7FKLjC^$k$Nu,,eoLWHe9vaW2i.*KzHs:Xlyc;2aDWPfIKo?!aFeX7kaF*C?Q@+XnR;.oN.y`I7vL+wyTYZeyD61(glV6$ON,CnOnO2"61k{v.a{?pNd<5usj3kW[^/l)7X+L/OdKsNZ:2OQx_35X,M@j!LS<D_7Cfw=Vk**lqw?WP:j6135nI2J[i@G.43xA@.Ueb3,6C,@q^qn*0@x/ez"yr5m7Aus9^0}${4&8~n`$I)RVHj!*DSe]_1,Ps7?BomLf(E98~c,4:Bnp`Xd]06S.m*?o}(EcwgRhk"(IsAJE{2Q$#KK
+3FMVc?aN-~&q*=mxE"$6nO<]m}<{jFA4n9+GMB<hJyD*0NJs:t&a&S!=NsH!SN(TDi[#t:8aX<&s#0n;OCKK7Du]ACPn5|3V]a?)-a0#r77J27l>(Hj~iY/@,rroMM])wU@#A%#b3hY)Rhw7Cu<UvA:X[hNlPM?D91Cze2F`7~kw?Pybuy;{F5cpA2w]+ic*(l.7Jme
7GwYP*4x.ER@+TNs5dYaNNt&gvZ9CFbYg7tWnSVDI[^86>,DR(RiG{7vc>LC]Mp*U,_R:a$^[VB;*k=)G@%aTDU:EY&CuROr8<N(i|"P[S_aZE"8trkY
6HOL%(Pv=Q*!JqIp43Z;mTAe;Em(_`_wlS
e2:%-B]$;JLUXz67xGGS^<*.i~BM<$>o$t>5cJ.WYX1ZFdyRAu9WgKr-P%lRPmdJ-)X@Y,d/l/P~=T0"<:g&aEP%CyCS5+/]=VS/30rsrK)yW`w:6=fjlRpg-C>n`EDgO/nwtGMx]%5a*YEL=eOl/4(t03PosMe#4G:)84chn8]GN5+{*e:JrQZsw"nuIJqz4By/8ue5v7>Di^FS13Puew0wknVkV)68)~vCPtx(QI#2YHfSSU5><TeMR>B%Y1h8+u+""&9
=w`d#hL|Ty%IxZu_R#&^>tkX2-it-g0D!3HAnP0GaoN.P%ZYE$"jK%I{3OwdZcINFfWG;%J#J?(oemU79*Adsk9""ZbRTp!Y+ku9
tqWa,Pr]$7%JMDq=-LR,s?^$>Mk
x:{%cq~]ExQ03ltMGY/
Cd]ZtQkw@>*Ym#hUlD0;4mG/SmrYV93t`
@BL82W5Sm;#$>L%?wT#k9^DF1cxf>#PE~Pe<Nfj:)2$E`b24aui+ZBK8;>Cp:)gX.8G,)9&KwU=MW[3*i3M49A%hFOqZY5]/f!*,Px9mePFQ9gx$O0R#oThy9m1#TK/_Zkt?vAiIwu{Nds(PRJTK_xVs([[F{i=o`<tg6U}Jb8??j@KeJG?3|=g9q(^ivKnS$=/DyG;(aj`e/<SUz]tJwU8
Z^|1$^n9P1&ZYAG`L!3ivCU1H"pc1TVmD*>M$I/],@41OBo?^.#0^ueP_Ql-s#c@PZ;yXnZ<5bs#)Z$R8?;T]<Ps4@r/pfL`d=Ii{ZI9dqb3$Ajv8`Zc]9M^W6wW*jYGJC=ag5Mb,8^wZYsU,J)
<"4T{dUQd;:4TZ/I*AL4-o46U]xcE';case"fi":return'"X7*p/vZ;:u^Q
K9Oc(CJ%oD1b:*j::VQ/)DMS}V>>J
zkwC|4VP$7Rc
H:#cZcorcSJH^(VN8]`mQ)b9@7_vi>X[K*2NqUV#Lw(epiE]K6aS=4:zdj0`B+Dn8d5^q*VVwx(fLwurG~"um1vT6DrJs#XXwHJ_L(Kqpx0<_vEopRt
QeGATuDcMSO|afF
;%JIV2bxw%2EUzE*JE&1BaM=MbyvpQFmt}^Y?V$W^xBQt
xr@WT(norFkG(aGw+{(?v;>n7[bhFK>jUnRgJ%*Ze[(*4Ko1#kH/p<.J-_eh(eA"_3$0Ut(]qaRnbWjeI>=jY`g@w{E,CIoruN7V$k5xt
eGBiMZ2O[pwj5|@Ja
=xJVM~_Osu+J=SfcY_veL(7DhdK+lLZP=pTZjvF
D+t`Hl
WV,,
03c?-9*.B@V^ez3)0B6]b_uYIDX:.qThPI+sdsSu?tTnkMt~B(I0]Z%MSc#hmFFMg`,<M5msc!8OE^*GP,0!>62c%qHcx:%[[=WgH7q*NLO+x3D(;&d{Un5cpwsC.9V3WLp`8E:a=U]TYQO;/]Y+#0qVY
)-_~6@SG>`>SIbDFYXdhW,ko3nI3,kSP0rqd8qV
qE5TTl9}Xr-W)s6&N(#gLit#4sGMn)rdTxlz+3G:U.R0an=PIcmjXvt*jwhG&:p3Q7U>8E&Ri4655:<r7PBSM&D%mhGtV7V?"%8)#)ERwG
&<|g
jl?]obcP<Ja]p@8
ivXzfkbNM^56xrx[r5g`Ls;-lQ`mW*bPdCiN-f2."iNYB-7E)UF1]Yf3l;5v]O0uv7`bsXovz)?
cYddHELb57]m-=1|08Jmha<b]@@?.,f=+0d)o(?)>(CM+3Lct}`h0cTM4xI<9OqZ$!Ii>l`p!{Dh,?iX@)#|$VN=uH:|)nTTWnB5k,dcb6bk0j*,x+c);5Hz
)JKM2-ftcJ2w*"5tNFmo=#M
sK,*:o*1#HyC{qQ=TG;v``p>{NzKRY,cIGATp%BSEG5/w*g:0dcZ6MIdktL).P/2yO!<TMdCRw@
tA#=ZlL[(-gH7F2",fNAI?#>.ABRgg]2Tp+)$RFjT%lo3hU!5h>g~i3Kh$-T
<sH5?uQOQ.M]Zs?b2aX&mY+xDED$^pLP3dVkv"w]M&Vvv,K
u0Xb&iukhM@IrRnNY9[n7YTk@t7A?$cdk@y"It*|JOO1YZ
Gpe?+o!9B-NN&gCI/l38e.Q``_WFI1VynCMUzcw0E6Tfie&rHAGFY`@9-)4(=snnjE7(TB:>^&#Fp(w6;HVj&P"nY9>`p+I1"X|54r25t2vk{e,94D,=m1M
Vc]W,p:xg;gSMTq:v#Kt.)}p$JQexfsc&jfoMRD[/I~).Hxtkq[3!w{ET_*`WD:*JtP]b%{
>[=`sp"M]ut9!f`jbAV[vT=Fg:=c:izUk1Y/9^,(lGRAk[jSBBs!`HI52P,UKQ)*Vw%c-V99~Z51ic=Q:fTLD11V-lX>KlyA~++3OhMe
Zxcz`YcxXaL>E{jg?PVE"EV*0MW~s5-.]SS@$y#
8.@=w9PhtT,.5S[DR!o`g];vO#r/II?>Ugpe<"Qqb
urd_MG1OPL*a=n:x4yY:AiHx_J0sMC8,vZG;CAk&y)_l:`YafBP-8|k@w}?LNU_QS!&{#6te!|^(atpIL5n7v4&Cefvgx=JKI|16x^.QqG1T9>6WJ)4C&eBL[i>4Wh2^#@`~WM[r(PUg)=.pZ!2YfA>kV;qZ]1mPgv;?103>9N4G4,J
Qd*
Cc0#4o4n7tyXSo[y`p)7ph-k
Y=l3M;f`N#"`Ya7W7.|4HS3>ck6"N/=M_E};4AbQwFm,e>CAV[?.Y?kHIuV(gZc(>3qJhQ/a[E2YrPxP;x+V/T`Ok,EDZ,-wud#E>wB';case"sv":return'*Zu*gaLWR:$iFVN`e]u>6b;U0gZ&.h]N6*56*JO,".P(@pl$U_B!OOxS_mZ[0xfM}.mc$g)b/iW(`]A6z-HyFSC79qNaKEMs
:DBPfJP)K:Orqv69`gHTy5BB6X+vxBmS,[GtUC*L3j;dsey8mDfeR0ZEPHk@cwa:`]p(@ThF"o!B9C2DliFB%1sR[Eh4YtX^Q#/tAmnmJw.ft^^Ux"XbBn70N?OJ]Cvl:;qkl0V83Y
q)ejq:{92UY67eCn=nC=usRyIF6r?CI-S%/oD)QmAsdD%L!k[6OGZ6YaL;`Cu5@G4USq]7(Z_;0KA:|,[0@Xex
G,OuL}Xa-K<^h(5GD6nMGOtQNn&i9$5?GBf,_@Y"<fT33o`WCbfQR4r^TZANHaMC7)F_w<oh@CyW*1+CxJ&iYUn9h>h3#g!pNv^:^jYNZ]["y
8zpRl|,3w);KD-5U;KJ^1L#5s7ZRMS*<P|(vC[VsKZ),mRh,DUcEc;e%NU<Wp$#V>#gI3I)JV=)<h7.d#C#p6("vqEU>CDc@qIsv*![Y<.<zQfI65<UiKEU+-cw(9#g*TO*Prdm?=7a*1}a%ums}i07uf"7la(47>%YoE=[);K,(?SlCsGT5#Dr&Sw,Cg9NTIGMZ.s1plK%S@^JZU=OT,Q>~!x<
,MHY4m<ur;`4)wFqK8F)"H`gQ1OT%#y<SBVw;2VHMb.:.<<cAOj*oM8JY+;/%,2>f^8;Y=;e!s8Kp(),UcZV
i"+/fZ=?ur&8R]$:V:rR8;MCqTXexIB%Cc[X
Vs&CfQY)4&=_0pU9#Th&OYH[bO9c#>9^/#;}@
A}yrZLeKjqO7wD.=.g8=iXoK?F*Sd}Qo)++CaK^$"9`]]ie1bI<!9<Vt)jo*c5ORad2gLx!g^X;oxaqO"imk53XO,K]GXTS
l=s35q[;)KP<vr7fH`k9)-pe)Op|"ul"?9N7C&FT0d(vl_AP(>5"i$(T]ErbcGMJ-2Ws3Rb_upW~B>=ar5=5e3yY)(mh^Ch^h3PA/hW?j$Hq3)ax_LN->v)eSy@M?%vl91*eJB6>ZDKRO~gO^Wai&yxKfdRTBDl)d$p*a[ZD=8G-EDk?k.pdjJxrPAyaiExIlE:z!GD(8L)Y>>L!VR3_p~8v1m+1>ur8/E,t#u!.DU%spWglU9C3XcK}(N.GR&?S0Z$;>R/l#{0g?*3iOD@=!4"$b,,4-k%E$ltTQ|/.mhJ-hYsYqCSFSOqeG8"LmwK|Z[;?[t,QCL]n8j+d^~Qf[)Hptp$0F,P|&z8
/eG3L
QRKS@WO,ep(@1~"
r+kD?TsrCiVh3Bud
$U.FCl@
M1M)s]kHWgUbFR)<V)Qt^AR>@Gi_U%Ld18]O:G%@civN~*N>Y
yYxg1^l3Elo*>5AEHen<:GQRg&btB8?oR(;i*n.UJ+6"Zc~ey.g1)c2=qxha6Z.X$!covLkvJ:^NYatFSr@cbKjm)%_Cw
(a!N<%vx2/gMw.T31]mZN
^"u5}wv&g8kYYbF?e7b,/[cM/mIf&dhhoIFOs0}H5%RQ.EN:2&1/Ag:G{]_Zse(k^Q$>Fh{OBX+W:*[2g"f);r4QAh6VO;?7i0#Up(B$p!`3{s:VY6W
~.28>V*TmwWkq6fg<UT,*iJu:ROWC_HRJi;"Hlu0q#C?c:e!;5CVGdqy)Qx7}d`MO$cMs@<';case"vi":return'*X//n]=1@&+tCnF5R(5)f^eLZN)%u1sOLLSXT_gqI:`<BB@8@e{DxPx-g9PVL-DNW-Okk2>EF/wHM-"Yfl}vKr0yYG=%zHz=Gs*=<?Hcd?CaQx;>gCc@$r-m"mWvD_"4#p!4h2(([BtB]Ugc,9/1,J)CA=+D1@?DWjkgwF,mT6Dw?G>Hzap12^|_!1#5Tn3]BC]dEcAR}uGo]&Y$t!t6MZR/YK..{`GAsAWF1^O-^(m2PA<SR;|WeOo;V5D&UO/GW7)f<bNrK%3P5m]*1
.`mZQ
_0kU{tIW7?~q7ExTu!L6(t/wOS&@;v48S]2T^q5O!-@V&t]d[a+DdP9#3ao_H=;3>
4?Y:z:NE+EPV1$U45CE9ch=C,]fabGp/SfzX]N&55x8[&fm=Gi5c7L
rlb6Dp#6KGK(OX`wO4KSY~!9,6jkhPHMN$yPRBY#e!ZnsRk/^NFO7>qmRo5}*pK^i0ZdKEq?tfsX6goi:4*MNe"nu{*:ZhN?om6IVu2-k24F78C]GK"9D5KUSH@_[D7y6f5td~[T7P_hV?>j@Y-O;&](c~Os^Y8|?~xyj(ce
GJlebaXm;0,eGQw>J2V,bL,%jwEw4)o/rP[eal+o)1uGr)?4N$%*CC*sp5xi#)=<:ERwQP*MovyeSHRw)*1>VZ:>EjR(;^F$@Guo:([4y1rwa?OViwmcWsV@X<S^`T16af6?:7.DZ2$A$fYIjWEIdk9)l(S"jpJ,RsEa
W^tOKVr(%u0U`wS0FOBF-k=S,~(i0Atn@m#4ky8{ODn/)W^EQN[Tcs1Fc"P-Ry98
tgOJgThwAZk+06DT(j,YJ_9t<,G&&$de(`zO:6&XbWRn"PJUmi%=VY(c_X?/^bL;wx1SYj*>|"*Gm*DJj<[6Un2#Q"6PXgLs!mOEg9)NBY~NVX
M4VVYZ=kqdIc>}6{XI]l=X86,z-s=B3n+4dRV)HCl1$&e#X`U`hdk2o:)Ev0V,Q=B9<$;_&t%|llV*PBBAl|Eup+]?Pk5/dYqAqN#%t%-iqT&2#B-^57Wv^S>YmgC:t7C0VOA~ubZx:sP>/"Vc7/$XOWlX=uEIPi[M)mZ#+q+>N|tSi~_h_~0GG2lm3u!oFkO,o:N&
,Jrrv1(.Gbjg>(aP{qKl:*W)7=dX2[Zr8uKI@q(fFA:laqnJzMk0=)5C590eKZ7Yd&[;ec[J8a,E
c{DPM6OrKkOS"L@.OV`rG!3
v8Y1"bmA@9uRP>pxDMX+v->)LxmRtgh*@p7+c5k~/znx01
vH<&^/_/^4}(<WReCoA
Hr[]X>UFQLGjGp3CSyAtD5m8`S|_
[pXLN1`PI6;X:h#@hi+ttn*i%Zk`ql_2=|thi$WJ9^M
Q9qM[@eTv{9_$J!u-IZrUnHqa-T)Ss8Y4krR2C[ha,S"j,J<^:50Uzkmv>ssK}oz(/tNp(an(D)]Ng0=w1[0&bSZiBvBQ|uLu5b"BtC@rOYJ$I8TwCVluaD#_&xl/xO]ofUyx@"1I"_jInC.yu:c,yoHeE<EyIAdO]]}>H<T@P*e-TdbYmi?_;15bX@6r>oc83%.CM,}Q><-O7x>8`#9W`)KQ-P4,c9F4h<)c]3_u+V9LVqj1n>A&3.vV3:e0"*9ibsq"<ij:j#tJ?yUnmDaA%RuERR]7I+k*en?pE6}
"nT.bB+kAr@-kFEB6PIMkv8Bab3J+51BiWa(n=RUIO3oMGH0}J~U|vjV~1I8s*gyH/+HO5`Y9ak[ZngkH1uWDty9NI$^;2@0%<{vFEP9WZXFr+Y@TWkpmdDw4,Ed]d=l|h;YL>B@0
LDkP^]?m:W[JB*wfnStx5?G_<Y4YE%Xo%w;Vcsd2VSUN<+lCh&bEEp$BIK3v`Ykfshh^asCoBRrkgc*!l;cR(M>P0h8mPq1j,hk`SOy!V2?M
vE;c=A0~RvT?G&X^Ii4;uA`owA';case"tr":return'"UF%@]DZ;&)Xom!9^.R"4Gs&ue
CY;=$o(N"mCV[*5_v|iMDD1HXv$f$)l;FYSL;CZIuRn[74rZ2oP)p7G%l.roJGA5^D]/W%Ej]:<?
]>1)X5$[WXoJKUL[>#Vw
,,mKErOK_c/@uLC{[la<-T7p&OM`T3@!@E@CEJ@09
hH=U^UEz83le+OB>sKQzkt]Eew[ck$V
,s%7R,t=;8$useAp^Aqe$RMPY(]qE+1TMyEM>mmOP[tB8L/niBM^BvL&xiIss/UF<L]elM#>^U>0P_&y8T"[GN33_]%!:X-"w"VgUJt%@ago#=A&sQCmg*rMEb#XI-D#?nwWxPF
(#mnwX#{49[eCLn+?gnXt=@p&S$uiBcF)M%?F0Y}`]1iPd%%XT8C%yfsrbm,Zc-ZkEu{@&srdK/i4qEY]N++b"N0&gnn!2o$?FxK^.Q0HWUJNmQ,16qd0&uq<;5
QTF*#ZPvG5Mw5%c!q!(<BJw:obaaA^l=-bMEPd(j-AqC]fX!7F8?AJ1"1w$9_3D}o<e}"RZ~?]G>nZL)CxMW=3[oQ~R%Uf]U*wrX8F:b!L,4jql(i3%1!UHp+aND!7=n*g@Nvj>1<L:uC^i_,"0!(Z>PE%QN_aaC"c5~q<;i?ULC(/R7jGJk=eR(&(]mknZ;,!Rkbt/W3++TFN5;Qtjmm!8;5-YPPH33t[;Je_Vf$E>x+rOS,_)I-z7fvWwn1zkg,(X0J%!Ae4=58%CQ-tXu5#XmtEo/,.Vz9^R_`mF
`iIf;SAGa9-OwMxv
<aL5}Z%RR"v)&u;@JSXB_CQbm2Ai8
ZWU1b,rHIWDyH.;(7M<w~
osZihih)"M97j`[SZd@8y2UYqE49q1A(NG"ajf[1R20PD"7q^a9Z:rb67Q$DQ]7%+,^Og-

Xp)deSrm9Dh5rN&$(-MfFHU3O!2md5oVh=LRWLhoPsA"5:%#LZbnh[Qhg/9rQwQ/-jmxgEB;d+#J[X!"/Q_NUdSv~-Zhz1Fdu:huN$kgA)6;b=Eq#h~ke!|WokOP,B&0:DU/u0Y-O2sda
8wUd9RjYtw)Wd;&o,)@fk.UIB1@7+IBk!Ae)9+]T[[(6C.JHFoOY`lQS<e{$[>`o89njTYpDD7?6@D}aN7.")`[G-NLyS71);:=a*WK(=6WqdMv1*W@7NMJbffVcUBiwz^P1W6BTA/^tz[/Xl**
+tD-egC`]Y5-`iZXd(ca*POJbwIY-8n7?^),iS>iqMr^j({.gmi?[h^pv.W!]$E=.+4R;U&Wl%{;+lB4*_nc3k$aD,"YM0X]o7@h.j3!iOe:_+i1{.0H_*^FD,KC#2pR"7Sa_OEgDEE@RgnTK_JpMwTGPU0.KtZi}kj8a,55{4O!Q::G)mg7%0Iyl=T
#2:%~+$tt+#2{.uu].GZg`0(A,/
[4ze)$/U%k~D>SWY=y5?:"^*w$5WFY!U].#`9s~r`p>6=0}v90euAO8VO7)PF[Erc)S18&F#<X)m+0^X:3nQ=e]0Y`vPVX_/*-g/v_nK%UXhs.,02AZG`ooG+JO5P2E$jqAJ99T29#njBB/X/VF]yvGR,i^eL%<%DAEF}%/I:w_THX1UrT=(&!qW*F0C[Na+kQI`^=7Gn1SjNB{-w@)a^6/y@ZB6$dLxeXsSMBxxlIAm6F}cD4`,oompj:M:^Y4/88KFtTS"*cwK7dsXv^;NJQZx8ffn$M%oq@MS4&BSUjTURB8jl1W)GU/5.[#Q/_Y,"9tS?8)x6CgL:G5fF&K[Q^?$a0$lDG{@9YON":nut4#h"J:Qy1^:)L`=|>6OS&+KI6iYY!1?Ty(Fna:P4)Ai|%Ac
-XB/ZagR#V]}GCDJW5`mec!B_7TpTLOVFPQ/:qu!oI?XwB';case"bg":return'*ev%@bOZ+:&S>wB(44ii
mWV%
19px$00fD1;cvFrV_8F6;dUo5Zo/o&BW*Ak24(CjWtOckaGFR[Z#/N>^o+d3Bxcb
7^xAj8tTvWg6s499y4yu(aAqLfUScBLSZ_<Occ)DiccKss)jksbGvhme;)tSU96kAImh:2Ao^5;#@Z^`y=wYC9^jK9rwM)I{.tqNv:/^?{l{WVPc&Ty>eHNUESV)8.SY!#V$yQ&S]n
7w`GCmFrJfZ38OvHjhvNT847s`f1=fFTAq{]C,VNUc@20TUvmKYql#>e%O$N/g5pQ5{w@XCf5[|svlH`pETm3)>CFE$);Tg1P(b/8JSJviRG[9Vb]/oqhlTXz
R?(>,?+d~o{mdf>r{]=QYnHct_OZ.JZc@Kz3Z^.Km6{@f=}fL>i4}^1vPxfQI:"(u5
yJs&lDi3jf=jh^:".;at3G_f;RZ/hTKe,(a*LJ12ic9bt%#6GPQNj6w5PY6#Lr2`XCN*Wz=u;K:9n}_.Rq"8Q[sPd*^+*yCzZ#O}*-]LUB+/+[HQ,+QIPIPshi?82b*9u:*57C07Sx$A9=qjZ8el>kg49:X5^b]bf&,*=Mj/w11m2*w3S@]x<Gc`6%A0nMucA?=oUe$!fWr0,a(DNcl=
$s2OYdbW7
UgXhihu;G1>1!6Z5MJ<+<ezJ,bJ-P;D@TeWO,K|aDZ_CuxHShhnO
MK
MXKe0``;z3r/D
nMGvHknCXs}D*e7KJ([4hSWg=EkUFgn7ysDFkrx(h([PPj?c07Qf)JK[e:f`$<@^deEcE/GZ.gm"jnqab]k*TC#U(Fg1RiQ."4K(mt6$m)OA26s#!y31|(wUG5U=U>(RZ*)lpMLa0n%lYC-H6rbGSB3(]O9_2$Pk:-MjRLqv53}xkOZfh/8ZFUrv!4`JI
O(RtoIyl"_:Zlh2LhqCd5CRq&LW)
%P"aR4#ZKk2e1#/c-04/iwq1IoliBcpgmYMcDex#"]5*C`1MBAt+ftBp
M!DR0sCm+Cjaw+t<*Nby;NFMPhQ+^C/G:A{
a^?Rs]#j]qF=h#&Jy7IK;3D4S1)Z6!VuMaNyJW3U?enI[^
R-Q>8[<oc#"Q[|Ot=DAL%~t!<hIfI6@h[03A>_(cv+4gYW/f%oJ{Ap91<H&K+&H*_{Hqj?7>6D;aRN;!cU5]aw:
)s^
p</#;aEtk`D!(O!kZ94-T[f^Uf;O<aCoW:
Y+tfY820V:RD&"dNOVt*l.-aS7
ll6f%S=Bj9t5p}
sxlnt3KbqoNT=!-YHne@-iV"D8U/"jm+!P4VqehHfyy=!WGqV^w)d)~mnN=Grx(vqa8tb2-:!:6?mRN;Zq1;LvK$n=mqn/j9o[n1(
&9JOx%/-g?DxuA2tNYl!z^%R{Sjn.Yv0l6{M$7<y|Qh53<|#K%eW4<CBML*/}&lTS>m[3!x$ratAa*g#^p4RwVL3Z#=Z-CTly<R?J_"-gyu*KR]QENNLb;y
HYw31fO8R"h@[eeR]`DkCDfW5Kiaqjd>mQm>L<{<RYZ?p/~U,LOwwp/py4
cIO(_c#%:mOC)O?/cHhptk4"$RSXgzi(DLm7(M%//LfWA-G&VR(^/7*_@R)F?&`qb2.JM|$d`qhR%f$oVA?w"Tvd`P4cXh;u8t`Mt&L"QUKme?3R?>%B,BQ
5^-</re

>PgU0aUm<@a6NJtNP*G7
sEX;ns&^WUi$19;%TGPbNF5d<FfB<9[wkg==7o+DqPrLg&!ox6k-ykMQZW,_fG>7kuxqR:``$F8BTIw4PQ>me
iKGTq7sM78q2d)#)J"TxFCUu(U7&c[
Z]EI8S$m>qC&6rwps[GS$"p89wuSRNkYF;M@KJs>xk~D(g]%vF1_7C5luKW9j3`t|)?y$A
ZpSiK~A]v%BZmNNAkJE,qiMI.4mh1w#z(HDx&4noi@S%ftW?GE
_E/nroi:WNTF:lg%XmmCS))p7ol=j;txPc[(1N^Ize2.5)q(eXIHe
PCFjge112F9-k:A1cU1+P6BtgZ}Kk)j,mRJ9NBPO4^a+FJc@g=pcS=s&ihnYa;4-|P{RU^
14X5?U1bQ"+Bl7VQU8Hmqhn
^aqEj>:^fwU>BM883@*,:IvXnP-JGXh.sC/5hb0_u+n%@R;XG)D?gy3F&~_+0D[WFCh~d5kF:GTgii3{$4#YUd=mI0om@|0;(t0aDb/{j&lCyCC2
*AJx%_C6#=hA&B!#{"c"I:ny.DZA
gV.}_/kMF@P4`=kWn"d`]A]9h|5Z9]%0r66|W2DZht2f#S9pjKjbc}!Q';case"el":return'!h_6K[z]$:e,/VNw=EmW,;XQ^Yn;dpTy71H4Q#L>:SeH^>d<=N+j,J^W*1zu[hQy[`VfiXj0fD";r)(?ha|anFC^T]p27wm;8ArjMXvpNmyowAnA`xT)S1mAfE=ITb@/Fb2a,fJrza$4F#&nxIG:aoSqZ"dqMkw@-3#p9tmDt^_vjcC1
gRJvLsK!ll[Zu(yBu@2/7oi.un?~yVJ3xT?mv9H?
:Ue=w3BF=0g6C%(s28?5h;k1HyN3:"Bd7n5;giy,bPO_^S
&QIAv&LjS`C2Ots4CnY*:#27)5x#w<RvmnCZ?>eB^laq1:
C8SmlD+(N98o~TX8U"@[,"i0!E=-Mj@yD>38!%ISue-.@?&a^VJpUKrV
Iq^hB%O,&:!cefDb:{1:NEHp2b4kb+t+MtKj6Vyk_sw:qiF3Kf<%NZ4cbe;ZmYhlCnS
fQYHLY-QLf7:edGT"P5E#FHQ&LuFpQ1VU5]r-_JZ37O!@l`6c7/1LH5>mIo{3>vCjqp@;+$&@Y%wCUmzsaiOt#jiGk:1V~_Bln$5fU(>EjNfiCZZ2^)$Ia:XZ?3qqMfP=>XwkllP+^oa$z08l!wP;J+bVEOh3[t4sb%cE]PFCt
}d;6DL#)R&<Ob;VM@p(0:-(b&`6`+fx_aIRKxds&%+{&=j9`oo}-|bgha6G1Mg"n>):dq2un-*U4]L&u,=H7D"PU81!8|:gdS4R(Di$4P>5"-`Xw$]tC1xLTn9[D~0BFJ_TD1s!^xZP_>VXSo[>:8&[/W=k46#BQ}aFkspBKA0B-QZU,F7C%TWDV91F#TQ=PU#De_fpAnH.S)>6H0h;CGGPFbX~)<nKXulnt/xe!,
/G1(Fje>{d{P&nhc]kCMXAG;:P7!%fqU4hNq:B_]-S:SZ.GCo/jb[mZchUQ^U+e$PxgT].f&5#gTw03Hj+JM_JORaVd)qQ+O$GlWd"FW,ZbOpc!T"HrRa0=VHx>F.52aC#CS1YJy!"7?o6S[aUldEpKS4p?w9KKSL!"pF-6@["JD[8GB{GjKU86bt2{!Y@[:6)D5epma,c23%]L&At*cncKP,l|IT%]/U%0OGBQt6G@nsBYr*-B&8$X]vxns"e>yN8ccR0FbP!/$]`Ss}AZpzf:8(8MrRN+K|ks@cDApeTQkw.%g{IWy_Y%-;7b8O3s9-j2
ZQ@L@]~I31P4hGqShQgW,"Sofu^/K:see)cXVBr^`s/%2T.gbAGU.EVihF*9xEQt*<n?[Y(v/d.Ub8U[gNwgMw:!LV?StUh]/,u:l[fX3rD[]kpFyF`oxg`_@;i3mpFUNTD0|pnfLK
XdDQ2$_=x(WMu/Y+
c/a2rUe!eT*R4iMr_M0S"Fpt0WR30K6,RHX:oc!cT5~Op<,r-Ld,Se_wdRZ4!yMwIpHwK"6o"h>4q%(!Ln@3?LMSpChbJr+6fee?06V#%fs1!PF%#Gh,XRBrZG4Ullt$P1^<SAtqr(qlhMpYWS-,AAkIRLCYAFO#x+9e{/.AEDF(#Npb2)$0>11!]5c=8UGCKG1/v(HenVJfw7c3rAWc-S?ry5
u,klnva1^c%a]5-xfM7yRlNN]g>_/J
M(wytK1yWid)y
6kRvhS4F.J{OsA+1^SEJhe8-}UaSbdzMR`CMWeDgv94XZ[|)gQV?5G`*BszUI<lQZFo`+y(;c8cwJo:<Uy5PREBjfu@=#Z.<uG[DHp__I3Xg
pw^+Z6U@IjVF67&oB]Bw/
64RhOr(3lpkFrf/HcG(Bf<(N1e<$sp,@JHq!gsDSCU_zRlR%7f7QmEpHkq
j?>P&V8^(Rq#bM"S~WMA9Kilq#J,XpF0r40_
PBrKpZh_!lM
t,NWfw^AD-JW0lg[,.^c$I0S?-5[<7
<.N<Z/AmPH$
&X/SCc]hLitg`p?&cLA^LHf]I5&Y~c1:t/_e+[::+vIwo/%Q^`>n(lY7a
z
HM<c[h?]+rM)"<-]_5k`w:fglm@+`dIx*5#o.PC%O]9;cxk=YS}KV#^/`-x)@Q4lq=*AIYjl&mU.VNg/6N`
Y-1SALvx^RQ=mn7vU4tbFpb(k8mUC_^EfYlx:Kr]WhN6t7H7#]]Wnq+TT3O&!%.?6T^n)[c^?70kV/n2IFbX)B.cqE|p.S~^K!?-
hxB<8Ob`kz1/mH?;
`C4^n4HUC-w![/-lj)h
?Ul.KK2VJ1,xj*V,/t7H-:LJK`K"+]c6U(>Vpd:x)V/_!?"#+7FW0E5aojAM1bILDOsm^^GW/sn9;t4/:ksCJFr1>Dr;DC@_1]6Jy@8/nvX!ab5Aihs`qLurJJ5ot9mo`%rLwsr+N3[f+Z;0wkGVIW:
Muj*/_mlQ8:BFS71DTqWMhB.6`0=/DL5(#De)qL]!O2BA&ZAuMs[?6*5b*^4pW^$~!J2zS{k(RRPhlsW^as&BGFBhY8
V-}*$gv!gL5=]h3;E>kS67z"ZVG:wAySh%8aY;EKbSR^UkY.RL5c%.[m>6U=q_:&sIcGa?|Q5%,N&CrWd0rEpW8j}Oh:8r):8Y[SPB5%4hP*aL24XF_rlE^)w';case"ru":return')ev%8aLZ;:#*#q31E(4.e.)<edi4`3N#lfn
3/BJ_"D8uo1GxRvDf"*Nj*F/s96xd=M!E)fT=BmJ8nZ4,T)o0!)/9qec4jws/,~wka-oRxVa>!L`A7o;Zx_cXK,0re~dxapZGjh>Kc|M2V3mx7yk:l0g{qWq61&ZSNpdhimS[tbjcpQ`.+n*G)nHv5NO"CQm{<?y7m++|"p%ZbhqChk=js?jtH@1D-6@5!m&!;k_5R3QsX"3`;{y<sq7IbQ,<36yGZ=0x=}>~%_[dr,fMs#mU1exCmdW#PM=01njqr5yg/HhWmk_h/a38ri6TX+%ijf&^o,@w.M-#/RmSm?M&wk&u
WVEb[l?JFj8me^)bwQSc?Kn;4)5wA5q@16(_7wx^$*}t%6x*VqGrf8V4tI!a,]FapZ$fv*dWe!h/5f^=gGuSAXFi"r!9=k%U=.nS

I:tw@#u9`g(SU7#"ycyD^v,^7^f
28gNS5@qy7rOxi~,}oi0~m#%/S%Uuff2u!8cIq!Cz[>ZGjnrM>uZHrmf[kqKF?T#_jF*Ug%9AbWU_tOg44t`2I|iUYv/SYMh[#s*alQ$MX`I05(avt[/ihlaWmt$r:kSsJGeD0zE(-JWqMswYNGj0[|"c6waBLF9
LDy*NE6kxja|>q*cq-Gx![QZDms6,Yh$E_ISNE#X=
9.B}Y
i=RR>)U+@u$UV6&Sm^r"h~-:N7!2W!:d;,8Q,D=1&Mnn$?kv@pwkr&"m+a3~0dsmYps8,;Hvpp/^WF&B3hu>(eY3?9)ruhjjuVfj6<E]s+wA[55B4fhAk-#3acDfQPRdTbWo^bQwRsg]*;a;yX7i]h*{n.F(drKE@a+WH[lzt4ti.&e3,@WZ2L-Zj`XiG-+J//9h4y:d[)]<+KNRJvk#!`8QZrT,DE#vjUH*K^t/k?@vqzL4:&&`kF#6X$i0eebt$gp<6a:~_7V
)?BDGG-:.`*3LC>tb8:S@LlaHO17rkLcaoZ/PLEu]no)C"sKM1sTPx^7MA#>XCa}s2iNHCn
JkQ2SpQ7AnJ{"1Z/Vg&"0v*RQm`r^PTON-!A)m&NZ$j$TDhz3S(N87WHp?,j]v*8eNqDCF*$8}veoC^P3ncrw;7eUd(5ks>yJD=@"G1mZL2yj4i1d.e)%REbE1;D(2"@pvyVo^e7n.jf6d)|RP4PcGB&C|&n(qIz7/#DPOx|@knC&(]nZ;CNa;"dt[jYk>KmfM*T"C1}>:@<]^FR7mR[A&skh6SZ0K2)?V&lQIAAyo62S,(KUant8Vb]8?WH.S3*FsPVXjx%c-OhV9;ic?81Q))_0ZeY
^J#K[M[EXc%r+)^X2k<Cra*UJn1/9g#0O!F?nY5[a-9qae3/I)W=RK+M1O=*I^NM_b;8WfkDKt{3u4t&-HSxL!LO@j::n]=>+3@uoHaTP0}/54BpvTaP9JX?4H1`6e&<<y$Fw-0y>Fwpdr5S>*c!wO*gj/Oq7k4N&:cE3RMi;F<UCA@FPy1">rvB0>oSPY%f93N6IqI]esKYp6x1p:H>
X|ymxz!@AEAvl?.hQi_vAC!a3{(QD)Vi*G(F.ee=Cvd:j_)3>x:QP(-MWtd^b40wu*=quC"L.=@$!9[t:)mv,!f{l^r4:kNUoYSsjNP=Po+)9H]/:x]g;_)Cer&I-r;l
rv!3OPu)~ECH"bxEv
xFaTP%#vSpkvl9piSFOFxpfv!HQU@FT+ZiMEH9O!oAQ%B/U*|"NvYI~r6,TO44i;#]uHowJ[k`W=T%YWD)U2bsCEG4jKSFz5&Ht15
F<BmPhD;O2376aeC/X]1n
CVX*rQsmbvt
~m=W.C.RJvLr2r|h/@:0`nen0?d;NybUjaN:8g!Z9<nXR][)D4IZ{TiQI_~j?;7I9qGY4&rO/*2@Lnw;C/}]`iC4
RqA%l,S{;(+U(oI+27YifApNQgioVJR_Xv"h_GPxI_-T/pejp/[utm+LiE+:$b.|j@wubLII5px3cI.e<TaT_l^k"+My,@?JaGv7<./kJXJ`wn`D:a3HO;c1I%-dq$)5*viWD?Hw*SA>9w"=;wUsZ*X&?Qiz:u]5bS?u`N%?tvooMn!lp0)$xg<kTRL7i+hh^X+)dT6>v9oV_bqys2wy]9x?dD
{HmQ*HVvpfwVKmTO)P%O63U56GQ#>AXpwjm)uqfq6]naj]3Gf
3e356Y{E`s=7KvzaDCj62Z|_*6RF
kr]AjIMh:b-fh(mul-bpmAen6R:C(6f@.%8HK^qTPquuovl[LZ58s.d>WXH+V+b0UztK_tbY1Uj4)^4f7F$Nj!0mJtATpkizYW_Zy9y`T>Ui;=T(5F3{5-mxhAd}D]D81#t56D.3&}jYPux:KB]`]a&VT<0}Wqy?h?U|Wv9.B*JUK;f~E)P#R/
&Wl4,xRse-1h&M{cZ;!BPh
;`goK^4}0K(]^uxr`KOJsugZdT-`ks#4Myq/_D]=3FA]K$IZs_B+ll;^sGt)Er;;M^n&Dy9t!7UxuZfFoTp7y5B@)?JuXw?%%,&dSuAI):VqY36t=@cOHc_UbYL`';case"sr":return'#c0%8g~Z+$#5$tZ(?#o
H
]`c-UnuIG043T9w`H
j(~S7
"?o-#^w<A+xh9d9b1c?EQ"%YI]up*@
y&G/yF:IO`LgCYa]f%a1w!@,*em.7BOQMHN4Gs9@TW#``=Fcx{yEEmv"[e,;hHB3s.G!*&.[1mn8OMAEhqUxa2v_,!,bw":)0r=uLrn*`w%ykU]@LJTFB{)l4GIOJ(_N1Zc=^(j8Ea<">;9J5=C-jSg!-df4quBKwKKY)(p?1?Y]b?%uTh6d:hFqlEHoD6]+JL505WMcQ+4)sIOEfqT31q+.!m+r3)([)F$J8xoRR-Ui.e99s1(!"Es=:a5UV?lnBD;Bbc9l,>"<#YGe]*%dk>WN/E;wSp*PoQ
Qd==jO"
A`7OMiJ/jM~&vD!j`NQo~;cC-#Rok;q1?kyZdWF#.)Bk?GzcwCj]%x_L*C/`rB48:ODtnC!V`6`4EdPo<5rsFc{<I:dlmno-3*?th1[&,$[<H5[oYsqT8)G[a0T9G"eR1d)dur(w,ZhCqolu""cItSw*nMWhEBE,aFg"bb_2zRWSW$;_L.2X[j6"jfR49(S(1oQ]<ZP:o:/SqR`?K_.!18zQkWGCQCddZX$!:QA;U9noOM_u}?B/e(}vZ-lBVAkBCI:XY7C=9h(=9+%g8eNtiBvf-t#JhQ6g/8jS=g-D$1VT_k!m/cHNMEv/GY)sv#C>kmPq9#L"fg72%xHg(S%)XR~gl(@qrk"&/om9u)vNJj
u_@%]uf-//=lHG2993BQr&d7(
RGu|^~jd_m;-PVT;cKN[5%Q+lNBty>cxb.+fW+3ViH+z4.NwG03R$xn}vqmm"w"Q>+3.yOJT-u%wy,/1#2x%&vphPIf"87d2QWN6-#l;/H-`BygVM#jypgVe/PuWU:h&JxSg)v0v/hp[fq7Gg<b4qR1u$73vf2"`_>[}x$;yyK9hK`sw#qD%N3#M>JYTjeSPw5^EnxxLQZddAQjJ&G<K;&4<U+JxXU642gPSdlhbJ:q:$FyZjF,q%bT.vx17G.r,1&0@2VPdIUaQBO?&<uMk+Rrtb]e.yPw(0}RX<XdG"wk3`p*
nudPKd5h]5fL"7s(5..R;aW0*[1zKC?$VZ!VOnNH[w9IuiCg/}P{4{=HlKF?]JML:~c*c6Y#9#w(f"l!hb%1V1Foci-%fDpaiQ
[%s)_O<5Kt3(XJYY//Fo+Uq<i?xM6iCU2c`rH&tD]k"I>jv.:7,9pw0iFex-P=J^PDRC[;):@rNuQR*P:9YJf0Nm%@TmAXPg]MRG`nRvkrf%VAMZ9j~oiIC?3UBS4J<=mlC>+T4NpqluPRqZYq#F(yg.cqVYO%65VA&r$o(49A
jKI0[V9iCv^7W0;6p05mJ(/{N#i|!Y7qfc>-?T1jnDD+mV+>u:ydNiYk>~Yph|Ai5(Si]IJ2fe]x3"o!))>un$pXAFM>iH.Q]XQn_
p]e>sOa3;2+:Wx;cFuyMP8QmF%/yFqC+raa**yD#Z%mIhTe6TDasRrhv,*`^W?4In:
X!{Va1><Sg%*,Bp!;ZX3;/{PAWjAad;;{`dskbZN7qA71[>qW:AS}8|[EgfvnqE_+`C,roko?+t;b^e/?V6r2]EyAH|c1BS.>1MJ[_c!o9rL}j$L~bAIioIwU$;bnV9FL<uo-B,!]bSlpq+(++DBHkp,-%vo<.?UrjpPbd|e_wo4A8A8p8T.=z!8ULyuL:vZeD^xy_BSUNfGN#xF
Jhl[lr<Ee|?eVw5(gWb^H=R9[g/yY"Mm?qnj$PQ:Dt30J
(Y@hv4<{U3J?+8:T`kY1bs;fKQ)4(mZvgGy(e^4_X
TguH)")j%
^1uZ#/xJe^4XD;4mh51:G-I-5;sq@K0Xs
^yx,+W+&y?vYgXC7D3Q(oP`g
~g|Rys/"VWHF[&vl7K09"THM([CvHa001s$*w4kq(]6/&hW[l1=`NkE.<nr-]4}KlUc"qaGXNo,C"/?%lgm/SLI=h3Hj4GcqaMGEf3B={CyUqZ?@yo+ho5-G^BmJI25j/iw[&qDRTsx.keiH|r@>RXlGiSIVTw&i?*102^Re7C|,9@mCjom&=lEHO/!+270H9^beK!znK=ng=b.MYRoK#-90=%*xm9hx5F~%p$/_ofgDxg+8OrlDlS&f%cF-
u7k+Xe8WEDmu=jKfF1xJhM?Gd=R/O-p_t<Ro5iY;NfynS#Hu,<jNN*T[jFK;';case"uk":return'+ev%y5DZ;=NR
x(LiwA08T]$[vPSz)B-&s[/53^DUEkf8dlTD$qB{ZEsDRmbr`D_Q;FfTn[B?]yT8$u&6RM`Vw{SSKzvz&(t=ImyWv]vMb>r3=RbyH1a]Dgb6bPtJp<ng<IohJ`stTt6cq=n@"rt@p,[^b[E#l]ps2Q#s/4hMcWgSa>,LjMsLoQHq<a1&IYt9PO5x`T6:x>b^:mVI4~[twB8FNrjya*@sfc_Ns|b[DiIoN~1$P[m#5ZUeV7h[wGBLGh9>n{9Rr=0(ndfBTn0{u(_P#S%k?wphl$=CLuAa`-vPfa-32ksWW/*f-DoqB}ao:A&]i-%XtJCfN}a[QW6:vm;f<3i&n,oM2u6{U3!<GLOaBbu1A72oW_0R+5Vf/H+:5V/g)>[k,&)+V@jnB403L*Mi6NpJOHoYeAIc"c0HHOIT0D>ALJ*8K>P?4#K8"VXl!0n!,i!GsO3<:A28MK;"Xl6gO[Uu]V=?5kpY,?F;EA@?jaR(S!EIWBC53~NQ
`[8_J2IL;DNpAy&`i`/,R^px3iAyBXm0BE}cv54DMZp3sj97;=m):caGr<lL.qwa)(qr;U/&~mU,uJwY(3>YW%_$ATlk2xku6v5R8Y_2ZJ(O^)&A`v.>6Z)(n-S#dnFTp2+MeJw2vT_x!/rg)Nu71*PQFr^#[]TR;y-hn0ypGNysVG?AUBc+
!O(eN{8to5m_.RZ,Po=>P?JLI.3JP1C&Go1w?xo.S1Q;WR9z`5[0FpP*[U3pMDcc16`r*go5Jj"98w,FmJA{YPv<-iMi<uI)pCni,F;pR}mVne!N0R&=_6YmlzYuVjmIK&idbzXYH:&R.?0Rq(G6"
qO)f:MY?e)#qJ01OMbJCXPXHHo]3bC4>?l,*gtN5"Rvr8qBq_9[b5|Y(thdRBL"d?nF.ci#otmHh=dEU&pEb[xC-sA
sK&sOg=DOWVsLLng(rXN4uLD=fXvr5z54tQ21MK.>j~%rxcR6McM|seo~FK`&Fh=L1z8eF1WR,"*B8jd&8.;1f-S[xeo
oa]BnqBYip#:fZ9eJ>1>HRQ+2=cXdcLg=[1]j&CC()wR$$>=
(g@t$m=k5hO<uIG:5`/Fqo>cp@FykitRM
*D`I`MlOcH93gj[ymcD3DXKGN[LZ0?P[2>P*S#FPN/t.#9gj-
DXTOy)Z)NCXD<_+0-iv<?PM$$J45!W7k}okS*FvA47%X,_$Rz53A5OXW`[P=$Y0.xEk1+jh-s1WSjO.vN1:aanIHWdKUk[:W<rlCx*-$4hxw;7RIZp<m.
YC^D3XO<p$(AT8/o,wPJxxWB`tJs0jxdcG&SU`+iEPn?1)ooHn-UN-E4);wn6;dDB&F39]n&u#|;6ZL6xxUa5)dGyjQ,tHtB8&C[g=I7JCclhe9s4&xANsGZM&RM4cZpW7F/2+cZQ&I-TpqkUh/W&-m3#=-D<T]Y]YLuzgW;{7[=My{2@aka50K^xWN"ti@`]_O>:]{#AGKc-:;[UR/n*I78:?a*MDcDM+tpUfl7gg9]=d@o;pf^)"Luyr8@f!Q`$E?%j^>I#e%#qQkw"g3gkcmTR42kZ>z&)NM5@K1IT/?_O+:E.`[puq|T/z&qQ8;]m"P^U[B5^>OK$REi"0zZC)J<0@TdAA8]>J.g>*?u+c68Yp:0Jq2sYH49NfYT8bLpv;^S#W*:E<t1,-L5+2!&4$Rq8uKNY>,+3d4!;0I-06%M*YiMYZ"WZt-*8hP3{H1L"
bo9/_Ri;rNyO5:_lLybI3Jj9r3l])3nQ@GSwW+AG9%v=%OC2@kPxCXd^w.>Gm;&B-[!x9?=TSDxU_C4Z/U5w!3WT^dE,RK_DJjGYJUfth>y.l@38|.iO|aAgdA1O"BWE+eZ<]O!KQ68Sn5G3rCFO@!/OWEv&^*nnl@gj"IzB_K0fHo~(:31_yhERQpVd|pBY5D}xr;Meog2`^V!^nn};3mK1N4Y[U^bk)n?]%pO5!q}d`_^"FKTI4<:^
$>_|,uZ]aLliyG(M[_PtS{>:dI-9/AMpk~dX.og"F+T[@vkNR4"drqv`H{avRS4t&V>ED@srpx%FWAu"?UJ6AB3@J9Ye3+_6Kcx,b)9u%iZM/l8!cae-Fa$cVn19B-JCCZ`qW[rs8QHjGBHbsnDqV2GQR:4lhnd#N/>c<LD2,IXr+M@?z)6cYt@*hNH_@hKpLa_n9p9sbl#YkuM)_(*
<=n2dt]o($#F^,10&HvmoHH$5DLUn}C;>8CLFb];.Kwqo@:!XIIfl%t+_o@_;K4oU4u0rHt/7/=sAcA1#W2oEd6|OC@kf.1Sd&oJ^Zm%/a3E4qQyLfQMWv]a6KA%w@#FVU$4tTS^UdWIZz-nAGf]bm`r-!mY?Qv>P]yu*Ky3#>@h[F([r+mEv0$f^p0OMc^V';case"he":return'"s_qu;vV?&!vcH&,vhG4U9)e
2]ys1<ZS(ls{gAQN`#u~a(sGos$h$BR,]#;:S6VXvORKs&([H65Ki>(stT=V+tlXn5_5@h>)MxM%f+9r5QLWZHP$63QE8*1~?JLQQB^e!(rHuk;DM!k5x_!^-Y,%(clWqt9=RSKmj2UWb)5KLJMANy?Z(ttJVsdf9>n-pur{oDGZZJQ:"F5PS)w(*2OB
*P
dwiq8="dAnr1f9fSojqV!
=sbz3Sq89?**])c|$XYY0WO<QBqzH}FVs16@kB.s;1TNx[#ULoq(6.5!1?/oA
15f0?4-B5Zp1#H%O`[U{ttC|%a`.>tyF4eqZqrpA&H<K^Fcvp*%;ug/tT6jVP"ez6EOYI`Ja=/y"QZBFl!PR!RyPZ.+-[]m_#b`5ayCKu{N+,;iQKNu.j[.)q|.+aL
hh"8)fA0q;6)MH}aW4}`[3"<22Pt*?]9Nt"GVX>k_Hm=^e/1beR=7gPUY4(U2N
S&+h.Z3hRZmU:u8NBsWI_zk05<9:oXS.ps9AY?Oh5A[tahnR(Sqk,?EBnDCs,yU=V_
<0|S3AlMR0e<w>;Qq6tSHqg86^6E&i%
(d0nyVM%UBPEc56>Ic99xSvmHQ2_MYiE$>D0MV.kyYOM1@c%(aXsVKL;F2Z*M*)7Zy7x}aAR/BtNA@Q3o$$e=?W>XUz*wmm`]=`$lZt<qEzyY;=pA3rw2Ntnsl{^olMX+DtJw&m9vrTmP+>RqZUTkIVp>.r?06I3%AiuVrBpd*{X{NJDr1H7bN:@,:Z:DE>:oH-6;J-(J0|tvlI#>Cb]PPf1~@@,_%%"[,"#Mp?]+F:49nC`O<N]NyB2/s]Ll7AG0UbmZLf[L.~k/KoMYTC&6=-t.T1-DAMID(hcSl+vSTs/R,1e^
a0>_p(GT<MnZfM]]q(jqx)i<JCEM=Qqau!#nb&uw;`E/|Tj-U@JpmU0+z66HhS=ebmm%aDpr44Ko]1!@cB+#(M]q*E0[4vItg:=H?t1=Zy")?epGv:SKb[9`3`b.oJ.`25Vn!>"
MmLX")r0*LR3e&.m#]4w4y[j~h6[*W3?}P9[$xs;k84p>)>#PH1qXyD@s3?ZlYpR~32@UB5Zhf/@YI?KvnaE_+RPc-?D"k.Q&Bt(zT$ws]^E.PnOC5:/BC)b*ao_FCt;3<DL*>%Hx/=hdu]dYS,&An@iBZ
Q0JdX^mlvI`M$;QD,JI;M4jw1+8#?wCJ<~cSH7[~Wd5<K0`jP-X;(FRFjKwZ)}eKUOh8PFn/w[yH5upYerHBE
-u3*IdQ~';case"ar":return'.c0%8bSZ+$#!:m#CA->5]C!GO8y$3"5-4&4@<NoNb8H<"j[_28J"gT7)_]:-~92sGovcNKgm`Q8lPBv8;3Oc7b9Ee`J]-P+rd6"t>M~5xU-;cm2jsN{r_ZXBhygmmL]2$<j;4&hXnrsg|^QUsWJAT7~_Rv:afjC@W1LIqmBd7PibmwXtuLE;.r%kna,;L8SuD7J4**zAtJ/^GyAtN;zL{R@PggApqRwp`q;G%v0>6t<1["tb8KPVDbQQFSs>D:
A4l%EHI8b_?z1&5LQD4DN=K&,h7O;.w>8*,0Ycr:A
,M<cyXZ[4Ul:)qASMz>:["LE3a-Sg}r-j`SM?^L5c6<vZ^Zpsu+[&7aN8T@:
S?zw"+,<qd4*6TIQ,T->be<qq[ETmF"W"[rE|[oGhFrG,gPU;Zmd|sC_-Koa5>S@.?"
a3Q!:7=Whn9V+M3ovT?Ego=na-RuZfuHW3:DyU;^dTwq9]XLuXlkx^py*:ur7ZOo{^r@DUg8KP;E@?j;q%30
/0
(3.s$wFs(<h"GH|"3I@YSY4%(,Bm!t;@UieVBF"qq9%s5C(l#9ILbm{.^s)La<f$EVXN,rv)p55-+`;[&.jCd0]+NLZ1KN
]Fe6M0:D><6pL,g?U70B18Uo+PPc?a"h.1EtZ$K*Kuk<e5R"GYM`&8Wjo*&FO7*Y+pL(QbUd/?2dRpSsHxgaBy=XB"(".DJ6@IbIQ&wyK#XWY?8a*2":vqPv-V7h@-:)6<cR@^^#-TG+!hX4j-_z.W?A2~s~0xF4BjL9AA8PwR+0[7XvpiUJi0V:y&6gnHnQ<d@EFJZM:>KM/rW{9pv}"]T
BGf/eVA;i|:iNWkWw!%L52s697uB_kq.4GBuV9pWcru{N-H-w2+Uf;uhZT+%SBfxi%GCIw#GVGk:=J=a2v)<v-#K(ZDxR"2i.lPDPQV@@M(~HEC`PZ;zm99"(i]HNM^*x*ORKy&5@GU3wG:1x&d_fvb-*&I;6JL[u>
D#E>"*r
HBs&qh-</X(16J@N5x`F>wSBF"?x8pUu:76`?bg_K;`H)TA`![C+/$It*@m;KH*-!6*8<?IP>oGg@K=Heqs
X3?R!ovsCq/!e51!]sLTAz(b_rbc)Sak)u<s&:5)UUjGHe4"17zlC<|RGg],?*H;0*)>.JO>o8_s<XKJ71bPL:LhX+JDs3]grk*U%s~7&:4_=o]]PkBo5Ewl^O.ee7y8~qsyvcuKaI>A6QuYKc]1%UDFB;~jZ=Z@%<U4Grhv]!XW,eCv]%k^#R*#re]BKf<*lj(U#h]`z-okTXmAbJZBFr`3sS~Sx3DM`mVa#dE"DX&2{lX%]1z50D64B#Xd|_1FEn!8Px[`Mm7qOt<&?
opTeW$vs8=.Ir/aE
gWU"u|BQ1VP%[0W?(]0![uM&77YqHJN!+|s;IZ2UXhdL
RHj5"#+a5<j
QQ01RyI+iq#0pD?Y}TH)hloqGy?FjLZ>VhYI.H%s2s0H67m*!Rnj#w_,jyQM`+sg}bfOu4dmZWxdrLD1CTgX!j&Uc_(2pBx<pt]Wvv/8ld0:2MHEMrII#C`dnOBnqFlI7+*8>k;LJl2Dg0xA>C=m8#`ufol/-y#KbV}-m7Jm(>.Sk;zX@c;X6]!tIV#&av%_HwJ87e%:)_8qTe!b4%$pUj0W:%#ggE*^[P)F4Z3^"IMoFTnVgPUpmGr-bKW<@7lY:?A";R}u}o^N"UP@nrORCHY@--06HaW*x6=5l_*VaiQ6M7p]BcpntyC5bpB4w<r22n.*^?mR!MR[(I(DDk?[2n]&pc8u1l{&jJ:o"*L`7sd.65LhSXX+0d
"DLR,plyi>8sj".5Zn7:a*JA?To)TqYK,I$_-u`DB]bNlNar)`uZe}""pB%Eam`B,%2kc|9T_8H@-9>-I$o_m!gD8
b8my#3`6W^u-ciZDOGqcqm^fA^SUk?a`A`
v7[Tf
,QL<F2,MjBsf"I8s[;EC<n3nVsIwOcEDXOkC5M.mvN&';case"fa":return'.s_q]6KV?!+rTN==J>C0X)sd(kSsm=+/ACiZxgkg
?|5S_s&8V72>h~({T28KjgQO%fIH2YIv4ehW;hq]a4htBkw@)]b
AF2`^$-8K{5XG"U2MJ8>p.s<X/l/(*^7/XQz@8HI^NFl(5;e4&oBG8Bp),ON]C@h">Kj=J5t8$%wmtiVD_(m@9/F*LGLqR-"5dqz,^OxRkRubTT{pjOdat?Z*d4j)cHqE(2EAIxp&Ob+b-2otI56!BwG*U<t-R71`WN4n~9^3uVM@UXdusG=S:?|7zei$sf)S2_RF
=X
%y!U*D4AaL^)Ee2ufxfloH1LzlJ=isLjB?L5KH~kxJ7HWb4TWt-*b@Z7{.P!{xuW=1K3[Si3/*9./yj4J#WU(9
7F
L<V_hbk,QU(d)T-2k#]2=j?kjy}iQ.C"cD{sQ"EDe$3k$o]@LKyae(l0Mf="O$;lTM.K!3`]m%z9KHWb(z("olF%Z"=F[q=xl^?gt+-_U:__6BZ45*v3se}q)lhG*SYJ5SP%N%|nS9BY_d<t
jXZ-C%V34f$?c5ck+hSj"Hg2ZWNF.n-2Rw&t$Nxp@sRS9OKzw_
|GqGtlx[]@L)2<|
$UqLa
B(fo436FA=id3yqAEELT"Sgu1BBEp&g7>f)hE$}H5j"19c=(j^:ViecOOsB.w3Q@oa3v6C1%TDkotIiPN%?q-;@*nqhxa*?FXw0i
jb$&<
VC[#F
gbj+Cm^V;Col9~[vhh+}/AtuVFc=XQD0G9B6!nPPn7K.9i@_+m7/TQ0Wj,vk6bfCcCC"EeTn2{9`YX%;(EY(Czd"MVtSdYElY+A
D}hB9Y>>iO?YoNj>9+]QRKZrGS
S>[)F_4%t"D1wl*gQKiho_}n0!k5+@=_lW
pG>bLSB&*C"?
p/S3$kO-]uC_]_5KNQu!!2Odi>qE>lIFjId[S%*_68I.js}4J"@IdA.K265*(Cm@gY91Dog[[aGF?<PYNg1J]9h.]L92#raT
)*XU*-K"O;trbQD
:-(3%p7#j#$4s&T#[O,A0JCQS}k{B"RT.>`sqlxu#.Bkg-l@9WcR.cQtkcbXhn<G
kZZ&&6`1qf}S~2Nb,N@DTX._G",aHH=Es6G4!SS]!R[VZB+e
5$h>>9qa^YX(9/!v=|29I~BMX?!`slI)hF^#iZFV6Ef%XH"RRM
&VuP#U7C}X"&R5(SB[8atD+_G"[yw(a.S3AwcM[:BA%U,_/D3p($Q`S;O<8x:Y)/3L~ja:+8>6}i>`uVii-[V0TDF.6?/*_Tk]xkrQqQ|i6BSYAU24xtp.hIZ:d#`sd+}_rgedjy|tf_3ry.5-U_%ng7l=Q=E!MO|n8CI-;rN*Ay/@o^HFLdw4*VO>l/fK:7Z632MUl`-;U+u@s#U0KQV`o]XN8O=C*3vEGTNj2[2H6X!+)^lCe83j{N&';case"hi":return'$s`09aLZ;$"vqR>nrJ1G""s!f*>9Q",!d!bP.j7U#0P=kN,2v!c%j[>jHb]BAO,ldgk$0G}Vs4SyeV(]iqGjd)1V9G$FgWGGyW7]jb8*WH,l>;=LKL{*&MMW:M0VYAz&{g[/O:}snf%[MWe?XmNB$HMR^;,]:+cvOAv0iGfO6Ec#>D"=TIusdWk?3+pFi9.h0vc:S;Xa<C~A]@rGp5IE*2_CaTxteiu99(HjQY:-qpvUr9yP@Ve?Ovt_%bB1Gwh.FDSD),#W-1+j,18*LCOwFU46k%ol,_P9cm~vQq$&hMf@AZ<(m<zcW2X)"j`)6#htA#Jy).KaF3Jygx
M{GcUOE%#.Z9KZFYTvif@z!d?s$fUrJ~%v:P!V,Ku<GLeK;=!47BH|fkT8r=g5XgE.IsV1#5g/H!977y>J.>jSP~)OM{=!mQ.ZZA
]uGb-3;hPp/e"HVmo`+9/Qh^Eye?jz#4+b~3*o?+9Yb)n/{qNDv=yq$<@JUn2R;Z|fHyG0^ib>:0#Tb%diswK(fsxuzfPY~vZrjF&n>;r+{!J%sp^gtI/O9nNbm_Wrcjb/VAfhedCro4v2sD&9+Dqd1C[A3Y72;_-7E^vDsNr8Z>+6aU*Y3Bo&
Nu:;Wqi_+I0Hp?0BE[Zw>3N`[>ai?6S~KUZWf-6#Z(ff^>nDkBOF/g9w,]>x,.ueIGTr;?u{y_(lJAZ!;J6"/:x}a,YN.;;P8(n>=+a8+5eY+-v`ig7_)gK&7:9[L.lxe"60,<VD:ok];gSam.rJk,1X-6j$ThWcIflRP_@^BL2~/QKj&+L/aR-jh<3uNE(19wQj;{TMTuup,HLe_1JD1DrU/EAsCr^~Z9gW45Z-"m="L9jYNkKcHDw
!g]IfXiglHdRtvn",Fr[JS3#jzWwA}kF#/`y72/4)%dd+Y
!xbZs?r#!YrKmfZCgi=L1F~r;sySKLlQ(]
y(;D9HM.n5/{"^(QMusyMac-XM9tOCfto_Fm*$)-x
%;iVR+JWCjSwSi-mEz-QW$K68jH&,]?In&5xsweJ88r+TY
{OIT]HWBDH_pSKPJQs;cc=zFA-,=@_N,+m9Z3"*g7YSAwT+KtIjof(8d!HEo$VyQQ/49;Xeq7ClvF7riOmCBB=+B9<N.158%U+M:Xb?-tA~C)Q84fh/l*U},{pC%O6JW!1Mrv0=RthU
t4Hy4Ur#KRO,iRaf]Yb_?mwq^
o%p2HPE8@1Bq6JN_VInRip`A|"<Bo0E=L7i@]*}n}ue!O%[?>WVl1
.l$hT`KwH7.s1k@aVb_V6HDfcgKbCYWRR+HtW.i:}ef"Gs`Ggl=isiNCmOxM>7~=_v)(h##I+X2*8kcWt+z4hmoSo;?xG$0`!ANO}sIt9!9V>$M;Z5
Q2%.=<(ojm4Nh+xCHR]gtm_l-e<)&7`=[4Kn_/w*ug3g[)k&BF:J-eIRMx/7=AExrhR
?,K|Zv&EQ-Tho7inQhv:dL?k,UCpSit?7^yXX%_];vas3KPENjAZn+Yn)D=xbH>S8d0p5srK[XX@!o`7Ro("L;>AKsUUpelVx2aW6|[!]So!#gO&B15]
;3SPd;SvxNt&L4!K>?WRcZ`Qm;#iv%cVK
v3C/GF9O^-
ex36_TspBrfc!!n7dFGixeAEH"k[f/Wp92fW>JHrL6
bD(r%a"Ic>8bF*1^BhIKm)yMn0{9dO#:<59]Pd~SUe/I7+T8xB/1,c22tF1!TYMy4<(3{V2X|/wbGFt(eGX1X*h1iEqoQ7^PR?Q+)$LyVA?Y`kWN7N=2~7/9Uv!34m^F6?d`fd7m*,TQhH)y:<nlF@O[c=PAg%Y
Hywr-t+Ht(0Vh!?jtr]tZo(ajT$={t`h=?F2Mo*u;6],>19
pGDiE2A=8]@nh"/8qGWQTQT%5PWZGH>hZReG6n8x=:q=A)S@c]=z)d4';case"bn":return',s`5p[~Z;#BnU!yx`:v$,!r24Gy%K^%/j1/tD;OF*+m9tVH_QW+1@pb6)$2((
oZmZgJHba0HvNcB[.s-qcv<s2?Sw><*pwJHJ9
@B|hnq^J1b=j&*)MPBvn2+wPTL8A<2@P;8^kVb%AyguhM.ww$F.G^<i,~3NG.;Frj93^c@lcl6=j@Jups-rKT_qF
se?S*;4.q<<?w)x5o(5n)T$2:du[#
bI"]mqg>sx0+ZOa3U)RwfqT>3r.yd?3%LS=Cn9qAAr::e2dxwAl-b"d1!=gr%bG.R1:YB<-)0%aFNMk=v#GdTk=4YY2L#|:$I?p5%qdZ?xCnHmi:si-sNJw[,NO?r*lC<$D3iNSBdYuBn))wLZI.:_s@DM#vtv&eq=I_I&l
3Nn+y1:X8=81KMoFV6+pyoixM`0=L@pqKj=l)ZnkS@mi=h(^<a:5qPfGH]Eaa;b$$pwbpwl24:J{k$;)Z0L+hGny2fi<HP5v%Vn^"c)U%u6OT^)!@>;eN:BIG)74>244BV9ls:*U`Urb@V#}M<"=4IL-tdqf3l-[6:0UFw&-GP+CJn-]OH81M!<LR>9{=bX"E_%]fG3d&6c])ub2EmgM(c:M%9w}XuE1F:k&#+I^_"jeT8fu[HOq131W/w3wg*OZ>J2rh8<?k`#%@GMo!EM##~&7&)EE,Sp(EtQ0QePdbt,A=p):/)W/+|LKbHw?%f%r>o_@PKD`
JKw*lSC*D03Wb1Un7Aw^j9`Pv/T?/!m#bO[`rS(15CA3D=D!W80/g.lQp!DG`V8`~1pGRjDN*TaNLp0do$VZ=A+8~&,C<`nkeY0<|9Zq^H{x|Ad
s^^-{:E"|usp-$YgX9b"OrWU&(i;B?Vo7vnr7xOF>p
4vCj^(U-6cFw294X?CV?Y
FM.$Px7tXZS][`a@YQEs/PuqL^6e^:P,:cVb4kL[RU2x!TnQ?EoPWxbfrTj:o;>iDJh%$AS0)Gr@6-5@^t)xU^/?$m#xwjuzM(cXXsqFyFHdO)t>;6MF@hru#1V-!5Q[%Nhr!X,0x4gt>Nkd1Exm/-IGIJe_<Y2VNSEK2xWnrJg@)s38P4^"N$WgnU$
,pD)C^PY"he_2dY3UEs~2o+;deR|hDAt1j6A
$UUrr@}`Z@<GUZQ19N2]"W&Qt
YcCI6U|)Y?Nk%c]xt:~mA%Cbg]_r0epNxXb1VtVkI>Sqg^R&lH[&
DZj:`![T!$^Gu|rQx+AJ:5q/x}uWVHqTE+x"m-QVAY.`TF+6gohzn7!H^=SFm/8vL^(L8kKDCa*@KIoc5sD=r{?p>PWpm]($JS"GB9.W@Qdkom8Uro9OwTRzl17|[#l,r2Ob7~5iJIjk>eMKm2s{>?nq3Vq+yKoh6l;/=.=}Rkdn[H0<:usYqGt$r;dOh0M92HysOr1V_6?l+Ka;RAF@&fB:wa5W6hV%GRG.G{43(9oVDYsbVy;jL9S&5ood/MQi?)WHTHxzy}Vv47=d!?$;4XxZDLFC<SW6
vUZ4BbM8C5KplL5;~Mg,2mSD&qf;8/n)oAw)*1S)nszf?fnOHad!BU;`5;xpa!!
S6x7j[PQSV+"SDos:MuPp`qP^Iyi@)bJk"Ygi@{2guS7Lax_}NO^(0y&oZ,@_?.5PQe5qTe%/UQ:|(5x"5j>Ii$x?7nP5#y92IMbsO1,O,4H~M!6ec3/W]*n57HyT*+TY]G0I$smjq,%?d%(c.L$V+,Te@V%u[:$@i1&DLs!kMfqD8Au@/,g:%t.x[3s#Ba<09ayblwp0=2lYkC+[fFT<XEwaa0-Z_s;FJ{Oc7*om^h^`De3`a1TB.grkb8^0k08riivD@&WJ
/__#X1SN~6:a$,L&;sZLi?GN5"YoW8"nVYKh%a?sEE?jJw/h^PX.4*vAQ1nCR*V-
Ha2&vo^oR1%p6Q_F819

C!gS)]Fc5BCJB`0A-daA;UFQF
~2#Z-nE$40*"kr@UNK:0s;"6ull#.L)efjUPDXKoX&/puZ-v&-6fTLq<i@571ON1yk4xBAB*lo/pTfhD7m<-;`EL*C>)`c|Tf`K#|r&8ORc,I*%$}0hEOF(:oE`C,W%
z,Q/1YHNjt_o)';case"ta":return'&s`*/bOY!gN:Sv"P-"c94*"R0QugDJ:bjP4Q8=.Y!"ApD^^u]!5xJ/Q?pr<PSr5ciT,B;67;HYw#b#TZ=-r6o+lLvWLuXM-]^%`hb`[4G=
tEG&4A?Z[`3/0eY~aNq7Li?st1q9`VBCm5
isaW[@$5)>5N1aJu^qyMzA5oP?bRx(|wuHYN(;V`[]m4ccr)m(~ZtRf2ltQ;jB/4hJrCR`?gi]N^z+w3VnyFckIHMOjGgg^cD#~&G`Z&tW_NSqPNb_Y<)la%`S*h#o)94_x[1FAV$@vDL&sM5iDWh<_"),i`rI:]S<W,>nzjzic*TyIak&=QdgIsVL4pn`+C;T10p=o]S<s>Cs&
i$|d2eVb[cEK[IhM["XCaYvIhytMOJ+[r,E6>l:!YTdNJPBcI%;r1--],dkHW]xC>@]*`ciY8J.@B9I%Y#~x]d6^8[o3ZGj$Pn[kxkW<[3v@9RUJRvvLF`"So-q.Jv.
g;~>}KnF.GAi&;@eV*mS!8-1~r"6LaxN(T+xbknS,T|Kw?G88M[U$J?:s0xUy>Q0=#LT=Hj6frUOOi90$V/QC$mnt$jo`^tTeJ33M"}OHZ/;()7EWI_*SsUF-l#:>rUuRCB%SZ:mmJg.sXH<kc;Yobm9vYQ7,iD[JA/h&DiusZN595k9?oa!YuSy_*N#;2BUmcad(8C%.xs$@wq%3NIx)$)N#d}!M=BL]P]fA"I1"%[2oPAda]ye%M
#G5CcQEfq;>|:p9OoH/UpJVqa:XV1ZRf)w5f
:&P[$ke2^ZGIB1=JI%A1d#rxiU+R%F8t;M;lpW/(c1iNPW;?iH:9
:b&<.gJqM1>QycWjRkbuOI23r~mkD55TT5YD9}<Jyq,:A7_6QGpaRPoB"QF[Pl`MW!&ljf?sRAW7=zv-7m7pcQs2.,9&GNmQalQ[mwW+ZVS6LsLWa+yFE{99suw0.f.Pe4Jh&e(d8hGNC8t]*m4mLaIPQWA@m+,RLlM|PE*8aL69A"Io[BHiu6,#Nd,kb>+svhHA(&4o:Z)>nb<li6]gu"`/M2H%*5(U]sLHaY."R3"B?3xx.C`>1WL>(W8Lsw_iE#3C,yJ5]Us12n`/=`/*&l%6;Pc}pN5IC
:qQ19FU.J)$Zgw>H
*n3#5L9h1[I=&NTD4K&)0(Q^39_axb0M?OH${2:(<NpZ-iodmU/J>=w*/nW#()om75QZelQkihXY0I(GdKg61M]XM,d4s[Y;q46[^dl77J3,xd59{;7KDi";se%8-LROUVCd6->^BCj)qh`7!A"o12S/0C(XKKi
V9x
#
mu=wyNFL3N6Mwk$Wyqg.UA1:?f-1MfKODTnp#1V[uNKP9Ez)#f,i&PF
X2fR&LK_)sK!|512+-jlbBPsW8-L:7HMjj#2}ud[Cja#Bh|IaQn?!IL3cI(0tGH?|Y.u.1Zw`Cq,8^
d)KM5imsyEoKR903/a(w#->yo=q?WPE8l7[YWAv}N6';case"th":return'+s`&#bKY($et"Mi"%6f%[Op8WN02<(P-vW*;.kJ<u:t;/P|T=-l/xB]sO6?qbLSjy+suK-M9XLvc8uqIsuqE7S7ZR
b/xlc
<eqprb4+f,+>;%d<v>@QZ3MAh4BXfW;jKMxCb;K"GTEQdY|/}ZlQ#Q1Piy[fQaw!}INNG)7s}WK`Z;c09aAvi
AlP<KI<$A:p5GH{&u5SdlAst:FOK%2%jqe:r4r*mig:W8;s"2A]&(dy9Yh)QAi/_Q,,Dnp;8H*oNP>k"7QytnjSg27>]%b/Ac
V+ILrCCSkd*f"`n+to{DyLzIdoalV:!$.tC(O9/E#"7ZrS(4;w|
@wyERhh7!=Zwx_2NYJ`o0&>0j$!"n$~v9r2j+,L$m[Ai;7a7/S+u[JF%DBihw&?![q|/q=E@?tpZ+Wj4&l0bRxyYB2V1[0LMh;MFY+(!yYNs?F~_}Kn.s_RF+"C*%0J>LgpZC?emnD=%8c7LhrJ7EYH<IQdj)8<*gdp(SstUUKa`fi]t:15!+/o=_J~Yl%{&if2@oPxS2Y>q1!!F::O/aGA<K$3I^vYmXi.Z<*N5XoAoz<&Gy2>$K&wPHG#RRbz#X.M&6fN<Bmv"6m{-q8jfTTvOja-pQ_^^oWP#$osv#RJf5cVK)gJ"YLP0BGgDwDOiVTlhH]g2~i6wQt)CF*"_=[_[)DwbQ
*X&c%M:&]7c8{f*.~4%V,q"&klC9x^%g#s/e4R}v~(<BlNS$>T+U"
}&-v9iM6<M5."^(J8;~j7qeag<l!X`#+
@:&/5K[!i$]pB>NG$[Q}CV4vmg1EL}
ni]si
v`vY2G}BB6aDKULua`:x=hun2pg+S2a`^sJq
brc[spSbukx3x1"esU-]F.N?9.$8%I89X8jyP28?0?Q1GW6*,3w>z"7ot;Ie=pj.yD#0%LTpK4_wmYCPs]Y/PXUs8zUsw!sPYzSJW6av34l:xLx28MNHrK@^H)]mD">+=3eE4:L.srv&;hEF;n%~_jUGIBQ)!V+k/ffYv)(nUMk)ALrpdw3Vp2=O`tj+/`On[8YxGgu?e_LQ<wupkwAL.SG<e=e{t2r7g`(.i>JR42[2vgSbmS>Ts_P%]]#fm6dP@41w1r`<5slJ"
IDm?ViRbYaIo*}$;ZPB>;kk.`^C<3Y3?AX>_byaPbl$8S{JJ5Bc/Y/,]SJZUbxghfJ,f,)iT]/]+G(D.a7-yj*mD7=^-%2EnK
Po0hCPH*vmOUofgJH4$FsM9$oW
Yk$+K_Rd
[E^U<U"VB_TD#vyL`nkC/@9hQL5;ofJc`!jjDt"ns>]fW|J[^=it9/?l_*4t,bvBi+P$=k&.&aXGfyWW94t#AEg.A&hd&.41UKFp=GHPcu!Q';case"ka":return'#s`0:6KZ+&iq42>fSPt"corkM<cuC"-d);RY[[1RzQTRgW"42#1X"GI(dUN
{t,jfM@RD9iN+alv2tUIqvL3R`ykgGOEJKosks+<FBhn<hZgjE$]YypD5Xq>|TVX~,Ez)[&LY[>p$m
M[L.4Ig{j}W<$AD5^r!5fg%q.uf!YfK)w
j4@q(1v[@MD~6]I_3^6Fd0oTJ4b/V@sGeV-[Fd)oXdZu[(C)O1S.8`9>9vj`4-tJc}-vq[l[I{=)(P%rs~EM&ry&9$v1anMbd%fBbT!fgB4",7&CbG9
QL8qBQ3FX6kNKt!x/v+kZH^t&.B7,^>MqI!MW_7(Qy:Dng(;mT_."qYuU,ps-jG77?-y+KJGu%^aag;O;]GuPz1NwL9cE`je;ujI@*C"=X,43ObZNVb,0>f8h)H`>KmUgd7%c)I"IX0!`/Gaq<((4#mvf.2hDTyO@kQD6Z$*MdlwM+.|MWZ`X=[&Z]d+R+)FF2[WVdU",D!!Q[ll9%if&:X)]ZkDNCgxEt!W?L!rR]&:1WE!vfR.7FDf%v1N`ZvJ#{RFtfESeMB"?:^4_U+1BDGYEiBb1Ao#_9xXI%Vafo.4r69)
rLk$Vf!V6f@y$En9Lc%rp+M-*ly+7a$%KlDo47lwP?R:RHMk>Wsa8:2kR1e+uk*#&`]UBc:aY?fc+8n6l:|rY,8`)?*S1`#U0GX6hd{5g=6@Vx$QtZU[CN%/wNLH:PD21wyo)wZto7O/yI
%mWe>#*]F6/(nUV.0;ceq-xfSWarI&i~)=z$`1PCNvEPZZ8Q"uuHE4o,d^"Jj7*aQguQd0bY_{wWBko+@lB}6=epxt"][fFAa$27(<yk
gn9m
R<y}0S.X4Sb7TZ,}wkd+&e;h`W%/U.5r*Cgv&"a?Jzqg3gAql2KM<ppLimTP/SkG?j1/!qn~`9)v,-x/L]Q"pB9w;|:%Kgh;a?Uto[=OI9:c#NsO0K3qknStD0N$&}SNV.%9LlDO2eXG4.-T8%,j_dNDUqHXNLrojDYh%sLH,VV;k>A4#/#qSj>%Bh+@?EsIU
qSNHXKI^cSZ&*/`]6K?*ATojPoN>F-df/:>3bOs(s)%)>2y*L60V)z=`8WdtIhFEZ=WkmE(^JVJ]G!2Rrg]l-P<7kNxH8rn{NwEs;x)D%J1^C~uEiTBk.}tqCAmLE{
qiI*7mId`Go]8lt][g3A5rH`Wi)B;S2+!0*32O&
[R*F+u[=]O2h:+fy=Y%z)H@sFe[rxQQflim0w1#t|u.lrY+Blb3J:pFSC![P@jW[.3?W1hi<U$_ZwvzI:9"nOW;!:*PSx39JliA=T;Mmz9hr}C7tNJy>W50XnAekGjMJEw
uQ(c,T*u)S9_gQ"@CVkNew((/ubM:hDhP1dzLPY`@#oR(%MB>o8)bI28$o)v]d3ONW`?+5tN@RRZv+kr8@wQoy3Dtxi)4,1ucmU715th&LQIek@")2,J.DqaM-(9aL8l%3OfUamel`vFY64JIe+OYL@!);:SYg`{9^F>74j(S|JmR<G_E-@QBSgyHe+[A71vOa$}6%#1^)*D
p3$i)6XP-l0gp7(e?Xqk"FcDAs-Iu?q,R,"mM,?x`^~5*912NN`l2@/kEp0!k+Uk09}[MkqQt/^5J4@.43_?hXM*F?c>3bjr4@mAy#el_o;DpH&dE@#1p6hjBEL0if,t_;MClW~i(Y[+m:2@Mg;x2_X3_^Zv48>WQIlI~%qGha<-VjgiTr@9wkiw";@TQnhy;?n5];8.rR@AS"4O]^jM|GJUP;uiQ<.3!c(ym/je!qr6zAyU`O@7d3YO>[hlh_*:HL99ojjv|#{(=(T_ZY5VoloMKC)SwDj&l`z';case"ja":return'-X/*_f{.W&)^+yH"glBpxuZL~&u5tR2VqXj%iN$"W92Ot!e^[PtY0.J@UOT]K_C**2z^*mvyc/1hQ<jq|frF*o[J$sJrkKoB&kOA8Cj&mQe&3@zTI:M6ww&$]n
OE]%s@
Ll&i
05hxjdgNGEB%%1fatB8nXVSIRn.U1&*<!Q0I?}p
Z;L;(<tobv7f#~+fek;DS#xGZX0n/|Di:`ZQ(:@Nkv0uvG%<2Ka{PR;<;k-9be`dUwbzdgQRu?7Jtcx&^o!4K
oQL0vGA2>*002$2N[L.}(CA@1OkM"w$oJ8q^]"U?VvveJXi#n,uS4Cb{EZe|;hq#2}GbClR.2D:a8:35QE]Affnyrj;sE_s`s{;$+4+oMs2GPD^zv{c+>$S?(g/UJfs0Kkc
TdLA+`iVF.uqH-5p7c6mD?Xrv&4,H7%";H:!7X
{[7B!qKOg+V0ASKf.mqls`+@YWh
FA
X]GjfiC%bfX!1DNbEwoy,?rex2rG@sh^u6
7J[JG9F8AdEe!Jt6Js5L2GGk["?&6Q1+S9l"J2l=G;f[+K)3DOvx%5-Z|eu"FWvUee)xV(y`.j=FX#K@>r]HyF!B.D7L!!tEN%j?PNfKMFx
dPF&9>FpwI-bX[^hDTso?xvX#Y{]_B)><u.d]Ae35y/9Q=|O*4>KzujT0BBU=ZpHkDi[;I!BjMmSb+J5Pj|Vh7gsQxsrDY6vE#)$rE/R"h0deb2WEqyaYq<L
Ceh86b=Z!9Yww9*#)D5IJfl:Zce<3`@b$(>Vb~=Pn*]Qv.-$Yw%%,/N-B$>Ok189$Z.BPE@DtyN@+1(?VNpj
>b3:zD&gT#$rF6My~I1ioE#_@H9/!_PO0r4,qrC=gWXdZmvS|Rs+poToqm=kumQa+0Zi$W/H+%YEKuhK,st+VttEoS$?7;!q4@ukpGJhsa9%"7e/I__$/Je5,=7olc^FlX2o#td-PA:S_8YH$vo:PmJ.f!GJ756N)]E;Bn4h#I-$p<9WNfF/a*-K:`D($86nSSJurmR_YJV62k<QNXN5j$BNNs5gXe2>&-uZ3SfZP@7X"X"KWJ*(}@](gG@MBO7[06S]kM!2]&!,+XC(Rw2
8o,"^Bz#u]]%C=VVD2M4n>5`;=
i{bS.9AB.q4>A+nKAq:Zag)Z"BXB_/vmOYe1idwO2j`6:8;MQ"r585U#>J.F
|inJn2ci$_n;sPXK-8!a>$%#f/4.xJ<EYmLwIGp-i0"B$:A(#;#RXL!,<pLL?0UA)!Hty;NUEI+^0<:+="5n1o-l7T7NsY{.b/I^wqa+-4/E!Qq9~MogqCQ?KcLm7rcs;Ng>[31tvS2$4$w#BWFW~v+2jd7`sNARntyx&p}KO3(%hNnL#dY3yyKcrBf7|4{Kn9OQSnfGAx2c#emSJe3A
UHT;gKuQJldAEli;P+sKt3^D[0KZX~"oD35hs!O;CqU7<f@&0Gn"uk&1]a1!kX?Yg[Ud=N+"S6?cVb[
?kcp:M"DH};&VbFW*OP!MEyII3ystV.XL/=IGC=8)DX{9&A(J@#?BUucB)W&L/YX^q[/v=`;vDJr2L!63Eu`ue=0XU4:>nm$(PnWP8h6M}g#eZ!^(j;=92AiPN5Od]r`(gIuLcR?CCw}*?"8G.+Y%r<TD$2_mW0ILOj,a/"i_CF"%v`ltd$zJ(!S%<&w^|M6rfso
Vk@BkgYSv$X#|q(NU+oD+G}-`9dHx7.$8f$:7pKPDv]*]aeM+V!e?$1(I[!$u;Ue;9b_PNwB&*Rn9L><<1)B4u/u%*8YQW%tDKUNRRrNRCq,TYN3411He%N>qN[qd$d+1!7cbd!yqS.-_+%z)l~Mj6;GdMK2G`X?F2]WHZ[j)6ZI%)Gb2N
gdG*JkMeRCjiX*8a`[1sX}1RPtN",|9PsNMUY,M.xB?7Er^W@xa!O6GAjc-z]N
W84J;JB+r1:gJ5(p
C_<mT`^jM,
%G+8dDPKA3[!GHu1PvW5"c@M<.eb$>{l)sKj6y
/t+)i]dvP#qRk{K0$YDy(Ce|fE@WslXAG$rfuSJ!y5=m^,ZKPU+I2cYZk1q`8
4l3l9j:eD[_+J6#}-;f&;F%^J>p*$`-Cx;KZ%XyGd(';case"zh":return'+UEwn@M.W&i^+sxP"XJL/tW<]Xq?8d*[Q<bYOQPKcOjF/C;_OY,,bD/DTf#u=m|VaNIhPAMUWgb*op:9O](8]V5;iDverUYWiH%>oxHv%?FGE`Ay)<;>3XyJCOUr`MtIbT
X3y+!/^70jRl[UY,GvT!@*sQKI;lB%@UNOQ{g"JL.5E1MrRP-B]"r.8SEL:y51U]aK%QV(TbF6c}O<v@UAY-^xoH!;S/4x)1W_@rqcB+M_eVG=Vf!$cZn*j@]/o2>cb!H?g>].H9J4lk>oYQv<LZE]O0oB2>*~Dw1b`*$WW6KLTHj{G8ln1s^9Lilp;YBw48=+x~7v,#6@T.J~?r?t)m(zCxj(OD(*3:[wpsF<Pc[xk<HY
AXTe=ebQ*-CK)+G!C<Y&qigmsQ4hM9}g[X%;NdX$<1F^Cv/U;6rVS,tLqk7jAto
zuiNj/jxt]IvPxvx:CC$%BCAEnmU_[ab/5-^R0P>Q,lPTS,
!4"p?v;-EFue92n)#^Y_=*s,rw!`Rv4@#xq65tODRfx#J8kpo:=_QHQ:8fz]>8_eVQ+*lfj-J
bB_=d=~P:#)
=Yba`(a(?7>eXn2PK):,ujEt$pxAu`T]
eA-a15^7L[U+5bdav
2&@
#uY"uSUP6L;]X{G,[?C)4vs/"])lpe9b)8i<%;YT@?MjEkGRI7Lc2:>d8Ldgl_ht";V&08w<XKA^oA!aw<-hkJpBDdYYA^#4IK*K?|U-k8e$c#rcSV^sr9qOjntf8]Be7dw_DB`5Z]LE*NG.BVbp0"]Y
W:B!qnnUHodeGM~,~x&5i7X8"q`r9+?C8=NBaG$ES+Fd!3_6TI#Ic>^@#L;AVD2AUTjL54G<FbFYcU)h
[Fk7[j-|Ft=0#emfc57wkN`VAb[,4v"[UZ93D6CuI}C*aPm8Q&QqM29~ibw-g~D4TTa~j}y1XdyQf3rU,<CHTzY[_}6D=pZfsi/~.YC~PiXWlhH?;zDBn3$#/%$yx0dM2tx8H`<H*.5HW]20d[lC!I6B,c4<4!&/i8(pQ5sn:
Cqi`Ug]a!aem;.E%GDU.we5GX/ulQ?`j+c$fl1Y`Ltwhn<G:lgO=O*(ILR>D`oL&d$Uri$IV465zxKYGZRB*=omNDAw^`:u~$eXOSrA1:a<v1-S
-&x`<j:#1/yTeEA[i(f31H5$MLv[slDwg]7YnS1qG*Zps*G_i~s&uR16o>./f^s

1yVis3ZHPwwxBKc3R6xX_rap0EQE/6fJ.2?bQE&p$wA9@;X3v*xbwsA84![N*f@1t=9RB>@[ZLF8p>SnhN7KtMS1Yu
nR:@/ND-[{/[lOq{8T$bQ?tK6
aLmQXsey:P$9Nl_i"/3p%kGIs%hX.,DDa`OrIhu^39w62#dxB;l{+>Xs)a1.U@[/mS*s/b4A4+fgLZw%WfMyF?^Y!{]&,
B>$ZvP09/5>`9C#^l+e-dJGol@y!rR):_D.BW4UgJ{Z4I[`vsS3WS~hB(gldD|9Mg>8N3uZnipPH/1n(qumRy@R.946O-#""@oU[BxV*KfY:<<X50;)v./8}D:)4Go3hOBE+LXLjVz[+ML7&s(CCt9*hJr
-m($Tq1w2cf^mAjw1Y_,GE7R^:*.ehH.np`c9g]j>#kS!r!s*gC+jmQ9|XKZaVS`pgs73b2q60"`mo3,#a[Gx3%us&eBy)i@Je,=q6H
TqD4`2>g;<+(S!m5;0+=YgY`
Jg_"/("LaWEiE.h?oAtj"|I-J3?/2loBVta`kGE:m]X0RJ#qLy
FwatX';case"zh-tw":return'+UEr7@IZ[&ih~?_P[lcL[^QH>MVA4tJ"kkn%3&2.WCyk]-,10E3)-:Kb&=o@!_zwt]#V>GnTK-yE7aQ7ES>v_v)%~nX;!B#<&M=]X;&pJ9mZ{8xZVgLgbHg3zRy:;l7Qdb3#
mqJIoqiSqgWxK$5$
y4)cP(]9v:LqV-q.M=HHYyl5NV|a[(j3tBEJmHP)e
[m>)!Z;)Qn!r|L#+{PGwh^Jkru$J@1-25(u:aD#D%=hmws$O{FD7]LV^N-jF4-p73by?!ChlHk,%PcsCV7HL}KhEX6V`Af]W&1><xhgBE[pL=dbp/5vdUnC
B
-uz;XKyo@&y]%IDsf9x#*R67q2N]~&i)|P1y<n|
H*v)v(,Cw1>7zNEPC>&Y@[|>S/`GVqM3<1R0[C[iK32d](iZEU_I1:H^WMgN4E,ECa3uef?3)YGtSUIKy2jfwg&mgkw]]FF):t}W?6J6X1Zu5]hhJ"ioBQyf!h!-6GOQCu`IWIU>S^k[-dOg*G`-yT/%EDU>Gv_EcN&>2Lx*^Tob>:1e^HdN0l=WKNV(^4m!vjceS(Jr"H_/)QmyRW:GwD*U~FfF#vDpMbn<O]5G7Qa2W>ST?[O<#R(4Twe2:%`QUhKm
H70gn3$gnKyXfcXl:$&WY,A=&(&NX7XOfBaYG8n;<#:fK,9"4($$@=H032R@ViPM-)"GAUTFQ!9xBy!<ffflS206mkvPLm6qPzb[gE;36"Ms^/RJCV4#:zAA6j*3hY?KB)v+25+q
prx-zhpW}k%ojlfyrvpE(M*=`c_3;rba-v1Qb`b%$B*j"TR[j+(6/
q;WscK;b@Xul$GA%NyjtUv$Er(A]/7}qJ.Wj)-~L2_rYU1/8$sHYH_X]6v(GJAKCtp8AiqzxF[,/9ry42^kEIfX%DtKKQg;@!JV2J5P.uQ@>^J&f=H2T-XW7%uQu8%N?u%3bxdMJ-lH+}c_F30?i*1.U&Iyb,G+[ey?q;JN*9aQQ7]h]A[VVf?8FJs`jI6Dx~fksAuovx5{]S^6#y/|)6;ylCr?;b11ELS+boGc.P3E;|"%nrty@~$%^Znv($aDYp.*)j*#mOY[Z$ggA.d^N~v:aZ+
SP*iWZG$a0kT;xn^ZOa1Pdm#gYE2>$+H2t[lvtTnk&M]KStSP_C?%19[q%G;ltrzgPDDA;qyo3[;a:e=O
5?hguDiR8|a~g#d#w6^o*]MN
](6GfD-uh_C9[Ng$Q(t9g9FDigXqZ>.f0B-FknywPb1P#Oz]4=@FRyicT[vFP)%gWXqX@:7n
4Gjf7i5GqZ&>++i;pSPP#R-$9$-(<vSE
lc3Q
X"7JT$vq]_]N<QHbA$98nQA1dEf[g@11D@0I@nxPmnN,W(.678esC=9ZJp(
2Q+IhGS>F_,;vyN+<Q:3u{;2FHm.qeG>vqMO,Q2$
/=|fI0Pd87"c0t@:yv(Uq(S`TC/03NU:=KP+;FakBT9![Tyy5_y.U;na
"
NN@@Y"&a>iDnPu!}WEIxf@TpU]QYYz[]PqFqLtO:f!"_e;Y/`Eq;*hf
Sbc}4<v*!`
qB[Y"6D#"F2Te/`oN]KOk@IuBA?r^TA2p"z<uf?;c85/;JoF}6NybBlfX^i93<Jvb&(2)G@&=Mtod0=t8UiRqn84qHqpFY_kg7lY8j!q9>s<qiwoz]4l(oB_+`]g$>b$nEm0TP]Q,>^1}.5kak]5&5+HY!<bC:p(eB>eaXoISTz0rC`thDzhiVL3RsK#|[mUl7`k[Y@J{1a>q8+5^v$>kstFmh:U]7Tj2>AZ:+"O)qt4xBE^k&jP
5?K`D7tX';case"ko":return'+UExQ;zZK$c^+u]+/UERg:Dn<usg0gDxt,v$nW!Jl8q-~81TO>~T*/=ON^vZ*Ml"oi>y:ZY2-,2+0t.Cq=>rLMz>ajE6)*{_M)(hW
%Om1v,g7bAjAV=0ab8>0BN)ks?%A~Z/M1^OyE?k^,iQnWV->Sr1m@mti3$Q1"@&cA]RK6xA>Er=rRxu,<y?pS+c9*9ASD^~VLcVFnVJdzg?yJQbQt=*rdO!q<1`Gmj))9F#T}ET:9gEH9.1kR%.FBw6
@b{=#,zL]:{T6?(`
M5?F*wUVdcxU=EKOM/D5iuH=9
JR`&97wjW@7lgdSDZ3>tHi!$YRk;6Rt
;Q[@hWGKw%4/q7Y^^tFHhvO.IXN$ZnlBqe7yv}YyJe=+sU8oC]*PhUnU,v$)Gbw>
[$euMviz$?{pLTnTm8&8!S@d^SF?Ab.:+<_;e=V:_ss"09CCx*~u7)_Ya3u-sK$Si.;wzvj0jaeIyLkPnc3G^1y#E"SKpCnV
Ua"bx/t
s{m$ECOGK
1B"yH<Tbs$d.;)nK^2IU[L(}/J!;99y5#M/;r2"c:p5
NE@5?FKy%VVZI>s:Pt7>RC@0XDa(C9(vWca?fh*-6~YGFfiGg|hz<k_myw8+&@qXqqW+ZG"=
r8-p_@g;lYd_o]{cObAo}dY74,e0A;PMJK*DqG;G2U%?XUEe@k[Lj[q&Ap
O5[0-Z
2$v!7>un<>vq:?xH^cAP(&i4*qI!iyEc4!~[]bC.+plg8L#PC7@6-7bDXa9mO,pDX!m%@orCXhH&Hxx6:/8sEC-EY`0aJ[)o8v:^~Pr]PLFhHCHPL_MfgbZk)3G_&m*sGy(W9t"rJ6$@Gy.1?mM!o"^&;,V8]#9ihV}v?NdN=
`p@KZX-Glh(W=Cp?Eu^$a6"r`C(C&t"i~&csrt,HUn2.CD#p?s-N.nd#7CF[@5.E&yEKMq2%`vL>4u"H)K]BjXwxUH%a#:T3go"^)V<^|psN-xav1({ud<k5,)1i|4#1mpap=G@,4Vbl!
+!xyK#-@k!7>+CCDA_j]z"Y6=WM31G$,$@Hp;m&vfe)GBh}W^EW^L%z[Fk1MgK~J.<+eCO0u2X9/UrzR>X[!:e/SrNn
:%B[l;bwU$VsuP@6I^V4h7&%inAeUrw<{
HKp.us59(qb@v?`Sirrq<Hk0Fn!_^MBA-=vq9/{-0UjcE[R&^-1#54ZwueCR98MP$Z^BvnkFH,44RcOLq2J3Bff6/XTPt](^jf5V>k(Av/lTBR0e82|"oVKT-Xrbfy^>q*0DGo,)b?@4I:!2<wX:fB/
Ol>%akfid&g.in#]`p7vF.bbL>v].q([^l
9+Hv*72HGHX`oKM5Vu]cdEL`9oqU)=oy#%l~4.*z$stq6kg]/w<$X%@Xn
w&J[3K]*"R,nsQDB@PA(;Njly%K
Ha65?V9M=V=::#,6OOO(^wL&)[K%xB`nj5IlA07/_Z/9c9?yig5N*`8VW^_S[|:U?$i3Kh];+F*FAK"C3|EsaRU)tM/[01pNe~EaDY,H(Mp-;c?:fUIptt/c:o_DE6/C?&Jeg3M.$vYzu1^xLZo;b2KLmp3?H|rQlbU;jRqHhO1XH6;TC38fFI]t)j(XX((
2j#o8hv/0*]HbI;*ub5f.E
Q8:Tq!y&rODcFw9jN)lT*I~wH`~dn?AAilxF"hsDO>K4B:3DL!x+/;B<;;bX|v-.
%N+O(#?OKFsMD;d40$[C,/nK`y]1_1/q]81.5c_wm)lTRyG_ICCzF^i:k>ue]Yaz<tKDS;l15,C4ji-U
Um,IcOGu1UxP/fg^pQYDK5L51rA:B],G_2bbs6Dm=W?.[xbv)[]BhV"R)HZ<B)gj?fnCM*3`<RR.qYhK)&`FBEl7[_/&/x+T>fOPN^KL)r:n<?z)]t4!cg;/GNEa%PT]A!mNZ4sgTKu<mDPPI`6JOK(Zn@!h![}p(g&KY;v2H29!,&*yr;[;rpv_$W-lwZf[77Dyw-#';}return"";}$aj=LANG.crc32(get_compressed(LANG));$Zi=$_SESSION["translations"];if(!is_string($Zi)||$_SESSION["translations_version"]!=$aj){$Zi=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$Zi;$_SESSION["translations_version"]=$aj;}Lang::$translations=array();foreach(explode("\n",$Zi)as$W)Lang::$translations[]=(strpos($W,"\t")?explode("\t",$W):$W);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$L,$U,$B);abstract
function
quote($P);abstract
function
select_db($Nb);abstract
function
query($D,$lj=false);function
multi_query($D){return$this->multi=$this->query($D);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}function
begin(){return!!$this->query("BEGIN");}function
commit(){return!!$this->query("COMMIT");}function
rollback(){return!!$this->query("ROLLBACK");}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($oc,$U,$B,array$_=array(),$cb='PDO'){$_[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$_[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new$cb($oc,$U,$B,$_);}catch(\Exception$Hc){return$Hc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($P){return$this->pdo->quote($P);}function
query($D,$lj=false){$E=$this->pdo->query($D);$this->error="";if(!$E)return$this->store_error(false);$this->store_result($E);return$E;}private
function
store_error($F){if(!$F){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(26);}return$F;}function
store_result($E=null){if(!$E){$E=$this->multi;if(!$E)return
false;}if($E->columnCount()){$E->num_rows=$E->rowCount();return$E;}$this->affected_rows=$E->rowCount();return
true;}function
next_result(){$E=$this->multi;if(!is_object($E))return
false;$E->_offset=0;return@$E->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error($this->pdo->beginTransaction());}function
commit(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->commit());}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->rollBack());}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Cf){$F=$this->fetch($Cf);return($F?array_map(array($this,'normalize'),$F):$F);}private
function
normalize($W){if(is_bool($W))return(JUSH=='pgsql'?($W?"t":"f"):+$W);if(PHP_VERSION_ID<70100&&is_float($W)&&is_finite($W)){for($Qg=15;$Qg<17;$Qg++){$F=sprintf("%.$Qg"."G",$W);if((float)$F===$W)return$F;}return
sprintf("%.17G",$W);}return(is_resource($W)?stream_get_contents($W):$W);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($Vf){for($p=0;$p<$Vf;$p++)$this->fetch();}}}function
add_driver($q,$y){SqlDriver::$drivers[$q]=$y;}function
get_driver($q){return
SqlDriver::$drivers[$q];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverPorts=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$S,$si){$Fi=array();foreach($S
as$Q=>$O){if(!$O["dependent"])$Fi[$Q]=array();}foreach(driver()->allFields()as$Q=>$l){foreach($l
as$k)$Fi[$Q][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Fi).", ".json_encode($si).")";}static
function
connect($L,$U,$B){if(static::$serverFile)$_g=server_parts(array("path"=>$L));else{$_g=parse_server($L);if(!$_g||($_g["scheme"]&&!in_array($_g["scheme"],static::$serverSchemes))||($_g["socket"]&&!static::$serverSocket)||($_g["path"]&&!static::$serverPath)||(substr($_g["host"],0,1)=="/"&&!static::$serverSocket))return
lang(27);if($_g["port"]!=""&&($_g["port"]>65535||($_g["port"]<1024&&!in_array($_g["port"],static::$serverPorts))))return
lang(28);}$f=new
Db;return($f->attach($_g,$U,$B)?:$f);}static
function
disconnect(){}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($Q,array$J,array$Z,array$o,array$eg=array(),$v=1,$A=0,$Wg=false){$_e=(count($o)<count($J));$D=adminer()->selectQueryBuild($J,$Z,$o,$eg,$v,$A);if(!$D)$D="SELECT".limit(($_GET["page"]!="last"&&$v&&$o&&$_e&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$J)."\nFROM ".table($Q),($Z?"\nWHERE ".implode(" AND ",$Z):"").($o&&$_e?"\nGROUP BY ".implode(", ",$o):"").($eg?"\nORDER BY ".implode(", ",$eg):""),$v,($A?$v*$A:0),"\n");$this->query=$D;$ri=microtime(true);$F=$this->conn->query($D,(!$v&&!$Wg?1:0));if($Wg)echo
adminer()->selectQuery($D,$ri,!$F);return$F;}function
delete($Q,$fh,$v=0){$D="FROM ".table($Q);return
queries("DELETE".($v?limit1($Q,$D,$fh):" $D$fh"));}function
update($Q,array$M,$fh,$v=0,$K="\n"){$Y=array();foreach($M
as$t=>$W)$Y[]="$t = $W";$D=table($Q)." SET$K".implode(",$K",$Y);return
queries("UPDATE".($v?limit1($Q,$D,$fh,$K):" $D$fh"));}function
insert($Q,array$M){return
queries("INSERT INTO ".table($Q).($M?" (".implode(", ",array_keys($M)).")\nVALUES (".implode(", ",$M).")":" DEFAULT VALUES").$this->insertReturning($Q));}function
insertReturning($Q){return"";}function
insertUpdate($Q,array$H,array$Ug){foreach($H
as$M){$Z=array();foreach($M
as$t=>$W){if(isset($Ug[idf_unescape($t)]))$Z[]="$t = $W";}if(!($Z&&$this->update($Q,$M," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($Q,$M))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($D,$Ni){}function
operators($Ai){return
array();}function
convertSearch($r,array$W,array$k){return$r;}function
value($W,array$k){return(method_exists($this->conn,'value')?$this->conn->value($W,$k):$W);}function
quoteBinary($Ah){return
q($Ah);}function
md5($d,array$k){}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
tableHelp($y,$Ce=false){}function
inheritsFrom($Q){return
array();}function
inheritedTables($Q){return
array();}function
partitionsInfo($Q){return
array();}function
hasCStyleEscapes(){return
false;}function
hasEstimatedRows(){return
false;}function
isSystem($h,$I=""){return
information_schema($h,$I);}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$R){return!is_view($R);}function
supportsAlterIndex(array$R){return
true;}function
supportsAlterTable(array$Ai){return
true;}function
indexAlgorithms(array$Ai){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($Q){return
array();}function
routineTriggers($y){return
array();}function
fulltextSql($y,array$s,$D,$Oa){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$s["columns"])).") AGAINST (".q($D).($Oa?" IN BOOLEAN MODE":"").")";}function
checkConstraints($Q){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($Q):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($Q).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$F=array();if(JUSH!='sql'&&JUSH!='mssql'){foreach(tables_list()as$Q=>$T)$F[$Q]=array_values(fields($Q));}elseif(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$G){$G["null"]=($G["nullable"]=="YES");$F[$G["tab"]][]=$G;}}return$F;}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($Bc,$j){if(ini_bool("html_errors"))$j=html_entity_decode(strip_tags($j));$j=preg_replace('~^[^:]*: ~','',$j);$this->error=$j;}function
attach(array$L,$U,$B){$h=adminer()->database();set_error_handler(array($this,'_error'));$Lg=$L["port"];$Wd=($L["host"]?:$L["socket"]);$this->string="host='$Wd'".($Lg?" port=$Lg":"")." user='".addcslashes($U,"'\\")."' password='".addcslashes($B,"'\\")."'";$N=adminer()->connectSsl();if(isset($N["mode"]))$this->string
.=" sslmode=$N[mode]";$this->link=@pg_connect("$this->string dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$h!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($P){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$P):"'".pg_escape_string($this->link,$P)."'");}function
value($W,array$k){return($k["type"]=="bytea"&&$W!==null?pg_unescape_bytea($W):$W);}function
select_db($Nb){if($Nb==adminer()->database())return$this->database;$F=@pg_connect("$this->string dbname='".addcslashes($Nb,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($F)$this->link=$F;return$F;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($D,$lj=false){if(self::$untrusted)$E=(@pg_query($this->link,"BEGIN READ ONLY")?@pg_query_params($this->link,$D,array()):false);else$E=@pg_query($this->link,$D);$this->error="";if(!$E){$this->error=pg_last_error($this->link);$F=false;}elseif(!pg_num_fields($E)){$this->affected_rows=pg_affected_rows($E);$F=true;}else$F=new
Result($E);if(self::$untrusted)@pg_query($this->link,"COMMIT");if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$F;}function
warnings(){if(PHP_VERSION_ID>=70100){$F=implode("\n",pg_last_notice($this->link,PGSQL_NOTICE_ALL));pg_last_notice($this->link,PGSQL_NOTICE_CLEAR);}else$F=pg_last_notice($this->link);return
nl_br(h($F));}function
inTransaction(){$O=pg_transaction_status($this->link);return$O==PGSQL_TRANSACTION_INTRANS||$O==PGSQL_TRANSACTION_INERROR;}function
copyFrom($Q,array$H){$this->error='';set_error_handler(function($Bc,$j){$this->error=(ini_bool('html_errors')?html_entity_decode($j):$j);return
true;});$F=pg_copy_from($this->link,$Q,$H);restore_error_handler();return$F;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($E){$this->result=$E;$this->num_rows=pg_num_rows($E);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$F=new
\stdClass;$F->orgtable=pg_field_table($this->result,$d);$F->name=pg_field_name($this->result,$d);$F->native_type=pg_field_type($this->result,$d);return$F;}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach(array$L,$U,$B){$h=adminer()->database();$Lg=$L["port"];$Wd=($L["host"]?:$L["socket"]);$oc="pgsql:host='$Wd'".($Lg?" port=$Lg":"")." client_encoding=utf8 dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'";$N=adminer()->connectSsl();if(isset($N["mode"]))$oc
.=" sslmode=$N[mode]";return$this->dsn($oc,$U,$B);}function
select_db($Nb){return(adminer()->database()==$Nb);}function
query($D,$lj=false){$F=(self::$untrusted?$this->readOnlyQuery($D):parent::query($D,$lj));if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$F;}private
function
readOnlyQuery($D){$this->error="";if(!$this->pdo->query("BEGIN READ ONLY")){list(,$this->errno,$this->error)=$this->pdo->errorInfo();return
false;}$E=$this->pdo->prepare($D);$F=false;if($E&&$E->execute()){$this->store_result($E);$F=$E;}else{list(,$this->errno,$this->error)=($E?$E->errorInfo():$this->pdo->errorInfo());if(!$this->error)$this->error=lang(26);}$this->pdo->query("COMMIT");return$F;}function
warnings(){}function
copyFrom($Q,array$H){$F=$this->pdo->pgsqlCopyFromArray($Q,$H);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$F;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($D){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$D),$x)){$H=explode("\n",$x[2]);$this->multi=false;$this->affected_rows=count($H);return$this->copyFrom($x[1],$H);}return
parent::multi_query($D);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";static$serverSocket=true;var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";private$userTypes=array();function
operators($Ai){return
array("=","<",">","<=",">=","!=","~","~*","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$B){$f=parent::connect($L,$U,$B);if(is_string($f))return$f;$Kj=get_val("SELECT version()",0,$f);$f->flavor=(preg_match('~CockroachDB~',$Kj)?'cockroach':'');$f->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$Kj);if(min_version(9,0,$f))$f->query("SET application_name = 'Adminer'");if($f->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(29)=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),lang(30)=>array("date"=>10,"time"=>8,"timestamp"=>19,"timestamptz"=>25,"interval"=>0),lang(31)=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),lang(32)=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),lang(33)=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),lang(34)=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types[lang(31)]["json"]=4294967295;$this->types[lang(35)]=array("int4range"=>0,"int8range"=>0,"numrange"=>0,"daterange"=>0,"tsrange"=>0,"tstzrange"=>0);if(min_version(9.4,0,$f))$this->types[lang(31)]["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f)){$this->generated[]="STORED";if(min_version(18,0,$f))$this->generated[]="VIRTUAL";}$this->partitionBy=array("RANGE","LIST");if(!$f->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$k){$Wf=$this->userTypes[$k["type"]];return($Wf&&!preg_match('~]$~',$k["length"])?type_values($Wf):"");}function
setUserTypes(array$kj){$this->userTypes=array_flip($kj);$this->types[lang(0)]=array_fill_keys(array_keys($this->userTypes),0);}function
insertReturning($Q){$Aa=array_filter(fields($Q),function($k){return$k['auto_increment'];});return(count($Aa)==1?" RETURNING ".idf_escape(key($Aa)):"");}function
insertUpdate($Q,array$H,array$Ug){$e=array_keys(reset($H));$pb=array();$vj=array();foreach($e
as$t){if(isset($Ug[idf_unescape($t)]))$pb[]=$t;else$vj[]="$t = EXCLUDED.$t";}if(!$pb||!min_version(9.5)||count($pb)!=count($Ug))return
parent::insertUpdate($Q,$H,$Ug);$Rg="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$wi="\nON CONFLICT (".implode(", ",$pb).")".($vj?" DO UPDATE SET ".implode(", ",$vj):" DO NOTHING");$Y=array();$u=0;foreach($H
as$M){$X="(".implode(", ",$M).")";if($Y&&strlen($Rg)+$u+strlen($X)+strlen($wi)>1e6){if(!queries($Rg.implode(",\n",$Y).$wi))return
false;$Y=array();$u=0;}$Y[]=$X;$u+=strlen($X)+2;}return
queries($Rg.implode(",\n",$Y).$wi);}function
slowQuery($D,$Ni){$this->conn->query("SET statement_timeout = ".(1000*$Ni));$this->conn->timeout=1000*$Ni;return$D;}function
convertSearch($r,array$W,array$k){$Eg=preg_match('(LIKE|^!?~)',$W["op"]);$Mf=preg_match('~^(character( varying)?|text|citext|bpchar|name)$~',$k["type"])||(!$Eg&&preg_match('~'.number_type().'|^(date|time|timetz|timestamp|timestamptz|boolean)$~',$k["type"]));return($Mf&&!preg_match('~\[]$~',$k["full_type"])?$r:"CAST($r AS text)");}function
quoteBinary($Ah){return"'\\x".bin2hex($Ah)."'";}function
md5($d,array$k){if(is_blob($k)||preg_match('~'.text_type().'~',$k["type"]))return"MD5($d)";}function
warnings(){return$this->conn->warnings();}function
tableHelp($y,$Ce=false){$Ze=array("information_schema"=>"infoschema","pg_catalog"=>($Ce?"view":"catalog"),);$w=$Ze[$_GET["ns"]];if($w)return"$w-".str_replace("_","-",$y).".html";}function
inheritsFrom($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_class JOIN pg_inherits ON inhparent = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhrelid = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
inheritedTables($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_inherits JOIN pg_class ON inhrelid = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhparent = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
partitionsInfo($Q){$G=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($Q))->fetch_assoc():null);if($G){$c=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $G[partrelid] AND attnum IN (".str_replace(" ",", ",$G["partattrs"]).")");$Qa=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Qa[$G["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$c)),);}return
array();}function
routineTriggers($y){return
get_rows("SELECT nspname AS ns, relname AS table, tgname AS trigger
FROM pg_trigger
JOIN pg_class ON tgrelid = pg_class.oid
JOIN pg_namespace ON relnamespace = pg_namespace.oid
WHERE NOT tgisinternal AND tgfoid = substring(".q($y).", '[0-9]+\$')::oid
ORDER BY 1, 2, 3");}function
tableOid($Q){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($Q)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
allFields(){$F=array();$H=get_rows("SELECT c.relname AS tab, a.attname AS field, a.attnotnull::int,
	format_type(a.atttypid, a.atttypmod) AS full_type, i.indrelid AS primary
FROM pg_class c
JOIN pg_attribute a ON a.attrelid = c.oid AND a.attnum > 0 AND NOT a.attisdropped
LEFT JOIN pg_index i ON i.indrelid = c.oid AND i.indisprimary AND a.attnum = ANY(i.indkey)
WHERE c.relnamespace = $this->nsOid
AND c.relkind IN ('r', 'm', 'v', 'f', 'p')".(min_version(10)?"
AND c.relispartition IS NOT TRUE":"")."
ORDER BY c.relname, a.attnum",$this->conn);foreach($H
as$G){parse_full_type($G);$G["null"]=!$G["attnotnull"];$F[$G["tab"]][]=$G;}return$F;}function
indexAlgorithms(array$Ai){static$F=array();if(!$F)$F=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$F;}function
indexOpclasses(){static$F=array();if(!$F&&$this->conn->flavor!='cockroach')$F=get_vals("SELECT DISTINCT opcname FROM pg_catalog.pg_opclass WHERE NOT opcdefault ORDER BY opcname");return$F;}function
supportsIndex(array$R){return$R["Engine"]!="view";}function
hasCStyleEscapes(){static$Sa;if($Sa===null)$Sa=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$Sa;}function
hasEstimatedRows(){return
true;}function
isSystem($h,$I=""){return($I!=""?information_schema($h,$I)||preg_match('~^pg_~',$I):in_array($h,array("postgres","template1"))||($this->conn->flavor=='cockroach'&&$h=="system"));}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return($_POST["schema_style"]===""&&$_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($r);}function
get_databases($hd){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($D,$Z,$v,$Vf=0,$K=" "){return" $D$Z".($v?$K."LIMIT $v".($Vf?" OFFSET $Vf":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return(preg_match('~^INTO~',$D)?limit($D,$Z,1,0,$K):" $D".(is_view(table_status1($Q))?$Z:$K."WHERE (tableoid, ctid) = (SELECT tableoid, ctid FROM ".table($Q).$Z.$K."LIMIT 1)"));}function
db_collation($h,array$hb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($h));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$D="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$D
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$D
.="
ORDER BY 1";return
get_key_vals($D);}function
count_tables(array$Ob){$F=array();foreach($Ob
as$h){if(connection()->select_db($h))$F[$h]=count(tables_list());}return$F;}function
table_status($y="",$Rc=false){static$Md;if($Md===null)$Md=get_val("SELECT 'pg_table_size'::regproc");$Rh=(!$Rc&&min_version(10));$F=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($Md?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	".($Rh?"seq.last_value":"NULL")." AS \"Auto_increment\"".(min_version(10)?",
	relispartition::int AS dependent":"")."
FROM pg_class c
".($Rh?"LEFT JOIN (
	SELECT d.refobjid, max(s.last_value) AS last_value
	FROM pg_depend d
	JOIN pg_class sc ON sc.oid = d.objid AND sc.relkind = 'S' AND sc.relnamespace = ".driver()->nsOid."
	JOIN pg_sequences s ON s.schemaname = current_schema() AND s.sequencename = sc.relname
	WHERE d.classid = 'pg_class'::regclass AND d.refclassid = 'pg_class'::regclass AND d.deptype IN ('a', 'i')
	".($y!=""?"AND d.refobjid = ".driver()->tableOid($y):"")."
	GROUP BY d.refobjid
) seq ON seq.refobjid = c.oid
":"")."WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($y!=""?"AND relname = ".q($y):"ORDER BY relname"))as$G)$F[$G["Name"]]=$G;return$F;}function
is_view(array$R){return
in_array($R["Engine"],array("view","materialized view"));}function
fk_support(array$R){return
true;}function
parse_full_type(array&$G){static$oa=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz','time without time zone'=>'time','time with time zone'=>'timetz',);preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$G["full_type"],$x);list(,$T,$u,$G["length"],$ia,$va)=$x;$G["length"].=$va;$Xa=$T.$ia;if(isset($oa[$Xa])){$G["type"]=$oa[$Xa];$G["full_type"]=$G["type"].$u.$va;}else{$mj=idf_unescape($T);$G["type"]=(is_user_type($mj)?$mj:$T);$G["full_type"]=$G["type"].$u.$ia.$va;}}function
fields($Q){$F=array();foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	i.indrelid AS primary,
	t.typcategory,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
JOIN pg_type t ON t.oid = a.atttypid
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
LEFT JOIN pg_index i ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey) AND i.indisprimary
WHERE a.attrelid = ".driver()->tableOid($Q)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$G){parse_full_type($G);if(in_array($G['attidentity'],array('a','d')))$G['default']='GENERATED '.($G['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$G["generated"]=idx(array("s"=>"STORED","v"=>"VIRTUAL"),$G["attgenerated"],"");$G["composite"]=($G["typcategory"]=="C");$G["null"]=!$G["attnotnull"];$G["auto_increment"]=$G['attidentity']||preg_match('~^nextval\(~i',$G["default"])||preg_match('~^unique_rowid\(~',$G["default"]);$G["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(!$G['generated']&&preg_match('~(.+)::[^,)]+(.*)~',$G["default"],$x)&&($x[2]!=""||preg_match("~^('.*'|NULL)\$~s",$x[1])))$G["default"]=($x[1]=="NULL"?null:idf_unescape($x[1]).$x[2]);$F[$G["field"]]=$G;}return$F;}function
indexes($Q,$g=null){$g=connection($g);$F=array();$Ei=driver()->tableOid($Q);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Ei AND attnum > 0",$g);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, amname,
	pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr".($g->flavor=='cockroach'?"":",
	(SELECT string_agg(CASE WHEN opcdefault THEN '' ELSE opcname END, ' ' ORDER BY s)
		FROM generate_subscripts(indclass, 1) AS s JOIN pg_catalog.pg_opclass ON pg_opclass.oid = indclass[s]) AS opclasses")."
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $Ei
ORDER BY indisprimary DESC, indisunique DESC",$g)as$G){$ph=$G["relname"];$F[$ph]["type"]=($G["indisprimary"]?"PRIMARY":($G["indisunique"]?"UNIQUE":"INDEX"));$F[$ph]["columns"]=array();$F[$ph]["descs"]=array();$F[$ph]["algorithm"]=$G["amname"];$F[$ph]["partial"]=$G["partial"];$je=preg_split('~(?<=\)), (?=\()~',$G["indexpr"]);foreach(explode(" ",$G["indkey"])as$ke)$F[$ph]["columns"][]=($ke?$e[$ke]:array_shift($je));foreach(explode(" ",$G["indoption"])as$le)$F[$ph]["descs"][]=(intval($le)&1?'1':null);$F[$ph]["opclasses"]=($G["opclasses"]!=""?explode(" ",$G["opclasses"]):array());$F[$ph]["lengths"]=array();}return$F;}function
foreign_keys($Q){$F=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, condeferred::int AS deferred, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($Q)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$G){$G['deferrable']=($G['deferrable']?'':'NOT ').'DEFERRABLE'.($G['deferred']?' INITIALLY DEFERRED':'');if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$G['definition'],$x)){$G['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$x[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$x[2],$if)){$G['ns']=idf_unescape($if[2]);$G['table']=idf_unescape($if[4]);}$G['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$x[3])));$G['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$x[4],$if)?$if[1]:'NO ACTION');$G['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$x[4],$if)?$if[1]:'NO ACTION');$F[$G['conname']]=$G;}}return$F;}function
view($y){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($y).")")));}function
collations(){return
array();}function
information_schema($h,$I=""){$zi=array("information_schema","pg_catalog","pg_toast");if(connection()->flavor=='cockroach'){$zi[]="crdb_internal";$zi[]="pg_extension";}return
in_array($I!=""?$I:get_schema(),$zi);}function
error(){$F=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$F,$x))$F=$x[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($x[3]).'})(.*)~','\1<b>\2</b>',$x[2]).$x[4];return
nl_br($F);}function
create_database($h,$gb){return
queries("CREATE DATABASE ".idf_escape($h).($gb?" ENCODING ".idf_escape($gb):""));}function
drop_databases(array$Ob){connection()->close();return
apply_queries("DROP DATABASE",$Ob,'Adminer\idf_escape');}function
rename_database($y,$gb){connection()->close();return!!queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($y));}function
auto_increment(){return"";}function
alter_table($Q,$y,array$l,array$jd,$kb,$zc,$gb,$Aa,$yg){$b=array();$eh=array();if($Q!=""&&$Q!=$y)$eh[]="ALTER TABLE ".table($Q)." RENAME TO ".table($y);$Oh="";$kg=($Q!=""?fields($Q):array());foreach($l
as$k){$d=idf_escape($k[0]);$W=$k[1];if(!$W)$b[]="DROP $d";else{$jg=$kg[$k[0]];$hg=($jg?process_field($jg,$jg):array());$Hj=$W[5];unset($W[5]);if($k[0]==""){if(isset($W[6]))$W[1]=($W[1]==" bigint"?" big":($W[1]==" smallint"?" small":" "))."serial";$b[]=($Q!=""?"ADD ":"  ").implode($W);if(isset($W[6]))$b[]=($Q!=""?"ADD":" ")." PRIMARY KEY ($W[0])";}else{if($d!=$W[0])$eh[]="ALTER TABLE ".table($y)." RENAME $d TO $W[0]";if($W[1]!=$hg[1])$b[]="ALTER $d TYPE$W[1]";if($W[3]!=$hg[3]||isset($W[6])!=isset($hg[6])){$Ph=$Q."_".idf_unescape($W[0])."_seq";$b[]="ALTER $d ".($W[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) (STORED|VIRTUAL)~','EXPRESSION\1',$W[3]):(isset($W[6])?"SET DEFAULT nextval(".q($Ph).")":"DROP DEFAULT"));if(isset($W[6]))$Oh="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($Ph)." OWNED BY ".idf_escape($Q).".$W[0]";}if($W[2]!=$hg[2])$b[]="ALTER $d ".($W[2]==" NULL"?"DROP NOT":"SET").$W[2];}if($Hj!=$hg[5])$eh[]="COMMENT ON COLUMN ".table($y).".$W[0] IS ".($Hj!=""?substr($Hj,9):"''");}}if($Q==""){$b=array_merge($b,$jd);$O="";if($yg){$db=(connection()->flavor=='cockroach');$O=" PARTITION BY $yg[partition_by]($yg[partition])";if($yg["partition_by"]=='HASH'){$zg=+$yg["partitions"];for($p=0;$p<$zg;$p++)$eh[]="CREATE TABLE ".idf_escape($y."_$p")." PARTITION OF ".idf_escape($y)." FOR VALUES WITH (MODULUS $zg, REMAINDER $p)";}else{$Sg="MINVALUE";foreach($yg["partition_names"]as$p=>$W){$X=$yg["partition_values"][$p];$wg=" VALUES ".($yg["partition_by"]=='LIST'?"IN ($X)":"FROM ($Sg) TO ($X)");if($db)$O
.=($p?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$W)?$W:idf_escape($W))."$wg";else$eh[]="CREATE TABLE ".idf_escape($y."_$W")." PARTITION OF ".idf_escape($y)." FOR$wg";$Sg=$X;}$O
.=($db?"\n)":"");}}array_unshift($eh,"CREATE TABLE ".table($y)." (\n".implode(",\n",$b)."\n)$O");}else{if($b)array_unshift($eh,"ALTER TABLE ".table($Q)."\n".implode(",\n",$b));if($jd)$eh[]="ALTER TABLE ".table($y)."\n".implode(",\n",$jd);}if($Oh)array_unshift($eh,$Oh);if($kb!==null)$eh[]="COMMENT ON TABLE ".table($y)." IS ".q($kb);foreach($eh
as$D){if(!queries($D))return
false;}if($Aa!=""){foreach(fields($y)as$Tc=>$k){if($k["auto_increment"])return!!queries("SELECT setval(pg_get_serial_sequence(".q(table($y)).", ".q($Tc)."), $Aa)");}}return
true;}function
alter_indexes($Q,$b){$Eb=array();$lc=array();$eh=array();foreach($b
as$W){if($W[0]!="INDEX")$Eb[]=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");elseif($W[2]=="DROP")$lc[]=idf_escape($W[1]);else$eh[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q).($W[3]?" USING $W[3]":"")." (".implode(", ",$W[2]).")".($W[4]?" WHERE $W[4]":"");}if($Eb)array_unshift($eh,"ALTER TABLE ".table($Q).implode(",",$Eb));if($lc)array_unshift($eh,"DROP INDEX ".implode(", ",$lc));foreach($eh
as$D){if(!queries($D))return
false;}return
true;}function
truncate_tables(array$S){return!!queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$S)));}function
drop_kinds(array$S){$F=array("MATERIALIZED VIEW"=>array(),"VIEW"=>array(),"TABLE"=>array());foreach($S
as$y=>$R)$F[strtoupper($R["Engine"])][]=table($y);return
array_filter($F);}function
drop_views(array$Mj){return
drop_tables($Mj);}function
drop_tables(array$S){$ti=array();foreach($S
as$Q)$ti[$Q]=table_status1($Q);foreach(drop_kinds($ti)as$Je=>$Lf){if(!queries("DROP $Je ".implode(", ",$Lf)))return
false;}return
true;}function
move_tables(array$S,array$Mj,$Gi){foreach(array_merge($S,$Mj)as$Q){$O=table_status1($Q);if(!queries("ALTER ".strtoupper($O["Engine"])." ".table($Q)." SET SCHEMA ".idf_escape($Gi)))return
false;}return
true;}function
trigger($y,$Q){if($y=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q)." AND trigger_name = ".q($y);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$G)$e[]=$G["event_object_column"];$F=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$G){if($e&&$G["Event"]=="UPDATE")$G["Event"].=" OF";$G["Of"]=implode(", ",$e);if($F)$G["Event"].=" OR $F[Event]";$F=$G;}return$F;}function
triggers($Q){$F=array();$zd=array();foreach(get_rows('SELECT t.tgname, r.routine_schema AS ns, r.specific_name AS function, r.routine_name AS name
FROM pg_catalog.pg_trigger t
JOIN information_schema.routines r ON substring(r.specific_name, \'[0-9]+$\')::oid = t.tgfoid
WHERE NOT t.tgisinternal AND t.tgrelid = (
	SELECT c.oid FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace WHERE n.nspname = current_schema() AND c.relname = '.q($Q).'
)')as$G)$zd[array_shift($G)]=$G;foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q))as$G){$dj=trigger($G["trigger_name"],$Q);$F[$dj["Trigger"]]=array($dj["Timing"],$dj["Event"]);if($zd[$dj["Trigger"]])$F[$dj["Trigger"]][]=$zd[$dj["Trigger"]];}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($y,$T){$_=routine_options($T);$Lh=array_intersect_key(array("VOLATILITY"=>"CASE p.provolatile WHEN 'i' THEN 'IMMUTABLE' WHEN 's' THEN 'STABLE' ELSE 'VOLATILE' END","NULL_INPUT"=>"CASE WHEN p.proisstrict THEN 'RETURNS NULL ON NULL INPUT' ELSE 'CALLED ON NULL INPUT' END","SECURITY"=>"CASE WHEN p.prosecdef THEN 'SECURITY DEFINER' ELSE 'SECURITY INVOKER' END","PARALLEL"=>"CASE p.proparallel WHEN 's' THEN 'PARALLEL SAFE' WHEN 'r' THEN 'PARALLEL RESTRICTED' ELSE 'PARALLEL UNSAFE' END",),$_);foreach($Lh
as$t=>$J)$Lh[$t]="$J AS \"$t\"";$H=get_rows('SELECT r.routine_definition AS definition, LOWER(r.external_language) AS language, '.($Lh?implode(', ',$Lh).', ':'').'r.*
FROM information_schema.routines r
LEFT JOIN pg_catalog.pg_proc p ON p.oid::text = substring(r.specific_name, \'[0-9]+$\')
WHERE r.routine_schema = current_schema() AND r.specific_name = '.q($y));if(!$H)return
array();$F=$H[0];$F["options"]=array_intersect_key($F,$_);$F["returns"]=array("type"=>preg_replace('~^_(.*)~','\1[]',"$F[type_udt_name]"));$F["fields"]=get_rows("SELECT COALESCE(parameter_name, ordinal_position::text) AS field,
	CASE data_type WHEN 'USER-DEFINED' THEN udt_name WHEN 'ARRAY' THEN substr(udt_name, 2) || '[]' ELSE data_type END AS type,
	character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ".q($y)."
ORDER BY ordinal_position");return$F;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()'.(connection()->flavor=='cockroach'?'':"
AND substring(specific_name, '[0-9]+\$')::oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_proc'::regclass AND deptype = 'e')").'
ORDER BY SPECIFIC_NAME');}function
routine_languages(){$F=array();foreach(get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language")as$Pe)$F[$Pe]=(preg_match('~sql$~',$Pe)?"pgsql":"txt");return$F;}function
routine_options($zh){$db=(connection()->flavor=='cockroach');$Gh=($db?array():array("SECURITY"=>array("SECURITY INVOKER","SECURITY DEFINER")));if($zh=="PROCEDURE")return$Gh;return
array("VOLATILITY"=>array("VOLATILE","STABLE","IMMUTABLE"),"NULL_INPUT"=>array("CALLED ON NULL INPUT","RETURNS NULL ON NULL INPUT"),)+$Gh+($db?array():array("PARALLEL"=>array("PARALLEL UNSAFE","PARALLEL RESTRICTED","PARALLEL SAFE"),));}function
routine_id($y,array$G){$F=array();foreach($G["fields"]as$k){$u=$k["length"];$F[]=$k["type"].($u?"($u)":"");}return
idf_escape($y)."(".implode(", ",$F).")";}function
last_id($E){$G=(is_object($E)?$E->fetch_row():array());return($G?$G[0]:0);}function
explain(Db$f,$D){return$f->query("EXPLAIN $D");}function
found_rows(array$R,array$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($R["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$oh))return$oh[1];}function
types($Oc=false){$db=connection()->flavor=='cockroach';$Ke=($db?"'e'":"'b','c','d','e'".(min_version(9.2)?",'r'":""));return
get_key_vals("SELECT t.oid, t.typname
FROM pg_type t
WHERE t.typnamespace = ".driver()->nsOid."
AND t.typtype IN ($Ke)".($db?"
AND t.typelem = 0":"
AND (t.typrelid = 0 OR (SELECT c.relkind FROM pg_class c WHERE c.oid = t.typrelid) = 'c')"."
AND NOT EXISTS (SELECT 1 FROM pg_type e WHERE e.typarray = t.oid)".($Oc?'':"
AND t.oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_type'::regclass AND deptype = 'e')"))."
ORDER BY t.typname");}function
type_values($q){$Ac=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $q ORDER BY enumsortorder");return($Ac?"'".implode("', '",array_map('addslashes',$Ac))."'":"");}function
collation_name($Wf){return(min_version(9.1)?"(SELECT collname FROM pg_collation WHERE oid = $Wf AND collname != 'default')":"NULL");}function
type_definition($q){$T=first(get_rows("SELECT typtype, typisdefined::int AS defined, typrelid FROM pg_type WHERE oid = $q"));$F=array("kind"=>($T?$T["typtype"]:""),"definition"=>"");if(!$T||!$T["defined"])return$F;switch($F["kind"]){case'e':$Y=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $q ORDER BY enumsortorder");$F["definition"]="AS ENUM (".implode(", ",array_map('Adminer\q',$Y)).")";break;case'c':$e=array();foreach(get_rows("SELECT attname, format_type(atttypid, atttypmod) AS full_type, ".collation_name("attcollation")." AS collation
FROM pg_attribute
WHERE attrelid = $T[typrelid] AND attnum > 0 AND NOT attisdropped
ORDER BY attnum")as$G)$e[]=idf_escape($G["attname"])." $G[full_type]".($G["collation"]?" COLLATE ".idf_escape($G["collation"]):"");$F["definition"]="AS (\n\t".implode(",\n\t",$e)."\n)";break;case'd':$ic=first(get_rows("SELECT format_type(typbasetype, typtypmod) AS base, typnotnull::int AS notnull, typdefault, ".collation_name("typcollation")." AS collation
FROM pg_type WHERE oid = $q"));$F["definition"]="AS $ic[base]".($ic["collation"]?" COLLATE ".idf_escape($ic["collation"]):"").($ic["typdefault"]!=""?" DEFAULT $ic[typdefault]":"").($ic["notnull"]?" NOT NULL":"");foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE contypid = $q AND contype != 'n' ORDER BY conname")as$G)$F["definition"].=" CONSTRAINT ".idf_escape($G["conname"])." $G[definition]";break;case'r':$ih=first(get_rows("SELECT format_type(rngsubtype, NULL) AS subtype,
(SELECT opcname FROM pg_opclass WHERE oid = rngsubopc) AS subtype_opclass,
".collation_name("rngcollation")." AS collation,
NULLIF(rngcanonical, 0)::regproc::text AS canonical,
NULLIF(rngsubdiff, 0)::regproc::text AS subtype_diff".(min_version(14)?",
(SELECT typname FROM pg_type WHERE oid = rngmultitypid) AS multirange_type_name":"")."
FROM pg_range WHERE rngtypid = $q"));$_=array();foreach(array("subtype"=>0,"subtype_opclass"=>1,"collation"=>1,"canonical"=>0,"subtype_diff"=>0,"multirange_type_name"=>1)as$t=>$Ec){if($ih[$t]!="")$_[]=strtoupper($t)." = ".($Ec?idf_escape($ih[$t]):$ih[$t]);}$F["definition"]="AS RANGE (".implode(", ",$_).")";}return$F;}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return(string)get_val("SELECT current_schema()");}function
set_schema($I,$g=null){$_GET["ns"]=$I;$F=get_val("SELECT set_config('search_path', ".q(idf_escape($I)).", false) FROM pg_namespace WHERE nspname = ".q($I),0,$g);driver()->setUserTypes(types(true));return!!$F;}function
schema_comment($I){return(string)get_val("SELECT obj_description(oid, 'pg_namespace') FROM pg_namespace WHERE nspname = ".q($I));}function
set_schema_comment($I,$kb){return!!queries("COMMENT ON SCHEMA ".idf_escape($I)." IS ".q($kb));}function
drop_sql(array$S){$F="";foreach(drop_kinds($S)as$Je=>$Lf)$F
.="DROP $Je IF EXISTS ".implode(", ",$Lf).";\n";return($F?"$F\n":"");}function
foreign_keys_sql($Q){$F="";$ed=foreign_keys($Q);ksort($ed);foreach($ed
as$dd=>$cd){$F
.="ALTER TABLE ONLY ".table($Q)." ADD CONSTRAINT ".idf_escape($dd)." ".preg_replace_callback('~( REFERENCES )([^(.]+)\(~',function(array$x){return$x[1].table(idf_unescape($x[2]))."(";},$cd["definition"]).";\n";}return($F?"$F\n":$F);}function
indexes_sql($Q,$Ug=""){$F="";$D="SELECT indexdef, quote_ident(schemaname) || '.' || quote_ident(tablename) AS qualified, quote_ident(current_database()) AS db
FROM pg_catalog.pg_indexes
WHERE schemaname = current_schema() AND tablename = ".q($Q).($Ug!=""?" AND indexname != ".q($Ug):"");foreach(get_rows($D,null,"-- ")as$G)$F
.="\n\n".str_replace(array(" $G[db].$G[qualified] USING "," $G[qualified] USING ")," ".table($Q)." USING ",$G["indexdef"]).";";return$F;}function
create_sql($Q,$Aa,$vi){$xh=array();$Rh=array();$Sh=array();$Qh=array();$O=table_status1($Q);if(is_view($O)){$Lj=view($Q);$Eb="CREATE ".strtoupper($O["Engine"])." ".table($Q)." AS ".rtrim($Lj["select"],";").";";return
rtrim($Eb.indexes_sql($Q),';');}$l=fields($Q);if(count($O)<2||empty($l))return"";$F="CREATE TABLE ".table($O['Name'])." (\n    ";$Ci=q(table($O['Name']));foreach($l
as$k){$Th="";if($k['default']=="nextval('$O[Name]_$k[field]_seq')"){$Th=table("$O[Name]_$k[field]_seq");$k['default']=null;$k['full_type']=preg_replace('~int(eger)?~','serial',$k['full_type']);}$vg=idf_escape($k['field']).' '.full_type_sql($k).preg_replace_callback('~(nextval\(\')([^.\']+)\'~',function(array$x){return$x[1].str_replace("'","''",table(idf_unescape($x[2])))."'";},default_value($k)).($k['null']?"":" NOT NULL");$xh[]=$vg;if(preg_match('~nextval\(\'([^\']+)\'\)~',$k['default'],$jf)){$Ph=$jf[1];$ni=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($Ph)):"SELECT * FROM $Ph"),null,"-- "));$Oh=table(idf_unescape($Ph));$Rh[]=($vi=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $Oh;\n":"")."CREATE SEQUENCE $Oh INCREMENT $ni[increment_by] MINVALUE $ni[min_value] MAXVALUE $ni[max_value]"." CACHE $ni[cache_value];";if(get_val("SELECT pg_get_serial_sequence($Ci, ".q($k['field']).")"))$Sh[]="\n\nALTER SEQUENCE $Oh OWNED BY ".table($O['Name']).".".idf_escape($k['field']).";";if($Aa)$Qh[]=$Oh;}elseif($Aa&&$k['auto_increment']){$Oh=($Th?"":get_val("SELECT pg_get_serial_sequence($Ci, ".q($k['field']).")::regclass"));$Qh[]=($Oh?table(idf_unescape($Oh)):$Th);}}if(!empty($Rh))$F=implode("\n\n",$Rh)."\n\n$F";$Ug="";foreach(indexes($Q)as$ge=>$s){if($s['type']=='PRIMARY'){$Ug=$ge;$xh[]="CONSTRAINT ".idf_escape($ge)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$s['columns'])).")";}}foreach(driver()->checkConstraints($Q)as$rb=>$tb)$xh[]="CONSTRAINT ".idf_escape($rb)." CHECK ($tb)";$F
.=implode(",\n    ",$xh)."\n)";$wg=driver()->partitionsInfo($O['Name']);if($wg)$F
.="\nPARTITION BY $wg[partition_by]($wg[partition])";$F
.=(min_version(12)?"":"\nWITH (oids = ".($O['Oid']?'true':'false').")").";";$F
.=implode($Sh);if($O['Comment'])$F
.="\n\nCOMMENT ON TABLE ".table($O['Name'])." IS ".q($O['Comment']).";";foreach($l
as$Tc=>$k){if($k['comment'])$F
.="\n\nCOMMENT ON COLUMN ".table($O['Name']).".".idf_escape($Tc)." IS ".q($k['comment']).";";}$F
.=indexes_sql($Q,$Ug);foreach(array_filter($Qh)as$Oh){$ni=first(get_rows("SELECT last_value, is_called::int FROM $Oh",null,"-- "));if($ni['is_called'])$F
.="\n\nDO \$\$ BEGIN PERFORM setval(".q($Oh).", $ni[last_value]); END \$\$;";}return
rtrim($F,';');}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
truncate_all_sql(array$S){return($S?"TRUNCATE ".implode(", ",array_map('Adminer\table',$S)).";\n\n":"");}function
trigger_sql($Q){$O=table_status1($Q);$F="";foreach(triggers($Q)as$cj=>$bj){$dj=trigger($cj,$O['Name']);$F
.="\nCREATE TRIGGER ".idf_escape($dj['Trigger'])." $dj[Timing] $dj[Event] ON ".table($O['Name'])." $dj[Type] $dj[Statement];;\n";}return$F;}function
use_sql($Nb,$vi=""){$y=idf_escape($Nb);$F="";if(preg_match('~CREATE~',$vi)){if($vi=="DROP+CREATE")$F="DROP DATABASE IF EXISTS $y;\n";$F
.="CREATE DATABASE $y;\n";}return"$F\\connect $y";}function
use_schema_sql($I,$vi){$y=idf_escape($I);$F="";if(preg_match('~CREATE~',$vi)){if($vi=="DROP+CREATE")$F="DROP SCHEMA IF EXISTS $y CASCADE;\n";$F
.="CREATE SCHEMA IF NOT EXISTS $y;\n";$kb=schema_comment($I);if($kb!="")$F
.="COMMENT ON SCHEMA $y IS ".q($kb).";\n";}return$F."SET search_path TO $y";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field(array$k){if(preg_match('~^(geometry|geography)$~',$k["type"])&&strpos($k["full_type"],"[")===false)return"ST_AsEWKT(".idf_escape($k["field"]).")";}function
unconvert_field(array$k,$F){return($k["composite"]?"$F::".full_type_sql($k):$F);}function
support($Sc){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|fast_status|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|deferrable').(connection()->flavor=='cockroach'?'':'|transaction_ddl').(connection()->flavor=='cockroach'||!min_version(9.1)?'':'|extension').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$Sc);}function
kill_process($q){return
queries("SELECT pg_terminate_backend(".number($q).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach(array$L,$U,$B){$this->link=new
\SQLite3($L["path"]);if(method_exists($this->link,'setAuthorizer'))$this->link->setAuthorizer(array($this,'authorize'));$Kj=\SQLite3::version();$this->server_info=$Kj["versionString"];return'';}function
query($D,$lj=false){$E=@$this->link->query($D);$this->error="";if(!$E){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($E->numColumns())return
new
Result($E);$this->affected_rows=$this->link->changes();return
true;}function
quote($P){return(is_utf8($P)?"'".$this->link->escapeString($P)."'":"x'".bin2hex($P)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($E){$this->result=$E;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$kj=array(1=>"integer","real","text","blob","null");$d=$this->offset++;return(object)array("name"=>$this->result->columnName($d),"native_type"=>$kj[$this->result->columnType($d)],);}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach(array$L,$U,$B){$F=$this->dsn(DRIVER.":".$L["path"],"","",array(),(class_exists('Pdo\Sqlite')?'Pdo\Sqlite':'PDO'));if(!$F&&method_exists($this->pdo,'setAuthorizer'))$this->pdo->setAuthorizer(array($this,'authorize'));return$F;}function
quote($P){return(is_utf8($P)?parent::quote($P):"x'".bin2hex($P)."'");}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{private$attaching=false;function
attach(array$L,$U,$B){parent::attach($L,$U,$B);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($m){$D="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$m)?$m:dirname($_SERVER["SCRIPT_FILENAME"])."/$m")." AS a";$this->attaching=true;$ya=is_readable($m)&&$this->query($D);$this->attaching=false;if($ya)return!self::attach(server_parts(array("path"=>$m)),'','');return
false;}function
authorize($ea,$ta){return($ea!=24||$ta===''||$this->attaching?0:1);}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";static$passwords=false;static$serverFile=true;protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$fulltextOperator="MATCH";var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
operators($Ai){$F=array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");if(preg_match('~^fts\d+$~i',(string)idx($Ai,"Engine")))$F[]="MATCH";$F[]="SQL";return$F;}static
function
connect($L,$U,$B){return
parent::connect(":memory:","","");}function
__construct(Db$f){parent::__construct($f);if(min_version(3.31,0,$f))$this->generated=array("STORED","VIRTUAL");if(min_version(3.37,0,$f))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($Ah){return"x".q(bin2hex($Ah));}function
typeName(\stdClass$k){$F=strtolower(idx((array)$k,'sqlite:decl_type',parent::typeName($k)));return
idx(array("string"=>"text","double"=>"real"),$F,$F);}function
engines(){$F=array("table");if(min_version("3.8.2")){if(min_version(3.37)){$F[]="STRICT";$F[]="STRICT, WITHOUT ROWID";}$F[]="WITHOUT ROWID";}return$F;}private
function
isVirtual(array$R){$zc=$R["Engine"];return$zc!=""&&!in_array($zc,array_merge(array("view"),$this->engines()));}function
supportsIndex(array$R){return!is_view($R)&&!$this->isVirtual($R);}function
supportsAlterIndex(array$R){return$this->supportsIndex($R);}function
supportsAlterTable(array$Ai){return!$this->isVirtual($Ai);}function
shadowTables($Q){$F=array();if(min_version(3.37)){foreach(get_vals("SELECT name FROM pragma_table_list WHERE schema = 'main' AND type = 'shadow' ORDER BY name")as$y){if(preg_match('(^'.preg_quote($Q).'_[^_]*$)',$y))$F[]=array("table"=>$y,"ns"=>"");}}return$F;}function
fulltextSql($y,array$s,$D,$Oa){return
idf_escape($y)." MATCH ".q($D);}function
insertUpdate($Q,array$H,array$Ug){$Y=array();foreach($H
as$M)$Y[]="(".implode(", ",$M).")";return
queries("REPLACE INTO ".table($Q)." (".implode(", ",array_keys(reset($H))).") VALUES\n".implode(",\n",$Y));}function
tableHelp($y,$Ce=false){if(preg_match('~^sqlite_(seq|stat.)~',$y,$x))return"fileformat2.html#$x[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$y))return"schematab.html";}function
checkConstraints($Q){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$this->conn),$jf);return
array_combine($jf[2],$jf[2]);}function
allFields(){$F=array();if(min_version(3.16)){$H=get_rows('SELECT m.name AS tab, p.name AS field, p.type, p."notnull", p.pk AS '.idf_escape("primary")."
FROM sqlite_master m, pragma_table_".(min_version(3.31)?"x":"")."info(m.name) p
WHERE m.type IN ('table', 'view')".(min_version(3.31)?"
AND p.hidden != 1":"").(min_version(3.37)?"
AND m.name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (m.name LIKE 'sqlite_%'), m.name, p.cid",$this->conn);foreach($H
as$G){$G["type"]=type_affinity($G["type"]);$G["null"]=!$G["notnull"];$F[$G["tab"]][]=$G;}}else$F=parent::allFields();return$F;}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return
idf_escape($r);}function
get_databases($hd){return
array();}function
limit($D,$Z,$v,$Vf=0,$K=" "){return" $D$Z".($v?$K."LIMIT $v".($Vf?" OFFSET $Vf":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return(preg_match('~^INTO~',$D)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($D,$Z,1,0,$K):" $D WHERE rowid = (SELECT rowid FROM ".table($Q).$Z.$K."LIMIT 1)");}function
db_collation($h,array$hb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
virtual_module($oi){return(preg_match('~^CREATE\s+VIRTUAL\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:"[^"]*+"|`[^`]*+`|\[[^\]]*+\]|[^\s(]+)\s+USING\s+([a-z0-9_]+)~i',$oi,$x)?$x[1]:"");}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view')".(min_version(3.37)?" AND name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$Ob){return
array();}function
db_status(){$rg=get_val("PRAGMA page_size");$td=get_val("PRAGMA freelist_count")*$rg;return
array("Data_length"=>get_val("PRAGMA page_count")*$rg-$td,"Index_length"=>0,"Data_free"=>$td,);}function
table_status($y="",$Rc=false){$F=array();$H=array();if(!$Rc&&$y==""){connection()->query("PRAGMA optimize = 0x10002");$H=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment".(min_version(3.37)?", name IN (SELECT name FROM pragma_table_list WHERE type = 'shadow') AS dependent":"")." FROM sqlite_master WHERE type IN ('table', 'view') ".($y!=""?"AND name = ".q($y):"ORDER BY (name LIKE 'sqlite_%'), name"))as$G){if($G["Engine"]=="table"){$oi=preg_replace('~(?:\s|--[^\n]*|/\*.*?\*/)+$~s','',$G["sql"]);$wi=preg_replace('~.*\)~s','',$oi);$G["Engine"]=virtual_module($G["sql"])?:(implode(", ",array_filter(array((preg_match('~\bSTRICT\b~i',$wi)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$wi)?"WITHOUT ROWID":0),)))?:"table");}unset($G["sql"]);$G["Rows"]=idx($H,$G["Name"],0);$F[$G["Name"]]=$G;}if(!$Rc){foreach(get_rows("SELECT * FROM sqlite_sequence".($y!=""?" WHERE name = ".q($y):""),null,"")as$G)$F[$G["name"]]["Auto_increment"]=$G["seq"];}return$F;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
type_affinity($T){$T=strtolower($T);return(preg_match('~int~i',$T)?"integer":(preg_match('~char|clob|text~i',$T)?"text":(preg_match('~blob~i',$T)?"blob":(preg_match('~real|floa|doub~i',$T)?"real":(preg_match('~any~i',$T)?"any":"numeric")))));}function
fields($Q){$F=array();$oi=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q));$Yg=array("select"=>1,"where"=>1,"order"=>1);if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$Q))$Yg+=array("insert"=>1,"update"=>1);$vd=preg_match('~^fts\d+$~i',virtual_module($oi));foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($Q).")")as$G){if($G["hidden"]==1)continue;$y=$G["name"];$T=strtolower($G["type"]);$i=$G["dflt_value"];$F[$y]=array("field"=>$y,"type"=>($vd?"text":type_affinity($T)),"full_type"=>$T,"default"=>(preg_match("~^'(.*)'$~",$i,$x)?str_replace("''","'",$x[1]):($i=="NULL"?null:$i)),"null"=>!$G["notnull"],"privileges"=>$Yg,"primary"=>$G["pk"],);if($G["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$oi))$F[$y]["auto_increment"]=true;}$r='[(,]\s*(("[^"]*+")+|[a-z0-9_]+)';$th='(?:[^,()\']|\'[^\']*+\'|\([^)]*+\))*?';preg_match_all('~'.$r.'\s+text\b'.$th.'COLLATE\s+(\'[^\']+\'|[a-z0-9_]+)~i',$oi,$jf,PREG_SET_ORDER);foreach($jf
as$x){$y=str_replace('""','"',preg_replace('~^"|"$~','',$x[1]));if($F[$y])$F[$y]["collation"]=trim($x[3],"'");}preg_match_all('~'.$r.'\s'.$th.'GENERATED\s+ALWAYS\s+AS\s*\((.+?)\)\s+(STORED|VIRTUAL)~i',$oi,$jf,PREG_SET_ORDER);foreach($jf
as$x){$y=str_replace('""','"',preg_replace('~^"|"$~','',$x[1]));if($F[$y]){$F[$y]["default"]=$x[3];$F[$y]["generated"]=strtoupper($x[4]);}}return$F;}function
indexes($Q,$g=null){$g=connection($g);$F=array();$oi=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$g);if(preg_match('~^fts\d+$~i',virtual_module($oi)))return
array($Q=>array("type"=>"FULLTEXT","columns"=>array_keys(fields($Q)),"lengths"=>array(),"descs"=>array()));if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$oi,$x)){$F[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$x[1],$jf,PREG_SET_ORDER);foreach($jf
as$x){$F[""]["columns"][]=idf_unescape($x[2]).$x[4];$F[""]["descs"][]=(preg_match('~DESC~i',$x[5])?'1':null);}}if(!$F){foreach(fields($Q)as$y=>$k){if($k["primary"])$F[""]=array("type"=>"PRIMARY","columns"=>array($y),"lengths"=>array(),"descs"=>array(null));}}$qi=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($Q),$g);foreach(get_rows("PRAGMA index_list(".table($Q).")",$g)as$G){$y=$G["name"];$s=array("type"=>($G["unique"]?"UNIQUE":"INDEX"));$s["lengths"]=array();$s["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($y).")",$g)as$_h){$s["columns"][]=$_h["name"];$s["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($y).' ON '.idf_escape($Q),'~').' \((.*)\)$~i',$qi[$y],$oh)){preg_match_all('/("[^"]*+")+( DESC)?/',$oh[2],$jf);foreach($jf[2]as$t=>$W){if($W)$s["descs"][$t]='1';}}if(!$F[""]||$s["type"]!="UNIQUE"||$s["columns"]!=$F[""]["columns"]||$s["descs"]!=$F[""]["descs"]||!preg_match("~^sqlite_~",$y))$F[$y]=$s;}return$F;}function
foreign_keys($Q){$F=array();foreach(get_rows("PRAGMA foreign_key_list(".table($Q).")")as$G){$md=&$F[$G["id"]];if(!$md)$md=$G;$md["source"][]=$G["from"];$md["target"][]=$G["to"];}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($y))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($h,$I=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($y){$Oc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Oc)\$~",$y)){connection()->error=lang(36,str_replace("|",", ",$Oc));return
false;}return
true;}function
create_database($h,$gb){if(file_exists($h)){connection()->error=lang(37);return
false;}if(!check_sqlite_name($h))return
false;try{$w=new
Db();$w->attach(server_parts(array("path"=>$h)),'','');}catch(\Exception$Hc){connection()->error=$Hc->getMessage();return
false;}$w->query('PRAGMA encoding = "UTF-8"');$w->query('CREATE TABLE adminer (i)');$w->query('DROP TABLE adminer');return
true;}function
drop_databases(array$Ob){connection()->attach(server_parts(array("path"=>":memory:")),'','');foreach($Ob
as$h){if(!check_sqlite_name($h))return
false;if(!@unlink($h)){connection()->error=lang(37);return
false;}}return
true;}function
rename_database($y,$gb){if(!check_sqlite_name($y))return
false;connection()->attach(server_parts(array("path"=>":memory:")),'','');connection()->error=lang(37);return@rename(DB,$y);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($Q,$y,array$l,array$jd,$kb,$zc,$gb,$Aa,$yg){$zj=($Q==""||$jd||$zc);foreach($l
as$k){if($k[0]!=""||!$k[1]||$k[2]){$zj=true;break;}}$b=array();$mg=array();foreach($l
as$k){if($k[1]){$b[]=($zj?$k[1]:"ADD ".implode($k[1]));if($k[0]!="")$mg[$k[0]]=$k[1][0];}}if(!$zj){foreach($b
as$W){if(!queries("ALTER TABLE ".table($Q)." $W"))return
false;}if($Q!=$y&&!queries("ALTER TABLE ".table($Q)." RENAME TO ".table($y)))return
false;}elseif(!recreate_table($Q,$y,$b,$mg,$jd,$Aa,array(),"","",$zc))return
false;if($Aa){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $Aa WHERE name = ".q($y));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($y).", $Aa)");queries("COMMIT");}return
true;}function
recreate_table($Q,$y,array$l,array$mg,array$jd,$Aa="",$ie=array(),$mc="",$ha="",$zc=""){if($Q!=""){if(!$l){foreach(fields($Q)as$t=>$k){if($ie)$k["auto_increment"]=0;$l[]=process_field($k,$k);$mg[$t]=idf_escape($t);}}$Vg=false;foreach($l
as$k){if($k[6])$Vg=true;}$nc=array();foreach($ie
as$t=>$W){if($W[2]=="DROP"){$nc[$W[1]]=true;unset($ie[$t]);}}foreach(indexes($Q)as$Ge=>$s){$e=array();foreach($s["columns"]as$t=>$d){if(!$mg[$d])continue
2;$e[]=$mg[$d].($s["descs"][$t]?" DESC":"");}if(!$nc[$Ge]){if($s["type"]!="PRIMARY"||!$Vg)$ie[]=array($s["type"],$Ge,$e);}}foreach($ie
as$t=>$W){if($W[0]=="PRIMARY"){unset($ie[$t]);$jd[]="  PRIMARY KEY (".implode(", ",$W[2]).")";}}foreach(foreign_keys($Q)as$Ge=>$md){foreach($md["source"]as$t=>$d){if(!$mg[$d])continue
2;$md["source"][$t]=idf_unescape($mg[$d]);}if(!isset($jd[" $Ge"]))$jd[]=" ".format_foreign_key($md);}queries("BEGIN");}$Ta=array();foreach($l
as$k){if(preg_match('~GENERATED~',$k[3]))unset($mg[array_search($k[0],$mg)]);$Ta[]="  ".implode($k);}$Ta=array_merge($Ta,array_filter($jd));foreach(driver()->checkConstraints($Q)as$Wa){if($Wa!=$mc)$Ta[]="  CHECK ($Wa)";}if($ha)$Ta[]="  CHECK ($ha)";$Ii=($Q!=""&&$Q==$y?"adminer_$y":$y);if(!$zc&&$Q!="")$zc=idx(table_status1($Q),"Engine");if(!queries("CREATE TABLE ".table($Ii)." (\n".implode(",\n",$Ta)."\n)".($zc!="table"&&in_array($zc,driver()->engines())?" $zc":"")))return
false;if($Q!=""){if($mg&&!queries("INSERT INTO ".table($Ii)." (".implode(", ",$mg).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($mg)))." FROM ".table($Q)))return
false;$gj=array();foreach(triggers($Q)as$ej=>$Oi){$dj=trigger($ej,$Q);$gj[]="CREATE TRIGGER ".idf_escape($ej)." ".implode(" ",$Oi)." ON ".table($y)."\n$dj[Statement]";}$Aa=$Aa?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($Q));if(!queries("DROP TABLE ".table($Q))||($Q==$y&&!queries("ALTER TABLE ".table($Ii)." RENAME TO ".table($y)))||!alter_indexes($y,$ie))return
false;if($Aa)queries("UPDATE sqlite_sequence SET seq = $Aa WHERE name = ".q($y));foreach($gj
as$dj){if(!queries($dj))return
false;}queries("COMMIT");}return
true;}function
index_sql($Q,$T,$y,$e){return"CREATE $T ".($T!="INDEX"?"INDEX ":"").idf_escape($y!=""?$y:uniqid($Q."_"))." ON ".table($Q)." $e";}function
alter_indexes($Q,$b){foreach($b
as$Ug){if($Ug[0]=="PRIMARY")return
recreate_table($Q,$Q,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$W){if(!queries($W[2]=="DROP"?"DROP INDEX ".idf_escape($W[1]):index_sql($Q,$W[0],$W[1],"(".implode(", ",$W[2]).")")))return
false;}return
true;}function
truncate_tables(array$S){return
apply_queries("DELETE FROM",$S);}function
drop_views(array$Mj){return
apply_queries("DROP VIEW",$Mj);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
move_tables(array$S,array$Mj,$Gi){return
false;}function
trigger($y,$Q){if($y=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$r='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$fj=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$r\\s*(".implode("|",$fj["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($r))?\\s+ON\\s*$r\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($y)),$x);if(!$x)return
array();$Uf=$x[3];return
array("Timing"=>strtoupper($x[1]),"Event"=>strtoupper($x[2]).($Uf?" OF":""),"Of"=>idf_unescape($Uf),"Trigger"=>$y,"Statement"=>$x[4],);}function
triggers($Q){$F=array();$fj=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q))as$G){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$fj["Timing"]).')\s*(.*?)\s+ON\b~i',$G["sql"],$x);$F[$G["name"]]=array($x[1],$x[2]);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
last_id($E){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$f,$D){return$f->query("EXPLAIN QUERY PLAN $D");}function
found_rows(array$R,array$Z){}function
types($Oc=false){return
array();}function
create_sql($Q,$Aa,$vi){$F=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($Q));foreach(indexes($Q)as$y=>$s){if($y==''||$s['type']=='FULLTEXT')continue;$F
.=";\n\n".index_sql($Q,$s['type'],$y,"(".implode(", ",array_map('Adminer\idf_escape',$s['columns'])).")");}return$F;}function
truncate_sql($Q){return"DELETE FROM ".table($Q);}function
use_sql($Nb,$vi=""){return"";}function
trigger_sql($Q){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q)));}function
show_variables(){$F=array();foreach(get_rows("PRAGMA pragma_list")as$G){$y=$G["name"];if($y!="pragma_list"&&$y!="compile_options"){$F[$y]=array($y,'');foreach(get_rows("PRAGMA $y")as$G)$F[$y][1].=implode(", ",$G)."\n";}}return$F;}function
show_status(){$F=array();foreach(get_vals("PRAGMA compile_options")as$cg)$F[]=explode("=",$cg,2)+array('','');return$F;}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($Sc){return
preg_match('~^(check|columns|database|drop_col|dump|fast_status|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$Sc);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result,$warnings,$transaction=false;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$j){$this->errno=$j["code"];$this->error
.="$j[message]\n";}$this->error=rtrim($this->error);}function
attach(array$L,$U,$B){sqlsrv_configure("WarningsReturnAsErrors",0);$sb=array("UID"=>$U,"PWD"=>$B,"CharacterSet"=>"UTF-8","ReturnDatesAsStrings"=>true);if(isset($_GET["sql"])&&!self::$instance)$sb["MultipleActiveResultSets"]=false;$N=adminer()->connectSsl();if(isset($N["Encrypt"]))$sb["Encrypt"]=$N["Encrypt"];if(isset($N["TrustServerCertificate"]))$sb["TrustServerCertificate"]=$N["TrustServerCertificate"];$h=adminer()->database();if($h!="")$sb["Database"]=$h;$Lg=$L["port"];$this->link=@sqlsrv_connect($L["host"].($Lg?",$Lg":""),$sb);if($this->link){$me=sqlsrv_server_info($this->link);$this->server_info=$me['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($P){return
unicode_prefix($P)."'".str_replace("'","''",$P)."'";}function
select_db($Nb){return$this->query(use_sql($Nb));}function
query($D,$lj=false){$E=sqlsrv_query($this->link,$D);$this->error="";if(!$E){$this->get_error();return
false;}return$this->store_result($E);}function
multi_query($D){$this->result=sqlsrv_query($this->link,$D);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($E=null){if(!$E)$E=$this->result;if(!$E)return
false;$this->warnings=sqlsrv_errors(SQLSRV_ERR_WARNINGS);if(sqlsrv_field_metadata($E))return
new
Result($E);$this->affected_rows=sqlsrv_rows_affected($E);return
true;}function
next_result(){if(!$this->result)return
false;$F=sqlsrv_next_result($this->result);if($F===false){$this->get_error();$this->result=null;return
true;}return!!$F;}function
warnings(){$F=array();foreach((array)$this->warnings
as$Pj)$F[]=$Pj["message"];return$F;}function
inTransaction(){return$this->transaction||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT",0,$this));}function
begin(){$this->transaction=sqlsrv_begin_transaction($this->link);if(!$this->transaction)$this->get_error();return$this->transaction;}function
commit(){if($this->transaction&&!sqlsrv_commit($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}function
rollback(){if(!$this->transaction&&isset($_GET["sql"]))return!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK");if($this->transaction&&!sqlsrv_rollback($this->link)){$this->get_error();return
false;}$this->transaction=false;return
true;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($E){$this->result=$E;}function
fetch_assoc(){return
sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC);}function
fetch_row(){return
sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC);}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$kj=array(-155=>"datetimeoffset","time",-152=>"xml","varbinary","sql_variant",-11=>"uniqueidentifier","ntext","nvarchar","nchar","bit","tinyint","bigint","image","varbinary","binary","text",1=>"char","numeric","decimal","int","smallint","float","real","float",12=>"varchar",91=>"date","time","datetime",);$k=$this->fields[$this->offset++];$F=new
\stdClass;$F->name=$k["Name"];$F->native_type=idx($kj,$k["Type"],"");return$F;}function
seek($Vf){for($p=0;$p<$Vf;$p++)sqlsrv_fetch($this->result);}}function
last_id($E){return(string)get_val("SELECT SCOPE_IDENTITY()");}function
explain(Db$f,$D){$f->query("SET SHOWPLAN_ALL ON");$F=$f->query($D);$f->query("SET SHOWPLAN_ALL OFF");return$F;}}else{abstract
class
MssqlDb
extends
PdoDb{function
quote($P){return
unicode_prefix($P).parent::quote($P);}function
select_db($Nb){return$this->query(use_sql($Nb));}function
lastInsertId(){return$this->pdo->lastInsertId();}function
inTransaction(){return
parent::inTransaction()||(isset($_GET["sql"])&&get_val("SELECT @@TRANCOUNT"));}function
rollback(){return(parent::inTransaction()||!isset($_GET["sql"])?parent::rollback():!!$this->query("IF @@TRANCOUNT > 0 ROLLBACK"));}function
warnings(){$E=$this->multi;if(!is_object($E))return
array();$j=$E->errorInfo();return
array((string)$j[2]);}}function
last_id($E){return
connection()->lastInsertId();}function
explain(Db$f,$D){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach(array$L,$U,$B){$Lg=$L["port"];$oc="sqlsrv:Server=$L[host]".($Lg?",$Lg":"").(isset($_GET["sql"])&&!self::$instance?";MultipleActiveResultSets=0":"");$N=adminer()->connectSsl();foreach(array("Encrypt","TrustServerCertificate")as$t){if(isset($N[$t]))$oc
.=";$t=".($N[$t]?1:0);}return$this->dsn($oc,$U,$B,array(\PDO::SQLSRV_ATTR_DIRECT_QUERY=>true));}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach(array$L,$U,$B){$Lg=$L["port"];$ii=$L["socket"];$_=array(1002=>true);$F=$this->dsn("dblib:charset=utf8;host=$L[host]".($Lg!=""?";port=$Lg":($ii!=""?";unix_socket=$ii":"")),$U,$B,$_);if(!$F){$this->query("SET ANSI_NULLS, QUOTED_IDENTIFIER, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON");$this->server_info=get_val("SELECT CAST(SERVERPROPERTY('ProductVersion') AS varchar(20))",0,$this);}return$F;}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";static$serverSocket=true;var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$inout="|OUTPUT";private$unknownTypes=array();function
operators($Ai){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");}static
function
connect($L,$U,$B){if($L=="")$L="localhost:1433";return
parent::connect($L,$U,$B);}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(29)=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"numeric"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20,"vector"=>0,),lang(30)=>array("date"=>10,"smalldatetime"=>19,"datetime"=>23,"datetime2"=>19,"time"=>8,"datetimeoffset"=>26),lang(31)=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823,"uniqueidentifier"=>36,"xml"=>2147483647,"json"=>2147483647,"sql_variant"=>8000,"hierarchyid"=>892,),lang(32)=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),lang(34)=>array("geometry"=>0,"geography"=>0),);$kj=array_flip(get_vals("SELECT name FROM sys.types WHERE is_user_defined = 0 ORDER BY name"));if($kj){foreach($this->types
as$o=>$Dd){foreach($Dd
as$T=>$u){if(isset($kj[$T]))unset($kj[$T]);else
unset($this->types[$o][$T]);}if(!$this->types[$o])unset($this->types[$o]);}$this->unknownTypes=array_keys($kj);}}function
types(){return
parent::types()+array_fill_keys($this->unknownTypes,0);}function
structuredTypes(){return
array_merge(parent::structuredTypes(),$this->unknownTypes);}function
typeName(\stdClass$k){return
idx((array)$k,'sqlsrv:decl_type',parent::typeName($k));}function
insertUpdate($Q,array$H,array$Ug){$l=fields($Q);$vj=array();$Z=array();$M=reset($H);$e="c".implode(", c",range(1,count($M)));$Ra=0;$qe=array();foreach($M
as$t=>$W){$Ra++;$y=idf_unescape($t);if(!$l[$y]["auto_increment"])$qe[$t]="c$Ra";if(isset($Ug[$y]))$Z[]="$t = c$Ra";else$vj[]="$t = c$Ra";}$Y=array();foreach($H
as$M)$Y[]="(".implode(", ",$M).")";if($Z){$be=queries("SET IDENTITY_INSERT ".table($Q)." ON");$F=queries("MERGE ".table($Q)." USING (VALUES\n\t".implode(",\n\t",$Y)."\n) AS source ($e) ON ".implode(" AND ",$Z).($vj?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$vj):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($be?$M:$qe)).") VALUES (".($be?$e:implode(", ",$qe)).");");if($be)queries("SET IDENTITY_INSERT ".table($Q)." OFF");}else$F=queries("INSERT INTO ".table($Q)." (".implode(", ",array_keys($M)).") VALUES\n".implode(",\n",$Y));return$F;}function
begin(){remember_query("BEGIN TRANSACTION");return$this->conn->begin();}function
convertSearch($r,array$W,array$k){return(preg_match('~^(bit|n?text|xml|json|vector|uniqueidentifier|sql_variant|hierarchyid|geography|geometry)$~',$k["type"])?"CAST($r AS nvarchar(max))":$r);}function
quoteBinary($Ah){return"0x".bin2hex($Ah);}function
warnings(){$F=array();foreach($this->conn->warnings()as$wf){$wf=trim(preg_replace('~^(\[[^]]+])+~','',$wf));if($wf!="")$F[]=$wf;}return
nl_br(h(implode("\n",$F)));}function
tableHelp($y,$Ce=false){$Ze=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$w=$Ze[get_schema()];if($w)return"relational-databases/system-$w".preg_replace('~_~','-',strtolower($y))."-transact-sql";}function
isSystem($h,$I=""){return($I!=""?information_schema($h,$I)||preg_match('~^(guest|db_(owner|accessadmin|securityadmin|ddladmin|backupoperator|(deny)?data(reader|writer)))$~',$I):in_array($h,array("master","tempdb","model","msdb")));}}function
unicode_prefix($P){return(strlen($P)!=utf8_length($P)?"N":"");}function
idf_escape($r){return"[".str_replace("]","]]",$r)."]";}function
table($r){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($r);}function
get_databases($hd){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($D,$Z,$v,$Vf=0,$K=" "){return($v?" TOP (".($v+$Vf).")":"")." $D$Z";}function
limit1($Q,$D,$Z,$K="\n"){return
limit($D,$Z,1,0,$K);}function
db_collation($h,array$hb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($h));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables(array$Ob){$F=array();foreach($Ob
as$h){connection()->select_db($h);$F[$h]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$F;}function
table_status($y="",$Rc=false){$F=array();$gi=array();foreach(get_rows("SELECT object_id, SUM(CASE WHEN index_id < 2 THEN row_count ELSE 0 END) AS [Rows],
SUM(CASE WHEN index_id < 2 THEN used_page_count ELSE 0 END) * 8192 AS Data_length,
SUM(CASE WHEN index_id > 1 THEN used_page_count ELSE 0 END) * 8192 AS Index_length,
SUM(reserved_page_count - used_page_count) * 8192 AS Data_free
FROM sys.dm_db_partition_stats
GROUP BY object_id",null,"")as$G){$Tf=$G["object_id"];unset($G["object_id"]);$gi[$Tf]=$G;}foreach(get_rows("SELECT ao.object_id, ao.name AS Name, ao.type_desc AS Engine,
	(SELECT cast(value as varchar(max)) FROM fn_listextendedproperty('MS_Description', 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($y!=""?"AND name = ".q($y):"ORDER BY name"))as$G){$Tf=$G["object_id"];unset($G["object_id"]);$F[$G["Name"]]=$G+idx($gi,$Tf,array());}return$F;}function
is_view(array$R){return$R["Engine"]=="VIEW";}function
fk_support(array$R){return
true;}function
type_length($T,array$G){return(preg_match("~char|binary~",$T)?($G["max_length"]==-1?"max":intval($G["max_length"])/($T[0]=='n'?2:1)):($T=="decimal"?"$G[precision],$G[scale]":(preg_match('~^(datetime2|datetimeoffset|time)$~',$T)?$G["scale"]:($T=="vector"?(intval($G["max_length"])-8)/4:""))));}function
fields($Q){$mb=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($Q).", 'column', NULL)");$F=array();$Bi=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($Q));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name,
	COALESCE(bt.name, t.name) type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.types bt ON t.system_type_id = bt.user_type_id AND t.is_user_defined = 1
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($Bi))as$G){$T=$G["type"];$u=type_length($T,$G);$F[$G["name"]]=array("field"=>$G["name"],"full_type"=>$T.($u!=""?"($u)":""),"type"=>$T,"length"=>$u,"default"=>(preg_match("~^\(N?'(.*)'\)$~s",$G["default"],$x)?str_replace("''","'",$x[1]):$G["default"]),"default_constraint"=>$G["default_constraint"],"null"=>$G["is_nullable"],"auto_increment"=>$G["is_identity"],"collation"=>$G["collation_name"],"privileges"=>($T=="timestamp"?array("select"=>1,"where"=>1,"order"=>1):array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1)),"primary"=>$G["is_primary_key"],"comment"=>$mb[$G["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($Bi))as$G){$F[$G["name"]]["generated"]=($G["is_persisted"]?"PERSISTED":"VIRTUAL");$F[$G["name"]]["default"]=$G["definition"];}return$F;}function
indexes($Q,$g=null){$F=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($Q),$g)as$G){$y=$G["name"];$F[$y]["type"]=($G["is_primary_key"]?"PRIMARY":($G["is_unique"]?"UNIQUE":"INDEX"));$F[$y]["lengths"]=array();$F[$y]["columns"][$G["key_ordinal"]]=$G["column_name"];$F[$y]["descs"][$G["key_ordinal"]]=($G["is_descending_key"]?'1':null);}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($y))));}function
collations(){$F=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$gb)$F[preg_replace('~_.*~','',$gb)][]=$gb;return$F;}function
information_schema($h,$I=""){return
in_array($I!=""?$I:get_schema(),array("INFORMATION_SCHEMA","sys"));}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($h,$gb){return
queries("CREATE DATABASE ".idf_escape($h).(preg_match('~^[a-z0-9_]+$~i',$gb)?" COLLATE $gb":""));}function
drop_databases(array$Ob){return!!queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$Ob)));}function
rename_database($y,$gb){if(preg_match('~^[a-z0-9_]+$~i',$gb))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $gb");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($y));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($Q,$y,array$l,array$jd,$kb,$zc,$gb,$Aa,$yg){$b=array();$mb=array();$kg=fields($Q);foreach($l
as$k){$d=idf_escape($k[0]);$W=$k[1];if(!$W)$b["DROP"][]=" COLUMN $d";else{$ig=idx($kg[$k[0]],"comment");if($W[5]!=($ig!=""?" COMMENT ".q($ig):""))$mb[idf_unescape($W[0])]=array($ig!="",substr($W[5],9));unset($W[5]);if(preg_match('~ AS ~',$W[3]))unset($W[1],$W[2]);if($k[0]=="")$b["ADD"][]="\n  ".implode("",$W).($Q==""?substr($jd[$W[0]],16+strlen($W[0])):"");else{$i=$W[3];unset($W[3]);unset($W[6]);if($d!=$W[0])queries("EXEC sp_rename ".q(table($Q).".$d").", ".q(idf_unescape($W[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$W)][]="";$jg=$kg[$k[0]];if(default_value($jg)!=$i){if($jg["default"]!==null)$b["DROP"][]=" ".idf_escape($jg["default_constraint"]);if($i)$b["ADD"][]="\n $i FOR $d";}}}}if($Q==""){$ga=(array)$b["ADD"];foreach($jd
as$t=>$W){if(!is_string($t))$ga[]="\n$W";}if(!queries("CREATE TABLE ".table($y)." (".implode(",",$ga)."\n)"))return
false;}else{if($Q!=$y)queries("EXEC sp_rename ".q(table($Q)).", ".q($y));if($jd)$b[""]=$jd;foreach($b
as$t=>$W){if(!queries("ALTER TABLE ".table($y)." $t".implode(",",$W)))return
false;}}$bh="@name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($y);foreach($mb
as$t=>$W){list($lc,$lb)=$W;$ch="$bh, @level2type = N'Column', @level2name = ".q($t);if($lc)queries("EXEC sp_dropextendedproperty $ch");if($lb!="")queries("EXEC sp_addextendedproperty $ch, @value = $lb");}if($kb!==null){if(idx(table_status1($y),"Comment")!="")queries("EXEC sp_dropextendedproperty $bh");if($kb!="")queries("EXEC sp_addextendedproperty $bh, @value = ".q($kb));}return
true;}function
alter_indexes($Q,$b){$s=array();$lc=array();foreach($b
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY")$lc[]=idf_escape($W[1]);else$s[]=idf_escape($W[1])." ON ".table($Q);}elseif(!queries(($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q):"ALTER TABLE ".table($Q)." ADD PRIMARY KEY")." (".implode(", ",$W[2]).")"))return
false;}return(!$s||queries("DROP INDEX ".implode(", ",$s)))&&(!$lc||queries("ALTER TABLE ".table($Q)." DROP ".implode(", ",$lc)));}function
found_rows(array$R,array$Z){}function
foreign_keys($Q){$F=array();$Zf=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");$I=get_schema();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($Q).", @fktable_owner = ".q($I))as$G){$md=&$F[$G["FK_NAME"]];$md["db"]=($G["PKTABLE_QUALIFIER"]==DB?"":$G["PKTABLE_QUALIFIER"]);$md["ns"]=($G["PKTABLE_OWNER"]==$I?"":$G["PKTABLE_OWNER"]);$md["table"]=$G["PKTABLE_NAME"];$md["on_update"]=$Zf[$G["UPDATE_RULE"]];$md["on_delete"]=$Zf[$G["DELETE_RULE"]];$md["source"][]=$G["FKCOLUMN_NAME"];$md["target"][]=$G["PKCOLUMN_NAME"];}return$F;}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Mj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Mj)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$Mj,$Gi){return
apply_queries("ALTER SCHEMA ".idf_escape($Gi)." TRANSFER",array_merge($S,$Mj));}function
trigger($y,$Q){if($y=="")return
array();$H=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($y));$F=reset($H);if($F)$F["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$F["text"]);return($F?:array());}function
triggers($Q){$F=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($Q))as$G)$F[$G["name"]]=array($G["Timing"],$G["Event"]);return$F;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
routine($y,$T){$Sb=get_val("SELECT m.definition
FROM sys.objects o
JOIN sys.sql_modules m ON m.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($y)." AND o.type = ".q($T=="PROCEDURE"?"P":"FN"));if(!$Sb)return
array();$F=array("definition"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',$Sb),"fields"=>array());foreach(get_rows("SELECT p.name, TYPE_NAME(p.user_type_id) [type], p.max_length, p.precision, p.scale, p.is_output
FROM sys.parameters p
JOIN sys.objects o ON p.object_id = o.object_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.name = ".q($y)."
ORDER BY p.parameter_id")as$G){$Uc=$G["type"];$u=type_length($Uc,$G);$k=array("field"=>preg_replace('~^@~','',$G["name"]),"type"=>$Uc,"length"=>$u,"full_type"=>$Uc.($u!=""?"($u)":""),"null"=>true,"inout"=>($G["is_output"]?"OUTPUT":""),);if($k["field"]=="")$F["returns"]=$k;else$F["fields"][]=$k;}return$F;}function
routines(){return
get_rows("SELECT o.name SPECIFIC_NAME, o.name ROUTINE_NAME,
	CASE o.type WHEN 'P' THEN 'PROCEDURE' ELSE 'FUNCTION' END ROUTINE_TYPE, TYPE_NAME(p.user_type_id) DTD_IDENTIFIER
FROM sys.objects o
LEFT JOIN sys.parameters p ON o.object_id = p.object_id AND p.parameter_id = 0
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.type IN ('P', 'FN')
ORDER BY o.name");}function
routine_languages(){return
array();}function
routine_options($zh){return
array();}function
routine_id($y,array$G){return
table($y);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($I,$g=null){$_GET["ns"]=$I;return!!get_val("SELECT 1 FROM sys.schemas WHERE name = ".q($I),0,$g);}function
schema_comment($I){return(string)get_val("SELECT CAST(value AS nvarchar(max)) FROM fn_listextendedproperty('MS_Description', 'SCHEMA', ".q($I).", NULL, NULL, NULL, NULL)");}function
set_schema_comment($I,$kb){$bh="@name = N'MS_Description', @level0type = N'Schema', @level0name = ".q($I);return(schema_comment($I)==""||queries("EXEC sp_dropextendedproperty $bh"))&&($kb==""||queries("EXEC sp_addextendedproperty $bh, @value = ".q($kb)));}function
create_sql($Q,$Aa,$vi){$O=table_status1($Q);if(is_view($O)){$Lj=view($Q);return"CREATE VIEW ".table($Q)." AS $Lj[select]";}$bh="EXEC sp_addextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($Q);$mb=($O["Comment"]!=""?";\n\n$bh, @value = ".q($O["Comment"]):"");$l=array();$Ug=false;foreach(fields($Q)as$y=>$k){$W=process_field($k,$k);if($W[6])$Ug=true;unset($W[5]);$l[]=implode("",$W);if($k["comment"]!="")$mb
.=";\n\n$bh, @level2type = N'Column', @level2name = ".q($y).", @value = ".q($k["comment"]);}foreach(indexes($Q)as$y=>$s){if(!$Ug||$s["type"]!="PRIMARY"){$e=array();foreach($s["columns"]as$t=>$W)$e[]=idf_escape($W).($s["descs"][$t]?" DESC":"");$y=idf_escape($y);$l[]=($s["type"]=="INDEX"?"INDEX $y":"CONSTRAINT $y ".($s["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($Q)as$y=>$Wa)$l[]="CONSTRAINT ".idf_escape($y)." CHECK ($Wa)";return"CREATE TABLE ".table($Q)." (\n\t".implode(",\n\t",$l)."\n)$mb";}function
foreign_keys_sql($Q){$l=array();foreach(foreign_keys($Q)as$jd)$l[]=ltrim(format_foreign_key($jd));return($l?"ALTER TABLE ".table($Q)." ADD\n\t".implode(",\n\t",$l).";\n\n":"");}function
truncate_sql($Q){return"TRUNCATE TABLE ".table($Q);}function
use_sql($Nb,$vi=""){return"USE ".idf_escape($Nb);}function
use_schema_sql($I,$vi){$y=idf_escape($I);$Eb="EXEC(".q("CREATE SCHEMA $y").")";$kb=schema_comment($I);if($kb!="")$Eb="BEGIN\n$Eb\nEXEC sp_addextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q($I).", @value = ".q($kb)."\nEND";return($vi=="DROP+CREATE"?"DROP SCHEMA IF EXISTS $y;\n":"")."IF SCHEMA_ID(".q($I).") IS NULL $Eb";}function
trigger_sql($Q){$F="";foreach(triggers($Q)as$y=>$dj)$F
.=create_trigger(" ON ".table($Q),trigger($y,$Q)).";";return$F;}function
convert_field(array$k){}function
unconvert_field(array$k,$F){return$F;}function
support($Sc){return
preg_match('~^(check|comment|columns|database|drop_col|dump|fast_status|indexes|descidx|procedure|routine|scheme|sql|table|transaction_ddl|trigger|view|view_trigger)$~',$Sc);}}add_driver("oracle","Oracle");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");function
easy_connect(array$L){return
url_host($L["host"]).($L["port"]!=""?":$L[port]":"").$L["path"];}if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";private$link,$transaction=false;function
_error($Bc,$j){if(ini_bool("html_errors"))$j=html_entity_decode(strip_tags($j));$j=preg_replace('~^[^:]*: ~','',$j);$this->error=$j;}function
attach(array$L,$U,$B){$this->link=@oci_new_connect($U,$B,easy_connect($L),"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$j=oci_error();return($j?$j["message"]:lang(26));}function
quote($P){return"'".str_replace("'","''",$P)."'";}function
select_db($Nb){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Nb));}function
query($D,$lj=false){$E=oci_parse($this->link,$D);$this->error="";if(!$E){$j=oci_error($this->link);$this->errno=$j["code"];$this->error=$j["message"];return
false;}set_error_handler(array($this,'_error'));$F=@oci_execute($E,($this->transaction?OCI_NO_AUTO_COMMIT:OCI_COMMIT_ON_SUCCESS));restore_error_handler();if($F){if(oci_num_fields($E))return
new
Result($E);$this->affected_rows=oci_num_rows($E);oci_free_statement($E);}return$F;}function
timeout($Ff){return
function_exists('oci_set_call_timeout')&&oci_set_call_timeout($this->link,$Ff);}function
inTransaction(){return$this->transaction;}function
begin(){$this->transaction=true;return
true;}function
commit(){return$this->end_transaction(@oci_commit($this->link));}function
rollback(){return$this->end_transaction(@oci_rollback($this->link));}private
function
end_transaction($F){$this->transaction=false;if(!$F){$j=oci_error($this->link);$this->errno=$j["code"];$this->error=$j["message"];}return$F;}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($E){$this->result=$E;}private
function
convert($G){foreach((array)$G
as$t=>$W){if(is_a($W,'OCILob')||is_a($W,'OCI-Lob'))$G[$t]=$W->load();}return$G;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$F=new
\stdClass;$F->name=oci_field_name($this->result,$d);$T=oci_field_type($this->result,$d);$F->native_type=idx(array(100=>"binary_float","binary_double"),$T,$T);return$F;}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";function
attach(array$L,$U,$B){return$this->dsn("oci:dbname=//".easy_connect($L).";charset=AL32UTF8",$U,$B);}function
select_db($Nb){return$this->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Nb));}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";static$serverPath=true;var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
operators($Ai){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$B){$f=parent::connect($L,$U,$B);if(is_object($f))$f->query("ALTER SESSION SET CURSOR_SHARING = FORCE"." NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'"." NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF'"." NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF TZH:TZM'");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(29)=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),lang(30)=>array("date"=>19,"timestamp"=>29,"interval year"=>12,"interval day"=>28),lang(31)=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),lang(32)=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),lang(34)=>array("sdo_geometry"=>0),);}function
begin(){return$this->conn->begin();}function
convertSearch($r,array$W,array$k){$T=$k["type"];$Eg=strpos($W["op"],"LIKE")!==false;if($T=="xmltype")return"XMLSERIALIZE(CONTENT $r AS VARCHAR2(4000))";if($T=="json")return"JSON_SERIALIZE($r)";if(preg_match('~^(date|timestamp)~',$T))return"TO_CHAR($r, 'YYYY-MM-DD HH24:MI:SS')";if(preg_match('~char~',$T)||(preg_match('~clob~',$T)&&$Eg))return$r;return(!$Eg&&preg_match(number_type(),$T)?$r:"TO_CHAR($r)");}function
quoteBinary($Ah){return"HEXTORAW(".q(bin2hex($Ah)).")";}function
typeName(\stdClass$k){return
strtolower(parent::typeName($k));}function
hasCStyleEscapes(){return
true;}function
select($Q,array$J,array$Z,array$o,array$eg=array(),$v=1,$A=0,$Wg=false){if(in_array("*",$J)){$_b=array();$pd=false;foreach(fields($Q)as$y=>$k){$wa=convert_field($k);$pd=($pd||$wa);$_b[]=($wa?"$wa AS ":"").idf_escape($y);}if($pd)$J=$_b;}return
parent::select($Q,$J,$Z,$o,$eg,$v,$A,$Wg);}function
allFields(){$F=array();$H=get_rows('SELECT c.table_name "tab", c.column_name "field", c.data_type "type", c.nullable "nullable",
	c.data_precision "precision", c.data_scale "scale", c.char_col_decl_length "char_length"
FROM all_tab_columns c
WHERE '.where_owner("c.owner").'
ORDER BY c.table_name, c.column_id',$this->conn);foreach($H
as$G){$u="$G[precision],$G[scale]";$G["length"]=(strpos($G["type"],"(")?"":($u==","?$G["char_length"]:$u));$G["type"]=strtolower($G["type"]);$G["null"]=($G["nullable"]=="Y");$F[$G["tab"]][]=$G;}return$F;}}function
idf_escape($r){return'"'.str_replace('"','""',$r).'"';}function
table($r){return
idf_escape($r);}function
get_databases($hd){$F=get_vals("SELECT username FROM all_users WHERE oracle_maintained = 'N' ORDER BY 1");return($F?:get_vals("SELECT username FROM all_users ORDER BY 1"));}function
limit($D,$Z,$v,$Vf=0,$K=" "){return($Vf?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $D$Z) t WHERE rownum <= ".($v+$Vf).") WHERE rnum > $Vf":($v?" * FROM (SELECT $D$Z) WHERE rownum <= ".($v+$Vf):" $D$Z"));}function
limit1($Q,$D,$Z,$K="\n"){return" $D$Z";}function
db_collation($h,array$hb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
where_owner($pg="owner"){return"$pg = ".q(DB);}function
views_table($e){return"(SELECT $e FROM all_views WHERE ".where_owner().")";}function
objects_table(){return"(SELECT object_name, DECODE(object_type, 'VIEW', 'view', 'table') object_type FROM all_objects WHERE ".where_owner()." AND object_type IN ('TABLE', 'VIEW'))";}function
tables_list(){return
get_key_vals("SELECT * FROM ".objects_table()." ORDER BY 1");}function
count_tables(array$Ob){$F=array();foreach($Ob
as$h)$F[$h]=get_val("SELECT COUNT(*) FROM all_objects WHERE object_type IN ('TABLE', 'VIEW') AND owner = ".q($h));return$F;}function
table_status($y="",$Rc=false){$F=array();$Dh=q($y);if($Rc||$y!=""){foreach(get_rows('SELECT object_name "Name", object_type "Engine" FROM '.objects_table().($y!=""?" WHERE object_name = $Dh":"").' ORDER BY 1')as$G)$F[$G["Name"]]=$G;return$F;}foreach(get_rows('SELECT t.table_name "Name", \'table\' "Engine", s.bytes "Data_length", i.bytes "Index_length", t.num_rows "Rows"
FROM all_tables t
LEFT JOIN (SELECT segment_name, SUM(bytes) bytes FROM user_segments WHERE segment_type LIKE \'TABLE%\' GROUP BY segment_name) s ON s.segment_name = t.table_name
LEFT JOIN (SELECT i.table_name, SUM(s.bytes) bytes FROM user_indexes i
	JOIN user_segments s ON s.segment_name = i.index_name AND s.segment_type LIKE \'INDEX%\' GROUP BY i.table_name) i ON i.table_name = t.table_name
WHERE '.where_owner("t.owner")."
UNION SELECT view_name, 'view', 0, 0, 0 FROM ".views_table("view_name")."
ORDER BY 1")as$G)$F[$G["Name"]]=$G;return$F;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return
true;}function
fields($Q){$F=array();$be=null;foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($Q)." AND ".where_owner()." ORDER BY column_id")as$G){$T=$G["DATA_TYPE"];$u="$G[DATA_PRECISION],$G[DATA_SCALE]";if($u==",")$u=$G["CHAR_COL_DECL_LENGTH"];elseif(strpos($T,"("))$u="";$i=$G["DATA_DEFAULT"];if($i!==null){$i=rtrim($i);if(preg_match("~^'(.*)'\$~s",$i,$x))$i=str_replace("''","'",$x[1]);}if($G["IDENTITY_COLUMN"]=="YES"){if($be===null)$be=get_key_vals("SELECT column_name, generation_type FROM all_tab_identity_cols WHERE table_name = ".q($Q)." AND ".where_owner());$i="GENERATED ".$be[$G["COLUMN_NAME"]].($G["DEFAULT_ON_NULL"]=="YES"?" ON NULL":"")." AS IDENTITY";}$Yg=array("insert"=>1,"select"=>1,"update"=>1,"order"=>1);if($G["DATA_TYPE_OWNER"]==""||$T=="XMLTYPE")$Yg["where"]=1;$F[$G["COLUMN_NAME"]]=array("field"=>$G["COLUMN_NAME"],"full_type"=>$T.($u?"($u)":""),"type"=>strtolower($T),"length"=>$u,"default"=>$i,"null"=>($G["NULLABLE"]=="Y"),"auto_increment"=>($G["IDENTITY_COLUMN"]=="YES"),"privileges"=>$Yg,);}return$F;}function
table_constraints($Q,$g=null){$F=array();foreach(get_rows('SELECT c.constraint_name "name", c.constraint_type "type", c.r_owner "r_owner", c.r_constraint_name "r_constraint", c.delete_rule "delete_rule", cc.column_name "column"
FROM all_constraints c
JOIN all_cons_columns cc ON cc.owner = c.owner AND cc.constraint_name = c.constraint_name
WHERE c.constraint_type IN (\'P\', \'U\', \'R\') AND '.where_owner("c.owner")." AND c.table_name = ".q($Q).'
ORDER BY cc.position',$g)as$G){$y=$G["name"];$F[$y]["type"]=$G["type"];$F[$y]["r_owner"]=$G["r_owner"];$F[$y]["r_constraint"]=$G["r_constraint"];$F[$y]["delete_rule"]=$G["delete_rule"];$F[$y]["columns"][]=$G["column"];}return$F;}function
indexes($Q,$g=null){$F=array();$vb=array();foreach(table_constraints($Q,$g)as$y=>$ub)$vb[$y]=$ub["type"];foreach(get_rows("SELECT aic.*, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($Q)." AND ".where_owner("aic.table_owner")."
ORDER BY aic.column_position",$g)as$G){$ge=$G["INDEX_NAME"];$jb=$G["DATA_DEFAULT"];$jb=($jb?trim($jb,'"'):$G["COLUMN_NAME"]);$T=idx($vb,$ge);$F[$ge]["type"]=($T=="P"?"PRIMARY":($T=="U"?"UNIQUE":"INDEX"));$F[$ge]["columns"][]=$jb;$F[$ge]["lengths"][]=($G["CHAR_LENGTH"]&&$G["CHAR_LENGTH"]!=$G["COLUMN_LENGTH"]?$G["CHAR_LENGTH"]:null);$F[$ge]["descs"][]=($G["DESCEND"]&&$G["DESCEND"]=="DESC"?'1':null);}uasort($F,function($ca,$Da){$eg=array("PRIMARY"=>0,"UNIQUE"=>1,"INDEX"=>2);return$eg[$ca["type"]]-$eg[$Da["type"]];});return$F;}function
view($y){$H=get_rows('SELECT text "select" FROM '.views_table("view_name, text").' WHERE view_name = '.q($y));return($H?$H[0]:array());}function
collations(){return
array();}function
information_schema($h,$I=""){return
in_array($I!=""?$I:$h,array("INFORMATION_SCHEMA","SYS","SYSTEM"));}function
error(){return
h(connection()->error);}function
explain(Db$f,$D){$f->query("EXPLAIN PLAN FOR $D");return$f->query("SELECT * FROM plan_table");}function
found_rows(array$R,array$Z){}function
auto_increment(){return"";}function
alter_table($Q,$y,array$l,array$jd,$kb,$zc,$gb,$Aa,$yg){$b=$lc=array();$kg=($Q?fields($Q):array());foreach($l
as$k){$W=$k[1];if($W&&$k[0]!=""&&idf_escape($k[0])!=$W[0])queries("ALTER TABLE ".table($Q)." RENAME COLUMN ".idf_escape($k[0])." TO $W[0]");$jg=$kg[$k[0]];if($W&&$jg){$Xf=process_field($jg,$jg);if($W[2]==$Xf[2])$W[2]="";}if($W){list($W[2],$W[3])=array($W[3],$W[2]);$b[]=($Q!=""?($k[0]!=""?"MODIFY (":"ADD ("):"  ").implode($W).($Q!=""?")":"");}else$lc[]=idf_escape($k[0]);}if($Q=="")return
queries("CREATE TABLE ".table($y)." (\n".implode(",\n",array_merge($b,$jd))."\n)");return(!$b||queries("ALTER TABLE ".table($Q)."\n".implode("\n",$b)))&&(!$lc||queries("ALTER TABLE ".table($Q)." DROP (".implode(", ",$lc).")"))&&($Q==$y||queries("ALTER TABLE ".table($Q)." RENAME TO ".table($y)));}function
alter_indexes($Q,$b){$lc=array();$eh=array();foreach($b
as$W){if($W[0]!="INDEX"){$W[2]=preg_replace('~ DESC$~','',$W[2]);$Eb=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");array_unshift($eh,"ALTER TABLE ".table($Q).$Eb);}elseif($W[2]=="DROP")$lc[]=idf_escape($W[1]);else$eh[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q)." (".implode(", ",$W[2]).")";}if($lc)array_unshift($eh,"DROP INDEX ".implode(", ",$lc));foreach($eh
as$D){if(!queries($D))return
false;}return
true;}function
foreign_keys($Q){$F=array();$Hi=array();foreach(table_constraints($Q)as$y=>$ub){if($ub["type"]=="R"){$F[$y]=array("source"=>$ub["columns"],"target"=>array(),"on_delete"=>$ub["delete_rule"],"on_update"=>null,);$Hi[$y]=array($ub["r_owner"],$ub["r_constraint"]);}}if($Hi){$Z=array();foreach($Hi
as$Gi)$Z[]="(owner = ".q($Gi[0])." AND constraint_name = ".q($Gi[1]).")";foreach(get_rows("SELECT owner, constraint_name, table_name, column_name FROM all_cons_columns WHERE ".implode(" OR ",array_unique($Z))." ORDER BY position")as$G){foreach($Hi
as$y=>$Gi){if($Gi==array($G["OWNER"],$G["CONSTRAINT_NAME"])){$F[$y]["db"]=$G["OWNER"];$F[$y]["table"]=$G["TABLE_NAME"];$F[$y]["target"][]=$G["COLUMN_NAME"];}}}}return$F;}function
trigger($y,$Q){if($y=="")return
array();$H=get_rows('SELECT trigger_name "Trigger", trigger_type "Type", triggering_event "Event", trigger_body "Statement"
FROM all_triggers
WHERE trigger_name = '.q($y)." AND ".where_owner());$F=reset($H);if($F){$T=$F["Type"];$F["Timing"]=(preg_match('~^(BEFORE|AFTER|INSTEAD OF)~',$T,$x)?$x[1]:$T);$F["Type"]=(preg_match('~EACH ROW~',$T)||$T=="INSTEAD OF"?"FOR EACH ROW":"");}return($F?:array());}function
triggers($Q){$F=array();foreach(get_rows("SELECT trigger_name, trigger_type, triggering_event FROM all_triggers WHERE table_name = ".q($Q)." AND ".where_owner())as$G)$F[$G["TRIGGER_NAME"]]=array(preg_replace('~ (STATEMENT|EACH ROW)$~','',$G["TRIGGER_TYPE"]),$G["TRIGGERING_EVENT"]);return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE","INSERT OR UPDATE","INSERT OR DELETE","UPDATE OR DELETE","INSERT OR UPDATE OR DELETE"),"Type"=>array("FOR EACH ROW",""),);}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Mj){return
apply_queries("DROP VIEW",$Mj);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
last_id($E){return"0";}function
create_database($h,$gb){$F=queries("CREATE USER ".idf_escape($h)." NO AUTHENTICATION");return($F?queries("GRANT UNLIMITED TABLESPACE TO ".idf_escape($h)):$F);}function
drop_databases(array$Ob){$F=true;foreach($Ob
as$h)$F=!!queries("DROP USER ".idf_escape($h)." CASCADE")&&$F;return$F;}function
rename_database($y,$gb){return!!queries("ALTER USER ".idf_escape(DB)." RENAME TO ".idf_escape($y));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$F=array();$H=get_rows('SELECT * FROM v$instance');foreach(reset($H)as$t=>$W)$F[]=array($t,$W);return$F;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess
LEFT JOIN v$sql sql ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field(array$k){if($k["type"]=="sdo_geometry")return"SDO_UTIL.TO_WKTGEOMETRY(".idf_escape($k["field"]).")";}function
unconvert_field(array$k,$F){return($k["type"]=="sdo_geometry"?"SDO_UTIL.FROM_WKTGEOMETRY($F)":$F);}function
support($Sc){return
preg_match('~^(columns|database|drop_col|fast_status|indexes|descidx|processlist|sql|status|table|trigger|variables|view|view_trigger)$~',$Sc);}}class
Adminer{static$instance;var$error='';private$values=array();private$described=array();function
name(){return"<a href='https://www.adminer.org/editor/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.2+e29bdd90")."' width='24' height='24' alt='' id='logo'>".lang(38)."</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($Eb=false){return
password_file($Eb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($L){return'';}function
database(){if(connection()){$Ob=adminer()->databases(false);if(!$Ob)return
get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1)");foreach($Ob
as$h){if(!information_schema($h))return$h;}return$Ob[0];}return
null;}function
operators($Ai=null){return
array("<=",">=");}function
schemas(){return
schemas();}function
databases($hd=true){return
get_databases($hd);}function
pluginsLinks(){}function
queryTimeout(){return
5;}function
afterConnect(){}function
headers(){}function
csp(array$Hb){return$Hb;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$Wd=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$Mh=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer Editor".($Wd!=""?" - $Wd":""),'short_name'=>'Adminer Editor','description'=>lang(39),'start_url'=>$Mh,'scope'=>$Mh,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.2+e29bdd90",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($Lb=null){return
true;}function
bodyClass(){echo" editor";}function
css(){$F=array();foreach(array("","-dark")as$Cf){$m="adminer$Cf.css";if(file_exists($m)){$Vc=file_get_contents($m);$F["$m?v=".crc32($Vc)]=($Cf?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Vc)?'':'light'));}}return$F;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('username','<tr><th>'.lang(40).'<td>',input_hidden("auth[driver]","server").'<input name="auth[username]" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(41).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),"</table>\n","<p><input type='submit' value='".lang(42)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(43))."\n";}function
loginFormField($y,$Qd,$X){return$Qd.$X."\n";}function
login($ef,$B){if($B=="")return
lang(44);if(!Driver::$passwords)return
lang(45);if(!password_required())return
lang(46);return
true;}function
tableName(array$Ai){return
h(isset($Ai["Engine"])?($Ai["Comment"]!=""?$Ai["Comment"]:$Ai["Name"]):"");}function
fieldName(array$k,$eg=0){return
h(preg_replace('~\s+\[.*\]$~','',($k["comment"]!=""?$k["comment"]:$k["field"])));}function
selectLinks(array$Ai,$M=""){$a=$Ai["Name"];if($M!==null)echo'<p class="tabs"><a href="'.h(ME.'edit='.url_escape($a).$M).'">'.lang(47)."</a>\n";}function
foreignKeys($Q){return
foreign_keys($Q);}function
backwardKeys($Q,$_i){$F=array();foreach(get_rows("SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_NAME = ".q($Q)."
ORDER BY ORDINAL_POSITION",null,"")as$G)$F[$G["TABLE_NAME"]]["keys"][$G["CONSTRAINT_NAME"]][$G["COLUMN_NAME"]]=$G["REFERENCED_COLUMN_NAME"];foreach($F
as$t=>$W){$y=adminer()->tableName(table_status1($t,true));if($y!=""){$Dh=preg_quote($_i);$K="(:|\\s*-)?\\s+";$F[$t]["name"]=(preg_match("(^$Dh$K(.+)|^(.+?)$K$Dh\$)iu",$y,$x)?$x[2].$x[3]:$y);}else
unset($F[$t]);}return$F;}function
backwardKeysPrint(array$Ga,array$G){foreach($Ga
as$Q=>$Fa){foreach($Fa["keys"]as$ib){$w=ME.'select='.url_escape($Q);$p=0;foreach($ib
as$d=>$W)$w
.=where_link($p++,$d,$G[$W]);echo"<a href='".h($w)."'>".h($Fa["name"])."</a>";$w=ME.'edit='.url_escape($Q);foreach($ib
as$d=>$W)$w
.="&set[".url_escape(bracket_escape($d))."]=".url_escape($G[$W]);echo"<a href='".h($w)."' title='".lang(47)."'>+</a> ";}}}function
selectQuery($D,$ri,$Qc=false){return
sql_comment($D,format_time($ri));}function
rowDescription($Q){foreach(fields($Q)as$k){if(preg_match("~char|text~",$k["type"]))return
idf_escape($k["field"]);}return"";}function
rowDescriptions(array$H,array$ld){$F=$H;foreach($H[0]as$t=>$W){if(list($Q,$q,$y)=$this->_foreignColumn($ld,$t)){$ce=array();foreach($H
as$G){if(isset($G[$t]))$ce[$G[$t]]=q($G[$t]);}if(!$ce)continue;$Xb=$this->values[$Q];if(!$Xb)$Xb=get_key_vals("SELECT $q, $y FROM ".table($Q)." WHERE $q IN (".implode(", ",$ce).")");$this->described[$t]=true;foreach($H
as$Jf=>$G){if(isset($G[$t]))$F[$Jf][$t]=(string)$Xb[$G[$t]];}}}return$F;}function
selectLink($W,array$k){}function
selectVal($W,$w,array$k,$lg){$F="$W";if(isset($this->described[$k["field"]])&&$lg!==null)$F=shorten_utf8($lg,max(0,+adminer()->selectLengthProcess()));$w=h($w);if(is_blob($k)&&!is_utf8($W)){$F=lang(48,strlen($lg));$fi=(function_exists('getimagesizefromstring')?@getimagesizefromstring($lg):array());if($fi)$F="<img src='$w' alt='$F' $fi[3] loading='lazy'>";}if(like_bool($k)&&$F!="")$F=(preg_match('~^(1|t|true|y|yes|on)$~i',$W)?lang(49):lang(50));if($w)$F="<a href='$w'".(is_url($w)?target_blank():"").">$F</a>";if(preg_match('~date~',$k["type"]))$F="<div class='datetime'>$F</div>";return$F;}function
editVal($W,array$k){if(preg_match('~date|timestamp~',$k["type"])&&$W!==null)return
preg_replace('~^(\d{2}(\d+))-(0?(\d+))-(0?(\d+))~',lang(51),$W);return$W;}function
config(){return
array();}function
selectColumnsPrint(array$J,array$e){}private
function
searchColumns(array$l){$F=array();$Q=$_GET["select"];if($Q=="")return$F;$p=0;foreach($l
as$y=>$k){if(isset($k["privileges"]["where"])&&$this->fieldName($k)!=""&&($k["type"]=="enum"||like_bool($k)||is_array($this->foreignKeyOptions($Q,$y))))$F[--$p]=$y;}return$F;}function
selectSearchPrint(array$Z,array$e,array$ie,$Ai=null){$Z=(array)$_GET["where"];echo'<fieldset id="fieldset-search"><legend>'.lang(52)."</legend><div>\n";$l=fields($_GET["select"]);foreach($this->searchColumns($l)as$p=>$y){$k=$l[$y];$W=idx($Z[$p],"val");echo"<div>".h($e[$y]);if($k["type"]=="enum"||like_bool($k))echo": ",(like_bool($k)?"<select name='where[$p][val]' data-default=''>".optionlist(array(""=>"",lang(50),lang(49)),$W,true)."</select>":enum_input("checkbox"," name='where[$p][val][]'",$k,(array)$W,lang(53)));else{$_=$this->foreignKeyOptions($_GET["select"],$y);if($k["null"])$_[0]='('.lang(53).')';echo": <select name='where[$p][val]' data-default=''>".optionlist($_,$W,true)."</select>";}echo"</div>\n";unset($e[$y]);}$p=0;foreach($Z
as$t=>$W){if($t>=0&&($W["col"]==""||$e[$W["col"]])&&"$W[col]$W[val]"!=""){echo"<div><select name='where[$p][col]' data-default=''><option value=''>(".lang(54).")".optionlist($e,$W["col"],true)."</select>",html_select("where[$p][op]",array(-1=>"")+adminer()->operators($Ai),$W["op"]," data-default=''"),"<input type='search' name='where[$p][val]' value='".h($W["val"])."' data-default=''".on('keydown','selectSearchKeydown').on('search','selectSearchSearch')."></div>\n";$p++;}}echo"<div><select name='where[$p][col]' data-default=''".on('change','selectAddRow')."><option value=''>(".lang(54).")".optionlist($e,null,true)."</select>",html_select("where[$p][op]",array(-1=>"")+adminer()->operators($Ai),null," data-default=''"),"<input type='search' name='where[$p][val]' data-default=''".on('change','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch')."></div>\n","</div></fieldset>\n";}function
selectOrderPrint(array$eg,array$e,array$ie){$gg=array();foreach($ie
as$t=>$s){$eg=array();foreach($s["columns"]as$W)$eg[]=$e[$W];if(count(array_filter($eg,'strlen'))>1&&$t!="PRIMARY")$gg[$t]=implode(", ",$eg);}if($gg)echo'<fieldset><legend>'.lang(55)."</legend><div>","<select name='index_order' data-default=''>".optionlist(array(""=>"")+$gg,(idx($_GET["order"],0)!=""?"":$_GET["index_order"]),true)."</select>","</div></fieldset>\n";if($_GET["order"])echo"<div hidden>".hidden_fields(array("order"=>array(1=>reset($_GET["order"])),"desc"=>($_GET["desc"]?array(1=>1):array()),))."</div>\n";}function
selectLimitPrint($v){echo"<fieldset><legend>".lang(56)."</legend><div>",html_select("limit",array("","50","100"),(string)$v," data-default='50'"),"</div></fieldset>\n";}function
selectLengthPrint($Ki){}function
selectActionPrint(array$ie){echo"<fieldset><legend>".lang(57)."</legend><div>","<input type='submit' value='".lang(58)."'>","</div></fieldset>\n";}function
selectCommandPrint(){return
true;}function
selectImportPrint(){return
true;}function
selectEmailPrint(array$vc,array$e){}function
selectColumnsProcess(array$e,array$ie){return
array(array(),array());}function
selectSearchProcess(array$l,array$ie,$Ai=null){$F=array();$Eh=$this->searchColumns($l);if($_GET["select"]!=""&&!$_POST&&!is_ajax()){$He=array();$w="";foreach((array)$_GET["where"]as$t=>$Z){$Mg=($t>=0?array_search($Z["col"],$Eh,true):false);$k=idx($l,$Z["col"],array());if($Mg!==false&&$k["type"]!="enum"&&!is_array($Z["val"])&&$Z["val"]!=""&&$Z["op"]==(like_bool($k)?"":"=")){$He[]=$t;$w
.="&where[$Mg][val]=".url_escape($Z["val"]);}}if($He)redirect(remove_from_uri("where(%5B|\[)(".implode("|",$He).")(%5D|\])[^=]*").$w);}foreach((array)$_GET["where"]as$t=>$Z){if($t<0){$Z["col"]=idx($Eh,$t,"");$k=idx($l,$Z["col"],array());$Z["op"]=($k&&($k["type"]=="enum"||like_bool($k))?"":"=");$_GET["where"][$t]=$Z;}$Z+=array("col"=>"","op"=>"","val"=>"");$fb=$Z["col"];$ag=$Z["op"];$W=$Z["val"];if(($t>=0&&$fb!="")||$W!=""){$ob=array();foreach(($fb!=""?array($fb=>$l[$fb]):$l)as$y=>$k){if($fb!=""||is_searchable($k,$Z)){$y=idf_escape($y);if($fb!=""&&$k["type"]=="enum"){$ee=array();foreach(preg_grep('~^val-~',$W)as$Gj)$ee[]=q(substr($Gj,4));$ob[]=(in_array("null",$W)?"$y IS NULL OR ":"").($ee?"$y IN (".implode(", ",$ee).")":"0");}else{$Li=preg_match('~'.text_type().'~',$k["type"]);$X=q(!$ag&&$Li&&preg_match('~^[^%]+$~',$W)?"%$W%":$W);$ob[]=driver()->convertSearch($y,$Z,$k).($X=="NULL"?" IS".($ag==">="?" NOT":"")." $X":(in_array($ag,adminer()->operators($Ai))||$ag=="="?" $ag $X":($Li?" LIKE $X":" IN (".($X[0]=="'"?str_replace(",","', '",$X):$X).")")));if($t<0&&$W=="0")$ob[]="$y IS NULL";}}}$F[]=($ob?"(".implode(" OR ",$ob).")":"1 = 0");}}return$F;}function
selectOrderProcess(array$l,array$ie){$he=$_GET["index_order"];if($he!="")unset($_GET["order"][1]);if($_GET["order"])return
array(idf_escape(reset($_GET["order"])).($_GET["desc"]?" DESC":""));foreach(($he!=""?array($ie[$he]):$ie)as$s){if($he!=""||$s["type"]=="INDEX"){$Id=array_filter($s["descs"]);$Vb=false;foreach($s["columns"]as$W){if(preg_match('~date|timestamp~',$l[$W]["type"])){$Vb=true;break;}}$F=array();foreach($s["columns"]as$t=>$W)$F[]=idf_escape($W).(($Id?$s["descs"][$t]:$Vb)?" DESC":"");return$F;}}return
array();}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return"100";}function
selectEmailProcess(array$Z,array$ld){return
false;}function
selectQueryBuild(array$J,array$Z,array$o,array$eg,$v,$A){return"";}function
messageQuery($D,$Mi,$Qc=false){return" <span class='time'>".@date("H:i:s")."</span>".sql_comment($D,$Mi);}function
error(){return
error();}function
editRowPrint($Q,array$l,$G,$vj,$D='',$Mi=''){echo($D!=""?sql_comment($D,$Mi):"");}function
editFunctions(array$k){$F=array();if($k["null"]&&preg_match('~blob~',$k["type"]))$F["NULL"]=lang(53);$F[""]=($k["null"]||$k["auto_increment"]||like_bool($k)?"":"*");if(preg_match('~date|time~',$k["type"]))$F["now"]=lang(59);if(preg_match('~_(md5|sha1)$~i',$k["field"],$x))$F[]=strtolower($x[1]);return$F;}function
editInput($Q,array$k,$c,$X){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$c value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$c,$k,$X,lang(53));$_=$this->foreignKeyOptions($Q,$k["field"],$X);if($_!==null){if(!$k["null"]&&is_array($_))unset($_[""]);return(is_array($_)?"<select$c>".optionlist($_,(string)$X,true)."</select>":"<input value='".h($X)."'$c class='hidden'>"."<input value='".h($_)."' class='jsonly'".on('input','whisper',ME."script=complete&source=".url_escape($Q)."&field=".url_escape($k["field"])."&value=").">"."<div".on('click','whisperClick')."></div>");}if(like_bool($k))return'<input type="checkbox" value="1"'.(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?' checked':'')."$c>";$Ud="";if(preg_match('~time~',$k["type"]))$Ud=lang(60);if(preg_match('~date|timestamp~',$k["type"]))$Ud=lang(61).($Ud?" [$Ud]":"");if($Ud)return"<input value='".h($X)."'$c> ($Ud)";if(preg_match('~_(md5|sha1)$~i',$k["field"]))return"<input type='password' value='".h($X)."'$c>";return'';}function
editHint($Q,array$k,$X){return(preg_match('~\s+(\[.*\])$~',($k["comment"]!=""?$k["comment"]:$k["field"]),$x)?h(" $x[1]"):'');}function
processInput(array$k,$X,$n=""){if($n=="now")return"$n()";$F=$X;if(preg_match('~date|timestamp~',$k["type"])&&preg_match('(^'.str_replace('\$1','(?P<p1>\d*)',preg_replace('~(\\\\\\$([2-6]))~','(?P<p\2>\d{1,2})',preg_quote(lang(51)))).'(.*))',$X,$x))$F=($x["p1"]!=""?$x["p1"]:($x["p2"]!=""?($x["p2"]<70?20:19).$x["p2"]:gmdate("Y")))."-$x[p3]$x[p4]-$x[p5]$x[p6]".end($x);$F=q($F);if($X==""&&like_bool($k))$F="'0'";elseif($X==""&&($k["null"]||!preg_match('~char|text~',$k["type"])))$F="NULL";elseif(preg_match('~^(md5|sha1)$~',$n))$F="$n($F)";return
unconvert_field($k,$F);}function
dumpOutput(){return
array();}function
dumpFormat(){return
array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($h){}function
dumpTable($Q,$vi,$Ce=0){echo"\xef\xbb\xbf";}function
dumpData($Q,$vi,$D){$E=connection()->query($D,1);if($E){while($G=$E->fetch_assoc()){if($vi=="table"){dump_csv(array_keys($G));$vi="INSERT";}dump_csv($G);}}}function
dumpFilename($ae){return
friendly_url($ae);}function
dumpHeaders($ae,$Hf=false){$Mc="csv";header("Content-Type: text/csv; charset=utf-8");return$Mc;}function
dumpFooter(){}function
importServerPath(){return'';}function
homepage(){return
true;}function
navigation($Bf){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Nf=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/editor/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Nf)<0?h($Nf):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($Bf=="auth"){$bd=true;foreach((array)$_SESSION["pwds"]as$Jj=>$ai){foreach($ai[""]as$U=>$B){if($B!==null){if($bd){echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">";$bd=false;}echo"<li><a href='".h(auth_url($Jj,"",$U))."'>".($U!=""?h($U):"<i>".lang(53)."</i>")."</a>\n";}}}}else{adminer()->databasesPrint($Bf);$fa=adminer()->menuActions(array(),$Bf);echo($fa?"<p class='links'>\n".implode("\n",$fa)."\n":"");if($Bf!="db"&&$Bf!="ns"){$R=table_status('',true);if(!$R)echo"<p class='message'>".lang(13)."\n";else
adminer()->tablesPrint($R);}}}function
syntaxHighlighting(array$S){}function
databasesPrint($Bf){}function
menuActions(array$fa,$Bf){return$fa;}function
tablesPrint(array$S){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($S
as$G){echo'<li>';$y=adminer()->tableName($G);if($y!="")echo"<a href='".h(ME).'select='.url_escape($G["Name"])."'".bold($_GET["select"]==$G["Name"]||$_GET["edit"]==$G["Name"],"select")." title='".lang(62)."'>$y</a>\n";}echo"</ul>\n";}function
_foreignColumn(array$ld,$d){foreach((array)$ld[$d]as$kd){if(count($kd["source"])==1){$y=adminer()->rowDescription($kd["table"]);if($y!=""){$q=idf_escape($kd["target"][0]);return
array($kd["table"],$q,$y);}}}}private
function
foreignKeyOptions($Q,$d,$X=null){if(list($Gi,$q,$y)=$this->_foreignColumn(column_foreign_keys($Q),$d)){$F=&$this->values[$Gi];if($F===null){$R=table_status1($Gi);$F=($R["Rows"]>1000?"":array(""=>"")+get_key_vals("SELECT $q, $y FROM ".table($Gi)." ORDER BY 2"));}if(!$F&&$X!==null)return
get_val("SELECT $y FROM ".table($Gi)." WHERE $q = ".q($X));return$F;}}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($Kg){$kc=SqlDriver::$drivers;$Rd=" href='https://www.adminer.org/plugins/#use'".target_blank();if($Kg===null){$Kg=array();$Ja="adminer-plugins";if(is_dir($Ja)){foreach(glob("$Ja/*.php")as$m){$Wc=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$Wc)as$q=>$y)$this->driverFiles[$q]=$m;}}if(file_exists("$Ja.php")){$fe=$this->includeOnce("$Ja.php");if(is_array($fe)){foreach($fe
as$t=>$Ig)$Kg[is_object($Ig)?get_class($Ig):$t]=$Ig;}else$this->error
.=lang(63,"<b>$Ja.php</b>",$Rd)."<br>";}foreach(get_declared_classes()as$cb){if(!$Kg[$cb]&&(preg_match('~^Adminer\w~i',$cb)||is_subclass_of($cb,'Adminer\Plugin'))){$lh=new
\ReflectionClass($cb);$wb=$lh->getConstructor();if($wb&&$wb->getNumberOfRequiredParameters())$this->error
.=lang(64,$Rd,"<b>$cb</b>","<b>$Ja.php</b>")."<br>";else$Kg[$cb]=new$cb;}}}$ue=array_filter($Kg,function($Ig){return!is_object($Ig);});if($ue){$this->error
.=lang(65,$Rd)."<br>";$Kg=array_diff_key($Kg,$ue);}$this->drivers=array_diff_key(SqlDriver::$drivers,$kc);$this->plugins=$Kg;$ka=new
Adminer;$Kg[]=$ka;$lh=new
\ReflectionObject($ka);foreach($lh->getMethods()as$Af){foreach($Kg
as$Ig){$y=$Af->getName();if(method_exists($Ig,$y))$this->hooks[$y][]=$Ig;}}}function
includeOnce($m){return
include_once"./$m";}static
function
checksum($m){$Vc=str_replace("\r","",file_get_contents($m));$Vc=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Vc);return
dechex(crc32($Vc));}function
checksums(){$Xc=array_values($this->driverFiles);foreach($this->plugins
as$Ig){$lh=new
\ReflectionObject($Ig);$Xc[]=$lh->getFileName();}$F=array();foreach($Xc
as$m)$F[basename($m,'.php')]=self::checksum($m);return$F;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'5fb34a8b','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'eb823bb0','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'2b06a5b7','login-table'=>'811f8cef','menu-links'=>'c78461b3','name-patterns'=>'84c10d09','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-expression'=>'4b61d59c','select-foreign'=>'38ac915c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','sql-url-length'=>'cd70f1a0','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'92ca960d','elastic'=>'1582a04d','firebird'=>'1c4309b2','igdb'=>'a6a1750a','imap'=>'3da1022b','mongo'=>'63486492','redis'=>'6a653462','simpledb'=>'b8e2cc7d',);}function
__call($y,array$ug){$ua=array();foreach($ug
as$t=>$W)$ua[]=&$ug[$t];$F=null;foreach($this->hooks[$y]as$Ig){$X=call_user_func_array(array($Ig,$y),$ua);if($X!==null){if(!self::$append[$y])return$X;$F=$X+(array)$F;}}return$F;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($r,$z=null){$ua=func_get_args();$ua[0]=idx($this->translations[LANG],$r)?:$r;return
call_user_func_array('Adminer\lang_format',$ua);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($Ag){$this->password_hash=$Ag;}function
description(){return
lang(66);}function
credentials(){$B=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($B)&&!password_required()?"":$B));}function
login($ef,$B){if($this->passwordMatches($B))return
true;if(!Driver::$passwords||!password_required())return
false;}protected
function
passwordMatches($B){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($B),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$L,$U,$B){mysqli_report(MYSQLI_REPORT_OFF);$Lg=$L["port"];$xc=("$L[host]$Lg$L[socket]"=="");$N=adminer()->connectSsl();$Bj=($N&&($N['key']||$N['cert']||$N['ca']||isset($N['verify'])));if($Bj)$this->ssl_set($N['key'],$N['cert'],$N['ca'],'','');$F=@$this->real_connect((!$xc?$L["host"]:ini_get("mysqli.default_host")),(!$xc||$U!=""?$U:ini_get("mysqli.default_user")),(!$xc||$U.$B!=""?$B:ini_get("mysqli.default_pw")),null,($Lg!=""?intval($Lg):ini_get("mysqli.default_port")),($Lg!=""?null:$L["socket"]),($Bj?($N['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($F?'':$this->error);}function
set_charset($Va){if(parent::set_charset($Va))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Va");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($P){return"'".$this->escape_string($P)."'";}function
inTransaction(){return
false;}function
begin(){return$this->begin_transaction();}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$L,$U,$B){if(ini_bool("mysql.allow_local_infile"))return
lang(67,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$Lg="$L[port]$L[socket]";$y=$L["host"].($Lg!=""?":$Lg":"");$this->link=@mysql_connect(($y!=""?$y:ini_get("mysql.default_host")),($y.$U!=""?$U:ini_get("mysql.default_user")),($y.$U.$B!=""?$B:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Va){return
mysql_set_charset($Va,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($P){return"'".mysql_real_escape_string($P,$this->link)."'";}function
select_db($Nb){return
mysql_select_db($Nb,$this->link);}function
query($D,$lj=false){$E=@($lj?mysql_unbuffered_query($D,$this->link):mysql_query($D,$this->link));$this->error="";if(!$E){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($E===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($E);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($E){$this->result=$E;$this->num_rows=mysql_num_rows($E);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$F=mysql_fetch_field($this->result,$this->offset++);$F->orgtable=$F->table;$F->native_type=idx(array("string"=>"varchar","real"=>"double"),$F->type,$F->type);return$F;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach(array$L,$U,$B){$_=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$_[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$N=adminer()->connectSsl();if($N){if($N['key'])$_[\PDO::MYSQL_ATTR_SSL_KEY]=$N['key'];if($N['cert'])$_[\PDO::MYSQL_ATTR_SSL_CERT]=$N['cert'];if($N['ca'])$_[\PDO::MYSQL_ATTR_SSL_CA]=$N['ca'];if(isset($N['verify']))$_[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$N['verify'];}$Wd=$L["host"];$Lg=$L["port"];$ii=$L["socket"];return$this->dsn("mysql:charset=utf8".($Wd!=""?";host=$Wd":'').($Lg!=""?";port=$Lg":($ii!=""?";unix_socket=$ii":"")),$U,$B,$_);}function
set_charset($Va){return$this->query("SET NAMES $Va");}function
select_db($Nb){return$this->query("USE ".idf_escape($Nb));}function
query($D,$lj=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$lj);return
parent::query($D,$lj);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($Ai){return
array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","REGEXP","IN","BETWEEN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($L,$U,$B){$f=parent::connect($L,$U,$B);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($Ah=iconv("windows-1252","utf-8//IGNORE",$f))>strlen($f))$f=$Ah;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array(lang(29)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(30)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(31)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(68)=>array("enum"=>65535,"set"=>64),lang(32)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(34)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types[lang(31)]["json"]=4294967295;if(min_version('',10.7,$f)){$this->types[lang(31)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$f)){$this->types[lang(33)]["inet6"]=39;if(min_version('','10.10',$f))$this->types[lang(33)]["inet4"]=15;}if(min_version(9,11.7,$f))$this->types[lang(29)]["vector"]=16383;if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$k){return(preg_match("~binary~",$k["type"])?"<code class='jush-sql'>UNHEX</code>":($k["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($k["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$k["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($Q,array$M){return($M?parent::insert($Q,$M):queries("INSERT INTO ".table($Q)." ()\nVALUES ()"));}function
insertUpdate($Q,array$H,array$Ug){$e=array_keys(reset($H));$Rg="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Y=array();foreach($e
as$t)$Y[$t]="$t = VALUES($t)";$wi="\nON DUPLICATE KEY UPDATE ".implode(", ",$Y);$Y=array();$u=0;foreach($H
as$M){$X="(".implode(", ",$M).")";if($Y&&(strlen($Rg)+$u+strlen($X)+strlen($wi)>1e6)){if(!queries($Rg.implode(",\n",$Y).$wi))return
false;$Y=array();$u=0;}$Y[]=$X;$u+=strlen($X)+2;}return
queries($Rg.implode(",\n",$Y).$wi);}function
slowQuery($D,$Ni){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Ni FOR $D";elseif(preg_match('~^(SELECT\b)(.+)~is',$D,$x))return"$x[1] /*+ MAX_EXECUTION_TIME(".($Ni*1000).") */ $x[2]";}}function
convertColumn($r,array$k){if(preg_match("~binary~",$k["type"]))return"HEX($r)";if($k["type"]=="bit")return"BIN($r + 0)";if($k["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($r)";if(preg_match("~geom|point|linestring|polygon~",$k["type"]))return(min_version(8)?"ST_":"")."AsWKT($r)";return"";}function
convertSearch($r,array$W,array$k){return($this->convertColumn($r,$k)?:(preg_match('~'.text_type().'~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$W['val'])?"CONVERT($r USING ".charset($this->conn).")":$r));}function
typeName(\stdClass$k){$y=parent::typeName($k);if($y!=""){$kj=array("TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",);return
idx($kj,$y,strtolower($y));}$kj=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$F=idx($kj,$k->type,"");return($k->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$F):$F);}function
quoteBinary($Ah){return"X".q(bin2hex($Ah));}function
md5($d,array$k){if(is_blob($k)||preg_match('~'.text_type().'~',$k["type"]))return"MD5(".(is_blob($k)||preg_match("~^utf8~",$k["collation"])?$d:"CONVERT($d USING ".charset($this->conn).")").")";}function
warnings(){$E=$this->conn->query("SHOW WARNINGS");if($E&&$E->num_rows){ob_start();print_select_result($E);return
ob_get_clean();}}function
tableHelp($y,$Ce=false){$gf=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($gf?"$y-table/":str_replace("_","-",$y)."-table.html"));if(DB=="sys")return($gf?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$y)).".html"));if(DB=="mysql")return($gf?"mysql$y-table/":"system-schema.html");}function
partitionsInfo($Q){$ud="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($Q);$E=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $ud ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$G=($E?$E->fetch_row():null);if(!$G)return
array();$F=array();list($F["partition_by"],$F["partition"],$F["partitions"])=$G;$zg=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $ud AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$F["partition_names"]=array_keys($zg);$F["partition_values"]=array_values($zg);return$F;}function
checkConstraints($Q){$F=parent::checkConstraints($Q);return($this->conn->flavor=='maria'?$F:array_map('stripslashes',$F));}function
hasCStyleEscapes(){static$Sa;if($Sa===null){$pi=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Sa=(strpos($pi,'NO_BACKSLASH_ESCAPES')===false);}return$Sa;}function
hasEstimatedRows(){return
true;}function
isSystem($h,$I=""){return
information_schema($h,$I)||in_array($h,array("mysql","sys"));}function
lineComment(){return"#|-- ";}function
engines(){$F=array();foreach(get_rows("SHOW ENGINES")as$G){if(preg_match("~YES|DEFAULT~",$G["Support"]))$F[]=$G["Engine"];}return$F;}function
indexAlgorithms(array$Ai){return(preg_match('~^(MEMORY|NDB)$~',$Ai["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($r){return"`".str_replace("`","``",$r)."`";}function
table($r){return
idf_escape($r);}function
get_databases($hd){$F=get_session("dbs");if($F===null){$D="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$ri=microtime(true);$F=($hd?slow_query($D):get_vals($D));if(microtime(true)-$ri>0.1){restart_session();set_session("dbs",$F);stop_session();}}return$F;}function
limit($D,$Z,$v,$Vf=0,$K=" "){return" $D$Z".($v?$K."LIMIT $v".($Vf?" OFFSET $Vf":""):"");}function
limit1($Q,$D,$Z,$K="\n"){return
limit($D,$Z,1,0,$K);}function
db_collation($h,array$hb){$F=null;$Eb=get_val("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$Eb,$x))$F=$x[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$Eb,$x))$F=$hb[$x[1]][-1];return$F;}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$Ob){$F=array();foreach($Ob
as$h)$F[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$F;}function
table_status($y="",$Rc=false){$F=array();$D="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($y!=""?"AND TABLE_NAME = ".q($y):"ORDER BY Name");$I=array();foreach(($Rc?array():get_rows($D))as$G)$I[$G["Name"]]=$G;$Tg=null;foreach(get_rows($Rc?$D:"SHOW TABLE STATUS".($y!=""?" LIKE ".q(addcslashes($y,"%_\\")):""))as$G){$lg=idx($I,$G["Name"]);if($lg){if($G["Comment"]!==$lg["Comment"]&&$G["Comment"]!==$Tg)$G["Error"]=$G["Comment"];$Tg=$G["Comment"];$G["Comment"]=$lg["Comment"];$G["Engine"]=$lg["Engine"];}if($G["Engine"]=="InnoDB")$G["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$G["Comment"]);if(!isset($G["Engine"]))$G["Comment"]="";if($y!="")$G["Name"]=$y;$F[$G["Name"]]=$G;}return$F;}function
is_view(array$R){return$R["Engine"]===null;}function
fk_support(array$R){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$R["Engine"]);}function
parse_type($wd){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$wd,$x);return
array($x[1],$x[2],ltrim($x[3].$x[4]));}function
fields($Q){$gf=(connection()->flavor=='maria');$F=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($Q)." ORDER BY ORDINAL_POSITION")as$G){$k=$G["COLUMN_NAME"];$T=$G["COLUMN_TYPE"];$Ad=$G["GENERATION_EXPRESSION"];$Pc=$G["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Pc,$_d);list($jj,$u,$tj)=parse_type($T);$i=$G["COLUMN_DEFAULT"];if($i!=""){$Be=preg_match('~text|json~',$jj);if(!$gf&&$Be)$i=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($i));if($gf||$Be){$i=($i=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($x){return
stripslashes(str_replace("''","'",$x[1]));},$i));}if(!$gf&&preg_match('~binary~',$jj)&&preg_match('~^0x(\w*)$~',$i,$x))$i=pack("H*",$x[1]);}$F[$k]=array("field"=>$k,"full_type"=>$T,"type"=>$jj,"length"=>$u,"unsigned"=>$tj,"default"=>($_d?($gf?$Ad:stripslashes($Ad)):$i),"null"=>($G["IS_NULLABLE"]=="YES"),"auto_increment"=>($Pc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Pc,$x)?$x[1]:""),"collation"=>$G["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$G[PRIVILEGES],where,order")),"comment"=>$G["COLUMN_COMMENT"],"primary"=>($G["COLUMN_KEY"]=="PRI"),"generated"=>($_d[1]=="PERSISTENT"?"STORED":$_d[1]),);}return$F;}function
indexes($Q,$g=null){$F=array();foreach(get_rows("SHOW INDEX FROM ".table($Q),$g)as$G){$y=$G["Key_name"];$F[$y]["type"]=($y=="PRIMARY"?"PRIMARY":($G["Index_type"]=="FULLTEXT"?"FULLTEXT":($G["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$G["Index_type"])?$G["Index_type"]:"INDEX"):"UNIQUE")));$F[$y]["columns"][]=$G["Column_name"];$F[$y]["lengths"][]=($G["Index_type"]=="SPATIAL"?null:$G["Sub_part"]);$F[$y]["descs"][]=null;$F[$y]["algorithm"]=$G["Index_type"];}return$F;}function
foreign_keys($Q){static$Eg='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$F=array();$Fb=get_val("SHOW CREATE TABLE ".table($Q),1);if($Fb){preg_match_all("~CONSTRAINT ($Eg) FOREIGN KEY ?\\(((?:$Eg,? ?)+)\\) REFERENCES ($Eg)(?:\\.($Eg))? \\(((?:$Eg,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Fb,$jf,PREG_SET_ORDER);foreach($jf
as$x){preg_match_all("~$Eg~",$x[2],$mi);preg_match_all("~$Eg~",$x[5],$Gi);$F[idf_unescape($x[1])]=array("db"=>idf_unescape($x[4]!=""?$x[3]:$x[4]),"table"=>idf_unescape($x[4]!=""?$x[4]:$x[3]),"source"=>array_map('Adminer\idf_unescape',$mi[0]),"target"=>array_map('Adminer\idf_unescape',$Gi[0]),"on_delete"=>($x[6]?:"RESTRICT"),"on_update"=>($x[7]?:"RESTRICT"),);}}return$F;}function
view($y){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($y),1)));}function
collations(){$F=array();foreach(get_rows("SHOW COLLATION")as$G){if($G["Default"])$F[$G["Charset"]][-1]=$G["Collation"];else$F[$G["Charset"]][]=$G["Collation"];}ksort($F);foreach($F
as$t=>$W)sort($F[$t]);return$F;}function
information_schema($h,$I=""){return($h=="information_schema")||(min_version(5.5)&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($h,$gb){return
queries("CREATE DATABASE ".idf_escape($h).($gb?" COLLATE ".q($gb):""));}function
drop_databases(array$Ob){$F=apply_queries("DROP DATABASE",$Ob,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$F;}function
rename_database($y,$gb){$F=false;if(create_database($y,$gb)){$S=array();$Mj=array();foreach(tables_list()as$Q=>$T){if($T=='VIEW')$Mj[]=$Q;else$S[]=$Q;}$F=(!$S&&!$Mj)||move_tables($S,$Mj,$y);drop_databases($F?array(DB):array());}return$F;}function
auto_increment(){$Ba=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$s){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$s["columns"],true)){$Ba="";break;}if($s["type"]=="PRIMARY")$Ba=" UNIQUE";}}return" AUTO_INCREMENT$Ba";}function
alter_table($Q,$y,array$l,array$jd,$kb,$zc,$gb,$Aa,$yg){$b=array();foreach($l
as$k){if($k[1]){$i=$k[1][3];if(preg_match('~ GENERATED~',$i)){$k[1][3]=(connection()->flavor=='maria'?"":$k[1][2]);$k[1][2]=$i;}$b[]=($Q!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($Q!=""?$k[2]:"");}else$b[]="DROP ".idf_escape($k[0]);}$b=array_merge($b,$jd);$O=($kb!==null?" COMMENT=".q($kb):"").($zc?" ENGINE=".q($zc):"").($gb?" COLLATE ".q($gb):"").($Aa!=""?" AUTO_INCREMENT=$Aa":"");if($yg){$zg=array();if($yg["partition_by"]=='RANGE'||$yg["partition_by"]=='LIST'){foreach($yg["partition_names"]as$t=>$W){$X=$yg["partition_values"][$t];$zg[]="\n  PARTITION ".idf_escape($W)." VALUES ".($yg["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$O
.="\nPARTITION BY $yg[partition_by]($yg[partition])";if($zg)$O
.=" (".implode(",",$zg)."\n)";elseif($yg["partitions"])$O
.=" PARTITIONS ".(+$yg["partitions"]);}elseif($yg===null)$O
.="\nREMOVE PARTITIONING";if($Q=="")return
queries("CREATE TABLE ".table($y)." (\n".implode(",\n",$b)."\n)$O");if($Q!=$y)$b[]="RENAME TO ".table($y);if($O)$b[]=ltrim($O);return($b?queries("ALTER TABLE ".table($Q)."\n".implode(",\n",$b)):true);}function
alter_indexes($Q,$b){$Ta=array();foreach($b
as$W)$Ta[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return
queries("ALTER TABLE ".table($Q).implode(",",$Ta));}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Mj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Mj)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$Mj,$Gi){$qh=array();foreach($S
as$Q)$qh[]=table($Q)." TO ".idf_escape($Gi).".".table($Q);if(!$qh||queries("RENAME TABLE ".implode(", ",$qh))){$Tb=array();foreach($Mj
as$Q)$Tb[table($Q)]=view($Q);connection()->select_db($Gi);$h=idf_escape(DB);foreach($Tb
as$y=>$Lj){if(!queries("CREATE VIEW $y AS ".str_replace(" $h."," ",$Lj["select"]))||!queries("DROP VIEW $h.$y"))return
false;}return
true;}return
false;}function
copy_tables(array$S,array$Mj,$Gi){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($S
as$Q){$y=($Gi==DB?table("copy_$Q"):idf_escape($Gi).".".table($Q));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $y"))||!queries("CREATE TABLE $y LIKE ".table($Q))||!queries("INSERT INTO $y SELECT * FROM ".table($Q)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$G){$dj=$G["Trigger"];list($Fc,$Uf)=trigger_event($G);if(!queries("CREATE TRIGGER ".($Gi==DB?idf_escape("copy_$dj"):idf_escape($Gi).".".idf_escape($dj))." $G[Timing] $Fc".($Uf!=""?" $Uf":"")." ON $y FOR EACH ROW\n$G[Statement];"))return
false;}}foreach($Mj
as$Q){$y=($Gi==DB?table("copy_$Q"):idf_escape($Gi).".".table($Q));$Lj=view($Q);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $y"))||!queries("CREATE VIEW $y AS $Lj[select]"))return
false;}return
true;}function
trigger_event(array$G){$Gc=explode(",",$G["Event"]);$F=array();foreach(array("DELETE","INSERT","UPDATE")as$Fc){if(in_array($Fc,$Gc))$F[]=$Fc;}$F=implode(" OR ",$F);if(in_array("UPDATE",$Gc)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($G["Trigger"]),2),$x)&&preg_match('~\bOF\s+(.+)~is',$x[1],$Uf))return
array("$F OF",$Uf[1]);return
array($F,"");}function
trigger($y,$Q){if($y=="")return
array();$H=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($y));$F=reset($H);if($F)list($F["Event"],$F["Of"])=trigger_event($F);return($F?:array());}function
triggers($Q){$F=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$G){list($Fc)=trigger_event($G);$F[$G["Trigger"]]=array($G["Timing"],$Fc);}return$F;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($y,$T){$H=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND SPECIFIC_NAME = ".q($y)."
ORDER BY ORDINAL_POSITION");$l=array();foreach($H
as$G){$wd=$G["DTD_IDENTIFIER"];list($jj,$u,$tj)=parse_type($wd);$l[]=array("field"=>$G["PARAMETER_NAME"],"type"=>$jj,"length"=>$u,"unsigned"=>$tj,"null"=>true,"full_type"=>$wd,"inout"=>($T=="FUNCTION"?"":$G["PARAMETER_MODE"]),"collation"=>$G["COLLATION_NAME"],);}$F=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND ROUTINE_NAME = ".q($y))->fetch_assoc();if(!$F)return
array();$F['options']=array("DEFINER"=>$F['definer'],"DETERMINISTIC"=>$F['is_deterministic'],"SQL_DATA_ACCESS"=>$F['data_access'],"SQL_SECURITY"=>$F['security'],"COMMENT"=>$F['comment'],);if($l&&$l[0]['field']=='')$F['returns']=array_shift($l);$F['fields']=$l;return$F;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?array("sql"=>"sql","javascript"=>"js"):array());}function
routine_options($zh){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($y,array$G){return
idf_escape($y);}function
last_id($E){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$D){return$f->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$D);}function
found_rows(array$R,array$Z){return($Z||$R["Engine"]!="InnoDB"?null:$R["Rows"]);}function
create_sql($Q,$Aa,$vi){$F=get_val("SHOW CREATE TABLE ".table($Q),1);if(!$Aa)$F=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$F);return$F;}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
use_sql($Nb,$vi=""){$y=idf_escape($Nb);$F="";if(preg_match('~CREATE~',$vi)&&($Eb=get_val("SHOW CREATE DATABASE $y",1))){set_utf8mb4($Eb);if($vi=="DROP+CREATE")$F="DROP DATABASE IF EXISTS $y;\n";$F
.="$Eb;\n";}return$F."USE $y";}function
trigger_sql($Q){$F="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")),null,"-- ")as$G){list($G["Event"],$G["Of"])=trigger_event($G);$F
.="\n".create_trigger(" ON ".table($G["Table"]),$G+array("Type"=>"FOR EACH ROW")).";\n";}return$F;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$k){return
driver()->convertColumn(idf_escape($k["field"]),$k);}function
unconvert_field(array$k,$F){if(preg_match("~binary~",$k["type"]))$F="UNHEX($F)";if($k["type"]=="bit")$F="CONVERT(b$F, UNSIGNED)";if($k["type"]=="vector")$F=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($F)";if(preg_match("~geom|point|linestring|polygon~",$k["type"])){$Rg=(min_version(8)?"ST_":"");$F=$Rg."GeomFromText($F, $Rg"."SRID($k[field]))";}return$F;}function
support($Sc){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$Sc);}function
kill_process($q){return
queries("KILL ".number($q));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($Oc=false){return
array();}function
type_values($q){return"";}function
type_definition($q){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($I,$g=null){return
true;}function
schema_comment($I){return"";}function
set_schema_comment($I,$kb){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($Pi,$j="",$Pa=array(),$Qi="",$Qf=false,$hc=""){if($Qf){header("HTTP/1.1 404 Not Found");$j=($j?:lang(69));}page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Ri=$Pi.($Qi!=""?": $Qi":"");$Si=strip_tags($Ri.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(70),'\' class=\'',lang(70),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Si,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.1.2+e29bdd90"),'">
';$Ib=adminer()->css();if(is_int(key($Ib)))$Ib=array_fill_keys($Ib,'light');$Kd=in_array('light',$Ib)||in_array('',$Ib);$Hd=in_array('dark',$Ib)||in_array('',$Ib);$Lb=($Kd?($Hd?null:false):($Hd?:null));$uf=" media='(prefers-color-scheme: dark)'";if($Lb!==false)echo"<link rel='stylesheet'".($Lb?"":$uf)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.1.2+e29bdd90")."'>\n";echo"<meta name='color-scheme' content='".($Lb===null?"light dark":($Lb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.1.2+e29bdd90");if(adminer()->head($Lb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.2+e29bdd90")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Ib
as$yj=>$Cf){$c=($Cf=='dark'&&!$Lb?$uf:($Cf=='light'&&$Hd?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$c href='".h($yj)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(71))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(72)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Pa!==null){$w=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($w?:".").'">'.get_driver(DRIVER).'</a> » ';$w=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$L=adminer()->serverName(SERVER);$L=($L!=""?$L:lang(73));if($Pa===false)echo"$L\n";else{echo"<a href='".h($w.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$L</a> » ";$Fh="";if(is_string($Pa)){$Fh=$Pa;$Pa=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($Pa))){$Pb="$w&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($Pb.($_GET["ns"]==""?$Fh:"")).'">'.h(DB).'</a> » ';}if(is_array($Pa)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$Fh).'">'.h($_GET["ns"]).'</a> » ';foreach($Pa
as$t=>$W){$Vb=(is_array($W)?$W[1]:h($W));if($Vb!="")echo"<a href='".h(ME."$t=").url_escape(is_array($W)?$W[0]:$W)."'>$Vb</a> » ";}}echo"$Pi\n";}}echo"<h2>$Ri$hc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$Ob=&get_session("dbs");if(DB!=""&&$Ob&&!in_array(DB,$Ob,true))$Ob=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($Qf){page_footer($Qf===true?"":$Qf);exit;}}function
service_worker(){$nh=has_passwords();$eb=($nh?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.1.2+e29bdd90")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$eb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$ai){foreach($ai
as$Ej){foreach($Ej
as$B){if($B!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Hb){$Od=array();foreach($Hb
as$t=>$W)$Od[]="$t $W";header("Content-Security-Policy: ".implode("; ",$Od));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$Cj=array();foreach(array_keys(adminer()->css())as$yj)$Cj[preg_replace('~\?.*~','',$yj)]=true;$F=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($Cj[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$x);$F[$m]=array((string)$x[1],Plugins::checksum($m));}}return$F;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'d9169f12','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'1b8ac749','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'a38b3b6a','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Pf;if(!$Pf)$Pf=base64_encode(rand_string());return$Pf;}function
page_messages($j){$xj=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$xf=idx($_SESSION["messages"],$xj);if($xf){echo"<div class='message'>".implode("</div>\n<div class='message'>",$xf)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$xj]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Bf=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Bf);echo"</div>\n";if($Bf!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(40),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(74),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Jf){while($Jf>=2147483648)$Jf-=4294967296;while($Jf<=-2147483649)$Jf+=4294967296;return(int)$Jf;}function
long2str(array$V,$Oj){$Ah='';foreach($V
as$W)$Ah
.=pack('V',$W);if($Oj)return
substr($Ah,0,end($V));return$Ah;}function
str2long($Ah,$Oj){$V=array_values(unpack('V*',str_pad($Ah,4*ceil(strlen($Ah)/4),"\0")));if($Oj)$V[]=strlen($Ah);return$V;}function
xxtea_mx($Wj,$Vj,$xi,$Fe){return
int32((($Wj>>5&0x7FFFFFF)^$Vj<<2)+(($Vj>>3&0x1FFFFFFF)^$Wj<<4))^int32(($xi^$Vj)+($Fe^$Wj));}function
encrypt_string($ui,$t){if($ui=="")return"";$t=array_values(unpack("V*",pack("H*",md5($t))));$V=str2long($ui,true);$Jf=count($V)-1;$Wj=$V[$Jf];$Vj=$V[0];$dh=floor(6+52/($Jf+1));$xi=0;while($dh-->0){$xi=int32($xi+0x9E3779B9);$pc=$xi>>2&3;for($qg=0;$qg<$Jf;$qg++){$Vj=$V[$qg+1];$If=xxtea_mx($Wj,$Vj,$xi,$t[$qg&3^$pc]);$Wj=int32($V[$qg]+$If);$V[$qg]=$Wj;}$Vj=$V[0];$If=xxtea_mx($Wj,$Vj,$xi,$t[$qg&3^$pc]);$Wj=int32($V[$Jf]+$If);$V[$Jf]=$Wj;}return
long2str($V,false);}function
decrypt_string($ui,$t){if($ui=="")return"";if(!$t)return
false;$t=array_values(unpack("V*",pack("H*",md5($t))));$V=str2long($ui,false);$Jf=count($V)-1;$Wj=$V[$Jf];$Vj=$V[0];$dh=floor(6+52/($Jf+1));$xi=int32($dh*0x9E3779B9);while($xi){$pc=$xi>>2&3;for($qg=$Jf;$qg>0;$qg--){$Wj=$V[$qg-1];$If=xxtea_mx($Wj,$Vj,$xi,$t[$qg&3^$pc]);$Vj=int32($V[$qg]-$If);$V[$qg]=$Vj;}$Wj=$V[$Jf];$If=xxtea_mx($Wj,$Vj,$xi,$t[$qg&3^$pc]);$Vj=int32($V[0]-$If);$V[0]=$Vj;$xi=int32($xi-0x9E3779B9);}return
long2str($V,true);}$Hg=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($t)=explode(":",$W);$Hg[$t]=$W;}}function
add_invalid_login(){$Ia=get_temp_dir()."/adminer-invalid";foreach(glob("$Ia*")?:array($Ia)as$m){$rd=file_open_lock($m);if($rd)break;}if(!$rd)$rd=file_open_lock("$Ia-".rand_string());if(!$rd)return;$we=json_decode(stream_get_contents($rd),true);$Mi=time();if($we){foreach($we
as$xe=>$W){if($W[0]<$Mi)unset($we[$xe]);}}$ue=&$we[adminer()->bruteForceKey()];if(!$ue)$ue=array($Mi+30*60,0);$ue[1]++;file_write_unlock($rd,json_encode($we));}function
check_invalid_login(array&$Hg){$we=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$rd=file_open_lock($m);if($rd){$we=json_decode(stream_get_contents($rd),true);file_unlock($rd);break;}}$t=adminer()->bruteForceKey();$ue=idx($we,$t,array());$Of=($ue[1]>29?$ue[0]-time():0);if($Of>0){$j=lang(75,ceil($Of/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$t==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(76,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$Hg,false);}}function
password_required(){static$F;if($F===null){$F=(bool)get_session("password_required");if(!$F){$Gb=adminer()->credentials();$F=!is_object(Driver::connect($Gb[0],$Gb[1],""));if($F)set_session("password_required",true);}}return$F;}function
require_password_link($B){$Ef="<a href='https://www.adminer.org/password/'".target_blank().">".lang(77)."</a>";if(!function_exists('password_hash'))return" $Ef";$Jg=($B!==null?$B:base64_encode(substr(pack("H*",rand_string()),0,12)));$Nd=password_hash($Jg,PASSWORD_DEFAULT);$m="adminer-plugins.php";$Lc=file_exists("adminer-plugins.php");if($Lc)$te=($B!==null?lang(78,"<b>$m</b>"):lang(79,"<b>$m</b>","<b>$Jg</b>"));else{$m="<button name='password_less' value='".h($Nd)."' class='link'>$m</button>";$te=($B!==null?lang(80,$m):lang(81,$m,"<b>$Jg</b>"));}$Ye="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($Nd)."'</span>),";$F="<p>$te
<pre><code class='jush'>".($Lc?$Ye:"&lt;?php\n<a>return</a> <a>array</a>(\n$Ye\n);")."</code></pre>
<p>$Ef
";return" <a href='#password-less' class='toggle'>".lang(82)."</a>
<div id='password-less' class='hidden'>".($Lc?$F:"<form action='' method='post'>\n".$F.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$_a=$_POST["auth"];if($_a&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$Jj=$_a["driver"];$L=$_a["server"];$U=$_a["username"];$B=(string)$_a["password"];$h=$_a["db"];set_password($Jj,$L,$U,$B);$_SESSION["db"][$Jj][$L][$U][$h]=true;if($_a["permanent"]){$t=implode("-",array_map('base64_encode',array($Jj,$L,$U,$h)));$Xg=adminer()->permanentLogin(true);$Hg[$t]="$t:".base64_encode($Xg?encrypt_string($B,$Xg):"");cookie("adminer_permanent",implode(" ",$Hg));}if(!array_diff(array_keys($_POST),array("auth","token"))||$Jj!=DRIVER||$L!=SERVER||$U!==$_GET["username"]||$h!=DB)redirect(auth_url($Jj,$L,$U,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(array("pwds","db","dbs","queries")as$t)set_session($t,null);unset_permanent($Hg);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(83).' '.lang(84));}elseif($Hg&&!$_SESSION["pwds"]){session_regenerate_id();$Xg=adminer()->permanentLogin();foreach($Hg
as$t=>$W){list(,$bb)=explode(":",$W);list($Jj,$L,$U,$h)=array_map('base64_decode',explode("-",$t));set_password($Jj,$L,$U,decrypt_string(base64_decode($bb),$Xg));$_SESSION["db"][$Jj][$L][$U][$h]=true;}}function
unset_permanent(array&$Hg){foreach($Hg
as$t=>$W){list($Jj,$L,$U,$h)=array_map('base64_decode',explode("-",$t));if($Jj==DRIVER&&$L==SERVER&&$U==$_GET["username"]&&$h==DB)unset($Hg[$t]);}cookie("adminer_permanent",implode(" ",$Hg));}function
auth_error($j,array&$Hg,$ve=true){$bi=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$bi]||$_GET[$bi])&&!$_SESSION["token"])$j=lang(85);elseif($ve&&($B=get_password())!==null){restart_session();add_invalid_login();if($B===false)$j
.=($j?'<br>':'').lang(86,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($Hg);}}if(!$_COOKIE[$bi]&&$_GET[$bi]&&ini_bool("session.use_only_cookies"))$j=lang(87);$ug=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$ug["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(42),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(88)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Hg);page_header(lang(89),lang(90,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($Hg);$Gb=adminer()->credentials();$f=Driver::connect($Gb[0],$Gb[1],$Gb[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$ef=null;if(!is_object($f)||($ef=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($f)?nl_br(h($f)):(is_string($ef)?$ef:lang(91))).(preg_match('~^ | $~',get_password())?'<br>'.lang(92):'');auth_error($j,$Hg);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(74),lang(93));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($_a&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(93).' '.lang(94);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(95,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(96);}function
doc_link(array$Dg,$Ji=""){return"";}function
like_bool(array$k){return$k["type"]=="bool"||(preg_match('~bit|tinyint~',$k["type"])&&$k["length"]==1);}function
sql_comment($D,$Mi=""){return"<!--\n".str_replace("--","--><!-- ",$D)."\n".($Mi!=""?"($Mi)\n":"")."-->\n";}connection()->select_db(adminer()->database());if(support("scheme")){$I=get_schema();if($I!="")set_schema($I);}adminer()->afterConnect();add_driver(DRIVER,lang(42));if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");$Y=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Y)).".".friendly_url($_GET["field"]));$J=array(idf_escape($_GET["field"]));$E=driver()->select($a,$J,array(where($_GET,$l)),$J);$G=($E?$E->fetch_row():array());echo
driver()->value($G[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$vj=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$y=>$k){if((!$vj&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$y]);}if($_POST&&!$j&&!isset($_GET["select"])){$df=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$df=($vj?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$df))$df=ME."select=".url_escape($a);$ie=indexes($a);$oj=unique_array($_GET["where"],$ie);$gh="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($df,lang(97),driver()->delete($a,$gh,$oj?0:1));else{$M=array();foreach($l
as$y=>$k){$W=process_input($k);if($W!==false&&$W!==null)$M[idf_escape($y)]=$W;}if($vj){if(!$M)redirect($df);queries_redirect($df,lang(98),driver()->update($a,$M,$gh,$oj?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$E=driver()->insert($a,$M);$Re=($E?last_id($E):0);queries_redirect($df,lang(99,($Re?" $Re":"")),$E);}}}$G=null;$D="";$Mi="";if($Z){$J=array();$Jh=array("*");foreach($l
as$y=>$k){if(isset($k["privileges"]["select"])){$wa=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$d=($wa?"$wa AS ":"").idf_escape($y);$J[]=$d;if($wa)$Jh[]=$d;}}$G=array();if(!support("table")){$J=array("*");$Jh=$J;}if($J){$ri=microtime(true);$E=driver()->select($a,$J,array($Z),$J,array(),(isset($_GET["select"])?2:1));$D=str_replace("SELECT ".implode(", ",$J),"SELECT ".implode(", ",$Jh),driver()->query);$Mi=format_time($ri);if(!$E)$j=adminer()->error();else{$G=$E->fetch_assoc();if(!$G)$G=false;}if(isset($_GET["select"])&&(!$G||$E->fetch_assoc()))$G=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$E=driver()->select($a,array("*"),array(),array("*"));$G=($E?$E->fetch_assoc():false);if(!$G)$G=array(driver()->primary=>"");}if($G){foreach($G
as$t=>$W){if(!$Z)$G[$t]=null;$l[$t]=array("field"=>$t,"null"=>($t!=driver()->primary),"auto_increment"=>($t==driver()->primary));}}}if($_POST["save"]){$Ng=array();foreach((array)$_POST["fields"]as$t=>$W)$Ng[bracket_escape($t,true)]=$W;$G=$Ng+($G?$G:array());}edit_form($a,$l,$G,$vj,$j,$D,$Mi);}elseif(isset($_GET["select"])){$a=$_GET["select"];$R=table_status1($a);$ie=indexes($a);$l=fields($a);$nd=column_foreign_keys($a);$Wf=$R["Oid"];$yh=array();$e=array();$Eh=array();$fg=array();$Ki=null;foreach($l
as$t=>$k){$y=adminer()->fieldName($k);$Kf=html_entity_decode(strip_tags($y),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$y!=""){$e[$t]=$Kf;if(is_shortable($k))$Ki=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$y!="")$Eh[$t]=$Kf;if(isset($k["privileges"]["order"])&&$y!="")$fg[$t]=$Kf;$yh+=$k["privileges"];}list($J,$o)=adminer()->selectColumnsProcess($e,$ie);$J=array_unique($J);$o=array_unique($o);$_e=count($o)<count($J);$Z=adminer()->selectSearchProcess($l,$ie,$R);$eg=adminer()->selectOrderProcess($l,$ie);$v=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$pj=>$G){$wa=convert_field($l[key($G)]);$J=array($wa?:idf_escape(key($G)));$Z[]=where_check(bracket_escape($pj,true),$l);$F=driver()->select($a,$J,$Z,$J);if($F)echo
first($F->fetch_row());}exit;}$Ug=$sj=array();foreach($ie
as$s){if($s["type"]=="PRIMARY"){$Ug=array_flip($s["columns"]);$sj=($J?$Ug:array());foreach($sj
as$t=>$W){if(in_array(idf_escape($t),$J))unset($sj[$t]);}break;}}if($Wf&&!$Ug){$Ug=$sj=array($Wf=>0);$ie[]=array("type"=>"PRIMARY","columns"=>array($Wf));}if($_POST&&!$j){$Sj=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Za=array();foreach($_POST["check"]as$Wa)$Za[]=where_check($Wa,$l);$Sj[]="((".implode(") OR (",$Za)."))";}$Uj=$Sj;$Sj=($Sj?"\nWHERE ".implode(" AND ",$Sj):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Ih=($J?:array("*"));$Ab=convert_fields($e,$l,$J);if($Ab)$Ih[]=substr($Ab,2);$D="";if(is_array($_POST["check"])&&!$Ug){$ud=implode(", ",$Ih)."\nFROM ".table($a);$Cd=($o&&$_e?"\nGROUP BY ".implode(", ",$o):"").($eg?"\nORDER BY ".implode(", ",$eg):"");$nj=array();foreach($_POST["check"]as$W)$nj[]="(SELECT".limit($ud,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l).$Cd,1).")";$D=implode(" UNION ALL ",$nj);}adminer()->dumpData($a,"table",$D,$Ih,$Uj,($_e?$o:array()),$eg);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$nd)){if($_POST["save"]||$_POST["delete"]){$E=true;$ma=0;$Ka=false;$M=array();if(!$_POST["delete"]){foreach($l
as$y=>$W){$r=bracket_escape($y);if(isset($_POST["fields"][$r])||$_FILES["fields-$r"]){$W=process_input($l[$y]);if($W!==null&&($_POST["clone"]||$W!==false))$M[idf_escape($y)]=($W!==false?$W:idf_escape($y));}}}if($_POST["delete"]||$M){$D=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($M)).")\nSELECT ".implode(", ",$M)."\nFROM ".table($a):"");if($_POST["all"]||($Ug&&is_array($_POST["check"]))||$_e){$E=($_POST["delete"]?driver()->delete($a,$Sj):($_POST["clone"]?queries("INSERT $D$Sj".driver()->insertReturning($a)):driver()->update($a,$M,$Sj)));$ma=connection()->affected_rows;if(is_object($E))$ma+=$E->num_rows;}else{$Ka=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$W){$Rj="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$l);$E=($_POST["delete"]?driver()->delete($a,$Rj,1):($_POST["clone"]?queries("INSERT".limit1($a,$D,$Rj)):driver()->update($a,$M,$Rj,1)));if(!$E)break;$ma+=connection()->affected_rows;}if($Ka&&$E&&!driver()->commit())$E=false;}}$wf=lang(100,$ma);if($_POST["clone"]&&$E&&$ma==1){$Re=last_id($E);if($Re)$wf=lang(99," $Re");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$wf,$E);if($Ka)driver()->rollback();if(!$_POST["delete"]){$Ng=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$Ng),$Ng,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$E=true;$ma=0;$Ka=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$pj=>$G){$M=array();foreach($G
as$t=>$W){$t=bracket_escape($t,true);$M[idf_escape($t)]=(preg_match('~char|text~',$l[$t]["type"])||$W!=""?adminer()->processInput($l[$t],$W):"NULL");}$E=driver()->update($a,$M," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($pj,true),$l),($_e||$Ug?0:1)," ");if(!$E)break;$ma+=connection()->affected_rows;}if($Ka)$E=$E&&driver()->commit();queries_redirect(remove_from_uri(),lang(100,$ma),$E);if($Ka)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$Vc=get_file("csv_file",true);if(!is_string($Vc))$j=upload_error($Vc);elseif(!preg_match('~~u',$Vc))$j=lang(101);else{$ib=array_keys($l);$K=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Jb=parse_csv($Vc,$K);$ma=count($Jb);driver()->begin();$H=array();foreach($Jb
as$t=>$Y){if(!$t&&!array_diff($Y,$ib)){$ib=$Y;$ma--;}else{$M=array();foreach($Y
as$p=>$fb)$M[idf_escape($ib[$p])]=($fb==""&&$l[$ib[$p]]["null"]?"NULL":q(csv_value($fb)));$H[]=$M;}}$E=(!$H||driver()->insertUpdate($a,$H,$Ug));if($E)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(102,$ma),$E);driver()->rollback();}}}}$Di=adminer()->tableName($R);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(58).": $Di",$j,array(),"",(!$l&&support("table")),($l?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($R)))):""));$M=null;if(isset($yh["insert"])||!support("table")){$M="";foreach((array)$_GET["where"]as$W){$X=$W["val"];if(is_array($X))$X=(count($X)==1&&preg_match('~^val-(.*)~s',reset($X),$x)?$x[1]:"");if($W["col"]!=""&&$X!=""&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$X)))))$M
.="&set[".url_escape(bracket_escape($W["col"]))."]=".url_escape($X);}}adminer()->selectLinks($R,$M);if(!$e&&support("table"))echo"<p class='error'>".lang(103)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($J,$e);adminer()->selectSearchPrint($Z,$Eh,$ie,$R);adminer()->selectOrderPrint($eg,$fg,$ie);adminer()->selectLimitPrint($v);if($Ki!==null)adminer()->selectLengthPrint($Ki);adminer()->selectActionPrint($ie);echo"</form>\n";foreach((array)$_GET["where"]as$W){if($W["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(93).' '.lang(94)."\n";page_footer();exit;}}$A=$_GET["page"];$qd=null;if($A=="last"){$qd=get_val(count_rows($a,$Z,$_e,$o));$A=floor(max(0,intval($qd)-1)/$v);}$Hh=$J;$Bd=$o;if(!$Hh){$Hh[]="*";$Ab=convert_fields($e,$l,$J);if($Ab)$Hh[]=substr($Ab,2);}foreach($J
as$t=>$W){$k=$l[idf_unescape($W)];if($k&&($wa=convert_field($k)))$Hh[$t]="$wa AS $W";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$t=>$W){if(isset($Hh[$t])&&$W["fun"])$Hh[$t].=" AS ".idf_escape(apply_sql_function($W["fun"],($W["col"]!=""?$W["col"]:"*")));}}if(!$_e&&$sj){foreach($sj
as$t=>$W){$Hh[]=idf_escape($t);if($Bd)$Bd[]=idf_escape($t);}}$E=driver()->select($a,$Hh,$Z,$Bd,$eg,$v,$A,true);if(!is_object($E))echo"<p class='error'>".(adminer()->error()?:lang(26))."\n";else{if(JUSH=="mssql"&&$A)$E->seek($v*$A);$wc=array();$H=array();while($G=$E->fetch_assoc()){if($A&&JUSH=="oracle")unset($G["RNUM"]);$H[]=$G;}$Ld=($v&&(support("cursor")?$_GET["next"]!="":count($H)>=$v));if(is_ajax()&&$Ld)header("X-Next-Page: ".pagination_href($A+1));if($_GET["modify"]&&$H){$pf=max_input_vars(count($H[0])+1,20);echo($pf&&count($H)>$pf?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($wj).">\n";if($_GET["page"]!="last"&&$v&&$o&&$_e&&JUSH=="sql")$qd=get_val(" SELECT FOUND_ROWS()");if(!$H)echo"<p class='message'>".lang(16)."\n";else{$Ha=adminer()->backwardKeys($a,$Di);$wh=array();reset($J);foreach($H[0]as$t=>$W){if(!isset($sj[$t])){$W=idx($_GET["columns"],key($J))?:array();$wh[$t]=array("fun"=>$W["fun"],"col"=>($J?$W["col"]:$t));next($J);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$o&&$J?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(104)."'".on('click','formCheck','^check').">");$Lf=array();$jh=1;foreach($wh
as$t=>$W){$k=$l[$W["col"]];$y=($k?adminer()->fieldName($k,$jh):($W["fun"]?($W["col"]!=""?h($W["col"]):"*"):h($t)));if($y!=""){$jh++;$Lf[$t]=$y;$d=idf_escape($t);$Xd=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($t);$Vb="&desc[0]=1";$ji=preg_replace('~ DESC( NULLS LAST)?$~','',$eg[0]);$li=($ji==$d||$ji==$t);echo"<th id='th[".h(bracket_escape($t))."]'".($li?" aria-sort='".($ji==$eg[0]?"ascending":"descending")."'":"").">";$yd=apply_sql_function(h($W["fun"]),$y);$ki=isset($k["privileges"]["order"])||$W["fun"];echo($ki?"<a href='".h($Xd.($li&&$ji==$eg[0]?$Vb:''))."'>$yd</a>":$yd);$vf=($ki?"<a href='".h($Xd.$Vb)."' title='".lang(105)."' class='text'> ↓</a>":'');if(!$W["fun"]&&isset($k["privileges"]["where"]))$vf
.="<a href='#fieldset-search' title='".lang(52)."' class='text jsonly'".on('click','selectSearch',$t)."> =</a>";echo($vf?"<span class='column'>$vf</span>":"");}}$Ve=array();if($_GET["modify"]){foreach($H
as$G){foreach($G
as$t=>$W)$Ve[$t]=max($Ve[$t],min(40,utf8_length((string)$W)));}}$Td=array();$Sd=array();foreach((array)$_GET["where"]as$W){$W+=array("col"=>"","op"=>"","val"=>"");$fb=$W["col"];$Dh=$W["val"];if(!is_array($Dh)&&($Dh!=""||preg_match('~NULL$~',$W["op"]))&&(!$W["op"]||in_array($W["op"],adminer()->operators($R)))){$Xe=strtr(preg_quote($Dh),array("%"=>".*?","_"=>"."));$Fg=array("LIKE %%"=>$Xe,"ILIKE %%"=>$Xe,"REGEXP"=>$Dh)+(JUSH=="pgsql"?array("~"=>$Dh,"~*"=>$Dh):array())+($fb!=""?array():array("="=>'^'.preg_quote($Dh).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$Dh)))).')\z',"LIKE"=>"^$Xe\\z","ILIKE"=>"^$Xe\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($Dh).'(?=,|\z)',));foreach(($fb!=""?array($fb=>$l[$fb]):$l)as$y=>$k){if($fb!=""||is_searchable($k,$W)){$ag=$W["op"]?:(!preg_match('~'.text_type().'~',$k["type"])?"IN":(preg_match('~%~',$Dh)?"LIKE":"LIKE %%"));if(isset($Fg[$ag])){$ab=preg_match('~^ILIKE|\*$~',$ag)||($ag!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$Td[$y][]="(?".($ab?"i":"").":$Fg[$ag])";}elseif($ag=="IS NULL"&&$fb=="")$Sd[$y]=true;}}}}echo($Ha?"<th>".lang(106):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($H,$nd)as$Jf=>$G){$oj=unique_array($H[$Jf],$ie);if(!$oj){$oj=array();foreach($H[$Jf]as$t=>$W){if(!in_array(idx(idx($wh,$t,array()),"fun"),driver()->grouping))$oj[$t]=$W;}}$pj="";$p=0;foreach($oj
as$t=>$W){$vh=idx($wh,$t,array());if($J&&$vh&&!isset($l[$vh["col"]]))continue;$yd=idx($vh,"fun","");$fb=($yd?$vh["col"]:$t);$k=(array)$l[$fb];$ze=is_blob($k);if(!$yd&&strlen($W)>64&&driver()->md5(idf_escape($fb),$k)){$yd="md5";$W=md5($ze?(string)driver()->value($W,$k):$W);}if($yd){$pj
.="&fun[$p]=".url_escape($yd)."&col[$p]=".url_escape($fb).($W!==null?"&val[$p]=".url_escape($W===false?"f":$W):"");$p++;}else$pj
.="&".($W!==null?"where[".url_escape(bracket_escape($fb))."]=".url_escape($W===false?"f":$W):"null[]=".url_escape($fb));}echo"<tr>".(!$o&&$J?"":"<td class='hover check sticky'>".($_e||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$pj)."' class='edit'>".lang(107)."</a> ").checkbox("check[]",substr($pj,1),in_array(substr($pj,1),(array)$_POST["check"])));foreach($G
as$t=>$W){if(isset($Lf[$t])){$yd=$wh[$t]["fun"];$fb=$wh[$t]["col"];$k=(array)$l[$t];if($W!=""&&(!isset($wc[$t])||$wc[$t]!=""))$wc[$t]=(is_mail($W)?$Lf[$t]:"");$w="";if(is_blob($k)&&$W!="")$w=ME.'download='.url_escape($a).'&field='.url_escape($t).$pj;if(!$w&&$W!==null){foreach((array)$nd[$t]as$md){if(count($nd[$t])==1||end($md["source"])==$t){$w="";foreach($md["source"]as$p=>$mi)$w
.=where_link($p,$md["target"][$p],$H[$Jf][$mi]);$w=($md["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($md["db"]),ME):ME).'select='.url_escape($md["table"]).$w;if($md["ns"]!="")$w=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($md["ns"]),$w);if(count($md["source"])==1)break;}}}if($yd=="count"&&$fb==""){$w=ME."select=".url_escape($a);$p=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$oj))$w
.=where_link($p++,$V["col"],$V["val"],$V["op"]);}foreach($oj
as$Fe=>$V){if(idx(idx($wh,$Fe,array()),"fun")){$w="";break;}$w
.=where_link($p++,$Fe,$V);}}$Yd=select_value($W,$w,$k,$Ki,($yd?array():idx($Td,$t,array())));if($W===null&&!$yd&&isset($Sd[$t]))$Yd="<mark>$Yd</mark>";$r=bracket_escape($pj);$q=h("val[$r][".bracket_escape($t)."]");$Pg=idx(idx($_POST["val"],$r),bracket_escape($t));$vj=idx($k["privileges"],"update")&&!is_identity_always($k);$sc=$H[$Jf][$t];$tc=!is_array($G[$t])&&!is_blob($k)&&is_utf8($sc)&&!$yd&&!$k["generated"]&&$vj;$T=($yd=="min"||$yd=="max"?$l[$fb]["type"]:$k["type"]);$Ji=preg_match('~text|json|lob~',$T);$Ae=preg_match(number_type(),$T)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$yd);echo"<td id='$q'".($Ae&&($W===null||is_numeric(strip_tags($Yd))||$T=="money")?" class='number'":"");if(($_GET["modify"]&&$tc&&$sc!==null)||$Pg!==null){$Fd=h($Pg!==null?$Pg:$sc);echo">".($Ji?"<textarea name='$q' cols='30' rows='".(substr_count($sc,"\n")+1)."'>$Fd</textarea>":"<input name='$q' value='$Fd' size='$Ve[$t]'>");}else{$ff=strpos($Yd,"<i>…</i>");$rc=adminer()->editVal($sc,$k);echo($vj?" data-text='".($ff?2:($Ji?1:0))."'".($tc?"":" data-warning='".lang(108)."'").($tc&&!$ff&&html_entity_decode(strip_tags($Yd),ENT_QUOTES,"UTF-8")!==$rc?" data-value='".h($rc)."'":""):"").">$Yd";}}}if($Ha)echo"<td>";adminer()->backwardKeysPrint($Ha,$H[$Jf]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$la=get_settings("adminer_import");if($H||$A||$Ld){$Jc=true;if($_GET["page"]!="last"){if(!$v||(count($H)<$v&&($H||!$A)))$qd=($A?$A*$v:0)+count($H);elseif(JUSH!="sql"||!$_e){$qd=($_e?null:found_rows($R,$Z));$Jc=!driver()->hasEstimatedRows();if($qd===null||(!$Jc&&$qd<max(1e4,2*($A+1)*$v))){$qd=first(slow_query(count_rows($a,$Z,$_e,$o)));$Jc=true;}}}if(!support("cursor"))$Ld=(($qd===false?count($H)+1:$qd-$A*$v)>$v);$sg=($v&&($Ld||$A));if($sg)echo($Ld?'<p><a href="'.h(pagination_href($A+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(109)).'>'.lang(110).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($sg){$of=($qd===false?$A+($H?(count($H)>=$v?2:1):0):floor(($qd-1)/$v));echo"<fieldset><legend>".lang(111)."</legend>";if(!support("cursor")){echo
pagination(0,$A).($A>5?" …":"");for($p=max(1,$A-4);$p<min($of,$A+5);$p++)echo
pagination($p,$A);if($of>0)echo($A+5<$of?" …":""),($Jc&&$qd!==false?pagination($of,$A):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$of'>".lang(112)."</a>");}else
echo
pagination(0,$A).($A>1?" …":""),($A?pagination($A,$A):""),($Ld?pagination($A+1,$A)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(113)."</legend>";$ac=($Jc?"":"~ ").$qd;$Le=($qd!==false?($Jc?"":"~ ").lang(114,$qd):"");echo
checkbox("all",1,0,$Le,on('click','countRows',$ac))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(115)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(116),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(18),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(117),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(14),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(118),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(22),'\'',confirm(),'>
</div></fieldset>
';$od=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]||($d["col"]!=""&&!isset($l[$d["col"]]))){unset($od['sql']);break;}}if($od){print_fieldset("export",lang(119)." <span id='selected2'></span>");$og=adminer()->dumpOutput();echo($og?html_select("output",$og,$la["output"])." ":""),html_select("format",$od,$la["format"])," <input type='submit' name='export' value='".lang(119)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($wc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(120)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($wj?input_hidden(ini_get("session.upload_progress.name"),$wj):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$la["format"])." <input type='submit' name='import' value='".lang(120)."'>".($wj?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$o&&$J?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["script"])){if($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}elseif(list($Q,$q,$y)=adminer()->_foreignColumn(column_foreign_keys($_GET["source"]),$_GET["field"])){$v=11;$Ic=(preg_match('~^[0-9]+$~',$_GET["value"])?"$q = $_GET[value]":"");$E=connection()->query("SELECT".limit("$q, $y FROM ".table($Q)," WHERE ".($Ic?"$Ic OR ":"")."$y LIKE ".q("$_GET[value]%")." ORDER BY ".($Ic?"CASE WHEN $Ic THEN 0 ELSE 1 END, ":"")."2",$v));for($p=1;($G=$E->fetch_row())&&$p<$v;$p++)echo"<a href='".h(ME."edit=".url_escape($Q)."&where[".url_escape(bracket_escape(idf_unescape($q)))."]=".url_escape($G[0]))."'>".h($G[1])."</a><br>\n";if($G)echo"...\n";}exit;}else{page_header(lang(73),"",false);if(adminer()->homepage()){echo"<form action='' method='post'>\n","<p>".lang(121).": <input type='search' name='query' value='".h($_POST["query"])."'> <input type='submit' value='".lang(52)."'>\n";if($_POST["query"]!="")search_tables();echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly"'.on('click','formCheck','^tables\[').'>','<th>'.lang(122),'<th>'.lang(123),"<tbody>\n";foreach(table_status()as$Q=>$G){$y=adminer()->tableName($G);if($y!="")echo'<tr><td class="hover">'.checkbox("tables[]",$Q,in_array($Q,(array)$_POST["tables"],true)),"<th><a href='".h(ME).'select='.url_escape($Q)."'>$y</a>","<td align='right'><a href='".h(ME."edit=").url_escape($Q)."'>".format_status($G,"Rows")."</a>";}echo"</table>\n","</div>\n","</form>\n",script("tableCheck();");adminer()->pluginsLinks();}}page_footer();