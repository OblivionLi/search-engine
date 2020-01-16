<?php


class SiteResultProvider
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getNumResults($term)
    {
        $sql = "SELECT COUNT(*) as total FROM sites WHERE title LIKE '%$term%' OR url LIKE '%$term%' OR keywords LIKE '%$term%' OR description LIKE '%$term%'";

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

        $stmt = mysqli_prepare($this->db, "SELECT * FROM sites WHERE title LIKE '%$term%'
                                                    OR url LIKE '%$term%'
                                                    OR keywords LIKE '%$term%'
                                                    OR description LIKE '%$term%'
                                                   ORDER BY clicks DESC
                                                    LIMIT ?, ?");

        $stmt->bind_param('ii', $fromLimit, $pageSize);
        $stmt->execute();

        $result = $stmt->get_result();

        $resultHTML = "<div class='site-results'>";

        while ($row = $result->fetch_assoc()) {
            $id = $row["id"];
            $url = $row["url"];
            $title = $row["title"];
            $description = $row["description"];

            $title = $this->trimField($title, 55);
            $description = $this->trimField($description, 230);

            $resultHTML .= "<div class='result-container'>
                            
                                <h3 class='title'>
                                    <a href='$url' class='result' data-linkId='$id'>$title</a>
                                </h3>
                                <span class='url'>$url</span>
                                <span class='description'>$description</span>
                                

                            </div>";
        }

        $resultHTML .= "</div>";

        return $resultHTML;
    }

    private function trimField($string, $characterLimit)
    {
        $dots = strlen($string) > $characterLimit ? "..." : "";

        return substr($string, 0, $characterLimit) . $dots;
    }
}