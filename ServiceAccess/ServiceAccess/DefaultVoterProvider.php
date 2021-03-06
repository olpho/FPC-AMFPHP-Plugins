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
 *
 * The default voter provider.
 *
 * It is a {@link FPC_ComitySAVoterProvider} that handles @rolesAllowed (see {@link FPC_ReflectionRolesSAVoterProvider}), @isCurrentUserLogin (see {@link FPC_CurrentUserLoginSAVoterProvider})
 * and @methodCheck (see {@link FPC_ReflectionMethodSAVoterProvider}) annotations.
 * By default, this provider caches the data into the SESSION. This can be changed by passing false to the constructor (useful when developing).
 *
 * see the main documentation (in {@link ServiceAccess}) for more details
 *
 * @package FPC_AMFPHP_Plugins_ServiceAccess
 * @author Bastien Aracil
 */
class FPC_DefaultVoterProvider extends FPC_ProxySAVoterProvider {

    /**
     * @param bool $cached if true, the provider caches the voters found for a method
     * into the SESSION
     */
    public function __construct($cached = true) {
        parent::__construct(new FPC_ComitySAVoterProvider(
            array(
                 new FPC_ReflectionRolesSAVoterProvider(),
                 new FPC_ReflectionMethodSAVoterProvider(),
                 new FPC_CurrentUserLoginSAVoterProvider())
        ),
        $cached);
    }

}
