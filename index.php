<?php
/** KIRBY 3 PLUGIN: SEO Kit
 * -------------------------------------------------------------------
 * Plugin Name: Kirby 3 SEO Kit
 * Description: SEO Kit for Kirby 3 including sitemap.xml and robots.txt
 * @version    1.0.0
 * @author     Patrick Schumacher <hello@thepoddi.com>
 * @link       https://github.com/ThePoddi/kirby3-seokit
 * @license    MIT
 */

Kirby::plugin('thepoddi/seokit', [
    'options' => [
        # robots
        'robots.disallow.pages'       => array('error'),
        'robots.disallow.templates'   => array('error'),
        'robots.disallow.unlisted'    => true,
        'robots.sitemap'              => 'sitemap.xml',

        # sitemap
        'sitemap.ignore.pages'        => array('error'),
        'sitemap.ignore.templates'    => array('error'),
        'sitemap.ignore.unlisted'     => true,
        'sitemap.important.pages'     => array('home'),
        'sitemap.important.templates' => array('templates'),
        'sitemap.include.images'      => true,
    ],
    'routes' => function ($kirby) {
        return [

            # robots.txt
            [
                'pattern' => 'robots.txt',
                'method' => 'GET',
                'action' => function () use ( $kirby ) {

                    $ignorePages      = $kirby->option('thepoddi.seokit.robots.disallow.pages');
                    $ignoreTemplates  = $kirby->option('thepoddi.seokit.robots.disallow.templates');
                    $ignoreInvisible  = $kirby->option('thepoddi.seokit.robots.disallow.unlisted');
                    $sitemap          = $kirby->option('thepoddi.seokit.robots.sitemap');

                    $robots = '# robots.txt for ' . $kirby->url('index') . "\n";
                    $robots .= 'User-agent: *' . "\n";

                    // disallow crawling for some pages
                    foreach( $kirby->site()->index() as $p ) {
                        if (
                            in_array( $p->uid(), $ignorePages ) || // ignore pages defined in config
                            in_array( $p->intendedTemplate(), $ignoreTemplates ) || // ignore templates defined in config
                            ( $ignoreInvisible === true && $p->isUnlisted() && $p->isHomepage() === false ) // ignore unlisted pages (except home)
                        ) {
                            $robots .= 'Disallow: /' . $p->uid() . "\n";
                        };
                    };
                    // sitemap location
                    $robots .= 'Sitemap: ' . $kirby->url('index') . '/' . $sitemap . "\n";

                    return new Response( $robots, 'text' );
                }
            ],

            # sitemap.xml
            [
                'pattern' => 'sitemap.xml',
                'method' => 'GET',
                'action' => function () use ( $kirby ) {

                    $ignorePages            = $kirby->option('thepoddi.seokit.sitemap.ignore.pages');
                    $ignoreTemplates        = $kirby->option('thepoddi.seokit.sitemap.ignore.templates');
                    $ignoreInvisible        = $kirby->option('thepoddi.seokit.sitemap.ignore.unlisted');
                    $importantPages         = $kirby->option('thepoddi.seokit.sitemap.important.pages');
                    $importantTemplates     = $kirby->option('thepoddi.seokit.sitemap.important.templates');
                    $includeImages          = $kirby->option('thepoddi.seokit.sitemap.include.images');

                    // xml doctype
                    $sitemap  = '<?xml version="1.0" encoding="utf-8"?>';
                    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" ' . ( r( $includeImages === true, 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"' ) ) . '>';

                    // loop all pages
                    foreach( $kirby->site()->index() as $p ) :

                        // ignore pages defined in config
                        if( in_array( $p->uri(), $ignorePages ) ) continue;

                        // ignore templates defined in config
                        if( in_array( $p->intendedTemplate(), $ignoreTemplates ) ) continue;

                        // ignore unlisted pages
                        if( $ignoreInvisible === true && $p->isUnlisted() && $p->isHomepage() === false ) continue;

                        $sitemap .= '<url>';
                        $sitemap .= '<loc>' . $p->url() . '</loc>';

                        // set multilanguage canonicals
                        if ( $kirby->languages()->count() > 0 ) :
                            foreach( $kirby->languages() as $language ):
                                $sitemap .= '<xhtml:link rel="alternate" hreflang="' . $language->code() . '" href="' . $p->url($language->code()) . '" />';
                            endforeach;
                        endif;

                        // set image tags
                        if ( $p->hasImages() && $includeImages === true ) :
                            foreach( $p->images()->limit(1000) as $image ):
                                $sitemap .= '<image:image>';
                                    $sitemap .= '<image:loc>' . $image->url() . '</image:loc>';
                                    $sitemap .= r( $image->image_caption()->isNotEmpty(), '<image:caption>' . $image->image_caption()->xml() . '</image:caption>' );
                                    $sitemap .= r( $image->image_title()->isNotEmpty(), '<image:title>' . $image->image_title()->xml() . '</image:title>' );
                                    $sitemap .= r( $image->image_geo_location()->isNotEmpty(), '<image:geo_location>' . $image->image_geo_location()->xml() . '</image:geo_location>' );
                                    $sitemap .= r( $image->image_licence()->isNotEmpty(), '<image:licence>' . $image->image_licence()->xml() . '</image:licence>' );
                                $sitemap .= '</image:image>';
                            endforeach;
                        endif;

                        $sitemap .= '<lastmod>' . date( 'c', $p->modified() ) . '</lastmod>';
                        $sitemap .= '<priority>' . ( ( $p->isHomePage() || in_array( $p->uri(), $importantPages ) || in_array( $p->intendedTemplate(), $importantTemplates ) ) ? 1 : number_format( 0.5/$p->depth(), 1 ) ) . '</priority>';
                        $sitemap .= '</url>';

                    endforeach;

                    $sitemap .= '</urlset>';
                    return new Response( $sitemap, 'xml' );
                }
            ],
        ];
    }
]);
