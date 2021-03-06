# Semantic MediaWiki 2.3

This is not a release yet.


## New features

### SPARQLStore

Several improvements (including functional as well as performance related) have been made available to bring the `SPARQLStore` on par with the `SQLStore` supported feature set.

Added the following options (It is assumed that the selected TDB back-end supports SPARQL 1.1 otherwise the `$GLOBALS['smwgSparqlQFeatures']` has to be set to `SMW_SPARQL_QF_NONE`) to:
* #1001 `SMW_SPARQL_QF_REDI` to enable property/value redirects support in queries (),
* #1003 `SMW_SPARQL_QF_SUBP` to enable subproperty hierarchy support for the `SPARQLStore`
* #1012 `SMW_SPARQL_QF_SUBC` to enable subcategory hierarchy support for the `SPARQLStore`
* #1110 Extended `TurtleTriplesBuilder` to split larger turtle sets into chunks
* #1152 Added preference for use of canonical identifiers to support language agnostic category/property statements, (use `$GLOBALS['smwgExportBCNonCanonicalFormUse'] = true` to keep backwards compatibility until 3.x)
* #1158 Added basic support for `_geo` queries to the `SPARQLStore`
* #1159 Added limitation of the `aux` property usage in the Exporter (use `$GLOBALS['smwgExportBCAuxiliaryUse'] = true;` to keep backwards compatibility until 3.x)

### Property/Subject deletion

* #1100 introduce a deletion marker on entities that got deleted, making those entities no longer available to queries or special page display.
* #1127 Added `--shallow-update` to `rebuildData`, to only parse those entities that have a different last modified timestamp compared to that of the last revision. This enables to run `rebuildData` updates on deleted, redirects, and other out of sync entities.
* Solved #701 where an unconditional namespace query `[[Help:+]]` would display deleted subjects (in case those subjects were deleted)
* #1105 Added filter to mark deleted redirect targets with `SMW_SQL3_SMWDELETEIW`
* #1112 Added filter to mark unused/oudated subobjects with `SMW_SQL3_SMWDELETEIW`
* #1151 Added removal of unmatched "ghost" pages in the ID_TABLE

## Enhancements

* #1042 Extended `rebuildData.php` to inform about the estimated % progress
* #1047 Extended context help displayed on `Special:Types` and subsequent type pages
* #1049 Added resource targets to allow MobileFrontend to load SMW related modules
* #1053 Added a `CSS` rule to visually distinguish a "normal" from a subobject link
* #1063 Added `$GLOBALS['smwgValueLookupCacheType']` to improve DB lookup performance though the use of a responsive cache layer (such as `redis`) and buffer repeated requests either from the API or page view to the back-end.
* #1066, #1075 Extended `InTextAnnotationParser` to correctly handle `::` or `:::` and allow for proper processing of annotations such as `[[DOI::10.1002/123::abc]]` or `[[Foo:::123]]`
* #1097 Predefined property aliases are redirected to the base property
* #1107 #set to indicate the `last-element` in a template
* #1106 rebuildData / Add `--skip-properties` / remove marked for deletion first
* #1129 Extended `~*` search pattern for `_ema` and `_tel` to allow for searches like `[[Has telephone number::~*0123*]]` and `[[Has email::~*123.org]]`
* #1147 Added `columns=0` to be interpret as responsive column setting in the `format=category` printer (auto-column feature)

## Experimental features

* #1035, #1063 Added `CachedValueLookupStore` as post-cached layer to improve DB read access (`$GLOBALS['smwgValueLookupCacheType']`, $GLOBALS['smwgValueLookupCacheLifetime'])
* #1116 Added $GLOBALS['smwgValueLookupFeatures'] setting to fain grain the cache access level, default is set to `SMW_VL_SD | SMW_VL_PL | SMW_VL_PV | SMW_VL_PS;`
* #1117 Added `EmbeddedQueryDependencyLinksStore` to track query dependencies and update altered queries using `ParserCachePurgeJob` for when `$GLOBALS['smwgEnabledQueryDependencyLinksStore']` is enabled
* #1135 Added `$GLOBALS['smwgPropertyDependencyDetectionBlacklist']` to exclude properties from dependency detection
* #1141 Added detection of property and category hierarchy dependency in `EmbeddedQueryDependencyLinksStore`

## Bug fixes

* #682 Fixed id mismatch in `SQLStore`
* #1005 Fixed syntax error in `SQLStore`(`sqlite`) for temporary tables when a disjunctive category/subcategory query is executed
* #1033 Fixed PHP notice in `JobBase` that was based on an assumption that parameters are always an array
* #1038 Fixed Fatal error: Call to undefined method `SMWDIError::getString`
* #1046 Fixed RuntimeException in `UndeclaredPropertyListLookup` for when a DB prefix is used
* #1051 Fixed call to undefined method in `ConceptDescriptionInterpreter`
* #1054 Fixed behaviour for `#REDIRECT` to create the same data reference as `Special:MovePage`
* #1059 Fixed usage of `[[Has page::~*a*||~*A*]]` for `SPARQLStore` when `Has page` is declared as page type
* #1060 Fixed usage of `(a OR b) AND (c OR d)` as query pattern for the `SQLStore`
* #1067 Fixed return value of the `#set` parser
* #1074 Fixed duplicated error message for a `_dat` DataValue
* #1081 Fixed mismatch of `owl:Class` for categories when used in connection with a vocabulary import
* #1126 Fixed silent annotations added by the `Factbox` when content contains `[[ ... ]]`
* #1120 Fixed `\\` encoding in `Resources.php`
* #233 Fixed disabling of `$GLOBALS['wgFooterIcons']['poweredby']['semanticmediawiki']`
* #1137 Fixed re-setting of `smw-admin` user group permission to its default
* #1146 Fixed #set rendering of template supported output (refs #1067)
* #1096 Fixed inverse prefix for predefined properties that caused misinterpret `Concept` queries
* #1166 Fixed context awareness of `ParserAfterTidy` in connection with the `purge` action

## Internal changes

* #1018 Added `PropertyTableRowDiffer` to isolate code responsible for computing data diffs (relates to #682)
* #1039 Added `SemanticData::getLastModified`
* #1041 Added `ByIdDataRebuildDispatcher` to isolate `SMWSQLStore3SetupHandlers::refreshData`
* #1071 Added `SMW::SQLStore::AddCustomFixedPropertyTables` hook to simplify registration of fixed property tables by extension developers
* #1068 Added setting to support recursive annotation for selected formats (refs #1055, #711)
* #1086 Changed redirect update logic to accommodate the manual #REDIRECT (refs #895, #1054)
* Added `SMW::Browse::AfterInPropertiesLookupComplete` which allows to extend the incoming properties display for `Special:Browse`
* #1078 Renamed `ParserParameterFormatter` to `ParserParameterProcessor` and `ParameterFormatterFactory` to `ParameterProcessorFactory`
* #1102 Added `onoi/http-request:~1.0` dependency
* Decrease chunk size in `UpdateDispatcherJob` (refs #951)
* #1111 Added support for the atomic DB transaction mode to improve the rollback process in case of a DB transaction failure
* #1108 Added `CompositePropertyTableDiffIterator` which for the added `'SMW::SQLStore::AfterDataUpdateComplete'` returns ids that have been updated only (as diff of the update)
* #1119 Added `RequestOptionsProcessor`
* #1130 Added `AsyncJobDispatchManager` to decouple jobs during update
* #1133 Fixed MW 1.25/1.26 API tests
* #1145 Added `onoi/callback-container:~1.0` and removes all custom DIC code from SMW-core
* (964155) Added removal of whitespace for `DIBlob` values (" Foo " becomes "Foo")
* #1149 Added `InMemoryPoolCache` to improve performance for the `SPARQLStore` during turtle serialization
