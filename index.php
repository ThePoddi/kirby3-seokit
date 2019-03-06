<?php

Kirby::plugin('thepoddi/seokit', [
    'options' => [
        # robots
        'robots.disallow.pages'       => array('error'),
        'robots.disallow.templates'   => array('error'),
        'robots.disallow.unlisted'    => true,
        'robots.sitemap'              => 'sitemap.xml',
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
        ];
    }
]);
