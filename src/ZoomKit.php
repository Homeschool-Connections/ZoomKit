<?php

namespace HSC\ZoomKit;

class ZoomKit extends ZoomAPIWrapper {

    /**
     * ZoomKit Main Class
     *
     * MIT License
     * Copyright (c) 2021 Homeschool Connections, Gabriel Sieben
     * with portions of "Zoom API Wrapper" also under MIT License
     * Copyright (c) 2020 Ben Jefferson (skwirrel)
     * Copyright (c) 2020 Other Contributors (bafta-benj)
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in all
     * copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
     * SOFTWARE.
     */

    /**
     * Internal Response Processing
     * @throws Exception
     */
    protected final static function returnResponse(
        string $method,
        string $path,
        array $queryParams = [],
        array $pathParams = [],
        string $body = ''
    ): array|Exception {
        $zoom = new ZoomAPIWrapper(env('ZOOM_KEY'), env('ZOOM_SECRET'));
        $response = $zoom->doRequest(
            $method,
            $path,
            $queryParams,
            $pathParams,
            $body
        );

        if ($response === false) {
            throw new Exception("Errors: ".implode("\n",$zoom->requestErrors()));
        } else {
            if($zoom->responseCode() === 200) {
                return $response;
            } else {
                throw new Exception($response['message'].' (Status Code '.$zoom->responseCode().')');
            }
        }
    }
}
