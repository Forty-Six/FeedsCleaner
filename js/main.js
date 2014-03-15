/*
@name Feeds Cleaner
@author Forty-Six <Forty-Six>
@link https://github.com/Forty-Six
@licence CC by nc sa http://creativecommons.org/licenses/by-nc-sa/2.0/fr/
@version 0.2
@description Feeds Cleaner supprime les articles lus tout en préservant vos favoris.
@description Feeds Cleaner removes read articles while preserving your favorites.
*/

function FS_feedsCleaner_confirm(feed) {
	if (confirm( _t('P_FEEDSCLEANER_CLEAN_CONFIRM') )) window.location='action.php?action=TheFeedToClean&feed='+feed;
}
