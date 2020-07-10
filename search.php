<?php

include('config.php');
include('classes/SiteResultProvider.php');
include('classes/ImageResultProvider.php');

if (isset($_GET['term'])) {
    $term = $_GET['term'];
} else {
    exit('You must enter a search term');
}

$type = isset($_GET['type']) ? $_GET['type'] : 'sites';
$page = isset($_GET['page']) ? $_GET['page'] : 1;

?>

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Welcome to Loodle</title>

        <link rel="stylesheet" href="/search-engine/assets/css/style.css" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    </head>
    <body>
    <div class="wrapper">
        <div class="header">
            <div class="header-container">
                <div class="logo-container">
                    <a href="index.php">
                        <img src="/search-engine/assets/logo/logo.png" alt="Site Logo">
                    </a>
                </div>

                <div class="search-container">
                    <form action="search.php" method="get">
                        <div class="search-bar-container">
                            <input type="hidden" name="type" value="<?php echo $type; ?>">

                            <input type="text" class="search-boxx" name="term" value="<?php echo $term; ?>">
                            <button class="search-button">
                                <img src="/search-engine/assets/logo/search-icon.png">
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="tabs-container">
                <ul class="tab-list">
                    <li class="<?php echo $type == 'sites' ? 'active' : ''; ?>"><a
                                href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a></li>
                    <li class="<?php echo $type == 'images' ? 'active' : ''; ?>"><a
                                href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a></li>
                </ul>
            </div>
        </div>

        <div class="main-result-section">
            <?php

            if ($type == 'sites') {
                $resultProvider = new SiteResultProvider($db);
                $pageLimit = 20;
            } else {
                $resultProvider = new ImageResultProvider($db);
                $pageLimit = 30;
            }


            $numResults = $resultProvider->getNumResults($term);

            echo "<p class='result-count'>$numResults results found</p>";

            echo $resultProvider->getResultsHTML($page, $pageLimit, $term);
            ?>
        </div>

        <div class="pagination-container">
            <div class="page-buttons">
                <div class="page-number-container">
                    <img src="/search-engine/assets/logo/page-start.png">
                </div>

                <?php

                $pagesToShow = 10;
                $numPages = ceil($numResults / $pageLimit);
                $pageLeft = min($pagesToShow, $numPages);

                $currentPage = $page - floor($pagesToShow / 2);

                if ($currentPage < 1) {
                    $currentPage = 1;
                }

                if ($currentPage + $pageLeft > $numPages + 1) {
                    $currentPage = $numPages + 1 - $pageLeft;
                }

                while ($pageLeft != 0 && $currentPage <= $numPages) {
                    if ($currentPage == $page) {
                        echo "<div class=\"page-number-container\"><img src=\"/search-engine/assets/logo/page-selected.png\"><span class=\"page-number\">$currentPage</span></div>";

                        $currentPage++;
                        $pageLeft--;
                    } else {
                        echo "<div class=\"page-number-container\"><a href=\"search.php?term=$term&type=$type&page=$currentPage\"><img src=\"/search-engine/assets/logo/page.png\"><span class=\"page-number\">$currentPage</span></a></div>";

                        $currentPage++;
                        $pageLeft--;
                    }
                } ?>

                <div class="page-number-container">
                    <img src="/search-engine/assets/logo/page-end.png">
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script src="assets/js/app.js"></script>
    </body>
    </html>

<?php db_disconnect(); ?>