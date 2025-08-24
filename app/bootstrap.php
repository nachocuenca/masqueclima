<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($GLOBALS['config'])) {
  $GLOBALS['config'] = [
    'brand' => ['langs'=>['es','en','de','nl','ru'], 'default_lang'=>'es'],
    'app'   => ['base_url'=>'', 'ga4_id'=>''],
  ];
}

if (!function_exists('detect_lang')){
  function detect_lang(): string {
    $langs=$GLOBALS['config']['brand']['langs']??['es'];
    $def=$GLOBALS['config']['brand']['default_lang']??'es';
    if (isset($_GET['setlang'])) {
      $set=strtolower(trim($_GET['setlang']));
      if (in_array($set,$langs,true)) { setcookie('lang',$set,time()+31536000,'/'); return $set; }
    }
    $uri=strtok($_SERVER['REQUEST_URI']??'/','?');
    $seg=array_values(array_filter(explode('/',$uri)));
    if (!empty($seg) && in_array($seg[0],$langs,true)) { setcookie('lang',$seg[0],time()+31536000,'/'); return $seg[0]; }
    if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'],$langs,true)) return $_COOKIE['lang'];
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
      $a=strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2));
      if (in_array($a,$langs,true)) return $a;
    }
    return $def;
  }
}
$current_lang = detect_lang();
$GLOBALS['current_lang']=$current_lang;

if (isset($_GET['setlang'])) { require_once __DIR__.'/seo.php'; header('Location: '.lang_url($current_lang), true, 302); exit; }

if (!function_exists('load_translations')){
  function load_translations(string $lang): array {
    $f=__DIR__."/translations/{$lang}.php";
    if (is_file($f)) { $a=require $f; return is_array($a)?$a:[]; }
    return [];
  }
}
$GLOBALS['t']=load_translations($current_lang);

if (!function_exists('t')){
  function t(string $key,$fallback=null){
    $p=explode('.',$key); $v=$GLOBALS['t']??[];
    foreach($p as $k){ if(!is_array($v)||!array_key_exists($k,$v)) return $fallback??$key; $v=$v[$k]; }
    return $v;
  }
}
if (!function_exists('e')){ function e($v):string{ return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); } }

// CSRF + flash
if (!function_exists('csrf_token')){ function csrf_token():string{ if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16)); return $_SESSION['csrf']; } }
if (!function_exists('csrf_field')){ function csrf_field():string{ return '<input type="hidden" name="_token" value="'.e(csrf_token()).'">'; } }
if (!function_exists('csrf_check')){ function csrf_check():void{ $ok=isset($_POST['_token'],$_SESSION['csrf']) && hash_equals($_SESSION['csrf'],$_POST['_token']); if(!$ok){ http_response_code(400); die('CSRF token mismatch'); } } }
if (!function_exists('flash')){
  function flash(string $k, ?string $v=null){
    if($v!==null){ $_SESSION['flash'][$k]=$v; return; }
    $val=$_SESSION['flash'][$k]??null; if(isset($_SESSION['flash'][$k])) unset($_SESSION['flash'][$k]); return $val;
  }
}
