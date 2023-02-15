<?php

// [CollaborativeFiltering]
//
// Accepts @dataset that will be used to provide recommendations to user
// Eg:
// Make one seperate table "recommendation" with productId(one) and user_id(many)
//  
// recommendation
// *********************************
// *  product_id   **    user_id   *
// *********************************
// *  1            **    2,3,5     *
// *  2            **    1,3,7     *
// *********************************  
//
// Create a map and send it to the initialize() method
// $dataset = [];
// forEach item in table('recommendation') {
//          $dataset["1"] = ["2", "3", "4"];
//          $dataset[item->product_id] = (item->user_id).split(",");
// }
class CollaborativeFiltering
{
    public $countOfResult = 10;


    function getRecommendations($dataset, $userId)
    {
        // Initialize an array to store the recommended products
        $recommendedProducts = [];

        // Initialize an array to store the cosine similarity scores
        $similarityScores = [];

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

            // Calculate the cosine similarity score between the current user and the other user
            $similarityScores[$otherUserId] = $this->calculateCosineSimilarity($dataset[$userId], $products);
        }

        // Sort the similarity scores in descending order
        arsort($similarityScores);

        // Loop through the top N most similar users
        $numSimilarUsers = min(count($similarityScores), $this->countOfResult);
        $similarUserIds = array_keys(array_slice($similarityScores, 0, $numSimilarUsers));
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

    public function calculateCosineSimilarity($vector1, $vector2)
    {
        // Calculate the dot product of the two vectors
        $dotProduct = 0;
        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
        }

        // Calculate the magnitude of the first vector
        $magnitude1 = 0;
        for ($i = 0; $i < count($vector1); $i++) {
            $magnitude1 += $vector1[$i] * $vector1[$i];
        }
        $magnitude1 = sqrt($magnitude1);

        // Calculate the magnitude of the second vector
        $magnitude2 = 0;
        for ($i = 0; $i < count($vector2); $i++) {
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }
        $magnitude2 = sqrt($magnitude2);

        // Calculate the cosine similarity score
        $cosineSimilarity = $dotProduct / ($magnitude1 * $magnitude2);

        // Return the cosine similarity score
        return $cosineSimilarity;
    }
}
