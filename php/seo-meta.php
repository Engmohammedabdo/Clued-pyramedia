<?php
/**
 * PYRAMEDIA - SEO Meta Tags Generator
 * Advanced SEO optimization for all pages
 */

declare(strict_types=1);

class SEOMeta {
    private string $lang;
    private string $siteName = 'PYRAMEDIA';
    private string $siteUrl = 'https://ccode.pyramedia.info';
    private string $defaultImage = 'https://ccode.pyramedia.info/images/og-default.jpg';
    private array $socialLinks = [
        'facebook' => 'https://facebook.com/pyramedia',
        'twitter' => 'https://twitter.com/pyramedia',
        'instagram' => 'https://instagram.com/pyramedia',
        'linkedin' => 'https://linkedin.com/company/pyramedia'
    ];

    public function __construct(string $lang = 'en') {
        $this->lang = $lang;
    }

    /**
     * Generate complete meta tags for a page
     */
    public function generate(array $config): string {
        $defaults = [
            'title' => $this->lang === 'ar' ? 'بيراميديا - وكالة تسويق رقمي' : 'PYRAMEDIA - Digital Marketing Agency',
            'description' => $this->lang === 'ar'
                ? 'وكالة رائدة في التسويق الرقمي والإعلام في منطقة الخليج. نمكّن الشباب من خلال قوة الإعلام والابتكار.'
                : 'Leading marketing & media agency in the GCC region. We empower youth through the power of media and innovation.',
            'keywords' => 'digital marketing, marketing agency, social media, GCC, UAE, Dubai',
            'image' => $this->defaultImage,
            'url' => $this->siteUrl,
            'type' => 'website',
            'canonical' => null,
            'author' => 'PYRAMEDIA Team',
            'robots' => 'index, follow',
            'schema' => null
        ];

        $meta = array_merge($defaults, $config);

        $html = $this->basicMeta($meta);
        $html .= $this->openGraphMeta($meta);
        $html .= $this->twitterMeta($meta);
        $html .= $this->additionalMeta($meta);

        if ($meta['schema']) {
            $html .= $this->schemaMarkup($meta['schema']);
        }

        return $html;
    }

    /**
     * Basic meta tags
     */
    private function basicMeta(array $meta): string {
        $title = htmlspecialchars($meta['title'] . ' | ' . $this->siteName, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8');
        $keywords = htmlspecialchars($meta['keywords'], ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
    <!-- Basic Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{$title}</title>
    <meta name="description" content="{$description}">
    <meta name="keywords" content="{$keywords}">
    <meta name="author" content="{$meta['author']}">
    <meta name="robots" content="{$meta['robots']}">
    <meta name="language" content="{$this->lang}">
    <meta name="revisit-after" content="7 days">
    <meta name="rating" content="general">

HTML;

        if ($meta['canonical']) {
            $html .= "    <link rel=\"canonical\" href=\"{$meta['canonical']}\">\n";
        }

        return $html;
    }

    /**
     * Open Graph meta tags (Facebook)
     */
    private function openGraphMeta(array $meta): string {
        $title = htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8');

        return <<<HTML
    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="{$this->siteName}">
    <meta property="og:title" content="{$title}">
    <meta property="og:description" content="{$description}">
    <meta property="og:type" content="{$meta['type']}">
    <meta property="og:url" content="{$meta['url']}">
    <meta property="og:image" content="{$meta['image']}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{$this->getLocale()}">

HTML;
    }

    /**
     * Twitter Card meta tags
     */
    private function twitterMeta(array $meta): string {
        $title = htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8');

        return <<<HTML
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@pyramedia">
    <meta name="twitter:creator" content="@pyramedia">
    <meta name="twitter:title" content="{$title}">
    <meta name="twitter:description" content="{$description}">
    <meta name="twitter:image" content="{$meta['image']}">

HTML;
    }

    /**
     * Additional SEO meta tags
     */
    private function additionalMeta(array $meta): string {
        return <<<HTML
    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="#FF6B35">
    <meta name="msapplication-TileColor" content="#FF6B35">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">

HTML;
    }

    /**
     * Schema.org markup (JSON-LD)
     */
    private function schemaMarkup(array $schema): string {
        $json = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return <<<HTML
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
{$json}
    </script>

HTML;
    }

    /**
     * Get locale code
     */
    private function getLocale(): string {
        return $this->lang === 'ar' ? 'ar_AE' : 'en_US';
    }

    /**
     * Generate Organization schema
     */
    public static function getOrganizationSchema(): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'PYRAMEDIA',
            'url' => 'https://ccode.pyramedia.info',
            'logo' => 'https://ccode.pyramedia.info/images/logo.png',
            'description' => 'Leading marketing & media agency in the GCC region',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'AE',
                'addressRegion' => 'Dubai',
                'addressLocality' => 'Dubai'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+971-XX-XXX-XXXX',
                'contactType' => 'customer service',
                'email' => 'info@pyramedia.ae',
                'availableLanguage' => ['English', 'Arabic']
            ],
            'sameAs' => [
                'https://facebook.com/pyramedia',
                'https://twitter.com/pyramedia',
                'https://instagram.com/pyramedia',
                'https://linkedin.com/company/pyramedia'
            ]
        ];
    }

    /**
     * Generate WebSite schema
     */
    public static function getWebSiteSchema(): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'PYRAMEDIA',
            'url' => 'https://ccode.pyramedia.info',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => 'https://ccode.pyramedia.info/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string'
            ]
        ];
    }

    /**
     * Generate Article schema (for blog posts)
     */
    public static function getArticleSchema(array $article): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article['title'],
            'description' => $article['excerpt'] ?? '',
            'image' => $article['image'] ?? '',
            'author' => [
                '@type' => 'Person',
                'name' => $article['author'] ?? 'PYRAMEDIA Team'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'PYRAMEDIA',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://ccode.pyramedia.info/images/logo.png'
                ]
            ],
            'datePublished' => $article['published_at'] ?? date('c'),
            'dateModified' => $article['updated_at'] ?? date('c'),
            'mainEntityOfPage' => $article['url'] ?? ''
        ];
    }

    /**
     * Generate Review/Testimonial schema
     */
    public static function getReviewSchema(array $review): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'itemReviewed' => [
                '@type' => 'Organization',
                'name' => 'PYRAMEDIA'
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $review['client_name']
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $review['rating'],
                'bestRating' => 5,
                'worstRating' => 1
            ],
            'reviewBody' => $review['testimonial_text'],
            'datePublished' => $review['created_at'] ?? date('c')
        ];
    }

    /**
     * Generate Breadcrumb schema
     */
    public static function getBreadcrumbSchema(array $items): array {
        $itemList = [];
        foreach ($items as $index => $item) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url']
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList
        ];
    }

    /**
     * Generate FAQ schema
     */
    public static function getFAQSchema(array $faqs): array {
        $mainEntity = [];
        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity
        ];
    }
}

/**
 * Helper function to render SEO meta tags
 */
function renderSEO(array $config, string $lang = 'en'): string {
    $seo = new SEOMeta($lang);
    return $seo->generate($config);
}
