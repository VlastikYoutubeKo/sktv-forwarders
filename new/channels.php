<?php
// channels.php
// Definice kanálů a jejich metadat

$channels = [
    "Prima" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p111013", "referer" => ""],
    "PrimaCool" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p111014", "referer" => ""],
    "PrimaZoom" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p111015", "referer" => ""],
    "PrimaLove" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p111016", "referer" => ""],
    "PrimaMax" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p111017", "referer" => ""],
    "PrimaKrimi" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p432829", "referer" => ""],
    "PrimaNews" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p650443", "referer" => ""],
    "PrimaStar" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p846043", "referer" => ""],
    "PrimaShow" => ["group" => "CZ", "fetcher" => "prima_fetcher", "id" => "id-p899572", "referer" => ""],

    "Nova" => ["group" => "CZ", "fetcher" => "nova_fetcher", "id" => "nova-", "referer" => "https://media.cms.nova.cz/"],
    "NovaFun" => ["group" => "CZ", "fetcher" => "nova_fetcher", "id" => "nova-2-", "referer" => "https://media.cms.nova.cz/"],
    "NovaLady" => ["group" => "CZ", "fetcher" => "nova_fetcher", "id" => "nova-lady-", "referer" => "https://media.cms.nova.cz/"],
    "NovaGold" => ["group" => "CZ", "fetcher" => "nova_fetcher", "id" => "nova-gold-", "referer" => "https://media.cms.nova.cz/"],
    "NovaCinema" => ["group" => "CZ", "fetcher" => "nova_fetcher", "id" => "nova-cinema-", "referer" => "https://media.cms.nova.cz/"],
    "NovaAction" => ["group" => "CZ", "fetcher" => "nova_fetcher", "id" => "nova-action-", "referer" => "https://media.cms.nova.cz/"],
    "Nova TN Live" => ["group" => "CZ", "fetcher" => "nova_fetcher", "id" => "tnlive", "referer" => "https://mediatn.cms.nova.cz/"],

    "CT1" => ["group" => "CZ", "fetcher" => "ct_fetcher", "id" => "CH_1", "referer" => ""],
    "CT2" => ["group" => "CZ", "fetcher" => "ct_fetcher", "id" => "CH_2", "referer" => ""],
    "CT24" => ["group" => "CZ", "fetcher" => "ct_fetcher", "id" => "CH_24", "referer" => ""],
    "CTsport" => ["group" => "CZ", "fetcher" => "ct_fetcher", "id" => "CH_4", "referer" => ""],
    "CT_D" => ["group" => "CZ", "fetcher" => "ct_fetcher", "id" => "CH_5", "referer" => ""],
    "CTart" => ["group" => "CZ", "fetcher" => "ct_fetcher", "id" => "CH_6", "referer" => ""],

    "Markiza" => ["group" => "SK", "fetcher" => "markiza_fetcher", "id" => "markiza", "referer" => "https://media.cms.markiza.sk/"],
    "Doma" => ["group" => "SK", "fetcher" => "markiza_fetcher", "id" => "doma", "referer" => "https://media.cms.markiza.sk/"],
    "Dajto" => ["group" => "SK", "fetcher" => "markiza_fetcher", "id" => "dajto", "referer" => "https://media.cms.markiza.sk/"],
    "Krimi" => ["group" => "SK", "fetcher" => "markiza_fetcher", "id" => "krimi", "referer" => "https://media.cms.markiza.sk/"],
    "Klasik" => ["group" => "SK", "fetcher" => "markiza_fetcher", "id" => "klasik", "referer" => "https://media.cms.markiza.sk/"],
    "Markíza TN Live" => ["group" => "SK", "fetcher" => "markiza_fetcher", "id" => "IQZz0dRJL34_tn", "referer" => "https://media.cms.markiza.sk/"],

    "JOJ" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "joj-1080", "referer" => "https://media.joj.sk/"],
    "JOJ Plus" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "plus-1080", "referer" => "https://media.joj.sk/"],
    "Wau" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "wau-1080", "referer" => "https://media.joj.sk/"],
    "JOJ 24" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "joj_news-1080", "referer" => "https://media.joj.sk/"],
    "JOJ Šport" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "joj_sport-1080", "referer" => "https://media.joj.sk/"],
    "Jojko" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "jojko-1080", "referer" => "https://media.joj.sk/"],
    "JOJ Family" => ["group" => "CZ", "fetcher" => "joj_fetcher", "id" => "family-1080", "referer" => "https://media.joj.sk/"],
    "JOJ Cinema" => ["group" => "CZ", "fetcher" => "joj_fetcher", "id" => "cinema-1080", "referer" => "https://media.joj.sk/"],
    "CS Film" => ["group" => "CZ", "fetcher" => "joj_fetcher", "id" => "cs_film-1080", "referer" => "https://media.joj.sk/"],
    "CS History" => ["group" => "CZ", "fetcher" => "joj_fetcher", "id" => "cs_history-1080", "referer" => "https://media.joj.sk/"],
    "CS Mystery" => ["group" => "CZ", "fetcher" => "joj_fetcher", "id" => "cs_mystery-1080", "referer" => "https://media.joj.sk/"],

    "Jednotka" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "1", "referer" => ""],
    "Dvojka" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "2", "referer" => ""],
    "STVR :24" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "3", "referer" => ""],
    "STVR :O" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "4", "referer" => ""],
    "NR SR" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "5", "referer" => ""],
    "STVR Live" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "6", "referer" => ""],
    "Šport" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "15", "referer" => ""],
    
    "TA3" => ["group" => "SK", "fetcher" => "ta3_fetcher", "id" => "ta3", "referer" => ""],
    
    "ČT sport Plus" => ["group" => "CZ", "fetcher" => "ct_fetcher", "id" => "CH_25", "referer" => ""]
];

return $channels;
