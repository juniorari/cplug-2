<?php

use Illuminate\Http\JsonResponse;

function responseJson(int $code, string $message, ?array $resource = []): JsonResponse
{
    $result = [
        'code' => $code,
        'message' => $message,
        'data' => [],
    ];

    if (count($resource)) {
        $result = array_merge($result, ['data' => $resource['data'] ?? []]);

        if (count($resource) > 1) {
            $result = array_merge($result, ['pages' => ['links' => $resource['links'] ?? [], 'meta' => $resource['meta'] ?? []]]);
//            unset($result['pages']['meta']['links']);
        }
    }
    return response()->json($result, $code);
}

function getValueFromRequest($request, $field, $default, $valids = [])
{
    $value = $request->$field ?? $default;
    if (!in_array($value, $valids)) {
        $value = $default;
    }
    return $value;
}

function removerAcentos($string)
{

    $str = $string;

    $str = preg_replace('/[áàãâä]/u', 'a', $str);
    $str = preg_replace('/[éèêë]/u', 'e', $str);
    $str = preg_replace('/[íìîï]/u', 'i', $str);
    $str = preg_replace('/[óòõôö]/u', 'o', $str);
    $str = preg_replace('/[úùûü]/u', 'u', $str);
    $str = preg_replace('/[ç]/u', 'c', $str);
    $str = preg_replace('/[ñ]/u', 'n', $str);
    $str = preg_replace('/[ÁÀÃÂÄ]/u', 'A', $str);
    $str = preg_replace('/[ÉÈÊË]/u', 'E', $str);
    $str = preg_replace('/[ÍÌÎÏ]/u', 'I', $str);
    $str = preg_replace('/[ÓÒÕÔÖ]/u', 'O', $str);
    $str = preg_replace('/[ÚÙÜÛ]/u', 'U', $str);
    $str = preg_replace('/[Ç]/u', 'C', $str);
    $str = preg_replace('/[Ñ]/u', 'N', $str);
    $str = str_replace('  ', ' ', $str);

    $string = $str;
    return $string;
}

function slugify($text, $max = 100)
{
    $text = removerAcentos($text);

    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return (strlen($text) > $max ? substr($text, 0, $max) : $text);
}
