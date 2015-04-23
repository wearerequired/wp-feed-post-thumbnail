module.exports = {
	dist: {
		options: {
			cwd            : '',
			domainPath     : '/languages',
			exclude        : ['release/.*'],
			include        : [],
			mainFile       : 'wp-feed-post-thumbnail.php',
			potComments    : '',
			potFilename    : 'wp-feed-post-thumbnail.pot',
			potHeaders     : {
				poedit                 : true,
				'x-poedit-keywordslist': true,
				'report-msgid-bugs-to' : 'http://required.ch',
				'last-translator'      : 'required+',
				'language-team'        : 'required+ <support@required.ch>',
				'x-poedit-country'     : 'Switzerland'
			},
			processPot     : null,
			type           : 'wp-plugin',
			updateTimestamp: false
		}
	}
}
