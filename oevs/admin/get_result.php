<?php
include('dbcon.php');

$positions_query = mysqli_query($conn, "SELECT DISTINCT Position FROM candidate ORDER BY Position ASC");
$top_candidates = [];

while ($pos_row = mysqli_fetch_assoc($positions_query)) {
    $position = $pos_row['Position'];
    $safe_position = mysqli_real_escape_string($conn, $position);

    $top_cand_query = mysqli_query($conn, "
        SELECT c.CandidateID, c.FirstName, c.LastName, c.Year, c.Position, c.Photo, c.Qualification, c.Party,
        (SELECT COUNT(*) FROM votes v WHERE v.CandidateID = c.CandidateID) AS vote_count
        FROM candidate c
        WHERE c.Position = '$safe_position'
        ORDER BY vote_count DESC, c.LastName ASC
        LIMIT 1
    ");

    if ($top_cand = mysqli_fetch_assoc($top_cand_query)) {
        $top_candidates[] = $top_cand;
    }
}

header('Content-Type: application/json');
echo json_encode($top_candidates);
