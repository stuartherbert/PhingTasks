# PhingTasks

[PhingTasks](http://code.stuartherbert.com/php/phingtasks) is a collection of useful tasks for use with [Phing](http://phing.info), the PHP development automation tool.

Some of these tasks were originally bundled as part of the Phix project; they've been broken out into a separate project so that they can be more widely reused.

## Installation

You should install PhingTasks via Composer:

```json
{
	"require": {
		"stuart/phingtasks": "1.*"
	}
}
```

## Usage

Please see [the online documentation](http://code.stuartherbert.com/php/phingtasks) for each task:

* [Dedupe](http://code.stuartherbert.com/php/phingtasks/dedupe.html) - Remove duplicate files from a folder tree
* [Now](http://code.stuartherbert.com/php/phingtasks/now.html) - Set a project property to the current date and time
* [PhingCallIfExists](http://code.stuartherbert.com/php/phingtasks/phingcallifexists.html) - Call a target only if it has been defined

## License

[BSD](LICENSE.md)