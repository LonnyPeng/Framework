<?php

namespace App\Controller\Plugin;

use Framework\ServiceLocator\ServiceLocatorAwareInterface;
use Framework\ServiceLocator\ServiceLocator;
use Framework\Utils\Http;

class Funcs implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocator
     */
    protected $locator = null;

    /**
     * Redirect to some URL
     *
     * @param string $url
     * @param int $status
     * @return exit
     */
    public function redirect($url, $status = 302)
    {
        $url = (string) $url;

        // accept back or -1
        if ($url == 'back' || $url == '-1') {
            $url = $this->getReferer();
        }

        // headers were sent
        if (headers_sent()) {
            printf('<script>window.location="%s";</script>', $url);
            exit;
        }

        // redirect and exit
        Http::redirect($url, $status);
    }

    /**
     * Whether or not the POST request
     *
     * @return boolean
     */
    public function isPost()
    {
        return isset($_SERVER['REQUEST_METHOD']) && 'POST' === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Whether or not the AJAX request
     *
     * @return boolean
     */
    public function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
                || (isset($_REQUEST['X-Requested-With'])
                && $_REQUEST['X-Requested-With'] == 'XMLHttpRequest');
    }

    /**
     * Create new file
     *
     * @param string $path
     * @return boolean
     */
    public function makeFile($path = "")
    {
        $path = trim($path);
        if (!$path) {
            return false;
        }

        $path = preg_replace("/\\\\/", "/", $path);

        $filename = substr($path, strripos($path, "/") + 1);
        $ext = substr($filename, strripos($filename, ".") + 1);
        if (!$ext) {
            $filename = "";
        }

        $dirPathInfo = explode("/{$filename}", $path);
        array_pop($dirPathInfo);
        $dirPath = implode("/", $dirPathInfo);

        if ($filename) {
            if (is_dir($path)) {
                return false;
            }

            if (file_exists($path)) {
                return true;
            }
        } else {
            if (is_dir($path)) {
                return true;
            }
        }

        // make dir
        if (!is_dir($dirPath)) {
            if (file_exists($dirPath)) {
                return false;
            }

            if (!@mkdir($dirPath, 0777, true)) {
                if (!is_dir($dirPath)) {
                    return false;
                }
            }
        }

        // make file
        if ($filename) {
            $handle = fopen($path, 'a');
            fclose($handle);
        }

        if (file_exists($path)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set service locator
     *
     * @param ServiceLocator $serviceLocator
     * @return Controller
     */
    public function setServiceLocator(ServiceLocator $serviceLocator)
    {
        $this->locator = $serviceLocator;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->locator;
    }
}