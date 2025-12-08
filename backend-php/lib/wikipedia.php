<?php
function fetch_artist_from_wikipedia(string $name): ?array {
    $encoded = urlencode($name);
    $url = "https://en.wikipedia.org/api/rest_v1/page/summary/{$encoded}";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5
        ]
    ]);
    $json = @file_get_contents($url, false, $context);
    if ($json === false) {
        return null;
    }
    $data = json_decode($json, true);
    if (!is_array($data) || isset($data['type']) && $data['type'] === 'disambiguation') {
        return null;
    }
    $bio = $data['extract'] ?? null;
    $birthYear = null;
    if (!empty($data['description']) && preg_match('/(\d{4})/', $data['description'], $m)) {
        $birthYear = (int)$m[1];
    }
    $imageUrl = $data['thumbnail']['source'] ?? null;
    return [
        'bio' => $bio,
        'birth_year' => $birthYear,
        'image_url' => $imageUrl,
        'wikipedia_url' => $data['content_urls']['desktop']['page'] ?? null,
    ];
}
