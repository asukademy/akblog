<?php

/**
 * @package      JCE
 * @copyright    Copyright (C) 2005 - 2013 Ryan Demmer. All rights reserved.
 * @author		Ryan Demmer
 * @license      GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
// no direct access
defined('_JEXEC') or die('ERROR_403');

require_once(dirname(__FILE__) . '/s3/s3.php');

class WFS3FileSystem extends WFFileSystem {

    protected $instance;

    /**
     * Constructor activating the default information of the class
     *
     * @access	protected
     */
    public function __construct($config = array()) {
        parent::__construct($config);

        $this->accessKey = $this->getParam('editor.filesystem.s3.accesskey', '');
        $this->secretKey = $this->getParam('editor.filesystem.s3.secretkey', '');
        $this->bucket = $this->getParam('editor.filesystem.s3.bucket', '');
        $this->cname = $this->getParam('editor.filesystem.s3.cname', $this->bucket);
        $this->timeout = (int) $this->getParam('editor.filesystem.s3.timeout', 3600);
        $this->ssl = (int) $this->getParam('editor.filesystem.s3.ssl', 1);
        $this->endpoint = $this->getParam('editor.filesystem.s3.endpoint', 's3.amazonaws.com');

        // create instance
        $this->instance = new S3($this->accessKey, $this->secretKey, (bool) $this->ssl, $this->endpoint);
        $elements = array(
            's3_acl' => array(
                'label' => WFText::_('WF_FILESYSTEM_S3_ACL', 'ACL'),
                'options' => array(
                    'private' => WFText::_('WF_FILESYSTEM_S3_ACL_PRIVATE', 'Private'),
                    'public-read' => WFText::_('WF_FILESYSTEM_S3_ACL_PUBLIC_READ', 'Public Read'),
                    'public-read-write' => WFText::_('WF_FILESYSTEM_S3_ACL_PUBLIC_READ_WRITE', 'Public Read/Write'),
                    'authenticated-read' => WFText::_('WF_FILESYSTEM_S3_ACL_AUTHENTICATED_READ', 'Authenticated Read')
                )
            )
        );

        $config = array(
            'local' => false,
            'upload' => array(
                'stream' => false,
                'chunking' => false,
                'unique_filenames' => false,
                'elements' => $elements
            ),
            'folder_new' => array(
                'elements' => $elements
            ),
            'base' => $this->endpoint . '/' . $this->bucket
        );

        $this->setProperties($config);
    }

    /**
     * Get the base directory.
     * @return string base dir
     */
    public function getBaseDir() {
        return $this->getRootDir();
    }

    /**
     * Get the full base url
     * @return string base url
     */
    public function getBaseURL() {
        return $this->getRootDir();
    }

    /**
     * Return the full user directory path. Create if required
     *
     * @param string	The base path
     * @access public
     * @return Full path to folder
     */
    public function getRootDir() {
        static $root;

        if (!isset($root)) {
            $root = parent::getRootDir();

            if (empty($root)) {
                $root = 'images';
            }

            if (!$this->exists($root)) {
                if ($this->folderCreate($root)) {
                    return $root;
                }
            }
        }

        return $root;
    }

    /**
     * Count the number of files in a folder
     * @return integer File total
     * @param string $path Absolute path to folder
     */
    public function countItems($path) {
        if (strpos($path, $this->getBaseDir()) === false) {
            $path = WFUtility::makePath($this->getBaseDir(), $path);
        }

        $count = 0;

        $items = $this->instance->getBucket($this->bucket, $path, null, null, '/');

        if (is_array($items) && !empty($items)) {
            foreach ($items as $item => $value) {
                $item = basename($item);

                // skip some files
                if ($item == 'index.html' || $item[0] == '.' || $item == 'thumbs.db') {
                    continue;
                }

                $count++;
            }
        }

        return $count;
    }

    /**
     * Determine whether a key exists
     * @return boolean
     */
    public function exists($path) {
        if ($this->is_dir($path)) {
            $path .= '/index.html';
        }
        return @$this->instance->getObject($this->bucket, $path);
    }

    public function getFolders($relative) {
        $list = array();
        $path = WFUtility::makePath($this->getRootDir(), $relative);
        $path = WFUtility::fixPath($path);

        $list = $this->instance->getBucket($this->bucket, $path, null, null, '/', true);

        $folders = array();

        if (is_array($list) && !empty($list)) {
            natcasesort($list);
            foreach ($list as $id => $value) {
                if (array_key_exists('prefix', $value)) {
                    // get name
                    $item = basename($id);
                    // utf-8 encode
                    $item = WFUtility::isUTF8($item) ? $item : utf8_encode($item);

                    $data = array(
                        'id' => WFUtility::makePath($relative, $item, '/'),
                        'name' => $item,
                        'writable' => true,
                        'type' => 'folders'
                    );

                    $properties = self::getFolderDetails($id, $value);
                    $folders[] = array_merge($data, array('properties' => $properties));
                }
            }
        }

        return $folders;
    }

    public function getFiles($relative, $filter) {
        $list = array();
        $path = WFUtility::makePath($this->getRootDir(), $relative);
        $path = WFUtility::fixPath($path);

        $list = $this->instance->getBucket($this->bucket, $path, null, null, '/');

        $files = array();

        $x = 1;

        if (is_array($list) && !empty($list)) {
            // Sort alphabetically
            natcasesort($list);
            foreach ($list as $key => $values) {
                if ($this->is_file(basename($key))) {
                    // create name
                    $item = basename($key);
                    // encode
                    $item = WFUtility::isUTF8($item) ? $item : utf8_encode($item);

                    // remove leading slash
                    $url = $this->instance->getAuthenticatedURL($this->bucket, $key, $this->timeout);

                    // replace endpoint
                    $url = str_replace('s3.amazonaws.com', $this->endpoint, $url);

                    // create relative file
                    $id = WFUtility::makePath($relative, $item, '/');

                    // remove leading slash
                    $id = ltrim($id, '/');

                    $data = array(
                        'id' => $id,
                        'url' => $url,
                        'name' => $item,
                        'writable' => true,
                        'type' => 'files'
                    );

                    $properties = self::getFileDetails($key, $x, $values);
                    $files[] = array_merge($data, array('properties' => $properties));

                    $x++;
                }
            }
        }

        return $files;
    }

    /**
     * Get a folders properties
     * 
     * @return array Array of properties
     * @param string $dir Folder relative path
     */
    public function getFolderDetails($dir, $info = array()) {
        if (empty($info) || array_key_exists('time', $info) === false) {
            $info = $this->instance->getObjectInfo($this->bucket, $dir);
        }
        return array('modified' => strftime($info['time']));
    }

    /**
     * Get a files properties
     * 
     * @return array Array of properties
     * @param string $file File relative path
     */
    public function getFileDetails($file, $count = 1, $info = array()) {
        if (empty($info) || array_key_exists('size', $info) === false || array_key_exists('time', $info) === false) {
            $info = @$this->instance->getObjectInfo($this->bucket, $file);
        }

        $data = array(
            'size' => $info['size'],
            'modified' => strftime($info['time'])
        );

        if (preg_match('/\.(jpeg|jpg|gif|png)/i', $file) && $count < 100) {
            $url = $this->instance->getAuthenticatedURL($this->bucket, $file, $this->timeout);

            // replace endpoint
            $url = str_replace('s3.amazonaws.com', $this->endpoint, $url);

            $dimensions = '';

            // try getimagesize (may not work on some systems)
            $dim = @getimagesize($url);

            $width = $dim[0];
            $height = $dim[1];

            $image = array(
                'width' => $width,
                'height' => $height,
                'preview' => $url
            );
            return array_merge_recursive($data, $image);
        }

        return $data;
    }

    /**
     * Delete the relative file(s).
     * @param $files the relative path to the file name or comma seperated list of multiple paths.
     * @return string $error on failure.
     */
    public function delete($src, $recursive = false) {
        $path = WFUtility::makePath($this->getBaseDir(), $src);

        // get error class
        $result = new WFFileSystemResult();

        $path = WFUtility::makePath($this->getBaseDir(), $src);

        if ($this->is_file($path)) {
            $result->type = 'files';
            $result->state = $this->instance->deleteObject($this->bucket, $path);
        } else {
            $result->type = 'folders';

            if ($recursive == false && $this->countItems($src) > 0) {
                $result->message = JText::sprintf('WF_MANAGER_FOLDER_NOT_EMPTY', basename($path));
            } else {
                $result->state = $this->deleteItems($path);
            }
        }

        return $result;
    }

    protected function getItems($path) {
        // copy files / folders
        $files      = $this->instance->getBucket($this->bucket, $path . '/', null, null, '/');
        $folders    = $this->instance->getBucket($this->bucket, $path . '/', null, null, '/', true);

        return array_merge($folders, $files);
    }

    protected function deleteItems($src) {
        $items = $this->getItems($src);

        $return = false;

        foreach ($items as $item => $value) {
            if (array_key_exists('prefix', $value)) {
                $return = $this->deleteItems($item);
            }
            $return = $this->instance->deleteObject($this->bucket, $item);
        }

        return $return;
    }

    protected function copyItems($src, $destination, $delete = false) {
        $items = $this->getItems($src);

        if (!empty($items)) {
            foreach ($items as $item => $value) {
                $dest = WFUtility::makePath($destination, basename($item));

                $state = $this->instance->copyObject($this->bucket, $item, $this->bucket, $dest);
                // remove original
                if ($state && $delete) {
                    $this->instance->deleteObject($this->bucket, $item);
                }
            }
        }

        return true;
    }

    /**
     * Rename a file.
     * @param string $src The relative path of the source file
     * @param string $dest The name of the new file
     * @return string $error
     */
    public function rename($src, $dest) {

        $src = WFUtility::makePath($this->getBaseDir(), rawurldecode($src));
        $dir = dirname($src);

        $result = new WFFileSystemResult();

        if ($this->is_file($src)) {
            $ext = JFile::getExt($src);
            $file = $dest . '.' . $ext;
            $dest = WFUtility::makePath($dir, $file);

            if ($this->exists($dest)) {
                return $result;
            }

            if ($this->instance->copyObject($this->bucket, $src, $this->bucket, $dest)) {
                $result->state = $this->instance->deleteObject($this->bucket, $src);
                $result->path = $path;
            }

            $result->type = 'files';
        } else {
            $dest = WFUtility::makePath($dir, $dest);

            if ($this->exists($dest)) {
                return $result;
            }

            $this->instance->setExceptions(true);
            
            // create new folder, copy files
            $result->state = $this->copyItems($src, $dest, true);
            $result->type = 'folders';
        }

        return $result;
    }

    /**
     * Copy a file.
     * @param string $files The relative file or comma seperated list of files
     * @param string $dest The relative path of the destination dir
     * @return string $error on failure
     */
    public function copy($file, $destination, $delete = false) {
        $result = new WFFileSystemResult();

        $src = WFUtility::makePath($this->getBaseDir(), $file);
        $dest = WFUtility::makePath($this->getBaseDir(), WFUtility::makePath($destination, basename($file)));

        // src is a file
        if ($this->is_file($src)) {
            $result->type = 'files';

            $result->state = $this->instance->copyObject($this->bucket, $src, $this->bucket, $dest);

            if ($result->state && $delete) {
                $this->instance->deleteObject($this->bucket, $src);
            }

            $result->path = $dest;
        } else {
            // Folders cannot be copied into themselves as this creates an infinite copy / paste loop	
            if ($file === $destination) {
                $result->state = false;
                $result->message = WFText::_('WF_MANAGER_COPY_INTO_ERROR');

                return $result;
            }
            $result->state = $this->copyItems($src, $dest, $delete);
            $result->type = 'folders';
        }

        return $result;
    }

    /**
     * Copy a file.
     * @param string $files The relative file or comma seperated list of files
     * @param string $dest The relative path of the destination dir
     * @return string $error on failure
     */
    public function move($file, $dest) {
        return $this->copy($file, $dest, true);
    }

    /**
     * New folder base function. A wrapper for the JFolder::create function
     * @param string $folder The folder to create
     * @return boolean true on success
     */
    public function folderCreate($folder, $acl = 'private') {
        $input = '<html><body bgcolor="#FFFFFF"></body></html>';
        return @$this->instance->putObject($input, $this->bucket, $folder . '/index.html', $acl);
    }

    /**
     * New folder
     * @param string $dir The base dir
     * @param string $new_dir The folder to be created
     * @return string $error on failure
     */
    public function createFolder($dir, $new) {
        $dir = WFUtility::makePath(rawurldecode($dir), WFUtility::makeSafe($new));
        $dir = WFUtility::makePath($this->getBaseDir(), $dir);

        $acl = JRequest::getWord('s3_acl', 'private');
        $result = new WFFileSystemResult();
        $result->state = $this->folderCreate($dir, $acl);

        return $result;
    }

    public function getDimensions($path) {
        $path = WFUtility::makePath($this->getRootDir(), rawurldecode($path));
        $url = $this->instance->getAuthenticatedURL($this->bucket, $path, $this->timeout);

        $width = 0;
        $height = 0;

        // try getimagesize (may not work on some systems)
        $dim = @getimagesize($url);

        if ($dim && is_array($dim)) {
            $width = $dim[0];
            $height = $dim[1];
        }

        return array(
            'width' => $width,
            'height' => $height
        );
    }

    public function upload($method, $src, $dir, $name, $chunks = 1, $chunk = 0) {
        $path = WFUtility::makePath($this->getRootDir(), rawurldecode($dir));
        $dest = WFUtility::makePath($path, WFUtility::makeSafe($name));

        $result = new WFFileSystemResult();
        $acl = JRequest::getWord('s3_acl', 'private');

        switch ($method) {
            case 'multipart' :
                $input = $this->instance->inputFile($src);
                $result->state = @$this->instance->putObject($input, $this->bucket, $dest, $acl);
                $result->path = $dest;
                break;
            case 'multipart-chunking' :
                break;
            case 'stream' :
                break;
        }

        return $result;
    }

    public function read($file) {
        $path = WFUtility::makePath($this->getBaseDir(), rawurldecode($file));

        return @$this->instance->inputFile($this->bucket, $path);
    }

    public function write($file, $content) {
        $path = WFUtility::makePath($this->getBaseDir(), $file);

        return $this->instance->putObject($content, $this->bucket, $path);
    }

    /**
     * Get the source directory of a file path
     */
    public function getSourceDir($path) {
        if (!empty($path)) {
            // directory path relative to base dir        
            if ($this->is_dir($path)) {
                return $path;
            } else {
                $path = substr(dirname($path), strlen($this->getRootDir()));

                if (empty($path)) {
                    return '';
                }
            }
        }

        return $path;
    }

    public function isMatch($needle, $haystack) {
        $needle = parse_url($needle, PHP_URL_PATH);
        $haystack = WFUtility::makePath($this->bucket, $haystack);

        return trim($haystack, '/') == trim($needle, '/');
    }

    public function pathinfo($path) {
        $path = parse_url($path, PHP_URL_PATH);
        return pathinfo($path);
    }

    public function is_file($path) {
        return preg_match('#\.(' . implode('|', $this->get('filetypes')) . ')$#i', $path);
    }

    public function is_dir($path) {
        return $this->is_file($path) == false;
    }

}
