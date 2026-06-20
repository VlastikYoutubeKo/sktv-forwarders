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

    "JOJ" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "joj-1080", "referer" => "https://media.joj.sk/"],
    "JOJP" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "plus-1080", "referer" => "https://media.joj.sk/"],
    "Wau" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "wau-1080", "referer" => "https://media.joj.sk/"],
    "JOJ24" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "joj_news-1080", "referer" => "https://media.joj.sk/"],
    "JOJFamily" => ["group" => "SK", "fetcher" => "joj_fetcher", "id" => "family-1080", "referer" => "https://media.joj.sk/"],

    "STV1" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "1", "referer" => ""],
    "STV2" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "2", "referer" => ""],
    "STV24" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "3", "referer" => ""],
    "RTVS" => ["group" => "SK", "fetcher" => "stv_fetcher", "id" => "6", "referer" => ""]
];

return $channels;
