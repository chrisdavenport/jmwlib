<?php
/**
 * @version		$Id:$
 * @package		jmwlib
 * @copyright	Copyright (C) 2010 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * MediaWiki flagconfig request.
 *
 * action=flagconfig
 * Get basic information about review flag configuration for this site.
 * The following parameters are returned for each tag:
 *  name 	: The key name of this tag
 *  levels 	: Number of levels the tag has (above "not tagged")
 *  tier2 	: Level the tag must reach for a revision to be tier 2 (quality)
 *  tier3 	: Level the tag must reach for a revision to be tier 3 (pristine)
 * Flagged revisions have an assigned level for each tag. The highest tier
 * that all the tags meet is the review tier of the entire revision.
 *
 * This module requires read rights.
 *
 * Example:
 *   api.php?action=flagconfig
 */
class jmwModuleFlagconfig extends jmwModule
{
}