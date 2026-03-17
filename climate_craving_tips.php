<?php
// -----------------------------
// CLIMATE BASED FOOD TIPS
// -----------------------------
$climateTips = [
    "hot" => [
        "🌞 Hot weather irukku. Watermelon, tender coconut, buttermilk saptaa body cool aagum.",
        "🌞 Veyil time-la lemon water & cucumber romba nallaa irukkum."
    ],
    "rainy" => [
        "🌧️ Rainy climate-la soup, ginger tea body-ku comfort kudukkum.",
        "🌧️ Corn, roasted channa nallaa digest aagum."
    ],
    "cold" => [
        "❄️ Cold weather-la warm milk, oats, nuts energy kudukkum.",
        "❄️ Dal, vegetable curry body heat maintain pannum."
    ]
];

// -----------------------------
// GIRLS CRAVING SUPPORT TIPS
// -----------------------------
$cravingTips = [
    "period" => [
        "💗 Period time cravings normal dhaan. Dark chocolate (small), banana saptaa ok.",
        "💗 Greens & nuts iron support kudukkum."
    ],
    "stress" => [
        "😔 Stress-na ice cream venum nu thonum. Fruits or warm milk better choice.",
        "😔 Deep breath + herbal tea calm pannum."
    ],
    "night" => [
        "🌙 Late night hunger-na warm milk, dates safe option.",
        "🌙 Over eat panna vendam, body rest thevai."
    ]
];

// -----------------------------
// RANDOM SELECTION
// -----------------------------
$selectedClimate = array_rand($climateTips);
$selectedCraving = array_rand($cravingTips);

$climateMessage = $climateTips[$selectedClimate][array_rand($climateTips[$selectedClimate])];
$cravingMessage = $cravingTips[$selectedCraving][array_rand($cravingTips[$selectedCraving])];
?>