<?php

return [
    // Number of sentences for summary_text by default
    'summary_sentences_default' => 3,

    // Number of related articles to return
    'related_limit' => 3,

    // Cache TTL for related list (seconds)
    'related_cache_ttl' => 600,

    // Content output format: 'plain' or 'html' (we use 'plain' per user choice)
    'content_output_format' => 'plain',
];
