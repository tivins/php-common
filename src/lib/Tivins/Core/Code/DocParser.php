<?php

namespace Tivins\Core\Code;

class DocParser
{
    public static function clean(string $docClean): string {

        $docClean = explode("\n", $docClean);
        $docClean = array_map(fn($s) => trim($s, "\ \t\n\r\0\x0B/*"), $docClean);
        return join("\n", $docClean);
    }
    public static function parse(string $docString, string $objName = ''): array
    {
        $doc = explode('@', $docString);
        $doc = array_filter(array_map(fn($s) => trim($s, "\ \t\n\r\0\x0B/*"), $doc));
        $paramsDoc = [];
        foreach ($doc as $doc_param) {
            $doc_param_ex = preg_split('~\s~', $doc_param, 4, PREG_SPLIT_NO_EMPTY);
            $doc_var_ex = preg_split('~\s~', $doc_param, 3, PREG_SPLIT_NO_EMPTY);

            if (($doc_param_ex[0] ?? '') == 'since') {
                $paramsDoc['since'] = $doc_param_ex[1];
                continue;
            }
            if (($doc_var_ex[0] ?? '') == 'var') {
                $doc_var_ex[2] = self::clean($doc_var_ex[2]);
                //$name = trim($doc_var_ex[2],'$');
                $paramsDoc['var'] = [
                    'type'=>$doc_var_ex[1],
                    'doc'=>self::clean($doc_var_ex[2]),
                ];
                continue;
            }
            if (($doc_var_ex[0] ?? '') == 'see') {
                $paramsDoc['see'][] = join(' ', array_slice($doc_var_ex, 1));
                continue;
            }
            if (($doc_var_ex[0] ?? '') == 'deprecated') {
                $paramsDoc['deprecated'] = join(' ', array_slice($doc_var_ex, 1));
                continue;
            }
            if (($doc_var_ex[0] ?? '') == 'throws') {
                $paramsDoc['throws'][] = [
                    'type'=>$doc_var_ex[1],
                    'doc'=>self::clean($doc_var_ex[2] ?? ''),
                ];
                continue;
            }
            if (($doc_var_ex[0] ?? '') == 'return') {
                $paramsDoc['return'] = [
                    'type'=>$doc_var_ex[1],
                    'doc'=>self::clean($doc_var_ex[2] ?? ''),
                ];
                continue;
            }
            if (($doc_param_ex[0] ?? '') == 'param') {
                $doc_param_ex[3] = self::clean($doc_param_ex[3] ?? '');
                $name = trim($doc_param_ex[2],'$');
                $paramsDoc['param'][$name] = [
                    'type'=>$doc_param_ex[1],
                    'doc'=>self::clean($doc_param_ex[3]),
                ];
                continue;
            }
            if (($doc_param_ex[0] ?? '') == 'input') {
                $doc_param_ex[3] = self::clean($doc_param_ex[3] ?? '');
                $name = trim($doc_param_ex[2],'$');
                $paramsDoc['input'][$name] = [
                    'type'=>$doc_param_ex[1],
                    'doc'=>self::clean($doc_param_ex[3]),
                ];
                continue;
            }

            if (str_starts_with($doc_param_ex[2] ?? '', '$')) {
                $docClean = $doc_param_ex[4] ?? '';
            } else {
                $docClean     = $doc_param;
                $doc_param_ex = null;
            }

            $docClean = explode("\n", $docClean);
            $docClean = array_map(fn($s) => trim($s, "\ \t\n\r\0\x0B/*"), $docClean);
            $docClean = join("\n", $docClean);

            if ($doc_param_ex) {
                $paramsDoc[trim($doc_param_ex[2], '$')] = ['type' => $doc_param_ex[1], 'doc' => $docClean];
            } else {
                $paramsDoc['brief'] = $docClean;
            }
        }
//        var_dump($paramsDoc);
        return $paramsDoc;
    }

}