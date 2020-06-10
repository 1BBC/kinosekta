<?php
$date = date("Y-m-d");
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>http://<?=$_SERVER['HTTP_HOST']?>/filmy/</loc>
        <lastmod><?=$date?></lastmod>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>http://<?=$_SERVER['HTTP_HOST']?>/aktery/</loc>
        <lastmod><?=$date?></lastmod>
        <priority>0.7</priority>
    </url>
</urlset>