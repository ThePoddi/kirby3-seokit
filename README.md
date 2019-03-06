# Kirby 3 SEO Kit
SEO Plugin for Kirby 3.

In the first step, this plugin combines the Kirby 2 Plugins [Kirby Plugin: Robots](https://github.com/ThePoddi/kirby-robots) and [Kirby Plugin: Sitemap](https://github.com/ThePoddi/kirby-sitemap) in one SEO Kit for Kirby 3. More features like Meta Tags, Structured Data and options via panel are on my todo list.

----

## Installation

### Download
Download and copy this repository to `/site/plugins/seokit`.

### Git submodule
Include this repository as a submodule
```
git submodule add https://github.com/thepoddi/kirby3-seokit.git site/plugins/seokit
```

### Composer
```
composer require thepoddi/kirby3-seokit
```

----

## Usage
This plugin sets a robots file to `/robots.txt` and a sitemap file to `/sitemap.xml` as a kirby route. There is no actual file generated.

----

## Config

There are several config options you can edit via Kirby’s config file `/site/config/config.php`.


### robots.txt

#### Ignore Pages
Ignore specific pages by URI - example: 'blog/my-article'. (array) *Default: error*
```
'thepoddi.seokit.robots.disallow.pages' => array( 'error' ),
```

Ignore pages by intended templates. (array) *Default: error*
```
'thepoddi.seokit.robots.disallow.templates' => array( 'error' ),
```

Ignore unlisted pages. (boolean) *Default: true*
```
'thepoddi.seokit.robots.disallow.unlisted' => true,
```

#### Set Sitemap File
Set sitemap file in robots.txt. (string) *Default: sitemap.xml*
```
'thepoddi.seokit.robots.sitemap' => 'sitemap.xml',
```


### sitemap.xml

#### Ignore Pages
Ignore pages by uid. (array) *Default: error*
```
'thepoddi.seokit.sitemap.ignore.pages' => array( 'error' ),
```

Ignore pages by intended templates. (array) *Default: error*
```
'thepoddi.seokit.sitemap.ignore.templates' => array( 'error' ),
```

Ignore unlisted pages. (boolean) *Default: true*
```
'thepoddi.seokit.sitemap.ignore.unlisted' => true,
```

#### Prioritize Pages
Set high priority pages by uid. (array) *Default: home*
```
'thepoddi.seokit.sitemap.important.pages' => array( 'home' ),
```

Set high priority pages by intended template. (array) *Default: home*
```
'thepoddi.seokit.sitemap.important.templates' => array( 'home' ),
```

#### Include Image Sitemap
Include image tags in sitemap [Google Image Sitemaps](https://support.google.com/webmasters/answer/178636). (boolean) *Default: true*
```
'thepoddi.seokit.sitemap.include.images' => true,
```

## Authors

**Patrick Schumacher** - [GitHub](https://github.com/thepoddi) · [Website](https://www.thepoddi.com)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE.md) file for details.
