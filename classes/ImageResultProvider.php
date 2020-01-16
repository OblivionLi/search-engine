<?php


class ImageResultProvider
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getNumResults($term)
    {
        $sql = "SELECT COUNT(*) as total FROM images WHERE title LIKE '%$term%' OR alt LIKE '%$term%' AND broken = 0";

        $result = mysqli_query($this->db, $sql);

        if (!$result) {
            confirm_query_result($result);
        }

        $row = $result->fetch_assoc();
        return $row["total"];
    }

    public function getResultsHTML($page, $pageSize, $term)
    {
        $fromLimit = ($page - 1) * $pageSize;

        $stmt = mysqli_prepare($this->db, "SELECT * FROM images WHERE title LIKE '%$term%'
                                                    OR alt LIKE '%$term%'
                                                    AND broken = 0
                                                    ORDER BY clicks DESC
                                                    LIMIT ?, ?");

        $stmt->bind_param('ii', $fromLimit, $pageSize);
        $stmt->execute();

        $result = $stmt->get_result();

        $resultHTML = "<div class='image-results'>";

        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $count++;
            $id = $row["id"];
            $imageUrl = $row["imageUrl"];
            $siteUrl = $row["siteUrl"];
            $title = $row["title"];
            $alt = $row["alt"];

            if ($title) {
                $displayText = $title;
            } elseif ($alt) {
                $displayText = $alt;
            } else {
                $displayText = $imageUrl;
            }

            $resultHTML .= "<div class='grid-item image$count'>
								<a href='$imageUrl' data-fancybox data-caption='$displayText' data-siteurl='$siteUrl'>
									
									<script>
									$(document).ready(function() {
										loadImage(\"$imageUrl\", \"image$count\");
									});
									</script>

									<span class='details'>$displayText</span>
								</a>

							</div>";
        }

        $resultHTML .= "</div>";

        return $resultHTML;
    }

}