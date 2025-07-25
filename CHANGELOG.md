Version 1.0.0-beta10+dev
- added support for early hints for style and scripts (not enabled by default, currently only works on FrankenPHP)
- added mounts and buildings' maintenance into budget
- raised minimal version of PHP to 8.1

Version 1.0.0-beta10
- added profile sub-page comments and crimes
- allowed sharing articles on X (Twitter) and fediverse
- added new default style (autodetects dark mode)
- actions from current user are now marked in chronicle
- added pagination to homepage and articles' categories
- added notifications
- merged search types articles title and articles text
- added authentication for api
- added countdown to next shift for work
- added iCalendar for events

Version 1.0.0-beta9
- removed user setting infomails
- made charter for founding town configurable
- changed rss channel's link for comments feeds
- raised minimal version of PHP to 7.4
- moved publicly available files to subfolder www

Version 1.0.0-beta8
- raised minimal version of PHP to 7.3
- api sends list of allowed methods with 405 status code
- added support for OPTIONS and HEAD methods in api
- comments from current user/article's author/admin are marked now
- fixed skill's bonus to work success rate and reward
- single entities and collections in api now have links in json and send Link headers
- added Open Graph metadata for frontend pages
- allowed sharing articles on Facebook
- phinx is used for database migrations
- added support for If-Modified-Since in frontend pages (not enabled by default) and api
- added comments moderation

Version 1.0.0-beta7
- discount for learning skills in monastery now determined by library's level
- added support for OpenSearch
- changed url of profile sub-pages and rss channels for comments
- e-mail now serves as username
- user is now returned to previous page after logging in
- administrators can now manage royal towns
- fixed getting current events
- added profile sub-page achievements
- leaders of monasteries can change title of other members now
- high clerics can no longer own towns
- added REST api for reading publicly available data

Version 1.0.0-beta6
- guild/order actions now use acl
- separated authenticator from user manager
- added option to auto feed mounts
- do not show price for skills on max level in academy
- closed adventures are now marked differently than completed ones
- user's profile now contains number of finished jobs, removed number of sent/received messages
- added deposit accounts
- guild and order leader can see how much each member paid on fees
- raised minimal version of PHP to 7.2
- added chat
- added site search
- use package heroesofabenez/combat for handling combat
- monasteries provide discount for learning skills (since level 2)
- added achievements

Version 1.0.0-beta5
- some refactoring
- use package nexendrie/menu to generate menus
- rewritten Locale::plural() to use translator, changed its parameters
- redirect to the added article
- fixed a return type hint
- fixed name of caught exception in RssPresenter::renderComments() and OrderPresenter::actionJoin()
- added missing setRequired() for field money in GiftForm
- throw an exception in UserPresenter::actionEdit() if the user does not exist
- added profile sub-pages articles and skills

Version 1.0.0-beta4
- replaced BETA in pages' title by configurable version suffix
- separated rss channel generating into package nexendrie/rss
- fixed some invalid types of arguments
- some refactoring
- show success chance for work
- raised minimal version of PHP to 7.1
- renamed forms' processing method to process

Version 1.0.0-beta3
- forbid prosing marriage to the queen
- it is now possible to restrict registration by password
- fixed some invalid types of arguments
- removed dependency on nette/robot-loader
- moved app/log and app/temp to root
- reorganized presenters and their templates

Version 1.0.0-beta2
- raised minimal version of PHP to 7.0
- fixed invalid property name in MonasteriesRepository::findLedMonasteries()
- fixed TownPresenter::renderDetail() if the user is not logged in

Version 1.0.0-beta1
- first released version
