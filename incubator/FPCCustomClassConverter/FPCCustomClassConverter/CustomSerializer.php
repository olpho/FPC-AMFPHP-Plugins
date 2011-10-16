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
    DISCLAIMED. IN NO EVENT SHALL SILEX LABS BE LIABLE FOR ANY
    DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
    LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
    ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
    SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/


/**
 * User: Bastien Aracil
 * Date: 18/07/11
 */
 
class FPC_CustomSerializer extends Amfphp_Core_Amf_Serializer {

    private $manager;

    public function __construct($data, FPC_IPropertiesManager $manager) {
        parent::__construct($data);
        $this->manager = $manager;
    }

    protected function writeTypedObject($d)
    {
        if ($this->handleReference($d, $this->Amf0StoredObjects)) {
            return;
        }
        $this->writeByte(16); // write  the custom class code

        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $className = $d->$explicitTypeField;
        if (!$className) {
            throw new Amfphp_Core_Exception(Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE . " not found on a object that is to be sent as typed. " . print_r($d, true));
        }
        $this->writeUTF($className); // write the class name
        $objVars = $this->manager->getProperties($d);
        unset($objVars[$explicitTypeField]);
        foreach ($objVars as $key => $data) { // loop over each element
            if ($key[0] != "\0") {
                $this->writeUTF($key); // write the name of the object
                $this->writeData($data); // write the value of the object
            }
        }
        $this->writeObjectEnd();
    }

}
