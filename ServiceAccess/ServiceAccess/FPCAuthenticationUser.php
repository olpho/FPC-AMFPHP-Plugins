<?php
/**
 *   @copyright Copyright (c) 2011, Bastien Aracil
 *   All rights reserved.
 *   New BSD license. See http://en.wikipedia.org/wiki/Bsd_license
 *
 *   Redistribution and use in source and binary forms, with or without
 *   modification, are permitted provided that the following conditions are met:
 *      * Redistributions of source code must retain the above copyright
 *        notice, this list of conditions and the following disclaimer.
 *      * Redistributions in binary form must reproduce the above copyright
 *        notice, this list of conditions and the following disclaimer in the
 *        documentation and/or other materials provided with the distribution.
 *      * The name of Bastien Aracil may not be used to endorse or promote products
 *        derived from this software without specific prior written permission.
 *
 *   THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 *   ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *   WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *   DISCLAIMED. IN NO EVENT SHALL BASTIEN ARACIL BE LIABLE FOR ANY
 *   DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 *   (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 *   LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 *   ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *   (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 *   SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *   @package FPC_AMFPHP_Plugins_ServiceAccess
 */

/**
 * A {@link FPC_IServiceAccessUser} that uses the results of the FPCAuthentication plugin
 * to get the user login and roles.
 *
 * @package FPC_AMFPHP_Plugins_ServiceAccess
 * @author Bastien Aracil
 */
class FPC_FPCAuthenticationUser implements FPC_IServiceAccessUser {

    private $_sessionKey;

    /**
     * @param $sessionKey the Session key used by the FPCAuthentication plugin to save its authentication results.
     */
    public function __construct($sessionKey= "FPCAuthenticationResult") {
        $this->_sessionKey = $sessionKey;
    }

    /**
     * @return string the login of the user
     */
    function getLogin()
    {
        $result = $this->getFPCAuthenticationResult();
        return is_null($result)?null:$result['login'];
    }

    /**
     * @return array of strings of the roles given to the user
     */
    function getRoles()
    {
        $result = $this->getFPCAuthenticationResult();
        return is_null($result)?null:$result['roles'];
    }

    /**
     * @return array an associative array containing the FPCAuthentication plugin results
     */
    private function getFPCAuthenticationResult() {
        if (session_id() == "") {
            session_start();
        }

        $result = null;

        if (isset($_SESSION[$this->_sessionKey])) {
            $result = $_SESSION[$this->_sessionKey];
        }

        return $result;
    }

}
