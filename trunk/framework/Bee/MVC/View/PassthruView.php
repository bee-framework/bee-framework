<?php
namespace Bee\MVC\View;
/*
 * Copyright 2008-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
use Bee\Utils\Strings;
use MODEL;

/**
 * Use this for sending file resources to the browser 
 * 
 * @author Michael Plomer <michael.plomer@iter8.de>
 * @author Benjamin Hartmann
 */
class PassthruView extends AbstractView {

    private $download = false;

    /**
     *  @return boolean
     */
    public function getDownload() {
        return $this->download;
    }

    public function setDownload($download) {
        $this->download = $download;
    }


    public function getContentType() {
		return MODEL::get('mimeType');
	}

	protected function renderMergedOutputModel() {
        $resource = MODEL::get('resource');
        $file = fopen($resource, 'rb');
        $filename = MODEL::get('filename');
        $mimeType = MODEL::get('mimeType');

        $contentDisposition = $this->download ? 'attachment' : 'inline';

        header('Content-Type: '.$mimeType);
        header('Content-Length: ' . filesize($resource));
        if (Strings::hasText($filename)) {
            header('Content-Disposition: '.$contentDisposition.'; filename="'.$filename.'"');
        } else {
            header('Content-Disposition: '.$contentDisposition.'');
        }


//        header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
//        header('Cache-Control: post-check=0, pre-check=0", false'); // HTTP/1
//        header('Pragma: no-cache');
        header('Pragma: public');
//        header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($resource)) . ' GMT');
        header('Cache-Control: max-age=604800, public'); // HTTP/1.1
//        header("Expires: 1");
//        readfile($file);
        fpassthru($file);
	}

}