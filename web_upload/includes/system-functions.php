<?php
// *************************************************************************
//  This file is part of SourceBans++.
//
//  Copyright (C) 2014-2016 Sarabveer Singh <me@sarabveer.me>
//
//  SourceBans++ is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, per version 3 of the License.
//
//  SourceBans++ is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with SourceBans++. If not, see <http://www.gnu.org/licenses/>.
//
//  This file is based off work covered by the following copyright(s):  
//
//   SourceBans 1.4.11
//   Copyright (C) 2007-2015 SourceBans Team - Part of GameConnect
//   Licensed under GNU GPL version 3, or later.
//   Page: <http://www.sourcebans.net/> - <https://github.com/GameConnect/sourcebansv1>
//
// *************************************************************************

if(!defined("IN_SB")){echo "You should not be here. Only follow links!";die();}
/**
* Extended substr function. If it finds mbstring extension it will use, else
* it will use old substr() function
*
* @param string $string String that need to be fixed
* @param integer $start Start extracting from
* @param integer $length Extract number of characters
* @return string
*/
function substr_utf($string, $start = 0, $length = null) {
$start = (integer) $start >= 0 ? (integer) $start : 0;
if(is_null($length))
	$length = strlen_utf($string) - $start;
    return substr($string, $start, $length);
}

/**
* Equivalent to htmlspecialchars(), but allows &#[0-9]+ (for unicode)
* This function was taken from punBB codebase <http://www.punbb.org/>
*
* @param string $str
* @return string
*/
function clean($str) {
	$str = preg_replace('/&(?!#[0-9]+;)/s', '&amp;', $str);
	$str = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $str);
	return $str;
}

/**
* Check if selected email has valid email format
*
* @param string $user_email Email address
* @return boolean
*/
function is_valid_email($user_email) {
	$chars = EMAIL_FORMAT;
	if(strstr($user_email, '@') && strstr($user_email, '.')) {
		return (boolean) preg_match($chars, $user_email);
	}else{
		return false;
	}
}

/**
 * Returns the full location that the website is running in
 *
 * @return string location of SourceBans
 */
function GetLocation()
{
	return substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($base)-strlen("index.php"));
}

/**
 * Displays the header of SourceBans
 *
 * @return noreturn
 */
function BuildPageHeader()
{
	include TEMPLATES_PATH . "/header.php";
}

/**
 * Displays the sub-nav menu of SourceBans
 *
 * @return noreturn
 */
function BuildSubMenu()
{
	global $theme;
	$theme->left_delimiter = '<!--{';
	$theme->right_delimiter = '}-->';
	$theme->display('submenu.tpl');
	$theme->left_delimiter = '{';
	$theme->right_delimiter = '}';
}

/**
 * Displays the content header
 *
 * @return noreturn
 */
function BuildContHeader()
{
	global $theme, $userbank;
	if(isset($_GET['p']) && $_GET['p'] == "admin" && !$userbank->is_admin()) {
		echo "Вы не являетесь Администратором !";
		RedirectJS('index.php?p=login');
		PageDie();
	}

	if(!isset($_GET['s']) && isset($GLOBALS['pagetitle']))
	{
		$page = "<b>".$GLOBALS['pagetitle']."</b>";
	}

	$theme->assign('main_title', isset($page)?$page:'');
	$theme->assign('xleb', $GLOBALS['config']['page.xleb']);
	$theme->display('content_header.tpl');
}


/**
 * Adds a tab to the page
 *
 * @param string $title The title of the tab
 * @param string $utl The link of the tab
 * @param boolean $active Is the tab active?
 * @return noreturn
 */

function AddTab($title, $url, $desc, $active=false)
{
	global $tabs;
	$tab_arr = array(	);
	$tab_arr[0] = "Главная";
	$tab_arr[1] = "Серверы";
	$tab_arr[2] = "Список банов";
	$tab_arr[3] = "Список мутов\гагов";
	$tab_arr[4] = "Пожаловаться на игрока";
	$tab_arr[5] = "Аппеляция бана";
	$tab_arr[6] = "Список админов";

	$tabs = array();
	$tabs['title'] = $title;
	$tabs['url'] = $url;
	$tabs['desc'] = $desc;
	if($_GET['p'] == "default" && $title == $tab_arr[intval($GLOBALS['config']['config.defaultpage'])])
	{
		$tabs['active'] = true;
		$GLOBALS['pagetitle'] = $title;
	}
	else
	{
		if($_GET['p'] != "default" && substr($url, 12) == $_GET['p'])
		{
			$tabs['active'] = true;
			$GLOBALS['pagetitle'] = $title;
		}
		else
			$tabs['active'] = false;
	}
	include TEMPLATES_PATH . "/tab.php";
}

/**
 * Displays the pagetabs
 *
 * @return noreturn
 */
function BuildPageTabs()
{
	global $userbank;
	//AddTab("<i class='zmdi zmdi-home bgm-red'></i> Главная", "index.php?p=home", "Главная страница SourceBans. Список серверов, последних банов и блоков.");
	AddTab("<i class='zmdi zmdi-home zmdi-hc-fw'></i> Главная", "index.php?p=home", "Главная страница SourceBans. Список серверов, последних банов и блоков.");
	AddTab("<i class='zmdi zmdi-input-composite zmdi-hc-fw'></i> Серверы", "index.php?p=servers", "Список всех серверов и их текущий статус.");
	AddTab("<i class='zmdi zmdi-lock-outline zmdi-hc-fw'></i> Список банов", "index.php?p=banlist", "Список всех когда-либо выданных банов.");
	AddTab("<i class='zmdi zmdi-mic-off zmdi-hc-fw'></i> Список мутов/гагов", "index.php?p=commslist", "Список всех когда-либо выданных мутов и гагов.");
	if($GLOBALS['config']['config.enablesubmit']=="1")
		AddTab("<i class='zmdi zmdi-plus-circle-o-duplicate zmdi-hc-fw'></i>Пожаловаться на игрока", "index.php?p=submit", "Здесь вы можете оставить жалобу на игрока.");
	if($GLOBALS['config']['config.enableprotest']=="1")
		AddTab("<i class='zmdi zmdi-comment-edit zmdi-hc-fw'></i> Аппеляция бана", "index.php?p=protest", "Вы можете подать аппеляцию вашего бана, предоставив доказательства невиновности.");
	if($GLOBALS['config']['page.adminlist']=="1")
		AddTab("<i class='zmdi zmdi-accounts zmdi-hc-fw'></i> Админлист", "index.php?p=adminlist", "Список администраторов на доступных серверах.");
	if ($userbank->is_admin())
		AddTab("<i class='zmdi zmdi-star zmdi-hc-fw'></i> Админ-Панель", "index.php?p=admin", "Панель для администраторов. Управление серверами, администраторами, настройками.");

		include INCLUDES_PATH . "/CTabsMenu.php";

		// BUILD THE SUB-MENU's FOR ADMIN PAGES
		$submenu = new CTabsMenu();
		if($userbank->HasAccess(ADMIN_OWNER|ADMIN_LIST_ADMINS|ADMIN_ADD_ADMINS|ADMIN_EDIT_ADMINS|ADMIN_DELETE_ADMINS))
			$submenu->addMenuItem("Администраторы", 0,"", "index.php?p=admin&amp;c=admins", true);
		if(($userbank->HasAccess(ADMIN_OWNER)) && ($GLOBALS['config']['page.vay4er'] == "1"))
			$submenu->addMenuItem("Ваучеры", 0,"", "index.php?p=admin&amp;c=pay_card", true);
		if($userbank->HasAccess(ADMIN_OWNER|ADMIN_LIST_SERVERS|ADMIN_ADD_SERVER|ADMIN_EDIT_SERVERS|ADMIN_DELETE_SERVERS))
			$submenu->addMenuItem("Серверы", 0,"", "index.php?p=admin&amp;c=servers", true);
		if($userbank->HasAccess( ADMIN_OWNER|ADMIN_ADD_BAN|ADMIN_EDIT_OWN_BANS|ADMIN_EDIT_GROUP_BANS|ADMIN_EDIT_ALL_BANS|ADMIN_BAN_PROTESTS|ADMIN_BAN_SUBMISSIONS))
			$submenu->addMenuItem("Баны", 0,"", "index.php?p=admin&amp;c=bans", true);
		if($userbank->HasAccess( ADMIN_OWNER|ADMIN_ADD_BAN|ADMIN_EDIT_OWN_BANS|ADMIN_EDIT_ALL_BANS))
			$submenu->addMenuItem("Муты и гаги", 0,"", "index.php?p=admin&amp;c=comms", true);
		if($userbank->HasAccess(ADMIN_OWNER|ADMIN_LIST_GROUPS|ADMIN_ADD_GROUP|ADMIN_EDIT_GROUPS|ADMIN_DELETE_GROUPS))
			$submenu->addMenuItem("Группы", 0,"", "index.php?p=admin&amp;c=groups", true);
		if($userbank->HasAccess(ADMIN_OWNER|ADMIN_WEB_SETTINGS))
			$submenu->addMenuItem("Настройки", 0,"", "index.php?p=admin&amp;c=settings", true);
		if($userbank->HasAccess( ADMIN_OWNER|ADMIN_LIST_MODS|ADMIN_ADD_MODS|ADMIN_EDIT_MODS|ADMIN_DELETE_MODS))
			$submenu->addMenuItem("Моды", 0,"", "?p=admin&amp;c=mods", true);
		SubMenu( $submenu->getMenuArray() );
}

/**
 * Rewrites the breadcrumb html
 *
 * @return noreturn
 */
function BuildBreadcrumbs()
{
	$base = isset($GLOBALS['pagetitle']) ? $GLOBALS['pagetitle'] : '';
	if(isset($_GET['c']))
	{
		switch($_GET['c'])
		{
			case "admins":
				$cat = "Управление админами";
				break;
			case "servers":
				$cat = "Управление серверами";
				break;
			case "bans":
				$cat = "Управление банами";
				break;
			case "comms":
				$cat = "Управление мутами\гагами";
				break;
			case "groups":
				$cat = "Управление группами";
				break;
			case "settings":
				$cat = "Настройки SourceBans";
				break;
			case "mods":
				$cat = "Управление модами";
				break;
			case "pay_card":
				$cat = "Управление Ваучерами";
				break;
			default:
				unset($_GET['c']);
		}
	}

	if($GLOBALS['config']['page.xleb']){
		if(!isset($_GET['c']))
		{
			if(!empty($base))
				$bread = "<li class='active'>" . $base . "</li>";
			else
				unset ($bread);
		}
		else
		{
			if(!empty($cat))
				$bread = "<li><a href='index.php?p=". $_GET['p'] . "'>" . $base . "</a></li> <li class='active'>" . $cat . "</li>";
			else
				$bread = "<li href='index.php?p=". $_GET['p'] . "'>" . $base . "</li>";
		}

		if(!empty($bread))
			$text = $bread;
		else
			$text = "<li><a href='index.php?p=home'>Главная</a></li>";
		echo '<script type="text/javascript">$("breadcrumb").setHTML("' . $text . '");</script>';
	}
}
/**
 * Creates an anchor tag, and adds tooltip code if needed
 *
 * @param string $title The title of the tooltip/text to link
 * @param string $url The link
 * @param string $tooltip The tooltip message
 * @param string $target The new links target
 * @return noreturn
 */
function CreateLink($title, $url, $tooltip="", $target="_self", $wide=false)
{
	if($wide)
		$class = "perm";
	else
		$class = "tip";
	if(strlen($tooltip) == 0)
	{
		echo '<a href="' . $url . '" target="' . $target . '">' . $title .' </a>';
	}else{
		echo '<a href="' . $url . '" class="' . $class .'" title="' .  $title . ' :: ' .  $tooltip . '" target="' . $target . '">' . $title .' </a>';
	}
}

/**
 * Creates an anchor tag, and adds tooltip code if needed
 *
 * @param string $title The title of the tooltip/text to link
 * @param string $url The link
 * @param string $tooltip The tooltip message
 * @param string $target The new links target
 * @return URL
 */
function CreateLinkR($title, $url, $tooltip="", $target="_self", $wide=false, $onclick="")
{
	if($wide)
		$class = "perm";
	else
		$class = "tip";
	if(strlen($tooltip) == 0)
	{
		return '<a href="' . $url . '" onclick="' . $onclick . '" target="' . $target . '">' . $title .' </a>';
	}else{
		//return '<a href="' . $url . '" class="' . $class .'" title="' .  $title . ' :: ' .  $tooltip . '" target="' . $target . '">' . $title .' </a>';
		return '<a href="' . $url . '" class="' . $class .'" data-original-title="' .  $tooltip . '" target="' . $target . '" data-toggle="tooltip" data-placement="top">' . $title .' </a>';
	
	}
}

function HelpIcon($title, $text)
{
	return '<img border="0" align="absbottom" src="themes/' . SB_THEME .'/images/admin/help.png" class="tip" title="' .  $title . ' :: ' .  $text . '">&nbsp;&nbsp;';
}

/**
 * Allows the title of the page to change wherever the code is being executed from
 *
 * @param string $title The new title
 * @return noreturn
 */
function RewritePageTitle($title)
{
	$GLOBALS['TitleRewrite'] = $title;
}

/**
 * Build sub-menu
 *
 * @param array $el The array of elements for the menu
 * @return noreturn
 */
function SubMenu($el)
{
	$output = "";
	$first = true;
	foreach($el AS $e)
	{
        preg_match('/.*?&c=(.*)/', html_entity_decode($e['url']), $matches);
        if(!empty($matches[1]))
            $c = $matches[1];
        
		$output .= "<li style=\"".($first?"":"").(isset($_GET['c'])&&$_GET['c']==$c?"background-color: rgba(0, 0, 0, 0.075);":"")."\"><a href=\"" . $e['url'] . "\">" . $e['title']. "</a></li>";
		$first = false;
	}
	$GLOBALS['NavRewrite'] = $output;
}

/**
 * Converts a flag bitmask into a string
 *
 * @param integer $mask The mask to convert
 * @return string
 */
function BitToString($mask, $masktype=0, $head=true)
{
	$string = "";
		if($head)
			$string .= "<p class='c-blue'>Веб-права</p><ul class='clist clist-star'>";
		if($mask == 0)
		{
			$string .= "<li><i>Отсутствуют</i>";
			return $string;
		}
		if(($mask & ADMIN_LIST_ADMINS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Список администраторов</li>";
		if(($mask & ADMIN_ADD_ADMINS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Добавить администратора</li>";
		if(($mask & ADMIN_EDIT_ADMINS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Редактировать администратора</li>";
		if(($mask & ADMIN_DELETE_ADMINS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Удалить администратора</li>";

		if(($mask & ADMIN_LIST_SERVERS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Список серверов</li>";
		if(($mask & ADMIN_ADD_SERVER) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Добавить сервер</li>";
		if(($mask & ADMIN_EDIT_SERVERS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Редактировать сервер</li>";
		if(($mask & ADMIN_DELETE_SERVERS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Удалить сервер</li>";

		if(($mask & ADMIN_ADD_BAN) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Добавить бан</li>";
		if(($mask & ADMIN_EDIT_OWN_BANS) !=0 && ($mask & ADMIN_EDIT_ALL_BANS) ==0)
			$string .="<li> Редактировать свои баны</li>";
		if(($mask & ADMIN_EDIT_GROUP_BANS) !=0 && ($mask & ADMIN_EDIT_ALL_BANS) ==0)
			$string .= "<li> Редактировать групповые баны</li>";
		if(($mask & ADMIN_EDIT_ALL_BANS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Редактировать все баны</li>";
		if(($mask & ADMIN_BAN_PROTESTS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Протесты баны</li>";
		if(($mask & ADMIN_BAN_SUBMISSIONS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Жалобы на игроков</li>";

		if(($mask & ADMIN_UNBAN_OWN_BANS) !=0 && ($mask & ADMIN_UNBAN) ==0)
			$string .= "<li> Разбан своих банов</li>";
		if(($mask & ADMIN_UNBAN_GROUP_BANS) !=0 && ($mask & ADMIN_UNBAN) ==0)
			$string .= "<li> Разбан банов групп</li>";
		if(($mask & ADMIN_UNBAN) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Разбан всех банов</li>";
		if(($mask & ADMIN_DELETE_BAN) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Удаление всех банов</li>";
		if(($mask & ADMIN_BAN_IMPORT) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Импорт банов</li>";

		if(($mask & ADMIN_LIST_GROUPS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Просмотр групп</li>";
		if(($mask & ADMIN_ADD_GROUP) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Добавление групп</li>";
		if(($mask & ADMIN_EDIT_GROUPS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Редактирование групп</li>";
		if(($mask & ADMIN_DELETE_GROUPS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Удаление групп</li>";

		if(($mask & ADMIN_NOTIFY_SUB) !=0 || ($mask & ADMIN_NOTIFY_SUB) !=0)
			$string .= "<li> Уведомление по e-mail о предложении бана</li>";
		if(($mask & ADMIN_NOTIFY_PROTEST) !=0 || ($mask & ADMIN_NOTIFY_PROTEST) !=0)
			$string .= "<li> Уведомление по e-mail о протесте бана</li>";

		if(($mask & ADMIN_WEB_SETTINGS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Настройки ВЕБ</li>";

		if(($mask & ADMIN_LIST_MODS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Просмотр МОДов</li>";
		if(($mask & ADMIN_ADD_MODS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Добавление МОДов</li>";
		if(($mask & ADMIN_EDIT_MODS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Редактирование МОДов</li>";
		if(($mask & ADMIN_DELETE_MODS) !=0 || ($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Удаление МОДов</li>";

		if(($mask & ADMIN_OWNER) !=0)
			$string .= "<li> Главный админ</li>";
		
		if($head)
			$string .= "</ul>";

	return $string;
}


function SmFlagsToSb($flagstring, $head=true)
{

	$string = "";
	if($head)
		$string .= "<p class='c-blue'>Серверные права</p><ul class='clist clist-star'>";
	if(empty($flagstring))
		{
			$string .= "<li><i>Отсутствуют</i></li>";
			return $string;
		}
	if((strstr($flagstring, "a") || strstr($flagstring, "z")))
		$string .= "<li> Резервный слот</li>";
	if((strstr($flagstring, "b") || strstr($flagstring, "z")))
		$string .= "<li> Админ</li>";
	if((strstr($flagstring, "c") || strstr($flagstring, "z")))
		$string .= "<li> Кик</li>";
	if((strstr($flagstring, "d") || strstr($flagstring, "z")))
		$string .= "<li> Бан</li>";
	if((strstr($flagstring, "e") || strstr($flagstring, "z")))
		$string .= "<li> Разбан</li>";
	if((strstr($flagstring, "f") || strstr($flagstring, "z")))
		$string .= "<li> Слэй</li>";
	if((strstr($flagstring, "g") || strstr($flagstring, "z")))
		$string .= "<li> Смена карты</li>";
	if((strstr($flagstring, "h") || strstr($flagstring, "z")))
		$string .= "<li> Изменение КВАРов</li>";
	if((strstr($flagstring, "i") || strstr($flagstring, "z")))
		$string .= "<li> Исполнение конфигов</li>";
	if((strstr($flagstring, "j") || strstr($flagstring, "z")))
		$string .= "<li> Админский чат</li>";
	if((strstr($flagstring, "k") || strstr($flagstring, "z")))
		$string .="<li> Голосования</li>";
	if((strstr($flagstring, "l") || strstr($flagstring, "z")))
		$string .="<li> Пароль сервера</li>";
	if((strstr($flagstring, "m") || strstr($flagstring, "z")))
		$string .="<li> RCON</li>";
	if((strstr($flagstring, "n") || strstr($flagstring, "z")))
		$string .="<li> Разрешение читов</li>";
	if((strstr($flagstring, "z")))
		$string .="<li> Главный админ</li>";

	if((strstr($flagstring, "o") || strstr($flagstring, "z")))
		$string .="<li> Дополнительный флаг 1</li>";
	if((strstr($flagstring, "p") || strstr($flagstring, "z")))
		$string .="<li> Дополнительный флаг 2</li>";
	if((strstr($flagstring, "q") || strstr($flagstring, "z")))
		$string .="<li> Дополнительный флаг 3</li>";
	if((strstr($flagstring, "r") || strstr($flagstring, "z")))
		$string .="<li> Дополнительный флаг 4</li>";
	if((strstr($flagstring, "s") || strstr($flagstring, "z")))
		$string .="<li> Дополнительный флаг 5</li>";
	if((strstr($flagstring, "t") || strstr($flagstring, "z")))
		$string .="<li> Дополнительный флаг 6</li>";

	if($head)
		$string .= "</ul>";
	//if(($mask & SM_DEF_IMMUNITY) != 0)
	//{
	//	$flagstring .="&bull; Default immunity<br />";
	//}
	//if(($mask & SM_GLOBAL_IMMUNITY) != 0)
	//{
	//	$flagstring .="&bull; Global immunity<br />";
	//}
	
	return $string;

}

function PrintArray($array)
{
	echo "<pre>";
		print_r($array);
	echo "</pre>";
}

function NextGid()
{
	$gid = $GLOBALS['db']->GetRow("SELECT MAX(gid) AS next_gid FROM `" . DB_PREFIX . "_groups`");
	return ($gid['next_gid']+1);
}
function NextSGid()
{
	$gid = $GLOBALS['db']->GetRow("SELECT MAX(id) AS next_id FROM `" . DB_PREFIX . "_srvgroups`");
	return ($gid['next_id']+1);
}
function NextSid()
{
	$sid = $GLOBALS['db']->GetRow("SELECT MAX(sid) AS next_sid FROM `" . DB_PREFIX . "_servers`");
	return ($sid['next_sid']+1);
}
function NextAid()
{
	$aid = $GLOBALS['db']->GetRow("SELECT MAX(aid) AS next_aid FROM `" . DB_PREFIX . "_admins`");
	return ($aid['next_aid']+1);
}

function trunc($text, $len, $byword=true)
{
	if(strlen($text) <= $len)
		return $text;
    $text = $text." ";
    $text = substr($text,0,$len);
    if($byword)
    	$text = substr($text,0,strrpos($text,' '));
    $text = $text."...";
    return $text;
}

function StripQuotes($str)
{
	$str = str_replace("'", "", $str);
	$str = str_replace('"', "", $str);
	return $str;
}

function CreateRedBox($title, $content)
{
	$text = '<div id="msg-red-debug" style="">
	<i><img src="./images/warning.png" alt="Внимание" /></i>
	<b>' . $title .'</b>
	<br />
	' . $content . '</i>
</div>';

	echo $text;
}
function CreateGreenBox($title, $contnet)
{
	$text = '<div id="msg-green-dbg" style="">
	<i><img src="./images/yay.png" alt="Yay!" /></i>
	<b>' . $title .'</b>
	<br />
	' . $contnet . '</i>
</div>';

	echo $text;
}

function CreateQuote()
{
	global $userbank;
	$quote = array(
		array("Buy a new PC!", "Viper"),
		array("I'm not lazy! I just utilize technical resources!", "Brizad"),
		array("I need to mow the lawn", "sslice"),
		array("Like A Glove!", "Viper"),
		array("Your a Noob and You Know It!", "Viper"),
		array("Get your ass ingame", "Viper"),
		array("Mother F***ing Peices of Sh**", "Viper"),
		array("Shut up Bam", "[Everyone]"),
		array("Hi OllyBunch", "Viper"),
		array("Procrastination is like masturbation. Sure it feels good, but in the end you're only F***ing yourself!", "[Unknown]"),
		array("Rave's momma so fat she sat on the beach and Greenpeace threw her in", "SteamFriend"),
		array("Im just getting a beer", "Faith"),
		array("To be honest " . ($userbank->is_logged_in()?$userbank->getProperty('user'):'...') . ", I DONT CARE!", "Viper"),
		array("Yams", "teame06"),
		array("built in cheat 1.6 - my friend told me theres a cheat where u can buy a car door and run around and it makes u invincible....", "gdogg"),
		array("i just join conversation when i see a chance to tell people they might be wrong, then i quickly leave, LIKE A BAT", "BAILOPAN"),
		array("Lets just blame it on FlyingMongoose", "[Everyone]"),
		array("Don't step on that boom... mine...", "Recon"),
		array("Looks through sniper scope... Sit ;)", "Recon"),
		array("That plugin looks like something you found in a junk yard.", "Recon"),
		array("That's exactly what I asked you not to do.", "Recon"),
		array("Why are you wasting your time looking at this?", "Recon"),
		array("You must have better things to do with your time", "Recon"),
		array("I pity da fool", "Mr. T"),
		array("you grew a 3rd head?", "Tsunami"),
		array("I dont think you want to know...", "devicenull"),
		array("Sheep sex isn't baaaaaa...aad", "Brizad"),
		array("Oh wow, he's got skillz spelled with an 's'", "Brizad"),
		array("I'll get to it this weekend... I promise", "Brizad"),
		array("People do crazy things all the time... Like eat a Arby's", "Marge Simpson"),
		array("I wish my lawn was emo, so it would cut itself", "SirTiger"),
		array("Oh no! I've overflowed my balls!", "Olly"),
	);
	$num = rand(0, sizeof($quote)-1);
	return '"' . $quote[$num][0] . '" - <i>' . $quote[$num][1] . '</i>';
}

function CheckAdminAccess($mask)
{
	global $userbank;
	if(!$userbank->HasAccess($mask))
	{
		RedirectJS("index.php?p=login&m=no_access");
		die();
	}
}

function RedirectJS($url)
{
	echo '<script>window.location = "' . $url .'";</script>';
}

function RemoveCode($text)
{
	return htmlspecialchars(strip_tags($text));
}

function SecondsToString($sec, $textual=true)
{
	if($textual)
	{
		$div = array( 2592000, 604800, 86400, 3600, 60, 1 );
		$desc = array('Мес','Нед','Дн','Час','Мин','Сек');
		$ret = null;
		foreach($div as $index => $value)
		{
			$quotent = floor($sec / $value); //greatest whole integer
			if($quotent > 0) {
				$ret .= "$quotent {$desc[$index]}, ";
				$sec %= $value;
			}
		}
		return substr($ret,0,-2);
	}
	else
	{
		$hours = floor ($sec / 3600);
		$sec -= $hours * 3600;
		$mins = floor ($sec / 60);
		$secs = $sec % 60;
		return "$hours:$mins:$secs";
	}
}

// unused, as loading too slowly.
function CreateHostnameCache()
{
	require_once INCLUDES_PATH.'/CServerInfo.php';
	$res = $GLOBALS['db']->Execute("SELECT sid, ip, port FROM ".DB_PREFIX."_servers ORDER BY sid");
	$servers = array();
	while (!$res->EOF)
	{
		$info = array();
		$sinfo = new CServerInfo($res->fields[1],$res->fields[2]);
		$info = $sinfo->getInfo();
		if(!empty($info['hostname']))
			$servers[$res->fields[0]] = $info['hostname'];
		else
			$servers[$res->fields[0]] = $res->fields[1].":".$res->fields[2];
		$res->MoveNext();
	}
	return($servers);
}

function FetchIp($ip)
{
	$ip = sprintf('%u', ip2long($ip));
	if(!isset($_SESSION['CountryFetchHndl']) || !is_resource($_SESSION['CountryFetchHndl'])) {
		$handle = fopen(INCLUDES_PATH.'/IpToCountry.csv', "r");
		$_SESSION['CountryFetchHndl'] = $handle;
	}
	else {
		$handle = $_SESSION['CountryFetchHndl'];
		rewind($handle);
	}

	if (!$handle)
		return "zz";

	while (($ipdata = fgetcsv($handle, 4096)) !== FALSE) {
		// If line is comment or IP is out of range
		if ($ipdata[0][0] == '#' || $ip < $ipdata[0] || $ip > $ipdata[1])
			continue;

		if(empty($ipdata[4]))
			return "zz";
		return $ipdata[4];
	}

	return "zz";
}

function PageDie()
{
	include TEMPLATES_PATH.'/footer.php';
	die();
}

function GetMapImage($map)
{
	if(@file_exists(SB_MAP_LOCATION . "/" . $map . ".jpg"))
		return "images/maps/" . $map . ".jpg";
	else
		return "images/maps/nomap.jpg";
}

function CheckExt($filename, $ext)
{
	$filename = str_replace(chr(0), '', $filename);
	$path_info = pathinfo($filename);
	if(strtolower($path_info['extension']) == strtolower($ext))
		return true;
	else
		return false;
}

function ShowBox($title, $msg, $color, $redir="", $noclose=false)
{
	echo "<script>ShowBox('$title', '$msg', '$color', '$redir', $noclose);</script>";
}
function ShowBox_ajx($title, $msg, $color, $redir="", $noclose=false, &$response)
{
	$response->AddScript("ShowBox('$title', '$msg', '$color', '$redir', $noclose);");
}

function PruneBans()
{
	global $userbank;

	$res = $GLOBALS['db']->Execute('UPDATE `'.DB_PREFIX.'_bans` SET `RemovedBy` = 0, `RemoveType` = \'E\', `RemovedOn` = UNIX_TIMESTAMP() WHERE `length` != 0 and `ends` < UNIX_TIMESTAMP() and `RemoveType` IS NULL');
	$prot = $GLOBALS['db']->Execute("UPDATE `".DB_PREFIX."_protests` SET archiv = '3', archivedby = ".($userbank->GetAid()<0?0:$userbank->GetAid())." WHERE archiv = '0' AND bid IN((SELECT bid FROM `".DB_PREFIX."_bans` WHERE `RemoveType` = 'E'))");
	$submission = $GLOBALS['db']->Execute('UPDATE `'.DB_PREFIX.'_submissions` SET archiv = \'3\', archivedby = '.($userbank->GetAid()<0?0:$userbank->GetAid()).' WHERE archiv = \'0\' AND (SteamId IN((SELECT authid FROM `'.DB_PREFIX.'_bans` WHERE `type` = 0 AND `RemoveType` IS NULL)) OR sip IN((SELECT ip FROM `'.DB_PREFIX.'_bans` WHERE `type` = 1 AND `RemoveType` IS NULL)))');
    return $res?true:false;
}

function PruneComms()
{
	global $userbank;

	$res = $GLOBALS['db']->Execute('UPDATE `'.DB_PREFIX.'_comms` SET `RemovedBy` = 0, `RemoveType` = \'E\', `RemovedOn` = UNIX_TIMESTAMP() WHERE `length` != 0 and `ends` < UNIX_TIMESTAMP() and `RemoveType` IS NULL');
    return $res?true:false;
}

/*
function GetSVNRev()
{
	preg_match('/\\$Rev:[\\s]+([\\d]+)/', SB_REV, $rev, PREG_OFFSET_CAPTURE);
	return (int)$rev[1][0];
}*/

function GetGITRev()
{
	preg_match('/\\$Git:[\\s]+([\\d]+)/', SB_GITRev, $gitrev, PREG_OFFSET_CAPTURE);
	return (int)$gitrev[1][0];
}



// Function by Luman (http://snipplr.com/users/luman)
function array_qsort(&$array, $column=0, $order=SORT_ASC, $first=0, $last= -2)
{
  // $array  - the array to be sorted
  // $column - index (column) on which to sort
  //          can be a string if using an associative array
  // $order  - SORT_ASC (default) for ascending or SORT_DESC for descending
  // $first  - start index (row) for partial array sort
  // $last  - stop  index (row) for partial array sort
  // $keys  - array of key values for hash array sort

  $keys = array_keys($array);
  if($last == -2) $last = count($array) - 1;
  if($last > $first) {
   $alpha = $first;
   $omega = $last;
   $key_alpha = $keys[$alpha];
   $key_omega = $keys[$omega];
   $guess = $array[$key_alpha][$column];
   while($omega >= $alpha) {
     if($order == SORT_ASC) {
       while($array[$key_alpha][$column] < $guess) {$alpha++; $key_alpha = $keys[$alpha]; }
       while($array[$key_omega][$column] > $guess) {$omega--; $key_omega = $keys[$omega]; }
     } else {
       while($array[$key_alpha][$column] > $guess) {$alpha++; $key_alpha = $keys[$alpha]; }
       while($array[$key_omega][$column] < $guess) {$omega--; $key_omega = $keys[$omega]; }
     }
     if($alpha > $omega) break;
     $temporary = $array[$key_alpha];
     $array[$key_alpha] = $array[$key_omega]; $alpha++;
     $key_alpha = $keys[$alpha];
     $array[$key_omega] = $temporary; $omega--;
     if ($omega > 0)
     	$key_omega = $keys[$omega];
   }
   array_qsort ($array, $column, $order, $first, $omega);
   array_qsort ($array, $column, $order, $alpha, $last);
  }
}


function getDirectorySize($path)
{
	$totalsize = 0;
	$totalcount = 0;
	$dircount = 0;
	if ($handle = opendir ($path))
	{
		while (false !== ($file = readdir($handle)))
		{
			$nextpath = $path . '/' . $file;
			if ($file != '.' && $file != '..' && !is_link ($nextpath))
			{
				if (is_dir ($nextpath))
				{
					$dircount++;
					$result = getDirectorySize($nextpath);
					$totalsize += $result['size'];
					$totalcount += $result['count'];
					$dircount += $result['dircount'];
				}
				elseif (is_file ($nextpath))
				{
					$totalsize += filesize ($nextpath);
					$totalcount++;
				}
			}
		}
	}
	closedir ($handle);
	$total['size'] = $totalsize;
	$total['count'] = $totalcount;
	$total['dircount'] = $dircount;
	return $total;
}


function sizeFormat($size)
{
	if($size<1024)
	{
		return $size." bytes";
	}
	else if($size<(1024*1024))
	{
		$size=round($size/1024,1);
		return $size." KB";
	}
	else if($size<(1024*1024*1024))
	{
		$size=round($size/(1024*1024),2);
		return $size." MB";
	}
	else
	{
		$size=round($size/(1024*1024*1024),2);
		return $size." GB";
	}
}

function check_email($email) {
  $nonascii      = "\x80-\xff"; # Non-ASCII-Chars are not allowed

  $nqtext        = "[^\\\\$nonascii\015\012\"]";
  $qchar         = "\\\\[^$nonascii]";

  $protocol      = '(?:mailto:)';

  $normuser      = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
  $quotedstring  = "\"(?:$nqtext|$qchar)+\"";
  $user_part     = "(?:$normuser|$quotedstring)";

  $dom_mainpart  = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
  $dom_subpart   = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
  $dom_tldpart   = '[a-zA-Z]{2,5}';
  $domain_part   = "$dom_subpart$dom_mainpart$dom_tldpart";

  $regex         = "$protocol?$user_part\@$domain_part";

  return preg_match("/^$regex$/",$email);
}

// check, if one steamid is online on one specific server
function checkSinglePlayer($sid, $steamid)
{
	require_once(INCLUDES_PATH.'/CServerRcon.php');
	$serv = $GLOBALS['db']->GetRow("SELECT ip, port, rcon FROM ".DB_PREFIX."_servers WHERE sid = '".$sid."';");
	if(empty($serv['rcon'])) {
		return false;
	}
	$test = @fsockopen($serv['ip'], $serv['port'], $errno, $errstr, 2);
	if(!$test) {
		return false;
	}
	$r = new CServerRcon($serv['ip'], $serv['port'], $serv['rcon']);
	if(!$r->Auth())
	{
		$GLOBALS['db']->Execute("UPDATE ".DB_PREFIX."_servers SET rcon = '' WHERE sid = '".(int)$sid."';");
		return false;
	}

	$ret = $r->rconCommand("status");
	$search = preg_match_all(STATUS_PARSE,$ret,$matches,PREG_PATTERN_ORDER);
	$i = 0;
	foreach($matches[3] AS $match) {
		if(getAccountId($match) == getAccountId($steamid)) {
			$steam = $matches[3][$i];
			$name = $matches[2][$i];
			$time = $matches[4][$i];
			$ip = explode(":", $matches[8][$i]);
			$ip = $ip[0];
			$ping = $matches[5][$i];
			return array('name' => $name, 'steam' => $steamid, 'ip' => $ip, 'time' => $time, 'ping' => $ping);
		}
		$i++;
	}
	return false;
}

//function to check for multiple steamids on one server.
// param $steamids needs to be an array of steamids.
//returns array('STEAM_ID_1' => array('name' => $name, 'steam' => $steam, 'ip' => $ip, 'time' => $time, 'ping' => $ping), 'STEAM_ID_2' => array()....)
function checkMultiplePlayers($sid, $steamids)
{
	require_once(INCLUDES_PATH.'/CServerRcon.php');
	$serv = $GLOBALS['db']->GetRow("SELECT ip, port, rcon FROM ".DB_PREFIX."_servers WHERE sid = '".$sid."';");
	if(empty($serv['rcon'])) {
		return false;
	}
	$test = @fsockopen($serv['ip'], $serv['port'], $errno, $errstr, 2);
	if(!$test) {
		return false;
	}
	$r = new CServerRcon($serv['ip'], $serv['port'], $serv['rcon']);

	if(!$r->Auth())
	{
		$GLOBALS['db']->Execute("UPDATE ".DB_PREFIX."_servers SET rcon = '' WHERE sid = '".(int)$sid."';");
		return false;
	}

	$ret = $r->rconCommand("status");
	$search = preg_match_all(STATUS_PARSE,$ret,$matches,PREG_PATTERN_ORDER);
	$i = 0;
	$found = array();
	foreach($matches[3] AS $match) {
		foreach($steamids AS $steam) {
			if(getAccountId($match) == getAccountId($steam)) {
				$steam = $matches[3][$i];
				$name = $matches[2][$i];
				$time = $matches[4][$i];
				$ping = $matches[5][$i];
				$ip = explode(":", $matches[8][$i]);
				$ip = $ip[0];
				$found[$steam] = array('name' => $name, 'steam' => $steam, 'ip' => $ip, 'time' => $time, 'ping' => $ping);
				break;
			}
		}
		$i++;
	}
	return $found;
}

function getAccountId($steamid)
{
	if(strpos($steamid, "STEAM_") === 0) {
		$parts = explode(":", $steamid);
		if(count($parts) != 3)
			return -1;
		return (int)$parts[2]*2 + (int)$parts[1];
	}
	elseif(strpos($steamid, "[U:") === 0) {
		$parts = explode(":", $steamid);
		if(count($parts) != 3)
			return -1;
		return (int)substr($parts[2], 0, -1);
	}
	return -1;
}

function renderSteam2($accountId, $universe)
{
	return "STEAM_" . $universe . ":" . ($accountId & 1) . ":" . ($accountId >> 1);
}

function SBDate($format, $timestamp="")
{
    if(version_compare(PHP_VERSION, "5") != -1)
    {
        if($GLOBALS['config']['config.summertime'] == "1")
        {
            $str = date("r", $timestamp);
            $date = new DateTime($str);
            $date->modify("+1 hour");
            return $date->format($format);
        }
        else if(empty($timestamp))
            return date($format);
    }
    else
    {
        if($GLOBALS['config']['config.summertime'] == "1") {
            $summertime = 3600;
        } else {
            $summertime = 0;
        }
        if(empty($timestamp)) {
            $timestamp = time() + SB_TIMEZONE*3600 + $summertime;
        } else {
            $timestamp = $timestamp + SB_TIMEZONE*3600 + $summertime;
        }
    }
	return date($format, $timestamp);
}

/**
* Converts a SteamID to a FriendID
*
* @param string $authid the steamid to convert
* @return string
*/
function SteamIDToFriendID($authid)
{
	$friendid = $GLOBALS['db']->GetRow("SELECT CAST(MID('".$authid."', 9, 1) AS UNSIGNED) + CAST('76561197960265728' AS UNSIGNED) + CAST(MID('".$authid."', 11, 10) * 2 AS UNSIGNED) AS friend_id");
	return $friendid["friend_id"];
}

/**
* Converts a FriendID to a SteamID
*
* @param string $friendid the friendid to convert
* @return string
*/
function FriendIDToSteamID($friendid)
{

	$steamid = $GLOBALS['db']->GetRow("SELECT CONCAT(\"STEAM_0:\", (CAST('".$friendid."' AS UNSIGNED) - CAST('76561197960265728' AS UNSIGNED)) % 2, \":\", CAST(((CAST('".$friendid."' AS UNSIGNED) - CAST('76561197960265728' AS UNSIGNED)) - ((CAST('".$friendid."' AS UNSIGNED) - CAST('76561197960265728' AS UNSIGNED)) % 2)) / 2 AS UNSIGNED)) AS steam_id;");
	return $steamid['steam_id'];
}

/**
* Gets the friendid from a custom user id
*
* @param string $comid the customid to get the friendid for
* @return string
*/
function GetFriendIDFromCommunityID($comid)
{
	$raw = @file_get_contents("http://steamcommunity.com/id/".$comid."/?xml=1");
	preg_match("/<privacyState>([^\]]*)<\/privacyState>/", $raw, $status);
	if(($status && $status[1] != "public") || strstr($raw, "</profile>")) {
		$raw = str_replace("&", "", $raw);
		$raw = strip_31_ascii($raw);
		$raw = utf8_encode($raw);
		$xml = simplexml_load_string($raw);
		$result = $xml->xpath('/profile/steamID64');
		$friendid = (string)$result[0];
		return $friendid;
	}
	return false;
}
function GetCommunityName($steamid)
{
	$friendid = SteamIDToFriendID($steamid);
	$result = get_headers("http://steamcommunity.com/profiles/".$friendid."/", 1);
	$raw = file_get_contents(($result["Location"]!=""?$result["Location"]:"http://steamcommunity.com/profiles/".$friendid."/")."?xml=1");
	if(strstr($raw, "</profile>")) {
		$raw = str_replace("&", "", $raw);
        $raw = strip_31_ascii($raw);
		$raw = utf8_encode($raw);
		$xml = simplexml_load_string($raw);
		$result = $xml->xpath('/profile/steamID');
		$friendid = (string)$result[0];
		return $friendid;
	}
	return "";
}

function SendRconSilent($rcon, $sid)
{
	require_once(INCLUDES_PATH.'/CServerRcon.php');
	$serv = $GLOBALS['db']->GetRow("SELECT ip, port, rcon FROM ".DB_PREFIX."_servers WHERE sid = '".$sid."';");
	if(empty($serv['rcon'])) {
		return false;
	}
	$test = @fsockopen($serv['ip'], $serv['port'], $errno, $errstr, 2);
	if(!$test) {
		return false;
	}
	$r = new CServerRcon($serv['ip'], $serv['port'], $serv['rcon']);

	if(!$r->Auth())
	{
		$GLOBALS['db']->Execute("UPDATE ".DB_PREFIX."_servers SET rcon = '' WHERE sid = '".(int)$sid."';");
		return false;
	}

	$ret = $r->rconCommand($rcon);
	if($ret)
		return true;
	return false;
}

/* Function to check if a needle is inside a 2 layered recursive array
* like the one from ADODB->GetAll
* @param string $needle The string to search for
* @param array $array The array to search in
* @return boolean
*/
function in_array_dim($needle, $array)
{
	foreach($array as $secarray)
	{
		foreach($secarray as $part)
		{
			if($part == $needle)
				return true;
		}
	}
	return false;
}

// Strip all undisplayable chars from a string. e.g. or
function strip_31_ascii($string)
{
	for($i=0;$i<32;$i++)
		$string = str_replace(chr($i), "", $string);
	return $string;
}

function GetCommunityIDFromSteamID2($sid) {
    $parts = explode(':', str_replace('STEAM_', '' ,$sid)); 
    return bcadd(bcadd('76561197960265728', $parts['1']), bcmul($parts['2'], '2'));
}

/*
function GetUserAvatar($sid = -1, $upd = false) {
    global $userbank;
    $AvatarFile = sprintf("themes/new_box/img/profile-pics/%d.jpg", rand(1,9));
    $communityid = GetCommunityIDFromSteamID2(($sid==-1)?$userbank->getProperty("authid"):$sid);
    $res = $GLOBALS['db']->GetRow(sprintf("SELECT url, expires FROM `%s_avatars` WHERE `authid` = '%s'", DB_PREFIX, $communityid));
    if (count($res) != 0 && !$upd) {
        if ($res['expires'] < time()) return GetUserAvatar($sid, true);
        else $AvatarFile = $res['url'];
    } else {
        $SteamResponse = @json_decode(file_get_contents(sprintf("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s", STEAMAPIKEY, $communityid)));
        if (isset($SteamResponse->response->players[0]->avatarfull))
            $AvatarFile = $SteamResponse->response->players[0]->avatarfull;
        $ExpireTime = time()+AVATAR_LIFETIME;
        $query = null;
        $AF = $GLOBALS['db']->qstr($AvatarFile);
        if ($upd) $query = sprintf("UPDATE `%s_avatars` SET `url` = %s, `expires` = %d", DB_PREFIX, $AF, $ExpireTime);
        else $query = sprintf("INSERT INTO `%s_avatars` (`authid`, `url`, `expires`) VALUES ('%s', %s, %d)", DB_PREFIX, $communityid, $AF, $ExpireTime);
        $GLOBALS['db']->Execute($query);
    }
    return $AvatarFile;
}*/
function GetUserAvatar($sid = -1) {
    global $userbank;
    $communityid = false;
    $success = false;
    $AvatarFile = sprintf("themes/new_box/img/profile-pics/%d.jpg", rand(1,9));
    $sid = ($sid==-1)?($userbank->is_logged_in()?$userbank->getProperty("authid"):0):$sid;
    if ($sid) $communityid = GetCommunityIDFromSteamID2($sid);
    if ($communityid) {
        $res = $GLOBALS['db']->GetRow(sprintf("SELECT url, expires FROM `%s_avatars` WHERE `authid` = '%s'", DB_PREFIX, $communityid));
        $success = count($res)>0?true:false;
    }
    if ($success && $res['expires'] > time())
        $AvatarFile = $res['url'];
    else if ($communityid) {
        $SteamResponse = @json_decode(file_get_contents(sprintf("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s", STEAMAPIKEY, $communityid)));
        if (isset($SteamResponse->response->players[0]->avatarfull))
            $AvatarFile = $SteamResponse->response->players[0]->avatarfull;
        $ExpireTime = time()+AVATAR_LIFETIME;
        $query = null;
        $AF = $GLOBALS['db']->qstr($AvatarFile);
        if ($success) $query = sprintf("UPDATE `%s_avatars` SET `url` = %s, `expires` = %d", DB_PREFIX, $AF, $ExpireTime);
        else $query = sprintf("INSERT INTO `%s_avatars` (`authid`, `url`, `expires`) VALUES ('%s', %s, %d)", DB_PREFIX, $communityid, $AF, $ExpireTime);
        $GLOBALS['db']->Execute($query);
    }
    return $AvatarFile;
}

?>