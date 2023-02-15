<?php

include 'engine/pearson.php';


$dataset = [
    1 => [10, 11, 12, 13, 14, 15, 16, 17, 18, 19],
    2 => [11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
    3 => [12, 13, 14, 15, 16, 17],
    4 => [13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
    5 => [14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
    6 => [15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
    7 => [16, 17, 18, 19, 20, 21],
    8 => [17, 18, 19, 20, 21, 22, 23, 24],
    9 => [18, 19, 20, 21, 22, 23, 24, 25, 26, 27],
    10 => [19, 20, 21, 22, 23, 24, 25, 26, 27, 28]
];

// Create a new instance of the CollaborativeFiltering class
$cf = new CollaborativeFiltering();

// Call the getRecommendations method for user ID 1
$recommendedProducts = $cf->getRecommendations($dataset, 1);

// Print the recommended products
foreach ($recommendedProducts as $index => $value) {
    echo "$value";
}
