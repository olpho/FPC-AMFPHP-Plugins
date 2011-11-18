<?php
/*
    Copyright (c) 2011, Bastien Aracil
    All rights reserved.
    New BSD license. See http://en.wikipedia.org/wiki/Bsd_license

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:
       * Redistributions of source code must retain the above copyright
         notice, this list of conditions and the following disclaimer.
       * Redistributions in binary form must reproduce the above copyright
         notice, this list of conditions and the following disclaimer in the
         documentation and/or other materials provided with the distribution.
       * The name of Bastien Aracil may not be used to endorse or promote products
         derived from this software without specific prior written permission.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
    ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    DISCLAIMED. IN NO EVENT SHALL BASTIEN ARACIL BE LIABLE FOR ANY
    DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
    LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
    ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
    SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * User: Bastien Aracil
 * Date: 04/11/11
 */


require_once "ClassLoader.php";

class FPCAuthentication {

    //Name of the emulated service
    const EMULATED_SERVICE_NAME = "fpcAuthentication";

    //configuration key for the secret provider
    const SECRET_PROVIDER_KEY = "secretProvider";

    //configuration key for the role provider
    const ROLES_PROVIDER_KEY = "rolesProvider";

    //configuration key for the challenge solver
    const CHALLENGE_SOLVER_KEY = "challengeSolver";

    //configuration key for the result builder;
    const BUILDER_KEY = "builder";

    //configuration key for the challenge provider
    const CHALLENGE_PROVIDER_KEY = "challengeProvider";

    //session key for the loginService configuration
    const FPC_LOGIN_CONFIG_KEY = "FPCAuthenticationConfig";

    //session key for the common secret key
    const FPC_COMMON_SECRET_KEY = "FPCAuthenticationCommonSecret";

    /**
     * @var Amfphp_Core_Common_ClassFindInfo
     */
    private $loginServiceClassInfo;

    /**
     * @var FPCAuthentication_LoginServiceConfig
     */
    private $loginServiceConfig;

    public function __construct(array $config = null) {

        //hook the plugin
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERVICE_NAMES_2_CLASS_FIND_INFO, $this, "filterServiceNames2ClassFindInfo");
        $filterManager->addFilter(Amfphp_Core_Common_ServiceRouter::FILTER_SERVICE_OBJECT, $this, "filterServiceObject");

        //prepare the plugin default configuration
        $this->loginServiceConfig = new FPCAuthentication_LoginServiceConfig();
        $this->loginServiceConfig->setDefaultBuilder(new FPCAuthentication_DefaultBuilder());
        $this->loginServiceConfig->setDefaultRolesProvider(new FPCAuthentication_DefaultRolesProvider());
        $this->loginServiceConfig->setDefaultChallengeSolver(new FPCAuthentication_DefaultChallengeSolver());
        $this->loginServiceConfig->setDefaultChallengeProvider(new FPCAuthentication_DefaultChallengeProvider());

        if ($config) {
            if (isset($config[self::SECRET_PROVIDER_KEY])) {
                $this->loginServiceConfig->setSecretProvider($config[self::SECRET_PROVIDER_KEY]);
            }
            if (isset($config[self::ROLES_PROVIDER_KEY])) {
                $this->loginServiceConfig->setRolesProvider($config[self::ROLES_PROVIDER_KEY]);
            }
            if (isset($config[self::BUILDER_KEY])) {
                $this->loginServiceConfig->setBuilder($config[self::BUILDER_KEY]);
            }
            if (isset($config[self::CHALLENGE_SOLVER_KEY])) {
                $this->loginServiceConfig->setBuilder($config[self::CHALLENGE_SOLVER_KEY]);
            }
            if (isset($config[self::CHALLENGE_PROVIDER_KEY])) {
                $this->loginServiceConfig->setBuilder($config[self::CHALLENGE_PROVIDER_KEY]);
            }
        }

        $this->loginServiceConfig->validate();
        $this->loginServiceClassInfo = new Amfphp_Core_Common_ClassFindInfo(dirname(__FILE__)."/LoginService.php","FPCAuthentication_LoginService");

    }

    public function filterServiceNames2ClassFindInfo($serviceNames2ClassFindInfo) {
        $serviceNames2ClassFindInfo[self::EMULATED_SERVICE_NAME] = $this->loginServiceClassInfo;
        return $serviceNames2ClassFindInfo;
    }

    public function filterServiceObject($serviceObject, $serviceName, $methodName, $parameters) {
        $allowedNotAuthenticated = false;


        if ($serviceName == self::EMULATED_SERVICE_NAME) {
            $this->setConfiguration($serviceObject);
            $allowedNotAuthenticated = true;
        }
        else {
            $allowedNotAuthenticated = $this->allowedNotAuthenticated($serviceName, $methodName);
        }

        if (!$allowedNotAuthenticated && !$this->isAuthenticated()) {
            throw new FPCAuthentication_Exception("Call to $serviceName.$methodName is not allowed for not authenticated user");
        }

        return $serviceObject;
    }

    private function setConfiguration(FPCAuthentication_LoginService $service) {
        $service->setConfig($this->loginServiceConfig);
    }

    private function allowedNotAuthenticated($serviceName, $methodName)
    {
        //TODO allows the user to configure a white and black list of service/method that can be called
        //even if the user is not authenticated.
        // For now, allows any method.
        return true;
    }

    /**
     * @return bool
     */
    private function isAuthenticated() {
        $result = FPCAuthentication_Result::getResult();
        return $result->getAuthenticated();
    }

}
