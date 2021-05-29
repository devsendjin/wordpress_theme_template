<?php

class Utils
{
    public const CSRF_TOKEN_NAME = 'csrf-token';

    public static function clearPhone($phone, $additionalFilters = [])
    {
        $symbols_to_replace = ['-', ' ', '(', ')'];
        if (!empty($additionalFilters)) {
            foreach ($additionalFilters as $symbol) {
                $symbols_to_replace[] = $symbol;
            }
        }
        return str_replace($symbols_to_replace, '', $phone);
    }

    public static function getAssetUrlWithTimestamp($pathFromThemeRoot): string
    {
        if (mb_substr($pathFromThemeRoot, 0, 1) !== '/') {
            $pathFromThemeRoot = '/'.$pathFromThemeRoot;
        }
        $fullPath = get_template_directory().$pathFromThemeRoot;
        if (file_exists($fullPath)) {
            return get_template_directory_uri() . $pathFromThemeRoot . '?' . filemtime($fullPath);
        }

        return '';
    }

    public static function getFileContentByAttachmentId($id): string
    {
        $url = wp_get_attachment_url($id);
        if (!empty($url)) {
            $file = file_get_contents($url);
        }
        return $file ? $file : '';
    }

    public static function getFullImageUrlByAttachmentId($id)
    {
        return wp_get_attachment_image_src($id, 'full')[0];
    }

    public static function getNormalizedMetaData($postId = null, $postType = 'post', $showHiddenFields = false): array
    {
        if (empty($postId)) {
            global $post;
            $postId = $post->ID;
        }
        if (isset($postId)) {
            $metaData = get_metadata($postType, $postId, '');
            if (!empty($metaData)) {
                return self::normalizeMetaData($metaData, $showHiddenFields, $postId);
            }

            return [];
        }

        return [];
    }

    private static function normalizeMetaData($metaData, $showHiddenFields, $postId): array
    {
        global $metaFieldsObj;
        if (!$showHiddenFields) {
            $metaData = array_filter($metaData, function ($key) {
                return mb_substr($key, 0, 1) !== '_';
            }, ARRAY_FILTER_USE_KEY);
        }

        $metaDataUnserialized = array_map(function ($item) {
            return maybe_unserialize($item[0]);
        }, $metaData);

        $pageMetaFields = $metaFieldsObj->getPageMetaFields(get_post($postId));
        $pageMetaFieldsTypes = [];
        foreach ($pageMetaFields as $item) {
            if (isset($item['id'])) {
                $pageMetaFieldsTypes[$item['id']] = $item['type'];
            }
        }

        foreach ($metaDataUnserialized as $key => $value) {
            if ($pageMetaFieldsTypes[$key] === 'repeater') {
                $metaDataUnserialized[$key] = array_map(function ($item) {
                    return (object)$item;
                }, $value);
            }
        }
        return $metaDataUnserialized;
    }

    // получить страницу связанную с шаблоном
    public static function getBindedPage($templateName)
    {
        $templateName = Utils::normalizeTemplateFileName($templateName);
        return (new WP_Query(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => basename($templateName),
        )))->posts[0];
    }

    public static function explodeList($listStr)
    {
        $listStr = str_replace(["\n", "\r"], ',', $listStr);
        $list = explode(',', $listStr);
        $list = array_map('trim', $list);
        return array_filter($list);
    }

    private static function generateRandomString($stringLength = null): string
    {
        if (empty($stringLength)) {
            $stringLength = random_int(40, 50);
        }
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $stringLength; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function getRequestScheme(): string
    {
        return (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https' : 'http';
    }

    public static function clearValue($val): string
    {
        return nl2br(htmlspecialchars(trim($val), ENT_QUOTES));
    }

    public static function getNonceName(): string
    {
        return 'ajax-mail';
    }

    public static function getNonceActionName($flag = false): string
    {
        $nonceActionFileName = __DIR__.'/_nonceActionName.php';
        if (file_exists($nonceActionFileName)) {
            require_once '_nonceActionName.php';
            $actionName = nonceActionName();
        } else {
            $actionName = self::generateRandomString(10);
            file_put_contents($nonceActionFileName, "<?php function nonceActionName() { return '. $actionName . '; }");
        }
        if ($flag) {
            echo $actionName;
        }
        return $actionName;
    }

    public static function getTemplateFileName($postId)
    {
        return get_post_meta($postId, '_wp_page_template', true);
    }

    public static function getPageUrlByTemplateFileName($templateName)
    {
        return get_permalink(self::getBindedPage($templateName)->ID);
    }

    public static function emailRegExp(): string
    {
        return '/^(([^<>()\[\]\.,;:\s@"]+(\.[^<>()\[\]\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
    }

    public static function emailValid($email)
    {
        return preg_match(self::emailRegExp(), $email);
    }

    public static function csrfField(): string
    {
        $nonce_field = '<input type="hidden" name="' . self::CSRF_TOKEN_NAME . '" value="' . wp_create_nonce(self::getNonceActionName()) . '" />';
        $nonce_field .= wp_referer_field(false);
        return $nonce_field;
    }

    public static function antiBotInput(): string
    {
        return "<label style='display: none'><input type='checkbox' class='agree' name='agree' checked value=''>Согласие на обработку персональных данных</label>";
    }

    public static function getCsrfToken()
    {
        return wp_create_nonce(self::getNonceActionName());
    }

    public static function cyr2lat($string): string
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',    'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            'ґ' => 'g',   'і' => 'i',   'ї' => 'i',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
            'Ґ' => 'G',   'І' => 'I',   'Ї' => 'I',
        );
        return strtr($string, $converter);
    }

    public static function textShorter($string, $amountOfWords = 4, $dostAtTheEnd = '...'): string
    {
        if (empty($string)) {
            return '';
        }
        $string = strip_tags($string);

        $primaryWords = explode(' ', trim($string));
        //разделяем на слова, отделяем 4(по умолчанию) первых слова
        $wordsSliced = array_slice($primaryWords, 0, $amountOfWords);
        //заменяем запятую в последнем слове
        $wordsSliced[count($wordsSliced) - 1] = str_replace(',', '', $wordsSliced[count($wordsSliced) - 1]);
        //соединяем в 1 строку
        $resultStr = implode(' ', $wordsSliced);
        // если изначальное кол-во слов меньше возвращаем стороку без точек, иначе с точками
        return count($primaryWords) <= $amountOfWords ? $resultStr : $resultStr . $dostAtTheEnd;
    }

    private static function normalizeTemplateFileName($fileName)
    {
        if (substr($fileName, -4) !== '.php') {
            $fileName .= '.php';
        }
        return $fileName;
    }

    public static function svgContentByAttachmentId($attachmentId): string
    {
        $fileContent = self::getFileContentByAttachmentId($attachmentId);
        if ($fileContent !== '') {
            $startPosition = mb_strpos($fileContent, '<svg');
            if ($startPosition !== false) {
                $endPosition = mb_strpos($fileContent, '</svg>', $startPosition);
                if ($endPosition !== false) {
                    $fileContent = mb_substr($fileContent, $startPosition, $endPosition - $startPosition+6); // 6 = length of '</svg>' string
                } else {
                    $fileContent = '';
                }
            } else {
                $fileContent = '';
            }
        }
        return $fileContent;
    }
}
