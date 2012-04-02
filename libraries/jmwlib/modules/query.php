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
 * MediaWiki query request.
 *
 * Query API module allows applications to get needed pieces of data from the MediaWiki databases,
 * and is loosely based on the old query.php interface.
 * All data modifications will first have to use query to acquire a token to prevent abuse from malicious sites.
 *
 * This module requires read rights.
 * Parameters:
 *   titles         - A list of titles to work on
 *   pageids        - A list of page IDs to work on
 *   revids         - A list of revision IDs to work on
 *   prop           - Which properties to get for the titles/revisions/pageids
 *                    Values (separate with '|'): info, revisions, links, langlinks, images, imageinfo, templates, categories, extlinks, categoryinfo, duplicatefiles, flagged, globalusage
 *   list           - Which lists to get
 *                    Values (separate with '|'): allimages, allpages, alllinks, allcategories, allusers, backlinks, blocks, categorymembers, deletedrevs, embeddedin, imageusage, logevents, recentchanges, search, tags, usercontribs, watchlist, watchlistraw, exturlusage, users, random, protectedtitles, oldreviewedpages, globalblocks, abuselog, abusefilters
 *   meta           - Which meta data to get about the site
 *                    Values (separate with '|'): siteinfo, userinfo, allmessages, globaluserinfo
 *   generator      - Use the output of a list as the input for other prop/list/meta items
 *                    NOTE: generator parameter names must be prefixed with a 'g', see examples.
 *                    One value: links, images, templates, categories, duplicatefiles, allimages, allpages, alllinks, allcategories, backlinks, categorymembers, embeddedin, imageusage, search, watchlist, watchlistraw, exturlusage, random, protectedtitles, oldreviewedpages
 *   redirects      - Automatically resolve redirects
 *   indexpageids   - Include an additional pageids section listing all returned page IDs.
 *   export         - Export the current revisions of all given or generated pages
 *   exportnowrap   - Return the export XML without wrapping it in an XML result (same format as Special:Export). Can only be used with export
 *
 *   Examples:
 *     api.php?action=query&prop=revisions&meta=siteinfo&titles=Main%20Page&rvprop=user|comment
 *     api.php?action=query&generator=allpages&gapprefix=API/&prop=revisions
 */
class jmwModuleQuery extends jmwModule
{
	/**
	 * Query element type.
	 */
	protected $query_element = 'pages';

	/**
	 * Valid arguments.
	 */
	protected $valid_extra_parms = array(
		'titles',
		'pageids',
		'revids',
		'prop',
		'list',
		'meta',
		'generator',
	 	'redirects',
	 	'indexpageids',
	 	'export',
	 	'exportnowrap',
	);

	/**
	 * Limit on the number of pages returned when using a generator call.
	 * If zero then all pages will be returned (use with caution).
	 */
	protected $limit = 10;

	/**
	 * Make the API call to the wiki.
	 * This method automatically handles paging of results if a generator is used.
	 * The number of pages returned is limited by default, but can be overridden
	 * by passing a parameter to the generator method.
	 *
	 * @param	object	Mediawiki object.
	 * @param	integer	Optional limit on the number of pages returned.
	 * @return	object	This object for method chaining.
	 */
	public function call( jmwWiki $wiki, $limit = 10 )
	{
		// Save limit.
		$this->limit = $limit;

		// If we are expecting a list of pages to be returned, then determine
		// the name of the element that will contain the page data.  This will
		// depend on whether we are using a generator or a list.
		if (isset( $this->args['generator'] )) {
			$continue_element = $this->args['generator'];
		}
		if (isset( $this->args['list'] )) {
			$continue_element = $this->args['list'];
		}

		// Initialise.
		$data = array();
		if ($continue_element != '') {
			$data[$continue_element] = array();
		}

		// Loop through pages of returned results.
		do {

			$continue = false;

			// Make the call.
			parent::call( $wiki );

			// Clear for next call.
			if (isset( $parameter )) {
				unset( $this->args[$parameter] );
			}

			// Get page of data and merge if handling multiple pages.
			if (isset( $this->data['query'][$continue_element] ) && $continue_element != '') {
				$data[$continue_element] = array_merge( $data[$continue_element], $this->data['query'][$continue_element] );
			}
			else {
				$data = $this->data;
			}

			// Setup ready for next query when handling muliple pages.
			if (isset( $this->data['query-continue'] )) {
				foreach ($this->data['query-continue'][$continue_element] as $parameter => $argument) {
					$this->args[$parameter] = $argument;
				}
				if ($this->limit == 0 || count( $data[$continue_element] ) < $this->limit) {
					$continue = true;
				}
			}

		} while ($continue);

		// Copy merged, multi-page data back into module object.
		$this->data = $data;

		return $this;
	}

	/**
	 * Magic method.
	 *
	 * @param	string	Name of a non-existent method.
	 * @param	array	Array of arguments that were passed to the method.
	 * @return	object	This object for method chaining.
	 */
	public function __call( $name, $args )
	{
		// Let the parent handle it first.
		parent::__call( $name, $args );

		// Can only have exactly one of 'titles', 'pageids', 'revids'.
		// Enforce precedence: revids -> pageids -> titles.
		if (isset( $this->args['revids'] )) {
			if (isset( $this->args['pageids'] )) {
				unset( $this->args['pageids'] );
			}
			if (isset( $this->args['titles'] )) {
				unset( $this->args['titles'] );
			}
		}
		if (isset( $this->args['pageids'] )) {
			if (isset( $this->args['titles'] )) {
				unset( $this->args['titles'] );
			}
		}

		return $this;
	}

	/**
	 * Specify a generator.
	 *
	 * @param	string	Name of the generator.
	 * @param	integer	Limit on the number of pages to be returned (0 = all).
	 * @return	object	This object for method chaining.
	 */
	public function generator( $generator, $limit = 20 )
	{
		static $valid = array(
			'links',
			'images',
			'templates',
			'categories',
			'duplicatefiles',
			'allimages',
			'allpages',
			'alllinks',
			'allcategories',
			'backlinks',
			'categorymembers',
			'embeddedin',
			'imageusage',
			'search',
			'watchlist',
			'watchlistraw',
			'exturlusage',
			'random',
			'protectedtitles',
			'oldreviewedpages',
			);

		if (in_array( $generator, $valid )) {
			$this->args['generator'] = $generator;
		}

		// Override the default limit if required.
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Specify meta information.
	 *
	 * @param	string	Type of meta information requested.
	 * @param	string	Array of properties (within the type).
	 */
	public function meta( $type, $meta = array() )
	{
		static $valid = array(

		/**
		 * meta=siteinfo (si)
		 * Return general information about the site.
		 *
		 * This module requires read rights.
		 *
		 * Examples:
		 *   api.php?action=query&meta=siteinfo&siprop=general|namespaces|namespacealiases|statistics
		 *   api.php?action=query&meta=siteinfo&siprop=interwikimap&sifilteriw=local
		 *   api.php?action=query&meta=siteinfo&siprop=dbrepllag&sishowalldb
		 */
		'siteinfo' => array(

			// siprop         - Which sysinfo properties to get:
			//                   general      - Overall system information
			//                   namespaces   - List of registered namespaces and their canonical names
			//                   namespacealiases - List of registered namespace aliases
			//                   specialpagealiases - List of special page aliases
			//                   magicwords   - List of magic words and their aliases
			//                   statistics   - Returns site statistics
			//                   interwikimap - Returns interwiki map (optionally filtered)
			//                   dbrepllag    - Returns database server with the highest replication lag
			//                   usergroups   - Returns user groups and the associated permissions
			//                   extensions   - Returns extensions installed on the wiki
			//                   fileextensions - Returns list of file extensions allowed to be uploaded
			//                   rightsinfo   - Returns wiki rights (license) information if available
			//                   languages    - Returns a list of languages MediaWiki supports
			//                  Values (separate with '|'): general, namespaces, namespacealiases, specialpagealiases, magicwords, interwikimap, dbrepllag, statistics, usergroups, extensions, fileextensions, rightsinfo, languages
			//                  Default: general
			'siprop',

			// sifilteriw     - Return only local or only nonlocal entries of the interwiki map
			//                  One value: local, !local
			'sifilteriw',

			// sishowalldb    - List all database servers, not just the one lagging the most
			'sishowalldb',

			// sinumberingroup - Lists the number of users in user groups
			'sinumberingroup',

			),

		/**
		 * meta=userinfo (ui)
		 * Get information about the current user
		 *
		 * This module requires read rights.
		 *
		 * Examples:
		 *   api.php?action=query&meta=userinfo
		 *   api.php?action=query&meta=userinfo&uiprop=blockinfo|groups|rights|hasmsg
		 */
		'userinfo' => array(

			// uiprop         - What pieces of information to include
			//                    blockinfo  - tags if the current user is blocked, by whom, and for what reason
			//                    hasmsg     - adds a tag "message" if the current user has pending messages
			//                    groups     - lists all the groups the current user belongs to
			//                    rights     - lists all the rights the current user has
			//                    changeablegroups - lists the groups the current user can add to and remove from
			//                    options    - lists all preferences the current user has set
			//                    editcount  - adds the current user's edit count
			//                    ratelimits - lists all rate limits applying to the current user
			//                  Values (separate with '|'): blockinfo, hasmsg, groups, rights, changeablegroups, options, preferencestoken, editcount, ratelimits, email
			'uiprop',
			),

		/**
		 * meta=allmessages (am)
		 * Return messages from this site.
		 *
		 * This module requires read rights.
		 *
		 * Examples:
		 *   api.php?action=query&meta=allmessages&amfilter=ipb-
		 *   api.php?action=query&meta=allmessages&ammessages=august|mainpage&amlang=de
		 */
		'allmessages' => array(

			// ammessages     - Which messages to output. "*" means all messages
			//                  Default: *
			'ammessages',

			// amprop         - Which properties to get
			//                  Values (separate with '|'): default
			'amprop',

			// amenableparser - Set to enable parser, will preprocess the wikitext of message
			//                  Will substitute magic words, handle templates etc.
			'amenableparser',

			// amargs         - Arguments to be substituted into message
			'amargs',

			// amfilter       - Return only messages that contain this string
			'amfilter',

			// amlang         - Return messages in this language
			'amlang',

			// amfrom         - Return messages starting at this message
			'amfrom',
			),

		/**
		 * meta=globaluserinfo (gui)
		 * Show information about a global user.
		 *
		 * This module requires read rights.
		 *
		 * Examples:
		 *   api.php?action=query&meta=globaluserinfo
		 *   api.php?action=query&meta=globaluserinfo&guiuser=Catrope&guiprop=groups|merged|unattached
		 */
		'globaluserinfo' => array(

			// guiuser        - User to get information about. Defaults to the current user
			'guiuser',

			// guiprop        - Which properties to get:
			//                    groups     - Get a list of global groups this user belongs to
			//                    merged     - Get a list of merged accounts
			//                    unattached - Get a list of unattached accounts
			//                  Values (separate with '|'): groups, rights, merged, unattached
			'guiprop',
			),
		);

		if (isset( $valid[$type] )) {
			$this->args['meta'] = $type;
//			$this->query_element = 'namespaces';		// TEMPORARY
			$meta = (array) $meta;
			foreach ($meta as $key => $value) {
				if (in_array( $key, $valid[$type] )) {
					$this->args[$key] = $value;
				}
			}
		}

		return $this;
	}

	/**
	 * Specify one or more properties to be returned.
	 *
	 * @param	string	Type of property.
	 * @param	string	Array of properties (within the type).
	 */
	public function prop( $type, $props = array() )
	{
		static $valid = array(

			/*
			 * prop=info (in)
			 * Get basic page information such as namespace, title, last touched date, ...
			 *
			 * This module requires read rights.
			 * Examples:
			 *   api.php?action=query&prop=info&titles=Main%20Page
			 *   api.php?action=query&prop=info&inprop=protection&titles=Main%20Page
			 */
			'info' => array(

				// inprop       - Which additional properties to get:
				//                      protection   - List the protection level of each page
				//                      talkid       - The page ID of the talk page for each non-talk page
				//                      watched      - List the watched status of each page
				//                      subjectid    - The page ID of the parent page for each talk page
				//                      url          - Gives a full URL to the page, and also an edit URL
				//                      readable     - Whether the user can read this page
				//                      preload      - Gives the text returned by EditFormPreloadText
				//                Values (separate with '|'): protection, talkid, watched, subjectid, url, readable, preload
				'inprop',

				// intoken      - Request a token to perform a data-modifying action on a page
				//                Values (separate with '|'): edit, delete, protect, move, block, unblock, email, import
				'intoken',

				// incontinue   - When more results are available, use this to continue
				'incontinue',
				),

			/**
			 * prop=revisions (rv)
			 * Get revision information.
			 * This module may be used in several ways:
			 *  1) Get data about a set of pages (last revision), by setting titles or pageids parameter.
			 *  2) Get revisions for one given page, by using titles/pageids with start/end/limit params.
			 *  3) Get data about a set of revisions by setting their IDs with revids parameter.
			 * All parameters marked as (enum) may only be used with a single page (#2).
			 * This module requires read rights.
			 * Examples:
			 *   Get data with content for the last revision of titles "API" and "Main Page":
			 *     api.php?action=query&prop=revisions&titles=API|Main%20Page&rvprop=timestamp|user|comment|content
			 *   Get last 5 revisions of the "Main Page":
			 *     api.php?action=query&prop=revisions&titles=Main%20Page&rvlimit=5&rvprop=timestamp|user|comment
			 *   Get first 5 revisions of the "Main Page":
			 *     api.php?action=query&prop=revisions&titles=Main%20Page&rvlimit=5&rvprop=timestamp|user|comment&rvdir=newer
			 *   Get first 5 revisions of the "Main Page" made after 2006-05-01:
			 *     api.php?action=query&prop=revisions&titles=Main%20Page&rvlimit=5&rvprop=timestamp|user|comment&rvdir=newer&rvstart=20060501000000
			 *   Get first 5 revisions of the "Main Page" that were not made made by anonymous user "127.0.0.1"
			 *     api.php?action=query&prop=revisions&titles=Main%20Page&rvlimit=5&rvprop=timestamp|user|comment&rvexcludeuser=127.0.0.1
			 *   Get first 5 revisions of the "Main Page" that were made by the user "MediaWiki default
			 *     api.php?action=query&prop=revisions&titles=Main%20Page&rvlimit=5&rvprop=timestamp|user|comment&rvuser=MediaWiki%20default
			 */
			'revisions' => array(

				// rvprop         - Which properties to get for each revision.
				//                  Values (separate with '|'): ids, flags, timestamp, user, size, comment, parsedcomment, content, tags, flagged
				//                  Default: ids|timestamp|flags|comment|user
				'rvprop',

				// rvlimit        - Limit how many revisions will be returned (enum)
				//                  No more than 500 (5000 for bots) allowed.
				'rvlimit',

				// rvstartid      - From which revision id to start enumeration (enum)
				'rvstartid',

				// rvendid        - Stop revision enumeration on this revid (enum)
				'rvendid',

				// rvstart        - From which revision timestamp to start enumeration (enum)
				'rvstart',

				// rvend          - Enumerate up to this timestamp (enum)
				'rvend',

				// rvdir          - Direction of enumeration - towards "newer" or "older" revisions (enum)
				//                  One value: newer, older
				//                  Default: older
				'rvdir',

				// rvuser         - Only include revisions made by user
				'rvuser',

				// rvexcludeuser  - Exclude revisions made by user
				'rvexcludeuser',

				// rvtag          - Only list revisions tagged with this tag
				'rvtag',

				// rvexpandtemplates - Expand templates in revision content
				'rvexpandtemplates',

				// rvgeneratexml  - Generate XML parse tree for revision content
				'rvgeneratexml',

				// rvsection      - Only retrieve the content of this section
				'rvsection',

				// rvtoken        - Which tokens to obtain for each revision
				//                  Values (separate with '|'): rollback
				'rvtoken',

				// rvcontinue     - When more results are available, use this to continue
				'rvcontinue',

				// rvdiffto       - Revision ID to diff each revision to.
				//                  Use "prev", "next" and "cur" for the previous, next and current revision respectively.
				'rvdiffto',

				// rvdifftotext   - Text to diff each revision to. Only diffs a limited number of revisions.
				//                  Overrides diffto. If rvsection is set, only that section will be diffed against this text.
				'rvdifftotext',
				),

			/**
			 * prop=links (pl)
			 * Returns all links from the given page(s)
			 *
			 * This module requires read rights.
			 * Examples:
			 *     Get links from the [[Main Page]]:
			 *       api.php?action=query&prop=links&titles=Main%20Page
			 *     Get information about the link pages in the [[Main Page]]:
			 *       api.php?action=query&generator=links&titles=Main%20Page&prop=info
			 *     Get links from the Main Page in the User and Template namespaces:
			 *       api.php?action=query&prop=links&titles=Main%20Page&plnamespace=2|10
			 * Generator:
			 *     This module may be used as a generator
			 */
			'links' => array(
				// plnamespace    - Show links in this namespace(s) only
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'plnamespace',

				// pllimit        - How many links to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'pllimit',

				// plcontinue     - When more results are available, use this to continue
				'plcontinue',
				),

			/**
			 * prop=langlinks (ll)
			 * Returns all interlanguage links from the given page(s)
			 *
			 * This module requires read rights.
			 * Examples:
			 *   Get interlanguage links from the [[Main Page]]:
			 *     api.php?action=query&prop=langlinks&titles=Main%20Page&redirects
			 */
			'langlinks' => array(
				// lllimit        - How many langlinks to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'lllimit',

			 	// llcontinue     - When more results are available, use this to continue
				'llcontinue',
				),

			/**
			 * prop=images (im)
			 * Returns all images contained on the given page(s)
			 *
			 * This module requires read rights.
			 * Examples:
			 *   Get a list of images used in the [[Main Page]]:
			 *     api.php?action=query&prop=images&titles=Main%20Page
			 *   Get information about all images used in the [[Main Page]]:
			 *     api.php?action=query&generator=images&titles=Main%20Page&prop=info
			 * Generator:
			 *   This module may be used as a generator
			 */
			'images' => array(
				// imlimit        - How many images to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'imlimit',

				// imcontinue     - When more results are available, use this to continue
				'imcontinue',
				),

			/**
			 * prop=imageinfo (ii)
			 * Returns image information and upload history
			 *
			 * This module requires read rights.
			 * Examples:
			 *   api.php?action=query&titles=File:Albert%20Einstein%20Head.jpg&prop=imageinfo
			 *   api.php?action=query&titles=File:Test.jpg&prop=imageinfo&iilimit=50&iiend=20071231235959&iiprop=timestamp|user|url
			 */
			'imageinfo' => array(
				// iiprop         - What image information to get.
				//                  Values (separate with '|'): timestamp, user, comment, url, size, dimensions, sha1, mime, metadata, archivename, bitdepth
				//                  Default: timestamp|user
				'iiprop',

				// iilimit        - How many image revisions to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 1
				'iilimit',

				// iistart        - Timestamp to start listing from
				'iistart',

				// iiend          - Timestamp to stop listing at
				'iiend',

				// iiurlwidth     - If iiprop=url is set, a URL to an image scaled to this width will be returned.
				//                  Only the current version of the image can be scaled.
				//                  Default: -1
				'iiurlwidth',

				// iiurlheight    - Similar to iiurlwidth. Cannot be used without iiurlwidth
				//                  Default: -1
				'iiurlheight',

				// iicontinue     - When more results are available, use this to continue
				'iicontinue',
				),

			/**
			 *  prop=templates (tl)
			 *  Returns all templates from the given page(s)
			 *
			 *  This module requires read rights.
			 *
			 *  Examples:
			 *    Get templates from the [[Main Page]]:
			 *      api.php?action=query&prop=templates&titles=Main%20Page
			 *    Get information about the template pages in the [[Main Page]]:
			 *      api.php?action=query&generator=templates&titles=Main%20Page&prop=info
			 *    Get templates from the Main Page in the User and Template namespaces:
			 *      api.php?action=query&prop=templates&titles=Main%20Page&tlnamespace=2|10
			 *  Generator:
			 *    This module may be used as a generator
			 */
			'templates' => array(
				// tlnamespace    - Show templates in this namespace(s) only
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'tlnamespace',

				// tllimit        - How many templates to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'tllimit',

				// tlcontinue     - When more results are available, use this to continue
				'tlcontinue',
				),

			/**
			 * prop=categories (cl)
			 * List all categories the page(s) belong to
			 *
			 * This module requires read rights.
			 * Examples:
			 *   Get a list of categories [[Albert Einstein]] belongs to:
			 *     api.php?action=query&prop=categories&titles=Albert%20Einstein
			 *   Get information about all categories used in the [[Albert Einstein]]:
			 *     api.php?action=query&generator=categories&titles=Albert%20Einstein&prop=info
			 * Generator:
			 *   This module may be used as a generator
			 */
			'categories' => array(
				// clprop         - Which additional properties to get for each category.
				//                  Values (separate with '|'): sortkey, timestamp, hidden
				'clprop',

				// clshow         - Which kind of categories to show
				//                  Values (separate with '|'): hidden, !hidden
				'clshow',

				// cllimit        - How many categories to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'cllimit',

				// clcontinue     - When more results are available, use this to continue
				'clcontinue',

				// clcategories   - Only list these categories. Useful for checking whether a certain page is in a certain category
				'clcategories',
				),

			/**
			 * prop=extlinks (el)
			 * Returns all external urls (not interwikies) from the given page(s)
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   Get a list of external links on the [[Main Page]]:
			 *     api.php?action=query&prop=extlinks&titles=Main%20Pag
			 */
			'extlinks' => array(
				// ellimit        - How many links to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'ellimit',

				// eloffset       - When more results are available, use this to continue
				'eloffset',

				),

			/**
			 *  prop=categoryinfo (ci)
			 *  Returns information about the given categories
			 *
			 *  This module requires read rights.
			 *  Example:
			 *    api.php?action=query&prop=categoryinfo&titles=Category:Foo|Category:Bar
			 */
			'categoryinfo' => array(

				// cicontinue     - When more results are available, use this to continue
				'cicontinue',
				),

			/**
			 * prop=duplicatefiles (df)
			 * List all files that are duplicates of the given file(s).
			 *
			 * This module requires read rights.
			 * Examples:
			 *   api.php?action=query&titles=File:Albert_Einstein_Head.jpg&prop=duplicatefiles
			 *   api.php?action=query&generator=allimages&prop=duplicatefiles
			 * Generator:
			 *   This module may be used as a generator
			 */
			'duplicatefiles' => array(

				// dflimit        - How many files to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'dflimit',

				// dfcontinue     - When more results are available, use this to continue
				'dfcontinue',
				),

			/**
			 *  prop=flagged
			 *  Get information about the flagging status of the given pages.
			 *  If a page is flagged, the following parameters are returned:
			 *   stable_revid      : The revision id of the latest stable revision
			 *   level, level_text : The highest flagging level of the page
			 *   pending_since     : If there are any current unreviewed revisions for that page, holds the timestamp of the first of them
			 *
			 *  This module requires read rights.
			 *  Examples:
			 *    api.php?action=query&prop=info|flagged&titles=Main%20Page
			 *    api.php?action=query&generator=allpages&gapfrom=K&prop=flagged
			 */
			'flagged' => array(),

			/**
			 * prop=globalusage (gu)
			 * Returns global image usage for a certain image
			 *
			 * This module requires read rights.
			 * Examples:
			 *   Get usage of File:Example.jpg:
			 *     api.php?action=query&prop=globalusage&titles=File:Example.jpg
			 */
			'globalusage' => array(

				// gulimit        - How many links to return
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'gulimit',

				// gucontinue     - When more results are available, use this to continue
				'gucontinue',

				// gufilterlocal  - Filter local usage of the file
				'gufilterlocal',
				),
			);

		if (isset( $valid[$type] )) {
			$this->args['prop'] = $type;
			$props = (array) $props;
			foreach ($props as $key => $value) {
				if (in_array( $key, $valid[$type] )) {
					$this->args[$key] = $value;
				}
			}
		}

		return $this;
	}

	/**
	 * Specify one or more lists to be returned.
	 *
	 * @param	string	Type of list
	 * @param	string	Array of lists (within the type).
	 */
	public function lists( $type, $lists = array() )
	{
		static $valid = array(

			/**
			 * list=allimages (ai)
			 * Enumerate all images sequentially
			 *
			 * This module requires read rights.
			 * Examples:
			 *   Simple Use
			 *     Show a list of images starting at the letter "B"
			 *       api.php?action=query&list=allimages&aifrom=B
			 *   Using as Generator
			 *     Show info about 4 images starting at the letter "T"
			 *       api.php?action=query&generator=allimages&gailimit=4&gaifrom=T&prop=imageinfo
			 * Generator:
			 *   This module may be used as a generator
			 */
			'allimages' => array(

				// aifrom         - The image title to start enumerating from.
				'aifrom',

				// aiprefix       - Search for all image titles that begin with this value.
				'aiprefix',

				// aiminsize      - Limit to images with at least this many bytes
				'aiminsize',

				// aimaxsize      - Limit to images with at most this many bytes
				'aimaxsize',

				// ailimit        - How many total images to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'ailimit',

				// aidir          - The direction in which to list
				//                  One value: ascending, descending
				//                  Default: ascending
				'aidir',

				// aisha1         - SHA1 hash of image
				'aisha1',

				// aisha1base36   - SHA1 hash of image in base 36 (used in MediaWiki)
				'aisha1base36',

				// aiprop         - Which properties to get
				//                  Values (separate with '|'): timestamp, user, comment, url, size, dimensions, sha1, mime, metadata, archivename, bitdepth
				//                  Default: timestamp|url
				'aiprop',
				),

			/**
			 * list=allpages (ap)
			 * Enumerate all pages sequentially in a given namespace
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   Simple Use
			 *     Show a list of pages starting at the letter "B"
			 *       api.php?action=query&list=allpages&apfrom=B
			 *   Using as Generator
			 *     Show info about 4 pages starting at the letter "T"
			 *       api.php?action=query&generator=allpages&gaplimit=4&gapfrom=T&prop=info
			 *     Show content of first 2 non-redirect pages begining at "Re"
			 *       api.php?action=query&generator=allpages&gaplimit=2&gapfilterredir=nonredirects&gapfrom=Re&prop=revisions&rvprop=content
			 * Generator:
			 *   This module may be used as a generator
			 */
			'allpages' => array(
				// apfrom         - The page title to start enumerating from.
				'apfrom',

				// apprefix       - Search for all page titles that begin with this value.
				'apprefix',

				// apnamespace    - The namespace to enumerate.
				//                  One value: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				//                  Default: 0
				'apnamespace',

				// apfilterredir  - Which pages to list.
				//                  One value: all, redirects, nonredirects
				//                  Default: all
				'apfilterdir',

				// apminsize      - Limit to pages with at least this many bytes
				'apminsize',

				// apmaxsize      - Limit to pages with at most this many bytes
				'apmaxsize',

				// apprtype       - Limit to protected pages only
				//                  Values (separate with '|'): edit, move
				'apprtype',

				// apprlevel      - The protection level (must be used with apprtype= parameter)
				//                  Can be empty, or Values (separate with '|'): autoconfirmed, sysop
				'apprlevel',

				// apprfiltercascade - Filter protections based on cascadingness (ignored when apprtype isn't set)
				//                  One value: cascading, noncascading, all
				//                  Default: all
				'apprfiltercascade',

				// aplimit        - How many total pages to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'aplimit',

				// apdir          - The direction in which to list
				//                  One value: ascending, descending
				//                  Default: ascending
				'apdir',

				// apfilterlanglinks - Filter based on whether a page has langlinks
				//                  One value: withlanglinks, withoutlanglinks, all
				//                  Default: all
				'apfilterlanglinks',

			),

			/*
			 *  list=alllinks (al)
			 *  Enumerate all links that point to a given namespace
			 *
			 *  This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=alllinks&alunique&alfrom=B
			 * Generator:
			 *   This module may be used as a generator
			 */
			'alllinks' => array(

				// alcontinue     - When more results are available, use this to continue.
				'alcontinue',

				// alfrom         - The page title to start enumerating from.
				'alfrom',

				// alprefix       - Search for all page titles that begin with this value.
				'alprefix',

				// alunique       - Only show unique links. Cannot be used with generator or prop=ids
				'alunique',

				// alprop         - What pieces of information to include
				//                  Values (separate with '|'): ids, title
				//                  Default: title
				'alprop',

				// alnamespace    - The namespace to enumerate.
				//                  One value: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				//                  Default: 0
				'alnamespace',

				// allimit        - How many total links to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'allimit',
				),

			/*
			 *  list=allcategories (ac)
			 *  Enumerate all categories
			 *
			 *  This module requires read rights.
			 *
			 *  Examples:
			 *    api.php?action=query&list=allcategories&acprop=size
			 *    api.php?action=query&generator=allcategories&gacprefix=List&prop=info
			 *
			 *  Generator:
			 *    This module may be used as a generator
			 */

			'allcategories' => array(

				// acfrom         - The category to start enumerating from.
				'acfrom',

				// acprefix       - Search for all category titles that begin with this value.
				'acprefix',

				// acdir          - Direction to sort in.
                //                  One value: ascending, descending
                //                  Default: ascending
				'acdir',

				// aclimit        - How many categories to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'aclimit',

				// acprop         - Which properties to get
				//                  Values (separate with '|'): size, hidden
				//                  Default:
				'acprop',
				),

			/*
			 * list=allusers (au)
			 * Enumerate all registered users
			 *
			 * This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=allusers&aufrom=Y
			 */
				'allusers' => array(

					// aufrom         - The user name to start enumerating from.
					'aufrom',

					// auprefix       - Search for all page titles that begin with this value.
					'auprefix',

					// augroup        - Limit users to a given group name
					//                  One value: bot, sysop, bureaucrat, checkuser, reviewer, steward, accountcreator, import, transwiki, ipblock-exempt, oversight, founder, rollbacker, confirmed, autoreviewer, researcher, abusefilter
					'augroup',

					// auprop         - What pieces of information to include.
					//                  `groups` property uses more server resources and may return fewer results than the limit.
					//                  Values (separate with '|'): blockinfo, groups, editcount, registration
					'auprop',

					// aulimit        - How many total user names to return.
					//                  No more than 500 (5000 for bots) allowed.
					//                  Default: 10
					'aulimit',

					// auwitheditsonly - Only list users who have made edits
					'auwitheditsonly',
				),

			/*
			 * list=backlinks (bl)
			 * Find all pages that link to the given page
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=backlinks&bltitle=Main%20Page
			 *   api.php?action=query&generator=backlinks&gbltitle=Main%20Page&prop=info
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
				'backlinks' => array(

				// bltitle        - Title to search.
				'bltitle',

				// blcontinue     - When more results are available, use this to continue.
				'blcontinue',

				// blnamespace    - The namespace to enumerate.
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'blnamespace',

				// blfilterredir  - How to filter for redirects. If set to nonredirects when blredirect is enabled, this is only applied to the second level
				//                  One value: all, redirects, nonredirects
				//                  Default: all
				'blfilterredir',

				// bllimit        - How many total pages to return. If blredirect is enabled, limit applies to each level separately (which means you may get up to 2 * limit results).
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'bllimit',

				// blredirect     - If linking page is a redirect, find all pages that link to that redirect as well. Maximum limit is halved.
				'blredirect',

				),

			/*
			 * list=blocks (bk)
			 * List all blocked users and IP addresses.
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=blocks
			 *   api.php?action=query&list=blocks&bkusers=Alice|Bob
			 */
				'blocks' => array(

				// bkstart        - The timestamp to start enumerating from
				'bkstart',

				// bkend          - The timestamp to stop enumerating at
				'bkend',

				// bkdir          - The direction in which to enumerate
				//                  One value: newer, older
				//                  Default: older
				'bkdir',

				// bkids          - Pipe-separated list of block IDs to list (optional)
				'bkids',

				// bkusers        - Pipe-separated list of users to search for (optional)
				'bkusers',

				// bkip           - Get all blocks applying to this IP or CIDR range, including range blocks.
				//                  Cannot be used together with bkusers. CIDR ranges broader than /16 are not accepted.
				'bkip',

				// bklimit        - The maximum amount of blocks to list
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'bklimit',

				// bkprop         - Which properties to get
				//                  Values (separate with '|'): id, user, by, timestamp, expiry, reason, range, flags
				//                  Default: id|user|by|timestamp|expiry|reason|flags
				'bkprop',
				),

			/*
			 * list=categorymembers (cm)
			 * List all pages in a given category
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   Get first 10 pages in [[Category:Physics]]:
			 *     api.php?action=query&list=categorymembers&cmtitle=Category:Physics
			 *   Get page info about first 10 pages in [[Category:Physics]]:
			 *     api.php?action=query&generator=categorymembers&gcmtitle=Category:Physics&prop=info
			 * Generator:
			 *   This module may be used as a generator
			 */
			'categorymembers' => array(

				// cmtitle        - Which category to enumerate (required). Must include Category: prefix
				'cmtitle',

				// cmprop         - What pieces of information to include
				//                  Values (separate with '|'): ids, title, sortkey, timestamp
				//                  Default: ids|title
				'cmprop',

				// cmnamespace    - Only include pages in these namespaces
				//                  NOTE: Due to $wgMiserMode, using this may result in fewer than "limit" results
				//                  returned before continuing; in extreme cases, zero results may be returned.
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'cmnamespace',

				// cmcontinue     - For large categories, give the value retured from previous query
				'cmcontinue',

				// cmlimit        - The maximum number of pages to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'cmlimit',

				// cmsort         - Property to sort by
				//                  One value: sortkey, timestamp
				//                  Default: sortkey
				'cmsort',

				// cmdir          - In which direction to sort
				//                  One value: asc, desc
				//                  Default: asc
				'cmdir',

				// cmstart        - Timestamp to start listing from. Can only be used with cmsort=timestamp
				'cmstart',

				// cmend          - Timestamp to end listing at. Can only be used with cmsort=timestamp
				'cmend',

				// cmstartsortkey - Sortkey to start listing from. Can only be used with cmsort=sortkey
				'cmstartsortkey',

				// cmendsortkey   - Sortkey to end listing at. Can only be used with cmsort=sortkey
				'cmendsortkey',
				),

			/*
			 *  list=deletedrevs (dr)
			 *  List deleted revisions.
			 *  This module operates in three modes:
			 *  1) List deleted revisions for the given title(s), sorted by timestamp
			 *  2) List deleted contributions for the given user, sorted by timestamp (no titles specified)
			 *  3) List all deleted revisions in the given namespace, sorted by title and timestamp (no titles specified, druser not set)
			 *  Certain parameters only apply to some modes and are ignored in others.
			 *  For instance, a parameter marked (1) only applies to mode 1 and is ignored in modes 2 and 3.
			 *
			 *  This module requires read rights.
			 *
			 *  Examples:
			 *    List the last deleted revisions of Main Page and Talk:Main Page, with content (mode 1):
			 *      api.php?action=query&list=deletedrevs&titles=Main%20Page|Talk:Main%20Page&drprop=user|comment|content
			 *    List the last 50 deleted contributions by Bob (mode 2):
			 *      api.php?action=query&list=deletedrevs&druser=Bob&drlimit=50
			 *    List the first 50 deleted revisions in the main namespace (mode 3):
			 *      api.php?action=query&list=deletedrevs&drdir=newer&drlimit=50
			 *    List the first 50 deleted pages in the Talk namespace (mode 3):
			 *      api.php?action=query&list=deletedrevs&drdir=newer&drlimit=50&drnamespace=1&drunique
			 */
			'deletedrevs' => array(

				// drstart        - The timestamp to start enumerating from. (1,2)
				'drstart',

				// drend          - The timestamp to stop enumerating at. (1,2)
				'drend',

				// drdir          - The direction in which to enumerate. (1,2)
				//                  One value: newer, older
				//                  Default: older
				'drdir',

				// drfrom         - Start listing at this title (3)
				'drfrom',

				// drcontinue     - When more results are available, use this to continue (3)
				'drcontinue',

				// drunique       - List only one revision for each page (3)
				'drunique',

				// druser         - Only list revisions by this user
				'druser',

				// drexcludeuser  - Don't list revisions by this user
				'drexcludeuser',

				// drnamespace    - Only list pages in this namespace (3)
				//                  One value: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				//                  Default: 0
				'drnamespace',

				// drlimit        - The maximum amount of revisions to list
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'drlimit',

				// drprop         - Which properties to get
				//                  Values (separate with '|'): revid, user, comment, parsedcomment, minor, len, content, token
				//                  Default: user|comment
				'drprop',
				),

			/**
			 * list=embeddedin (ei)
			 * Find all pages that embed (transclude) the given title
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=embeddedin&eititle=Template:Stub
			 *   api.php?action=query&generator=embeddedin&geititle=Template:Stub&prop=info
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
				'embeddedin' => array(

				// eititle        - Title to search.
				'eititle',

				// eicontinue     - When more results are available, use this to continue.
				'eicontinue',

				// einamespace    - The namespace to enumerate.
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'einamespace',

				// eifilterredir  - How to filter for redirects
				//                  One value: all, redirects, nonredirects
				//                  Default: all
				'eifilterredir',

				// eilimit        - How many total pages to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'eilimit',
				),

			/**
			 * list=imageusage (iu)
			 * Find all pages that use the given image title.
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=imageusage&iutitle=File:Albert%20Einstein%20Head.jpg
			 *   api.php?action=query&generator=imageusage&giutitle=File:Albert%20Einstein%20Head.jpg&prop=info
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
			'imageusage' => array(

				// iutitle        - Title to search.
				'iutitle',

				// iucontinue     - When more results are available, use this to continue.
				'iucontinue',

				// iunamespace    - The namespace to enumerate.
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'iunamespace',

				// iufilterredir  - How to filter for redirects. If set to nonredirects when iuredirect is enabled, this is only applied to the second level
				//                  One value: all, redirects, nonredirects
				//                  Default: all
				'iufilterredir',

				// iulimit        - How many total pages to return. If iuredirect is enabled, limit applies to each level separately (which means you may get up to 2 * limit results).
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'iulimit',

				// iuredirect     - If linking page is a redirect, find all pages that link to that redirect as well. Maximum limit is halved.
				'iuredirect',
				),

			/**
			 * list=logevents (le)
			 * Get events from logs.
			 *
			 * This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=logevents
			 */
				'logevents' => array(

				// leprop         - Which properties to get
				//                  Values (separate with '|'): ids, title, type, user, timestamp, comment, parsedcomment, details, tags
				//                  Default: ids|title|type|user|timestamp|comment|details
				'leprop',

				// letype         - Filter log entries to only this type(s)
				//                  Can be empty, or One value: block, protect, rights, delete, upload, move, import, patrol, merge, suppress, review, stable, gblblock, renameuser, globalauth, gblrights, abusefilter, newusers
				'letype',

				// lestart        - The timestamp to start enumerating from.
				'lestart',

				// leend          - The timestamp to end enumerating.
				'leend',

				// ledir          - In which direction to enumerate.
				//                  One value: newer, older
				//                  Default: older
				'ledir',

				// letitle        - Filter entries to those related to a page.
				'letitle',

				// letag          - Only list event entries tagged with this tag.
				'letag',

				// lelimit        - How many total event entries to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'lelimit',
				),

			/**
			 * list=recentchanges (rc)
			 * Enumerate recent changes
			 *
			 * This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=recentchanges
			 */
			'recentchanges' => array(

				// rcstart        - The timestamp to start enumerating from.
				'rcstart',

				// rcend          - The timestamp to end enumerating.
				'rcend',

				// rcdir          - In which direction to enumerate.
				//                  One value: newer, older
				//                  Default: older
				'rcdir',

				// rcnamespace    - Filter log entries to only this namespace(s)
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'rcnamespace',

				// rcuser         - Only list changes by this user
				'rcuser',

				// rcexcludeuser  - Don't list changes by this user
				'rcexcludeuser',

				// rctag          - Only list changes tagged with this tag.
				'rctag',

				// rcprop         - Include additional pieces of information
				//                  Values (separate with '|'): user, comment, parsedcomment, flags, timestamp, title, ids, sizes, redirect, patrolled, loginfo, tags
				//                  Default: title|timestamp|ids
				'rcprop',

				// rctoken        - Which tokens to obtain for each change
				//                  Values (separate with '|'): patrol
				'rctoken',

				// rcshow         - Show only items that meet this criteria.
				//                  For example, to see only minor edits done by logged-in users, set show=minor|!anon
				//                  Values (separate with '|'): minor, !minor, bot, !bot, anon, !anon, redirect, !redirect, patrolled, !patrolled
				'rcshow',

				// rclimit        - How many total changes to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'rclimit',

				// rctype         - Which types of changes to show.
				//                  Values (separate with '|'): edit, new, log
				'rctype',
				),

			/**
			 * list=search (sr)
			 * Perform a full text search
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=search&srsearch=meaning
			 *   api.php?action=query&list=search&srwhat=text&srsearch=meaning
			 *   api.php?action=query&generator=search&gsrsearch=meaning&prop=info
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */

			'search' => array(

				// srsearch       - Search for all page titles (or content) that has this value.
				'srsearch',

				// srnamespace    - The namespace(s) to enumerate.
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				//                  Default: 0
				'srnamespace',

				// srwhat         - Search inside the text or titles.
				//                  One value: title, text
				'srwhat',

				// srinfo         - What metadata to return.
				//                  Values (separate with '|'): totalhits, suggestion
				//                  Default: totalhits|suggestion
				'srinfo',

				// srprop         - What properties to return.
				//                  Values (separate with '|'): size, wordcount, timestamp, snippet
				//                  Default: size|wordcount|timestamp|snippet
				'srprop',

				// srredirects    - Include redirect pages in the search.
				'srredirects',

				// sroffset       - Use this value to continue paging (return by query)
				//                  Default: 0
				'sroffset',

				// srlimit        - How many total pages to return.
				//                  No more than 50 (500 for bots) allowed.
				//                  Default: 10
				'srlimit',
				),

			/**
			 * list=tags (tg)
			 * List change tags.
			 *
			 * This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=tags&tgprop=displayname|description|hitcount
			 */
			'tags' => array(

				// tgcontinue     - When more results are available, use this to continue
				'tgcontinue',

				// tglimit        - The maximum number of tags to list
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'tglimit',

				// tgprop         - Which properties to get
				//                  Values (separate with '|'): name, displayname, description, hitcount
				//                  Default: name
				'tgprop',
				),

			/**
			 * list=usercontribs (uc)
			 * Get all edits by a user
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=usercontribs&ucuser=YurikBot
			 *    api.php?action=query&list=usercontribs&ucuserprefix=217.121.114.
			 */
			'usercontribs' => array(

				// uclimit        - The maximum number of contributions to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'uclimit',

				// ucstart        - The start timestamp to return from.
				'ucstart',

				// ucend          - The end timestamp to return to.
				'ucend',

				// uccontinue     - When more results are available, use this to continue.
				'uccontinue',

				// ucuser         - The user to retrieve contributions for.
				'ucuser',

				// ucuserprefix   - Retrieve contibutions for all users whose names begin with this value. Overrides ucuser.
				'ucuserprefix',

				// ucdir          - The direction to search (older or newer).
				//                  One value: newer, older
				//                  Default: older
				'ucdir',

				// ucnamespace    - Only list contributions in these namespaces
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'ucnamespace',

				// ucprop         - Include additional pieces of information
				//                  Values (separate with '|'): ids, title, timestamp, comment, parsedcomment, size, flags, patrolled, tags
				//                  Default: ids|title|timestamp|comment|size|flags
				'ucprop',

				// ucshow         - Show only items that meet this criteria, e.g. non minor edits only: show=!minor
				//                  NOTE: if show=patrolled or show=!patrolled is set, revisions older than $wgRCMaxAge won't be shown
				//                  Values (separate with '|'): minor, !minor, patrolled, !patrolled
				'uchsow',

				// uctag          - Only list revisions tagged with this tag
				'uctag',
				),

			/**
			 * list=watchlist (wl)
			 * Get all recent changes to pages in the logged in user's watchlist
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=watchlist
			 *   api.php?action=query&list=watchlist&wlprop=ids|title|timestamp|user|comment
			 *   api.php?action=query&list=watchlist&wlallrev&wlprop=ids|title|timestamp|user|comment
			 *   api.php?action=query&generator=watchlist&prop=info
			 *   api.php?action=query&generator=watchlist&gwlallrev&prop=revisions&rvprop=timestamp|user
			 *   api.php?action=query&list=watchlist&wlowner=Bob_Smith&wltoken=d8d562e9725ea1512894cdab28e5ceebc7f20237
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
			'watchlist' => array(

				// wlallrev       - Include multiple revisions of the same page within given timeframe.
				'wlallrev',

				// wlstart        - The timestamp to start enumerating from.
				'wlstart',

				// wlend          - The timestamp to end enumerating.
				'wlend',

				// wlnamespace    - Filter changes to only the given namespace(s).
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'wlnamespace',

				// wluser         - Only list changes by this user
				'wluser',

				// wlexcludeuser  - Don't list changes by this user
				'wlexcludeuser',

				// wldir          - In which direction to enumerate pages.
				//                  One value: newer, older
				//                  Default: older
				'wldir',

				// wllimit        - How many total results to return per request.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'wllimit',

				// wlprop         - Which additional items to get (non-generator mode only).
				//                  Values (separate with '|'): ids, title, flags, user, comment, parsedcomment, timestamp, patrol, sizes, notificationtimestamp
				//                  Default: ids|title|flags
				'wlprop',

				// wlshow         - Show only items that meet this criteria.
				//                  For example, to see only minor edits done by logged-in users, set show=minor|!anon
				//                  Values (separate with '|'): minor, !minor, bot, !bot, anon, !anon, patrolled, !patrolled
				'wlshow',

				// wlowner        - The name of the user whose watchlist you'd like to access
				'wlowner',

				// wltoken        - Give a security token (settable in preferences) to allow access to another user's watchlist
				'wltoken',
				),

			/**
			 * list=watchlistraw (wr)
			 * Get all pages on the logged in user's watchlist
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=watchlistraw
			 *   api.php?action=query&generator=watchlistraw&gwrshow=changed&prop=revisions
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
			'watchlistraw' => array(

				// wrcontinue     - When more results are available, use this to continue
				'wrcontinue',

				// wrnamespace    - Only list pages in the given namespace(s).
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'wrnamespace',

				// wrlimit        - How many total results to return per request.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'wrlimit',

				// wrprop         - Which additional properties to get (non-generator mode only).
				//                  Values (separate with '|'): changed
				'wrprop',

				// wrshow         - Only list items that meet these criteria.
				//                  Values (separate with '|'): changed, !changed
				'wrshow',
				),

			/**
			 * list=exturlusage (eu)
			 * Enumerate pages that contain a given URL
			 *
			 * This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=exturlusage&euquery=www.mediawiki.org
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
			'exturlusage' => array(

				// euprop         - What pieces of information to include
				//                  Values (separate with '|'): ids, title, url
				//                  Default: ids|title|url
				'euprop',

				// euoffset       - Used for paging. Use the value returned for "continue"
				'euoffset',

				// euprotocol     - Protocol of the url. If empty and euquery set, the protocol is http.
				//                  Leave both this and euquery empty to list all external links
				//                  Can be empty, or One value: http, https, ftp, irc, gopher, telnet, nntp, worldwind, mailto, news, svn
				//                  Default:
				'euprotocol',

				// euquery        - Search string without protocol. See [[Special:LinkSearch]]. Leave empty to list all external links
				'euquery',

				// eunamespace    - The page namespace(s) to enumerate.
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'eunamespace',

				// eulimit        - How many pages to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'eulimit',
				),

			/**
			 * list=users (us)
			 * Get information about a list of users
			 *
			 * This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=users&ususers=brion|TimStarling&usprop=groups|editcount|gender
			 */

			'users' => array(

				// usprop         - What pieces of information to include
				//                    blockinfo    - tags if the user is blocked, by whom, and for what reason
				//                    groups       - lists all the groups the user belongs to
				//                    editcount    - adds the user's edit count
				//                    registration - adds the user's registration timestamp
				//                    emailable    - tags if the user can and wants to receive e-mail through [[Special:Emailuser]]
				//                    gender       - tags the gender of the user. Returns "male", "female", or "unknown"
				//                  Values (separate with '|'): blockinfo, groups, editcount, registration, emailable, gender
				'usprop',

				// ususers        - A list of users to obtain the same information for
				'ususers',

				// ustoken        - Which tokens to obtain for each user
				//                  Values (separate with '|'): userrights
				'ustoken',
				),

			/**
			 * list=random (rn)
			 * Get a set of random pages
			 * NOTE: Pages are listed in a fixed sequence, only the starting point is random. This means that if, for example, "Main Page" is the first
			 *       random page on your list, "List of fictional monkeys" will *always* be second, "List of people on stamps of Vanuatu" third, etc.
			 * NOTE: If the number of pages in the namespace is lower than rnlimit, you will get fewer pages. You will not get the same page twice.
			 *
			 * This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=random&rnnamespace=0&rnlimit=2
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
			'random' => array(

				// rnnamespace    - Return pages in these namespaces only
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'rnnamespace',

				// rnlimit        - Limit how many random pages will be returned
				//                  No more than 10 (20 for bots) allowed.
				//                  Default: 1
				'rnlimit',

				// rnredirect     - Load a random redirect instead of a random page
				'rnredirect',
				),

			/**
			 * list=protectedtitles (pt)
			 * List all titles protected from creation
			 *
			 * This module requires read rights.
			 *
			 * Example:
			 *   api.php?action=query&list=protectedtitles
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
			'protectedtitles' => array(

				// ptnamespace    - Only list titles in these namespaces
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				'ptnamespace',

				// ptlevel        - Only list titles with these protection levels
				//                  Values (separate with '|'): autoconfirmed, sysop
				'ptlevel',

				// ptlimit        - How many total pages to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'ptlimit',

				// ptdir          - The direction in which to list
				//                  One value: older, newer
				//                  Default: older
				'ptdir',

				// ptstart        - Start listing at this protection timestamp
				'ptstart',

				// ptend          - Stop listing at this protection timestamp
				'ptend',

				// ptprop         - Which properties to get
				//                  Values (separate with '|'): timestamp, user, comment, parsedcomment, expiry, level
				//                  Default: timestamp|level
				'ptprop',
				),

			/**
			 * list=oldreviewedpages (or)
			 * Returns a list of pages, that have an outdated review flag,
			 * sorted by timestamp of the first unreviewed edit of that page.
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   Show a list of pages with pending unreviewed changes
			 *     api.php?action=query&list=oldreviewedpages&ornamespace=0
			 *   Show info about some old reviewed pages
			 *     api.php?action=query&generator=oldreviewedpages&gorlimit=4&prop=info
			 *
			 * Generator:
			 *   This module may be used as a generator
			 */
			'oldreviewedpages' => array(

				// orstart        - Start listing at this timestamp.
				'orstart',

				// orend          - Stop listing at this timestamp.
				'orend',

				// ordir          - In which direction to list.
				//                  *newer: list the longest waiting pages first
				//                  *older: list the newest items first
				//                  One value: newer, older
				//                  Default: newer
				'ordir',

				// ormaxsize      - Maximum character count change size.
				//                  The value must be no less than 0
				'ormaxsize',

				// orfilterwatched - How to filter for pages on your watchlist.
				//                   One value: watched, all
				//                   Default: all
				'orfilterwatched',

				// ornamespace    - The namespaces to enumerate.
				//                  Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101, 108, 109
				//                  Default: 0
				'ornamespace',

				// orcategory     - Show pages only in the given category.
				'orcategory',

				// orfilterredir  - How to filter for redirects.
				//                  One value: redirects, nonredirects, all
				//                  Default: all
				'orfilterredir',

				// orlimit        - How many total pages to return.
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'orlimit',
				),

			/**
			 * list=globalblocks (bg)
			 * List all globally blocked IP addresses.
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=globalblocks
			 *   api.php?action=query&list=globalblocks&bgip=217.121.114.116
			 */
			'globalblocks' => array(

				// bgstart        - The timestamp to start enumerating from
				'bgstart',

				// bgend          - The timestamp to stop enumerating at
				'bgend',

				// bgdir          - The direction in which to enumerate
				//                  One value: newer, older
				//                  Default: older
				'bgdir',

				// bgids          - Pipe-separated list of block IDs to list (optional)
				'bgids',

				// bgaddresses    - Pipe-separated list of addresses to search for (optional)
				'bgaddresses',

				// bgip           - Get all blocks applying to this IP or CIDR range, including range blocks.
				//                  Cannot be used together with bkusers. CIDR ranges broader than /16 are not accepted.
				'bgip',

				// bglimit        - The maximum amount of blocks to list
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'bglimit',

				// bgprop         - Which properties to get
				//                  Values (separate with '|'): id, address, by, timestamp, expiry, reason, range
				//                  Default: id|address|by|timestamp|expiry|reason
				'bgprop',
				),

			/**
			 * list=abuselog (afl)
			 * Show events that were caught by one of the abuse filters.
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=abuselog
			 *   api.php?action=query&list=abuselog&afltitle=API
			 */
			'abuselog' => array(

				// aflstart       - The timestamp to start enumerating from
				'aflstart',

				// aflend         - The timestamp to stop enumerating at
				'aflend',

				// afldir         - The direction in which to enumerate
				//                  One value: newer, older
				//                  Default: older
				'afldir',

				// afluser        - Show only entries done by a given user or IP address.
				'afluser',

				// afltitle       - Show only entries occurring on a given page.
				'afltitle',

				// aflfilter      - Show only entries that were caught by a given filter ID
				'aflfilter',

				// afllimit       - The maximum amount of entries to list
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'afllimit',

				// aflprop        - Which properties to get
				//                  Values (separate with '|'): ids, filter, user, ip, title, action, details, result, timestamp, hidden
				//                  Default: ids|user|title|action|result|timestamp|hidden
				'aflprop',
				),

			/**
			 * list=abusefilters (abf)
			 * Show details of the abuse filters.
			 *
			 * This module requires read rights.
			 *
			 * Examples:
			 *   api.php?action=query&list=abusefilters&abfshow=enabled|!private
			 *   api.php?action=query&list=abusefilters&abfprop=id|description|pattern
			 */
			'abusefilters' => array(

				// abfstartid     - The filter id to start enumerating from
				'abfstartid',

				// abfendid       - The filter id to stop enumerating at
				'abfendid',

				// abfdir         - The direction in which to enumerate
				//                  One value: older, newer
				//                  Default: newer
				'abfdir',

				// abfshow        - Show only filters which meet these criteria
				//                  Values (separate with '|'): enabled, !enabled, deleted, !deleted, private, !private
				'abfshow',

				// abflimit       - The maximum number of filters to list
				//                  No more than 500 (5000 for bots) allowed.
				//                  Default: 10
				'abflimit',

				// abfprop        - Which properties to get
				//                  Values (separate with '|'): id, description, pattern, actions, hits, comments, lasteditor, lastedittime, status, private
				//                  Default: id|description|actions|status
				'abfprop',
				),
			);

			if (isset( $valid[$type] )) {
			$this->args['list'] = $type;
			$this->query_element = $type;
			$lists = (array) $lists;
			foreach ($lists as $key => $value) {
				if (in_array( $key, $valid[$type] )) {
					$this->args[$key] = $value;
				}
			}
		}

		return $this;
	}

}