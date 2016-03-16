<?php

    /*
     * This file is part of Fork CMS.
     *
     * For the full copyright and license information, please view the license
     * file that was distributed with this source code.
     */

    /**
     * In this file we store all helper functions that we will be using in the Litters module
     *
     * @author Yohann Bianchi <sbooob@gmail.com>
     */
    class BackendLittersHelper
    {
        /**
         * Cleans up a string so that it can safely be used as a filename (not bulletproof, only suits my use case)
         *
         * @param string    $text   the text to sanitize
         *
         * @return string   the sanitized representation of $text
         */
        public static function sanitizeFilename($text)
        {
            $text = preg_replace('/[áàâãªä]/u', 'a', $text);
            $text = preg_replace('/[ÁÀÂÃÄ]/u', 'A', $text);
            $text = preg_replace('/[ÍÌÎÏ]/u', 'I', $text);
            $text = preg_replace('/[íìîï]/u', 'i', $text);
            $text = preg_replace('/[éèêë]/u', 'e', $text);
            $text = preg_replace('/[ÉÈÊË]/u', 'E', $text);
            $text = preg_replace('/[óòôõºö]/u', 'o', $text);
            $text = preg_replace('/[ÓÒÔÕÖ]/u', 'O', $text);
            $text = preg_replace('/[úùûü]/u', 'u', $text);
            $text = preg_replace('/[ÚÙÛÜ]/u', 'U', $text);
            $text = str_replace('–', '-', $text);
            $text = str_replace('ç', 'c', $text);
            $text = str_replace('Ç', 'C', $text);
            $text = str_replace('ñ', 'n', $text);
            $text = str_replace('Ñ', 'N', $text);
            $text = preg_replace('/[^-A-Za-z0-9_.]/u', '_', $text);

            return $text;
        }

        /**
         * Create an HTML <img> tag from a given URL
         *
         * @param string            $src    the URL of the image
         * @param string [optional] $alt    the image alternative text
         *
         * @return string   the corresponding <img> tag or an empty string if URL is empty
         */
        public static function createImgTag($src, $alt = '', $width = null, $height = null)
        {
            $ret = '';

            if ($src !== null && !empty($src)) {
                $width = null === $width ? '' : ' width="' . (int)$width . '"';
                $height = null === $height ? '' : ' height="' . (int)$height . '"';
                $ret = <<< EOT
<img src="${src}"${width}${height} alt="${alt}" />
EOT;
            }

            return $ret;
        }
    }
