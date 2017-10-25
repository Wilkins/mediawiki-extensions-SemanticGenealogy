`ExtraneousLanguage` provides "extraneous" language functions independent
of MediaWiki required by Semantic MediaWiki and its registration system.

## JSON format

The location of the language files is determined by the [`$smwgExtraneousLanguageFileDir`](https://www.semantic-mediawiki.org/wiki/Help:$smwgExtraneousLanguageFileDir) setting.

### Field definitions

* `@...` fields leading with `@` are identified as comments fields
* `fallbackLanguage`defines a fallback language tag
* `namespaces`
* `namespaceAliases`

### Example

<pre>
{
	"fallbackLanguage": false,
	"namespaces":{
		"SMW_NS_PROPERTY": "Property"
	},
	"namespaceAliases": {
		"Property": "SMW_NS_PROPERTY"
	},
}
</pre>


## Technical notes

* `ExtraneousLanguage` interface for the language functions
  * `LanguageContents` to provide the raw content from a corresponding language file
    * `JsonLanguageContentsFileReader` providing access to the contents of a `JSON` file
    * `LanguageFallbackFinder` is responsible for resolving a fallback language
