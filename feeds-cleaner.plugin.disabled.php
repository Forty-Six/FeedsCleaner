<?php
/*
@name Feeds Cleaner
@author Forty-Six <Forty-Six>
@link https://github.com/Forty-Six
@licence CC by nc sa http://creativecommons.org/licenses/by-nc-sa/2.0/fr/
@version 0.2
@description Feeds Cleaner supprime les articles lus tout en préservant vos favoris.
@description Feeds Cleaner removes read articles while preserving your favorites.
*/


// Fonction d'ajout d'un lien dans le menu de gestion
function FS_FeedsCleaner_SettingLink (&$myUser) {

    $myUser = ( isset($_SESSION['currentUser']) ? unserialize($_SESSION['currentUser']) : false );
    if ($myUser != false) {
        echo '<li><a class="toggle" href="#feedsCleaner">'._t('P_FEEDSCLEANER_TITLE').'</a></li>';
    }
}

// Fonction d'affichage du formulaire de gestion
function FS_FeedsCleaner_SettingForm(&$myUser) {

    $myUser = ( isset($_SESSION['currentUser']) ? unserialize($_SESSION['currentUser']) : false );
    if ($myUser != false) {
    ?>
    
		<section class="feedsCleaner" id="feedsCleaner" name="feedsCleaner">
			<h2><?php echo _t('P_FEEDSCLEANER_TITLE') ?></h2>
			<p><?php echo _t('P_FEEDSCLEANER_DESCRIPTION') ?></p>
		
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="FS_feedsCleaner_row_title">
			<tr><th></th><th width="50px"><?php echo _t('P_FEEDSCLEANER_READ') ?></th><th width="50px"><?php echo _t('P_FEEDSCLEANER_FAVS') ?></th><th width="50px"><?php echo _t('P_FEEDSCLEANER_TOTAL') ?></th><th width="70px"></th></tr>
			</table>
		
		<?php
		$folderManager = new folder();
		$folders = $folderManager->populate('name');
		
		$events = FS_FeedsCleaner_countAllEvents();
		
		foreach ($folders as $folder) {
			echo '<h3 class="folder">'.$folder->getName().'</h3>';
			
			$feeds = $folder->getFeeds();
			
			echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
			foreach ($feeds as $feed) {
				
				$feed_id = $feed->getId();
				$readed = (isset($events[ $feed_id ]['read'])) ? $events[ $feed_id ]['read'] : "0";
				$favorites = $events[ $feed_id ]['favorite'];
				$total = (isset($events[ $feed_id ]['total'])) ? $events[ $feed_id ]['total'] : "0";
				
				echo '<tr class="FS_feedsCleaner_row"><td class="FS_feedsCleaner_feed_name">'.$feed->getName().'</td>
				<td width="50px"'.(($readed > 0) ? ' class="FS_feedsCleaner_read"' : '').'>'.$readed.'</td>
				<td width="50px"'.(($readed > 0) ? ' class="FS_feedsCleaner_favorites"' : '').'>'.$favorites.'</td>
				<td width="50px">'.$total.'</td>
				<td width="70px"><button class="button FS_feedsCleaner_Button" onclick="FS_feedsCleaner_confirm(\''.$feed_id.'\');">'._t('P_FEEDSCLEANER_CLEAN').'</button></td></tr>';
			}
			echo "</table>";
		}
		?>
		<br/>
		</section>
	<?php
	} else exit( _t('YOU_MUST_BE_CONNECTED_ACTION') );
}

// Comptage des articles lus et favoris par flux
function FS_FeedsCleaner_countAllEvents() {

	$FS_db = new MysqlEntity;
	$results = $FS_db->customQuery("SELECT ".MYSQL_PREFIX."event.id, ".MYSQL_PREFIX."event.feed, ".MYSQL_PREFIX."event.unread, ".MYSQL_PREFIX."event.favorite FROM ".MYSQL_PREFIX."event ORDER BY ".MYSQL_PREFIX."event.feed") ;
	
	$allEvents = array();
	if ($results != false) {
		while ($item = mysql_fetch_assoc($results)) {
			
			$allEvents[$item['feed']]['total'] += 1;
			if ($item['unread'] == '0') $allEvents[$item['feed']]['read'] += 1;
			if ($item['favorite'] == '1') $allEvents[$item['feed']]['favorite'] += 1;
		}
	}
	return  $allEvents;
}

// Fonction de suppression des articles
function FS_FeedsCleaner_letsClean(&$_) {
	
	if($_['action'] == 'TheFeedToClean') {
		if(isset($_['feed']) && $_['feed']!=''){
			$columns = array('feed'=>$_['feed'],'unread'=>'0','favorite'=>'0');
			$eventManager = new Event();
			$eventManager->delete($columns);
		}
	header('location: ./settings.php#feedsCleaner');
	}
}

// Restriction sur la configuration des plugins
// Issue: https://github.com/ldleman/Leed-market/issues/79
$myUser = ( isset($_SESSION['currentUser']) ? unserialize($_SESSION['currentUser']) : false );

if ($myUser != false) {
	// Insertion du lien dans le menu de gestion
	Plugin::addHook("setting_post_link","FS_FeedsCleaner_SettingLink");
	// Affichage du menu de gestion
	Plugin::addHook("setting_post_section","FS_FeedsCleaner_SettingForm");

	// Choix des flux et purge !
	Plugin::addHook("action_post_case", "FS_FeedsCleaner_letsClean"); 
}

// Insertion du javascript
Plugin::addJs("/js/main.js");
?>
