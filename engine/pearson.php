<?php

class CollaborativeFiltering
{
    public $countOfResult = 10;

    function getRecommendations($dataset, $userId)
    {
        // Initialize an array to store the recommended products
        $recommendedProducts = [];

        // Initialize an array to store the Pearson Correlation scores
        $correlationScores = [];

        // find the length of maximum item in the dataset for later padding
        $maxLength = max(array_map('count', $dataset));


        // Loop through all the users in the dataset
        foreach ($dataset as $otherUserId => $products) {
            // Skip the current user
            if ($otherUserId == $userId) {
                continue;
            }

            // Pad the current user's array with 0's if necessary
            if (count($products) < $maxLength) {
                $products = array_pad($products, $maxLength, 0);
            }

            // Calculate the Pearson Correlation score between the current user and the other user
            $correlationScores[$otherUserId] = $this->calculatePearsonCorrelation($dataset[$userId], $products);
        }

        // Sort the Pearson Correlation scores in descending order
        arsort($correlationScores);

        // Loop through the top N most similar users
        $numSimilarUsers = min(count($correlationScores), $this->countOfResult);
        $similarUserIds = array_keys(array_slice($correlationScores, 0, $numSimilarUsers));
        foreach ($similarUserIds as $similarUserId) {
            // Add the products that the similar user has but the current user does not have to the recommended products array
            if (array_key_exists($similarUserId, $dataset)) {
                $recommendedProducts = array_merge($recommendedProducts, array_diff($dataset[$similarUserId], $dataset[$userId]));
            }
        }

        // Remove any duplicates from the recommended products array
        $recommendedProducts = array_unique($recommendedProducts);

        // Return the recommended products array
        return $recommendedProducts;
    }

    public function calculatePearsonCorrelation($vector1, $vector2)
    {
        // Calculate the mean of both vectors
        $mean1 = array_sum($vector1) / count($vector1);
        $mean2 = array_sum($vector2) / count($vector2);

        // Calculate the numerator and denominator for the Pearson Correlation calculation
        $numerator = 0;
        $denominator1 = 0;
        $denominator2 = 0;
        for ($i = 0; $i < count($vector1); $i++) {
            $numerator += ($vector1[$i] - $mean1) * ($vector2[$i] - $mean2);
            $denominator1 += ($vector1[$i] - $mean1) * ($vector1[$i] - $mean1);
            $denominator2 += ($vector2[$i] - $mean2) * ($vector2[$i] - $mean2);
        }

        // Calculate the Pearson Correlation score
        $denominator = sqrt($denominator1 * $denominator2);
        $pearsonCorrelation = $numerator / $denominator;

        // Return the Pearson Correlation score
        return $pearsonCorrelation;
    }
}
