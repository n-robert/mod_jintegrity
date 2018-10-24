<?php
/**
 * @package      mod_jintegrity
 *
 * @copyright    © Robert N. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Restricted access');

class ModJintegrityHelper
{
    public static function getHash($directory)
    {
        if (!is_dir($directory))
        {
            return false;
        }

        $files = [];
        $dir = dir($directory);

        while (false !== ($file = $dir->read()) && false === strpos($file, 'installation'))
        {
            if ($file != '.' and $file != '..')
            {
                if (is_dir($directory . '/' . $file))
                {
                    $files[$file] = self::getHash($directory . '/' . $file);
                }
                else
                {
                    $files[$file] = crc32(file_get_contents($directory . '/' . $file));
                }
            }
        }

        $dir->close();

        return $files;
    }

    public static function saveHash($source, $file, $tmp_zip, $tmp_dir)
    {
        $is_url = filter_var($source, FILTER_VALIDATE_URL);
        $is_zip = (is_file($source) && pathinfo($source, PATHINFO_EXTENSION) == 'zip');


        if ($is_zip)
        {
            self::unzipPackage($source, $tmp_dir);
        }
        elseif ($is_url)
        {
            self::downloadPackage($source, $tmp_zip, $tmp_dir);
        }

        $source = !($is_zip || $is_url) ?: $tmp_dir;

        try
        {
            $result = file_put_contents($file, json_encode(self::getHash($source)));

            if ($result && $is_url)
            {
                JFolder::delete($source);
//				self::delete_dir($source);
            }
        }
        catch (Exception $e)
        {
            $result = $e->getMessage();
        }

        return $result;
    }

    public static function getChanges($test, $package, &$changes = [], $path = '')
    {
        if (empty($package))
        {
            return false;
        }

        @set_time_limit(ini_get('max_execution_time'));

        foreach ($package as $key => $value)
        {
            $new_path = $path . '/' . $key;

            if (is_array($value))
            {
                self::getChanges($test[$key], $value, $changes, $new_path);
            }
            else
            {
                if (isset($test[$key]) && $test[$key] !== $value)
                {
                    $changes[] = $new_path . ' (' . $value . ')';
                }
            }
        }

        return $changes;
    }

    public static function getResult($test, $package)
    {
        $test = self::getHash($test);
        $package = is_dir($package) ?
            self::getHash($package) :
            is_file($package) ?
                json_decode(file_get_contents($package), true) :
                [];
        $changes = self::getChanges($test, $package);

        if (!empty($changes))
        {
            $title = count($changes) . JText::plural('MOD_JINTEGRITY_FILES_MODIFIED', count($changes));
            array_unshift($changes, $title);
            $result = nl2br(implode(PHP_EOL, $changes));
        }
        elseif ($changes === false)
        {
            $result = JText::_('MOD_JINTEGRITY_NO_PACKAGE_HASHES');
        }
        else
        {
            $result = JText::_('MOD_JINTEGRITY_NO_CHANGES');
        }

        return $result;
    }

    public static function downloadPackage($url, $file_name, $folder)
    {
        try
        {
            $file = fopen($file_name, 'w+');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FILE, $file);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            @set_time_limit(ini_get('max_execution_time'));

            curl_exec($ch);
            curl_close($ch);
            fclose($file);

            $result = self::unzipPackage($file_name, $folder, true);
        }
        catch (Exception $e)
        {
            $result = $e->getMessage();
        }

        return $result;
    }

    public static function unzipPackage($file_name, $folder, $unlink = false)
    {
        try
        {
            @set_time_limit(ini_get('max_execution_time'));
            $zip = new JArchiveZip;

            if (!is_dir($folder))
            {
                mkdir($folder);
            }

            if ($result = $zip->extract($file_name, $folder))
            {
                if ($unlink)
                {
                    unlink($file_name);
                }
            }
        }
        catch (Exception $e)
        {
            $result = $e->getMessage();
        }

        return $result;
    }

    public static function delete_dir($dir)
    {
        if (!($handle = @opendir($dir)))
        {
            return false;
        }

        while (($file = readdir($handle)) !== false)
        {
            if ($file != '.' && $file != '..')
            {
                $tmp = $dir . '/' . $file;

                if (!empty($tmp) && is_dir($tmp))
                {
                    self::delete_dir($tmp);
                }
                elseif (is_file($tmp))
                {
                    unlink($tmp);
                }
            }
        }

        closedir($handle);

        return rmdir($dir);
    }
}
