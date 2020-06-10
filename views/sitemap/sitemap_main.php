<?php
$date = date("Y-m-d");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>http://<?=$_SERVER['HTTP_HOST']?>/uploads/sitemap1.xml</loc>
        <lastmod><?=$date?></lastmod>
    </sitemap>
    <sitemap>
        <loc>http://<?=$_SERVER['HTTP_HOST']?>/uploads/sitemap2.xml</loc>
        <lastmod><?=$date?></lastmod>
    </sitemap>
    <sitemap>
        <loc>http://<?=$_SERVER['HTTP_HOST']?>/uploads/sitemap3.xml</loc>
        <lastmod><?=$date?></lastmod>
    </sitemap>
</sitemapindex>